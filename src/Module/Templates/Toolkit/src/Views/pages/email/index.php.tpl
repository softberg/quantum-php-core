<div id="email-list">
    <?php if (!empty($emails)): ?>
        <ul class="row s12 collapsible">
            <li class="collapsible-header">
                <div class="col s1"></div>
                <div class="col s3"><strong>Recipient</strong></div>
                <div class="col s4"><strong>Subject</strong></div>
                <div class="col s3"><strong>Date</strong></div>
                <div class="col s1"></div>
            </li>
            <?php foreach ($emails as $i => $email): ?>
                <?php echo partial('partials/email-item', [
                    'email' => $email,
                    'index' => $i
                ]); ?>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <h5>No emails found.</h5>
    <?php endif; ?>
</div>

<footer class="row s12 container center fixed">
    <?php echo $pagination->getPagination(1, 5) ?>
</footer>

<?php
echo partial('partials/delete-modal', [
    'modalTitle' => 'Deleting Email',
    'message' => 'Are you sure you want to delete this email?',
]);
?>


