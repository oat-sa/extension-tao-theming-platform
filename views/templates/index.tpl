<?php use oat\tao\helpers\Template; ?>
<link rel="stylesheet" href="<?= Template::css('platform-customizer.css') ?>"/>
<div class="main-container flex-container-main-form platform-theme-selector">
    <h1><?= get_data('formTitle');?></h1>


    <div class="stylesheet-upload">
        <style>
            .stylesheet-upload .form-content {
                width: auto !important;
            }
            .stylesheet-upload .action-bar {
                margin: 0;
            }
        </style>
        <div class="form-content">
            <p class="action-bar"><?=__('Upload your custom CSS')?> </p>
            <div id="upload-stylesheet" data-url="<?=_url('upload', 'Main');?>"></div>
        </div>
    </div>


    <!-- disabled temporarily
    <div>
        <h3><?= __('Click on the elements below to change the appearance of the platform.')?></h3>
        <div class="customizer-selection-area">
            <header class="dark-bar clearfix customizer-trigger" data-local=".platform-theme-selector header, .platform-theme-selector footer" data-target=".dark-bar" data-popup=".platform-theme-selector .customizer-header-colorpicker">
                <a href="<?=get_data('logo_src')?>" title="<?=get_data('logo_title')?>" class="lft customizer-trigger" data-popup=".platform-theme-selector .logo-upload-form" target="_blank">
                    <img src="<?=get_data('logo')?>" alt="TAO Logo" class="logo">
                </a>
                <nav>
                    <ul class="plain clearfix lft main-menu">
                        <li class="active customizer-trigger" data-calculus=".dark-bar nav li>a:hover" data-local=".platform-theme-selector .main-menu li.active, .platform-theme-selector .main-menu li.active a" data-target=".dark-bar nav li.active>a" data-popup=".platform-theme-selector .customizer-active-tab-colorpicker">
                            <a href="#">
                                <span class="icon-item glyph"></span> Active Tab</a>
                        </li>
                        <li class="inactive customizer-trigger">
                            <a href="#">
                                <span class="icon-test glyph"></span> Inactive Tab</a>
                        </li>
                    </ul>
                </nav>
                <span class="part-description"><?=('Header and Footer')?></span>
            </header>
            <ul class="plain action-bar content-action-bar horizontal-action-bar customizer-trigger" data-important="true" data-calculus=".action-bar.horizontal-action-bar li.btn-info:hover, .action-bar.horizontal-action-bar li.btn-info.active" data-local=".platform-theme-selector .content-action-bar, .platform-theme-selector .action-bar li.active .li-inner" data-target=".action-bar, .section-container .tab-container li.active, .section-container .tab-container li:hover" data-popup=".platform-theme-selector .customizer-action-bar-colorpicker">
                <li class="action btn-info small active">
                    <a class="li-inner" href="#">
                        <span class="icon-edit glyph"></span> Button </a>
                </li>
                <li class="part-description">Action Bar</li>
            </ul>
            <footer class="dark-bar customizer-trigger" data-local=".platform-theme-selector footer, .platform-theme-selector header" data-target=".dark-bar" data-popup=".platform-theme-selector .customizer-header-colorpicker">
                <span class="part-description"><?=('Footer and Header')?></span>
            </footer>
            <div class="customizer-color-picker customizer-popup customizer-header-colorpicker">
                <p class="action-bar"><?=__('Header and Footer')?> <span class="closer"></span></p>
                <div class="color-picker background">
                    <p><?=__('Background color')?></p>
                    <input type="hidden" value="<?=get_data('header-background')?>" name="header-background">
                </div>
                <div class="color-picker color">
                    <p><?=__('Text Color')?></p>
                    <input type="hidden" value="<?=get_data('header-color')?>" name="header-color">
                </div>
            </div>
            <div class="customizer-color-picker customizer-popup customizer-active-tab-colorpicker">
                <p class="action-bar"><?=__('Active tab')?> <span class="closer"></span></p>
                <div class="color-picker background">
                    <p><?=__('Background Color')?></p>
                    <input type="hidden" value="<?=get_data('active-background')?>" name="active-background">
                </div>
                <div class="color-picker color">
                    <p><?=__('Text Color')?></p>
                    <input type="hidden" value="<?=get_data('active-color')?>" name="active-color">
                </div>
            </div>
            <div class="customizer-color-picker customizer-popup customizer-action-bar-colorpicker">
                <p class="action-bar"><?=__('Action bar')?> <span class="closer"></span></p>
                <div class="color-picker background">
                    <p><?=__('Background Color')?></p>
                    <input type="hidden" value="<?=get_data('action-background')?>" name="action-background">
                </div>
                <div class="color-picker color">
                    <p><?=__('Text Color')?></p>
                    <input type="hidden" value="<?=get_data('action-color')?>" name="action-color">
                </div>
            </div>
        </div>
        <h3>Miscellaneous</h3>
        <div>
            <div>
                <label>
                    <?= __('Stable status') ?>
                    <input type="checkbox" id="is-platform-stable" <?= (get_data('stable') === true) ? 'checked="checked"' : '' ?>/>
                    <span class="icon-checkbox"></span>    
                </label>
            </div>
            <div>
                <label for="platform-login-message"><?= __('Login message') ?>:</label><input type="text" id="platform-login-message" style="width:300px;" value="<?= get_data('login_msg') ?>"/>
            </div>
            <div>
                <label for="platform-login-label"><?= __('Login field') ?>:</label><input type="text" id="platform-login-label" style="width:300px;" value="<?= get_data('login_field') ?>"/>
            </div>
            <div>
                <label for="platform-password-label"><?= __('Password field') ?>:</label><input type="text" id="platform-password-label" style="width:300px;" value="<?= get_data('password_field') ?>"/>
            </div>
            <div>
                <label for="platform-password-label"><?= __('Copyright notice') ?>:</label><input type="text" id="platform-copyright-notice" style="width:300px;" value="<?= get_data('copyright_notice') ?>"/>
            </div>
        </div>
        <div class="button-bar">
            <button class="btn-info small rgt save" type="button"><span class="icon-save"></span><?=__('Save')?></button>
            <button class="btn-info small rgt customizer-trigger" data-popup=".platform-theme-selector .stylesheet-upload"type="button"><span class="icon-upload"></span><?=__('Upload CSS')?></button>
            <? if(has_data('css-file')):?>
            <a class="btn-info small rgt" target="dwl" href="<?=_url('getFile', 'Main', 'taoThemingPlatform', get_data('css-file'));?>">
                <span class="icon-download"></span>
                <?= __('Download CSS')?>
            </a>
            <iframe name="dwl" class="viewport-hidden"></iframe>
            <?endif;?>
        </div>
    </div>

    <div class="stylesheet-upload customizer-popup">
        <div class="form-content">
            <p class="action-bar"><?=__('Upload your custom CSS')?> <span class="closer"></span></p>
            <div id="upload-stylesheet" data-url="<?=_url('upload', 'Main');?>"></div>
        </div>
    </div>

    <div class="logo-upload-form customizer-popup">
        <div class="form-content">
            <div class="xhtml_form">
                <p class="action-bar"><?=__('Upload your logo')?> <span class="closer"></span></p>
                <?=get_data('myForm')?>
            </div>
        </div>
    </div>
    / disabled temporarily -->
</div>

<div class="confirm-modal-feedback modal">
    <div class="modal-body clearfix">
        <p><?= __('This will override your previous theme') ?></p>

        <div class="rgt">
            <button class="btn-regular small cancel" type="button"><?= __('Cancel') ?></button>
            <button class="btn-info small save" type="button"><?= __('Save') ?></button>
        </div>
    </div>
</div>