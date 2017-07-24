<?php 

namespace id2Auth\assetBundles;

class AuthFormAsset extends BaseAsset
{

    public $css = [
        'css/AuthForm.css',
    ];

    public $js = [
        'js/rx-login.js',
    ];

    public $depends = [
        'id2Auth\assetBundles\TopPanelAsset',
    ];

}
