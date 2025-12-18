<div class="main-wrapper teal accent-4">
    <div class="container wrapper center-align white-text">
        <h1><?php _t('common.about'); ?></h1>

        <div class="card teal accent-4">
            <div class="card-content">
                <h6><?php _t('common.about_framework'); ?></h6>

                <h4><?php _t('common.version'); ?></h4>
                <h6><?php _t('common.current_version', env('APP_VERSION')); ?></h6>

               <h4 class="mt-4">Quantum CLI Commands</h4>

                <div class="cli-list">

                    <?php
                    $commands = [
                        ['Install debug toolbar integration', 'php qt install:debugbar'],
                        ['Install developer toolkit', 'php qt install:toolkit'],
                        ['Generate application key', 'php qt app:key'],
                        ['Generate .env file', 'php qt app:env'],
                        ['Show framework version', 'php qt app:version'],
                        ['Show all available commands', 'php qt list'],
                    ];
                    ?>

                    <?php foreach ($commands as [$desc, $cmd]): ?>
                        <div class="cli-item">
                            <div class="cli-desc white-text">
                                <?= $desc ?>
                            </div>

                            <div class="cli-command">
                                <code><?= $cmd ?></code>
                                <button class="copy-btn tooltipped" title="Copy" data-command="<?= $cmd ?>">
                                    <i class="material-icons copy-content">content_copy</i>
                                    <i class="material-icons done-icon" style="display:none;">done</i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php echo partial('partials/bubbles') ?>