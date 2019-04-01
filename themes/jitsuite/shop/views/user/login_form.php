<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?= form_open('login', 'class="validate"'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="username" class="control-label"><?= lang('identity'); ?></label>
            <input type="text" name="identity" id="username" class="form-control" value="" required placeholder="<?= lang('email'); ?>">
        </div>
        <div class="form-group">
            <a href="#" class="forgot-password pull-right text-blue"><?= lang('forgot?'); ?></a>
            <label for="password" class="control-label"><?= lang('password'); ?></label>
            <input type="password" id="password" name="password" class="form-control" placeholder="<?= lang('password'); ?>" value="" required>
        </div>
        <?php
        if ($Settings->captcha) {
            ?>
            <div class="form-group">
            <div class="form-group text-center">
                    <span class="captcha-image"><?= $image; ?></span>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <a href="<?= admin_url('auth/reload_captcha'); ?>" class="reload-captcha text-blue">
                                <i class="fa fa-refresh"></i>
                            </a>
                        </span>
                        <?= form_input($captcha); ?>
                    </div>
                </div>
            </div>
            <?php
        } /* echo $recaptcha_html; */
        ?>
        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="1" name="remember_me"><span> <?= lang('remember_me'); ?></span>
                </label>
            </div>
        </div>
        <button type="submit" value="login" name="login" class="btn btn-block btn-success"><?= lang('login'); ?></button>
    </div>
</div>
<?= form_close(); ?>

<?php
$providers = config_item('providers');
foreach($providers as $key => $provider) {
    if($provider['enabled']) {
        echo '<div style="margin-top:10px;"><a href="'.site_url('social_auth/login/'.$key).'" class="btn btn-sm mt btn-default btn-block" title="'.lang('login_with').' '.$key.'">'.lang('login_with').' '.$key.'</a></div>';
    }
}
?>