<?php 

namespace id2Auth;

use Yii;
use yii\web\Controller;
use id2Auth\models\User;
use id2Auth\widgets\AuthForm;

class Module extends \yii\base\Module
{

    public $appId;
    public $publicationCode;
    public $secureKey;
    public $editionAccessId = [];

    public $urlPrefix = 'auth';

    public $serviceWSDL = 'https://id2.action-media.ru/api/soap?wsdl';
    

    protected $_service;

    public static $instance;

    public function init()
    {
        parent::init();

        if(($app = Yii::$app) instanceof \yii\web\Application){
            $app->getUrlManager()->addRules([
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->urlPrefix . '/login', 'route' => $this->id . '/auth/login'],
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->urlPrefix . '/logout', 'route' => $this->id . '/auth/logout'],
                ['class' => 'yii\web\UrlRule', 'pattern' => $this->urlPrefix . '/custom', 'route' => $this->id . '/auth/custom'],
            ], false);

            $app->user->loginUrl = NULL;
        }

        static::$instance = $this;

        Yii::$app->on(Controller::EVENT_BEFORE_ACTION, [$this, 'checkUrlParams']);
    }

    public function getRegistrationUrl()
    {
        return 'https://id2.action-media.ru/Account/Registration?' . $this->signParams->urlParams;
    }

    public function getRemindPasswordUrl()
    {
        return 'https://id2.action-media.ru/api/rest/Invoke?' . $this->getSignParams([
            'format' => 'jsonp',
            'method' => 'RemindPassword',
        ])->urlParams;
    }

    public function getSignParams($P = [])
    {
        $params = [
            'appId' => $this->appId,
            'callbackUrl' => urlencode(Yii::$app->request->absoluteUrl),
            'rand' => intval(rand(10000, 99999)),
        ] + $P;

        ksort($params);

        $paramsString = '';
        foreach($params as $k => $v){
            $paramsString .= $k . $v;
        }

        $params['sig'] = md5(md5(strtolower($paramsString) . $this->appId) . $this->secureKey);

        $urlParams = [];

        foreach($params as $k => $v){
            $urlParams[] = $k . '=' . $v;
        }

        return json_decode(json_encode([
            'params' => $params,
            'urlParams' => implode('&', $urlParams),
        ]));
    }

    public function getService()
    {
        if(is_null($this->_service)){
            $this->_service = new \SoapClient($this->serviceWSDL);
        }

        return $this->_service;
    }

    public function checkUrlParams()
    {
        foreach([
            'token' => function($token){
                if(is_object($user = User::findIdentityByAccessToken($token))){
                    Yii::$app->user->login($user);
                }
            },
            'activityId' => function($activityId){
                if(!is_null($E = Yii::$app->request->getQueryParam('error'))){
                    AuthForm::setError($E);
                }
            },
        ] as $paramName => $handler){
            if(!is_null($param = Yii::$app->request->getQueryParam($paramName))){
                $handler($param);
                Yii::$app->response->redirect($this->clearUrl, 301);
                Yii::$app->end();
            }
        }
    }

    protected function getClearUrl()
    {
        $params = [];

        foreach(Yii::$app->request->queryParams as $k => $v){
            if(!in_array($k, ['token', 'ttl', 'activityId'])){
                $params[] = $k . '=' . $v;
            }
        }

        return str_replace(Yii::$app->request->queryString, implode('&', $params), Yii::$app->request->url);
    }
}
