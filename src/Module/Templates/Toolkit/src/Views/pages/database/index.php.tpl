<div id="database">
    <div class="row">

       <?php if (session()->has('error')): ?>
            <?php echo partial('partials/messages/error') ?>
        <?php endif; ?>
        
        <div class="tables col s2">
            <ul class="collection with-header">
                <li class="collection-header table-header add-table waves-effect create-table">
                    <i class="material-icons right">add</i>Tables
                </li>
                <?php foreach ($tables as $table): ?>
                    <li class="table-item collection-item waves-effect" data-name="<?= $table ?>">
                        <?= ucfirst($table) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col s10">
            <iframe class="table-iframe" src=""></iframe>
        </div>
        <?php
        echo partial('partials/jsoneditor-modal', [
            'modalId' => 'createTableModal',
            'inputField' => raw_param('Table Name: <input id="tableName" class="title" type="text">'),
            'footerButtons' => [
                raw_param('<a href="#" class="modal-close waves-effect waves-green btn-flat">Cancel</a>'),
                raw_param('<button class="waves-effect waves-red btn-flat submit-table">Create</button>')
            ]
        ]); ?>
    </div>
</div>