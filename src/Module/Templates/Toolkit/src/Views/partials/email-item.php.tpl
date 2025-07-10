<li>
    <div class="collapsible-header" data-id="<?php echo $email["id"]; ?>">
        <div class="col s1 valign-wrapper chevron"><i class="material-icons">chevron_right</i></div>
        <div class="col s3"><?php echo $email['recipient']; ?></div>
        <div class="col s4"><?php echo mb_decode_mimeheader($email['subject']); ?></div>
        <div class="col s3"><?php echo $email['timestamp']; ?></div>
        <div class="col s1 valign-wrapper center">
            <button class="btn-floating waves-effect waves-light red delete-trigger"
                data-delete-url="<?php echo base_url(true) . '/email/delete/' . $email["id"] ?>"
                data-modal-id="emailDeleteModal"
                data-confirm-id="modal-delete-link">
                <i class="material-icons">delete</i>
            </button>
        </div>
    </div>
    <div class="collapsible-body">
        <div>
            <iframe src="<?php echo base_url(true) . '/email/' . $email["id"] ?>" class="email-iframe"></iframe>
        </div>
    </div>
</li> 