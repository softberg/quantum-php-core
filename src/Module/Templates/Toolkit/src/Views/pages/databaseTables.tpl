<div id="database">
    <div class="row">
        <div class="tables col s2">
            <ul class="collection with-header">
                <li class="collection-header table-header add-table waves-effect create-table"><i class="material-icons right">add</i>Tables</li>
            <?php foreach ($tables as $table){ ?>
                <li class="table-item collection-item waves-effect" data-name="<?= $table ?>">
                    <?= ucfirst($table) ?>
                </li>
            <?php } ?>
            </ul>
        </div>
        <div class="col s10">
            <iframe class="table-iframe" src=""></iframe>
        </div>
        <div id="createTableModal" class="modal" data-csrf="<?php echo csrf_token() ?>">
            <div class="modal-content">
                <div class="table-title">
                    Table Name : <input id="tableName" class="title" type="text">
                </div>
                <div id="jsoneditor">

                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
                <button class="waves-effect waves-red btn-flat submit-table">Create</button>
            </div>
        </div>
    </div>
</div>