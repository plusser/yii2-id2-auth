var aktion = aktion || [];

(function(){
    var s = document.createElement('script');
    s.type = 'text/javascript';
    s.async = true;
    s.src = '//m.action-media.ru/js/all.2.js';
    var es = document.getElementsByTagName('script')[0];
    es.parentNode.insertBefore(s, es);
})();

window.AsyncInit = function (){
    aktionid.init({
        appid: id2AuthConfig.appId,
        emid: id2AuthConfig.publicationCode,
        rater: false,
        loginblock: id2AuthConfig.id2AuthButtonContainer,
        style: 'default',
        supportlink: 'https://id2.action-media.ru/Feedback',
        reglink: id2AuthConfig.regLink,
        lang: 'ru',
    });

    aktionid.subscribe('status.auth', id2AuthConfig.id2Auth);
    aktionid.subscribe('status.noauth', id2AuthConfig.id2NoAuth);
    aktionid.subscribe('user.custom', id2AuthConfig.id2Custom);
};

function id2Auth(data){
    if(data.user.id != id2AuthConfig.currentUserId){
        $.ajax({
            type: 'POST',
            url: id2AuthConfig.loginUrl,
            data: 'token=' + data.status.token,
            success: function(data){
                console.log(data);

                if(data.status){
                    location.reload();
                }
            }
        });
    }
}

function id2NoAuth(){
    if(id2AuthConfig.currentUserId != 0){
        $.ajax({
            type: 'POST',
            url: id2AuthConfig.logoutUrl,
            success: function(data){
                console.log(data);

                location.reload();
            }
        });
    }
}

function id2Custom(data){
    $.ajax({
        type: 'POST',
        data: {data: data.data},
        url: id2AuthConfig.customUrl
    });
}
