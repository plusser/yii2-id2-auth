<?php 

namespace id2Auth\widgets;

use yii\base\Widget;

class TopPanel extends Widget
{

    public function run()
    {
        return $this->render('TopPanel/view');
    }

}
