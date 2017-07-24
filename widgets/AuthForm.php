<?php 

namespace id2Auth\widgets;

use Yii;
use yii\base\Widget;

class AuthForm extends Widget
{

    public $popup = FALSE;

    public static function setError($value)
    {
        Yii::$app->session->set(static::getErrorKey(), $value);
    }

    public static function getError()
    {
        return Yii::$app->session->get(static::getErrorKey());
    }

    protected static function getErrorKey()
    {
        return static::className() . '::ERROR';
    }

    public function run()
    {
        return $this->render('AuthForm/view');
    }

}
