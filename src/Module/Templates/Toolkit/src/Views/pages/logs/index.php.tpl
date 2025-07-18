<div id="logs">
    <?php if (!empty($logFiles)): ?>
        <div class="row">
            <div class="logFiles col s2">
                <ul class="collection">
                    <?php foreach ($logFiles as $logFile): ?>
                        <li class="logFile-item collection-item waves-effect" data-file="<?= $logFile ?>">
                            <?= $logFile ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col s10">
                <iframe class="logs-iframe" src=""></iframe>
            </div>
        </div>
    <?php else: ?>
        <h5>No log found.</h5>
    <?php endif ?>
</div>

