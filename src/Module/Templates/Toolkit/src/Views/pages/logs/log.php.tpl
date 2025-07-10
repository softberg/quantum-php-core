<?php if (!empty($logData)): ?>
    <ul id="log-collapsible" class="row s12 collapsible collapsible-accordion">
        <li class="collapsible-header">
            <div class="col s1"></div>
            <div class="col s3"><strong>Level</strong></div>
            <div class="col s2"><strong>Date</strong></div>
            <div class="col s6"><strong>Message</strong></div>
        </li>

        <?php foreach ($logData as $log): ?>
            <li>
                <div class="collapsible-header row">
                    <div class="col s1 valign-wrapper">
                        <i class="status-icon material-icons <?= getLevelClass($log['level']) ?>"
                           data-original="report"><?= getLevelIcon($log['level']) ?>
                        </i>
                    </div>
                    <div class="col s3 valign-wrapper <?= getLevelClass($log['level']) ?>">
                        <?php echo $log['level'] ? htmlspecialchars($log['level']) : "Unknown"; ?>
                    </div>
                    <div class="col s2">
                        <?php echo htmlspecialchars($log['date']); ?>
                    </div>
                    <div class="col s6 truncate">
                        <?php echo html_entity_decode(htmlspecialchars($log['message'])); ?>
                    </div>
                </div>
                <div class="collapsible-body">
                    <code><?php echo html_entity_decode(nl2br(htmlspecialchars($log['message']))); ?></code>
                </div>
            </li>
        <?php endforeach; ?>

    </ul>
<?php else: ?>
    <h5>This log file is empty.</h5>
<?php endif ?>

<footer class="row s12 container center fixed">
    <?php echo $pagination->getPagination(1, 5) ?>
</footer>
