<?php 

use yii\helpers\Json;
use id2Auth\Module;
use id2Auth\assetBundles\ButtonAsset;

$M = Module::$instance;

$this->registerJs(
    'var id2AuthConfig = ' . Json::htmlEncode([
        'appId' => $M->appId,
        'publicationCode' => $M->publicationCode,
        'id2AuthButtonContainer' => 'panelcontainer',
        'regLink' => $M->registrationUrl,
        'currentUserId' => Yii::$app->user->isGuest ? 0 : Yii::$app->user->identity->profile->id,
        'loginUrl' => Yii::$app->urlManager->createUrl([$M->id . '/auth/login']),
        'logoutUrl' => Yii::$app->urlManager->createUrl([$M->id . '/auth/logout']),
        'customUrl' => Yii::$app->urlManager->createUrl([$M->id . '/auth/custom']),
        'id2Auth' => is_null($id2Auth) ? 'id2Auth' : $id2Auth,
        'id2NoAuth' => is_null($id2NoAuth) ? 'id2NoAuth' : $id2NoAuth,
        'id2Custom' => is_null($id2Custom) ? 'id2Custom' : $id2Custom,
    ]) . ';',
    static::POS_HEAD,
    'id2Config'
);

ButtonAsset::register($this);

?>

<div id="panelcontainer" class="noclass"></div>
