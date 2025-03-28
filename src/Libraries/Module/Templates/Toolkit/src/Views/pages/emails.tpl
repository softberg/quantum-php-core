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
            <?php $emailId = $email["id"] ?>
            <li>
                <div class="collapsible-header" data-id="<?php echo $emailId; ?>">
                    <div class="col s1 valign-wrapper chevron"><i class="material-icons">chevron_right</i></div>
                    <div class="col s3"><?php echo $email['recipient']; ?></div>
                    <div class="col s4"><?php echo mb_decode_mimeheader($email['subject']); ?></div>
                    <div class="col s3"><?php echo $email['timestamp']; ?></div>
                    <div class="col s1 valign-wrapper center">
                        <button class="btn-floating waves-effect waves-light red delete-email modal-trigger" data-target="emailDropdown<?=$i?>">
                            <i class="material-icons">delete</i>
                        </button>
                    </div>
                </div>

                <div id="emailDropdown<?=$i?>" class="modal">
                    <div class="modal-content">
                        <h4>Deleting Email</h4>
                        <p>Are you sure you want to delete this email?</p>
                    </div>
                    <div class="modal-footer">
                        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
                        <a href="<?php echo base_url() . '/toolkit/emails/delete?emailId=' . $emailId ?>" class="waves-effect waves-red btn-flat">Delete</a>
                    </div>
                </div>

                <div class="collapsible-body">
                    <div>
                        <iframe src="<?php echo base_url() . '/toolkit/emails/view?emailId=' . $emailId ?>" class="email-iframe"></iframe>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
        <h5>No emails found.</h5>
    <?php endif; ?>
</div>
<footer class="row s12 container center fixed">
    <?php echo $pagination->getPagination(1, 5) ?>
</footer>