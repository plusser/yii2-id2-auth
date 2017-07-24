<?php 

namespace id2Auth\widgets;

use yii\base\Widget;

class Button extends Widget
{

    public function run()
    {
        return $this->render('Button/view');
    }

}
