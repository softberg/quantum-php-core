<div class="main-wrapper">
    <div class="container">
        <div class="row">
            <div class=" col s12 l8 offset-l2 center-align white-text">
                <?php if (session()->has('error')) : ?>
                    <?php echo partial('partials/messages/error') ?>
                <?php endif; ?>

                <?php if (session()->has('success')): ?>
                    <?php echo partial('partials/messages/success') ?>
                <?php endif; ?>

                <ul class="tabs teal account-tabs card accent-4">
                    <li class="tab col s3">
                        <a href="#account_profile" class="active">
                            <h6 class="white-text"><?php _t('common.profile'); ?></h6>
                        </a>
                    </li>
                    <li class="tab col s3">
                        <a href="#account_password">
                            <h6 class="white-text"><?php _t('common.password'); ?></h6>
                        </a>
                    </li>
                </ul>

                <?php echo partial('account/partials/account') ?>
                <?php echo partial('account/partials/password') ?>
            
            </div>
        </div>
    </div>
</div>