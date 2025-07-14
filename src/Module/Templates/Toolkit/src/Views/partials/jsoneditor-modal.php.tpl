<div id="<?= $modalId ?>"
     class="modal modal-fixed-footer"
     data-csrf="<?= csrf_token() ?>"
     data-table="<?= $dataTable ?? '' ?>">
    <div class=" modal-content">
        <h4 class="modal-title"></h4>
        <?php if (!empty($inputField)): ?>
            <div class="table-title">
                <?= $inputField ?>
            </div>
        <?php endif; ?>
        <div id="jsoneditor"></div>
    </div>
    <div class="modal-footer">
        <?php foreach ($footerButtons as $button): ?>
            <?php echo $button; ?>
        <?php endforeach; ?>
    </div>
</div> 