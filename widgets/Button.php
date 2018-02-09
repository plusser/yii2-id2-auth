<?php 

namespace id2Auth\widgets;

use yii\base\Widget;

class Button extends Widget
{

    public $id2Auth = NULL;
    public $id2NoAuth = NULL;
    public $id2Custom = NULL;

    public function run()
    {
        return $this->render('Button/view', [
            'id2Auth' => $this->id2Auth,
            'id2NoAuth' => $this->id2NoAuth,
            'id2Custom' => $this->id2Custom,
        ]);
    }

}
