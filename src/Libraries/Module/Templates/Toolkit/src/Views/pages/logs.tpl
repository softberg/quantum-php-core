<?php if (!empty($parsedLogs)): ?>
<ul id="log-collapsible" class="row s12 collapsible collapsible-accordion">
    <li class="collapsible-header">
        <div class="col s1"></div>
        <div class="col s3"><strong>Level</strong></div>
        <div class="col s2"><strong>Date</strong></div>
        <div class="col s6"><strong>Message</strong></div>
    </li>
    <?php
    $logStyles = [
        'error' => [
            'class' => 'red-text text-darken-2',
            'icon' => '<i class="status-icon material-icons red-text" data-original="error">error</i>'
        ],
        'warning' => [
            'class' => 'yellow-text text-darken-2',
            'icon' => '<i class="status-icon material-icons yellow-text text-darken-2" data-original="warning">warning</i>'
        ],
        'info' => [
            'class' => 'blue-text text-lighten-1',
            'icon' => '<i class="status-icon material-icons blue-text" data-original="info">info</i>'
        ],
        'notice' => [
            'class' => 'yellow-text text-darken-2',
            'icon' => '<i class="status-icon material-icons yellow-text text-darken-2" data-original="info">info</i>'
        ]
    ];
    ?>
    <?php foreach ($parsedLogs as $log): ?>
        <?php
        // Default styles
        $defaultStyle = [
            'class' => 'orange-text text-lighten-1',
            'icon' => '<i class="status-icon material-icons orange-text" data-original="help">help</i>'
        ];

        // Find matching style by keyword
        $levelStyle = $defaultStyle;
        foreach ($logStyles as $keyword => $style) {
            if (strpos(strtolower($log['level']), $keyword) !== false) {
                $levelStyle = $style;
                break;
            }
        }
        ?>
        <li>
            <div class="collapsible-header row">
                <div class="col s1 valign-wrapper <?php echo $levelStyle['class']; ?>">
                    <?php echo $levelStyle['icon']; ?>
                </div>
                <div class="col s3 valign-wrapper <?php echo $levelStyle['class']; ?>">
                    <?php echo $log['level'] ? htmlspecialchars($log['level']) : "Unknown"; ?>
                </div>
                <div class="col s2">
                    <?php echo htmlspecialchars($log['date']); ?>
                </div>
                <div class="col s6 truncate">
                    <?php echo htmlspecialchars($log['message']); ?>
                </div>
            </div>
            <div class="collapsible-body">
                <code><?php echo nl2br(htmlspecialchars($log['trace'])); ?></code>
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
