<?php

use Quantum\Libraries\Module\ModuleManager;

$moduleManager = ModuleManager::getInstance();

return '<div class="main-wrapper teal accent-4">
    <div class="container wrapper">
        <div class="center-align white-text">
            <div class="logo-block">
                <img src="<?php echo base_url() ?>/assets/images/quantum-logo-white.png" alt="<?php echo config()->get(\'app_name\') ?>" />
            </div>
            <h1>' . strtoupper($moduleManager->getModuleName()) . ' HOME PAGE</h1>
        </div>
    </div>
</div>
<?php echo partial(\'partials/bubbles\') ?>';