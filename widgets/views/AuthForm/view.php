<?php 

use id2Auth\assetBundles\AuthFormAsset;
use settings\helpers\SettingsHelper;
use id2Auth\Module;

AuthFormAsset::register($this);

$context = $this->context;
$signParams = Module::$instance->signParams;

?>

<?php if($context->popup){ ?>
<div class="popup-container">
    <div class="popup-container-content">
<?php } ?>

<form
    autocomplete="off"
    name="id2-auth-form"
    action="https://id2.action-media.ru/Account/Login"
    method="POST"
    accept-charset="utf-8"
    class="rx-box rx-box-inline rx-form">

    <input type="hidden" name="appid" value="<?php echo $signParams->params->appId; ?>" />
    <input type="hidden" name="sig" value="<?php echo $signParams->params->sig; ?>" />
    <input type="hidden" name="rand" value="<?php echo $signParams->params->rand; ?>" />
    <input type="hidden" name="callbackurl" value="<?php echo $signParams->params->callbackUrl; ?>" />

    <div class="rx-box-main">
        <div class="rx-p">
            <div class="rx-h1"><?php echo SettingsHelper::get('id2-auth-form-header', 'Вход на сайты изданий медиагруппы «Актион-МЦФЭР»'); ?></div>

            <p class="rx-p"><?php echo SettingsHelper::get('id2-auth-form-text'); ?></p>

            <div class="rx-cascade">

                <div class="rx-cascade-1">
                    <div class="rx-h2">У меня есть пароль</div>
                    <div class="rx-textbox-group">
                        <input
                            class="rx-textbox rx-first"
                            id="rx-user-field"
                            placeholder="Эл. почта или логин"
                            type="text"
                            name="login" />
                        <div class="rx-textbox-append rx-last" id="rx-pass-reading">
                            <input class="rx-textbox" id="rx-pass-field" placeholder="Пароль" type="password" name="pass" />
                            <div class="rx-textbox-addon">
                                <span
                                    class="rx-link rx-link-black rx-link-pseudo"
                                    id="rx-pass-remind"
                                    data-request="<?php echo Module::$instance->remindPasswordUrl; ?>">
                                    напомнить
                                </span>
                            </div>
                        </div>
                        <div class="rx-textbox-append rx-transparent rx-hidden rx-last" id="rx-pass-reminding">
                            <span class="rx-textbox">Пароль отправлен на почту</span>
                            <div class="rx-textbox-addon">
                                <span class="rx-whatever" id="rx-pass-read">Ввести</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="rx-cascade-2"></div>
                <div class="rx-cascade-3">
                    <div class="rx-h2">Я тут впервые</div>
                    <div class="rx-h2">
                        <a class="paywall-reg-button rx-registration" href="<?php echo Module::$instance->registrationUrl; ?>"><span><?php echo SettingsHelper::get('id2-auth-form-registration-button-text', 'Зарегистрироваться'); ?></span></a>
                    </div>                        
                    <div class="rx-p"><?php echo SettingsHelper::get('id2-auth-form-text-under-registration-button'); ?></div>
                </div>

            </div>
        </div>

        <div class="rx-cascade">
            <div class="rx-cascade-1 rx-cascade-hint">
                <div class="rx-submit">
                    <button class="rx-button rx-button-branded rx-button-large" id="rx-form-submit" type="submit">Войти</button>
                    <div class="rx-submit-hint rx-hidden" id="rx-hint-empty-user">Введите <nobr>эл. почту</nobr> или логин</div>
                    <div class="rx-submit-hint<?php if(!$context::getError()){ ?> rx-hidden<?php }else{$context::setError(NULL);} ?>" id="rx-hint-wrong-user">Неверный логин или пароль</div>
                    <div class="rx-submit-hint rx-hidden" id="rx-hint-wrong-pass">Неверный пароль</div>
                    <div class="rx-submit-hint rx-hidden" id="rx-hint-empty-pass">Введите пароль</div>
                </div>
            </div>
            <div class="rx-cascade-2"></div>
        </div>
    </div>
    <div class="rx-box-footer rx-footer rx-footer-inline">
        <div class="rx-logo rx-logo-bravo">&lt;Актион-МЦФЭР&gt;</div>
        Медиагруппа и сеть профессиональных сайтов
    </div>
</form>

<?php if($context->popup){ ?>
    </div>
</div>
<script>
document.body.style.overflow = 'hidden';
</script>
<?php } ?>
