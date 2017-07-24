$(function() {

    'use strict';

    // ----- Begin Демонстрация и сокрытие всплывающего окна -----

//    function popupBounce(bounces, speed) {
//        var initialRight = popup.css("right");
//        var rightNumber = Number(initialRight.slice(0, -2));
//        var distance = Math.pow(2, bounces - 2);
//
//        for (var i = 0; i < bounces; i++) {
//            popup.animate({
//                "right" : distance + rightNumber
//            }, speed * (1 - Math.pow(i / bounces, 2)), 'easieEaseOutSine');
//            distance = Math.floor(distance / -2);
//        }
//
//        popup.promise().done(function() {
//            popup.css({
//                "right" : initialRight
//            });
//        });
//    }

//    function popupClose() {
//        popup.addClass('rx-hidden');
//        $('.rx-auth').removeClass('rx-open');
//    }

//    function popupOpen($button) {
//        var container = $button.parent();
//
//        popup.removeClass('rx-hidden');
//        container.addClass('rx-open');
//
//        var offset = $button.offset();
//
//        $(window).resize(function() {
//            var offset = $button.offset();
//        });
//        $(window).trigger("resize");
//
//        if (popup.find('#rx-user-field').val() != '') {
//            popup.find("#rx-pass-field").focus();
//        } else {
//            popupFocusElem.focus();
//        }
//    }

    // ----- End Демонстрация и сокрытие всплывающего окна -----

    function isIE8() {
        var rv = -1;
        var ua = navigator.userAgent;
        var re = new RegExp("Trident\/([0-9]{1,}[\.0-9]{0,})");
        if (re.exec(ua) != null) {
            rv = parseFloat(RegExp.$1);
        }
        return (rv == 4);
    }

    // Проверка, поддерживает ли браузер работу с локальным хранилищем
    function supportsStorage() {
        try {
            return 'localStorage' in window && window['localStorage'] !== null;
        } catch (e) {
            return false;
        }
    }

    // Определяем, в какой форме авторизации показать ошибку
    function getLastFormName() {
        var rxForms = $('.rx-form');
        if (rxForms.length == 1) {
            return 'HeadForm';
        }
        rxForms = rxForms.eq(1);
        return rxForms.attr('name');
    }

    var isIE8 = isIE8();
    var popup = $('#rx-popup');

    popup.appendTo($("body"));

    // ----- Begin Фиксы для ie8 -----
    if (isIE8) {

        popup.addClass("ie8");

        popup.find("[placeholder]").each(function() {
            var $input = $(this);
            if ($input.hasClass("rx-first")) {
                var text = "Эл. почта<br/>или логин";
            } else {
                var text = $input.attr("placeholder");
            }
            var $label = $("<label class='ie-label'>" + text + "</label>");

            $label.insertBefore($input);
        });

        $("#rx-form").addClass("ie8").find("[placeholder]").each(function() {
            var $input = $(this);
            if ($input.hasClass("rx-first")) {
                var text = "Эл. почта<br/>или логин";
            } else {
                var text = $input.attr("placeholder");
            }
            var $label = $("<label class='ie-label'>" + text + "</label>");

            $label.insertBefore($input);
        });

    }
    // ----- End Фиксы для ie8 -----

//    var popupWidth = popup.width();
//    var popupFocusElem = popup.find('input:first');

    var registered = ($("#rx-user-field").val() != '');
    var isStorageAvailable = supportsStorage();
    var issetLoginError = false;

    if (registered) {
        $("#rx-form #rx-pass-field").focus();
    }

    // Ссылка "Вход" в верхней панели авторизации
    $(document).on('click', '.rx-auth > .rx-button', function(event) {
        event.preventDefault();
        var container = $(this).parent();
        if (container.hasClass('rx-open')) {
//            popupClose();
        } else {
            if (!$(this).hasClass('clicked')) {
                // GA: первый клик по кнопке "Вход"
                _gaq.push(['_trackEvent', 'HeadForm', 'PopUp', 'Ok', null, 'true']);
                $(this).addClass('clicked');
            }
//            popupOpen($(this), container);
        }
        event.stopPropagation();
    });

//    $(document).on('click', function() {
//        popupClose();
//    });

    popup.on('click', function(event) {
        event.stopPropagation();
    });

//    $(document).on('keyup', function(event) {
//        if (event.keyCode === 27) {
//            popupClose();
//        }
//    });

    $(".rx-textbox").on("keyup paste blur focus change", function() {
        var $input = $(".rx-textbox[type='password']");
        var $link = $input.parent().find("#rx-pass-remind");
        if ($input.val() != "") {
            $link.hide();
        } else {
            $link.show();
        }
    });

    // Форма авторизации, которую мы считаем активной
    var lastForm = (isStorageAvailable) ? localStorage.getItem('lastRxForm') : getLastFormName();

    $('.rx-form').each(
        function() {

            var $form = $(this);
            var formName = $form.attr('name');

            if (isStorageAvailable)
                $form.find('#rx-user-field').val(localStorage.getItem('lastRxUser'));

            // ----- Begin Действия при отображении формы, если сработал
            // автокомплит -----
            var checkAutocomplete = function() {
                $('#rx-pass-field').each(function() {
                    if ($(this).val() != '') {
                        $("#rx-pass-remind").hide();
                        $("#rx-user-field").unbind('focus', checkAutocomplete);
                        $('body').unbind('click', checkAutocomplete);
                    }
                });
            };
            $("#rx-user-field").bind('focus', checkAutocomplete);
            $('body').bind('click', checkAutocomplete);
            // ----- End Действия при отображении формы, если сработал
            // автокомплит -----

            // "Ввести пароль"
            $form.find('#rx-pass-read').on(
                'click',
                function() {
                    $form.find('#rx-form-submit').removeClass('rx-disabled').removeClass('rx-disabled-ie8').prop(
                        'disabled', false);
                    $form.find('#rx-pass-reading').removeClass('rx-hidden');
                    $form.find('#rx-pass-reminding').addClass('rx-hidden');
                });

            // ----- Begin Напоминание пароля -----
            $form.find('#rx-pass-remind').on('click', function() {
            
                if ($(this).attr('disabled') == 'disabled') {
                    return false;
                }
                $(this).css({
                    'display' : 'none'
                });
                $(this).attr('disabled', 'disabled');

                if (formName != 'MainForm') {
                    // GA: напоминание пароля
                    _gaq.push(['_trackEvent', formName, 'PassRemind', 'Send', null, 'true']);
                } else {
                    // GA: напоминание пароля (форма на странице)
                    _gaq.push(['_trackEvent', formName, 'PassRemind', location.pathname, null, 'true']);
                }

                // Отправка данных в id2
                $form.find('#rx-hint-wrong-user').addClass('rx-hidden');
                var loginVal = $form.find('#rx-user-field').val();
                if (loginVal) {
                    var remindLink = $form.find('#rx-pass-remind');
                    var remindRequest = remindLink.data('request');
                    $.ajax({
                        url : remindRequest + '&email=' + loginVal,
                        // dataType : 'jsonp', // С этим параметром перестало работать в chrome
                    }).done(function(data) {
                        if (data.Error.Code == '0') {
                            // Error.Code = 0 от id2
                            if (isIE8) {
                                $form.find('#rx-form-submit').addClass('rx-disabled-ie8');
                            } else {
                                $form.find('#rx-form-submit').addClass('rx-disabled').prop('disabled', true);
                            }

                            $form.find('#rx-pass-reminding').removeClass('rx-hidden');
                            $form.find('#rx-hint-empty-user').addClass('rx-hidden');
                            $form.find('#rx-pass-reading').addClass('rx-hidden');
                        } else {
                            // Error.Code = -1 от id2
                            $form.find('#rx-hint-wrong-user').removeClass('rx-hidden');
                            $form.find('#rx-user-field').addClass('rx-error');
                        }
                    });
                } else {
                    //popupBounce(6, 60);
                    $form.find('#rx-hint-empty-user').removeClass('rx-hidden');
                    $form.find('#rx-user-field').addClass('rx-error');
                }
                $(this).removeAttr('disabled');
                $(this).removeAttr('style');
            });
            // ----- End Напоминание пароля -----

            // Событие при клике "Зарегистрироваться"
            $form.find('a.rx-registration').on('click', function() {
                if (formName == 'MainForm') {
                    // GA: клик на ссылке "зарегистрироваться"
                    _gaq.push(['_trackEvent', formName, 'GoToReg', location.pathname, null, 'true']);
                } else {
                    // GA: клик на ссылке "зарегистрироваться"
                    _gaq.push(['_trackEvent', formName, 'Registration', 'GoToReg', null, 'true']);
                }
                return true;
            });

            // Валидация
            $(document).on('keydown input change cut paste', '.rx-error', function() {
                $(this).removeClass('rx-error');
            });

            $(document).on('keydown input change cut paste', '#rx-pass-field', function() {
                $('#rx-hint-empty-pass').addClass('rx-hidden');
            });

            $form.on('submit', function(event) {
                var user = $form.find('#rx-user-field').val();
                var pass = $form.find('#rx-pass-field').val();

                var emptyUser = false;
                var wrongUser = false;
                var wrongPass = false;
                var emptyPass = false;

                if (!user) {
                    emptyUser = true;
                } else {
                    if (!pass) {
                        emptyPass = true;
                    }
                }

                if ($form.find('#rx-form-submit').hasClass('rx-disabled-ie8')) {
                    emptyPass = true;
                    wrongPass = false;
                    emptyUser = false;
                    wrongUser = false;
                }

                $form.find('#rx-hint-empty-user').toggleClass('rx-hidden', !emptyUser);
                $form.find('#rx-hint-wrong-user').toggleClass('rx-hidden', !wrongUser);
                $form.find('#rx-hint-wrong-pass').toggleClass('rx-hidden', !wrongPass);
                $form.find('#rx-hint-empty-pass').toggleClass('rx-hidden', !emptyPass);

                $form.find('#rx-user-field').toggleClass('rx-error', emptyUser || wrongUser);
                $form.find('#rx-pass-field').parent().toggleClass('rx-error', wrongPass);

                if (isStorageAvailable) {
                    localStorage.setItem('lastRxUser', user);
                    localStorage.setItem('lastRxForm', formName);
                }

                if (wrongPass || wrongUser || emptyUser || emptyPass) {
                    //popupBounce(6, 60);
                    if (emptyUser) {
                        // GA: нажата кнопка "войти", но пустой логин
                        _gaq.push(['_trackEvent', formName, 'FieldBlank', 'login', null, 'true']);
                    } else if (emptyPass) {
                        // GA: нажата кнопка "войти", но пустой пароль
                        _gaq.push(['_trackEvent', formName, 'FieldBlank', 'Password', null, 'true']);
                    }
                } else {
                
                    if (formName == 'MainForm') {
                        // GA: Отправка данных на сервер авторизации (форма авторизации на странице)
                        _gaq.push(['_trackEvent', formName, 'Login', location.pathname, null, 'true']);
                    } else {
                        //@todo сделать нормально. Используем для отправки GA события Login через кнопку id2.
                        $.cookie("ASE_loginFromId2LoginForm", formName);
                    }
                    
                    return true;
                }
                event.preventDefault();
            });
            
            if (!$form.find('#rx-hint-wrong-user').hasClass('rx-hidden')) {
                issetLoginError = true;
            }

        });
    
    
    if (issetLoginError) {
        if (lastForm == 'MainForm') {
            _gaq.push(['_trackEvent', lastForm, 'LoginErr', location.pathname, null, 'true']);
        } else {
            _gaq.push(['_trackEvent', lastForm, 'LoginErr', 'Error', null, 'true']);
        }
    }

});