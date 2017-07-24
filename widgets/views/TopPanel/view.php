<?php 

use id2Auth\assetBundles\TopPanelAsset;
use id2Auth\widgets\Button;
use settings\helpers\SettingsHelper;

TopPanelAsset::register($this);

?>

<div class="rx-userbar black_panel">
    <div class="rx-userbar-inner">
        <div class="rx-userbar-right"><?php echo Button::widget(); ?></div>
        <div class="rx-userbar-left blck-pan">
            <div class="blck-pan rx-logo rx-logo-alpha">&lt;Актион-МЦФЭР&gt;</div>
            <div class="block_link">
                <?php echo SettingsHelper::get('id2-top-panel-content'); ?>
            </div>
        </div>
    </div>
</div>
