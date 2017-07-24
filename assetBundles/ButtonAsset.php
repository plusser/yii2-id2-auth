<?php 

namespace id2Auth\assetBundles;

class ButtonAsset extends BaseAsset
{

    public $js = [
        'js/Button.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];

}
