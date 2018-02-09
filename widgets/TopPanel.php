<?php 

namespace id2Auth\widgets;

use yii\base\Widget;

class TopPanel extends Widget
{

    public $id2Auth = NULL;
    public $id2NoAuth = NULL;
    public $id2Custom = NULL;

    public function run()
    {
        return $this->render('TopPanel/view');
    }

}
