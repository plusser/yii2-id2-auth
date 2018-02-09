<?php 

namespace id2Auth\assetBundles;

class CustomAsset extends BaseAsset
{

    public $js = [
        'js/customEventManager.js',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD,
    ];

}
