<?php 

namespace id2Auth\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use id2Auth\models\User;

class AuthController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['login', 'logout', 'custom', ],
                'rules' => [
                    [
                        'actions' => ['login', ],
                        'allow' => TRUE,
                        'verbs' => ['POST', ],
                    ],
                    [
                        'actions' => ['logout', 'custom', ],
                        'allow' => TRUE,
                        'roles' => ['@'],
                        'verbs' => ['POST', ],
                    ],
                ],
            ],
        ];
    }

    public function actionLogin()
    {
        $result = FALSE;

        if(!is_null($token = Yii::$app->request->post('token')) AND is_object($user = User::findIdentityByAccessToken($token))){
            $result = Yii::$app->user->login($user);
        }

        return $this->response(['status' => $result, ]);
    }

    public function actionLogout()
    {
        return $this->response([
            'status' => Yii::$app->user->logout(),
        ]);
    }

    public function actionCustom()
    {
        $result = FALSE;

        if(!is_null($data = Yii::$app->request->post('data'))){
            Yii::$app->user->identity->customFields = $data;
            $result = TRUE;
        }

        return $this->response(['status' => $result, ]);
    }

    protected function response($data)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->response->headers->set('Content-Type', 'application/json; charset=UTF-8');

        return $data;
    }

}
