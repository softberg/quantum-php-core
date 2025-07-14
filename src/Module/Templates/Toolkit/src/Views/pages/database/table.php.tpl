<table class="highlight">
    <thead>
    <tr>
        <?php foreach ($tableColumns as $column): ?>
            <th><?= $column ?></th>
        <?php endforeach; ?>

        <th class="actions-column">
            <button class="btn-floating waves-effect waves-light blue row-add" data-modal-title="Creating row">
                <i class="material-icons">add</i>
            </button>
        </th>
    </tr>
    </thead>
    <tbody class="table-body">
    <?php foreach ($tableData as $i => $row): ?>
        <?php echo partial('partials/database-row', [
            'row' => $row,
            'tableColumns' => $tableColumns,
            'tableName' => $tableName
        ]); ?>
    <?php endforeach; ?>
    </tbody>
</table>

<footer class="row s12 container center fixed">
    <?php echo $pagination->getPagination(1, 5) ?>
</footer>

<?php
echo partial('partials/jsoneditor-modal', [
    'modalId' => 'rowActionModal',
    'dataTable' => $tableName,
    'footerButtons' => [
        raw_param('<a href="#" class="modal-close waves-effect waves-green btn-flat">Cancel</a>'),
        raw_param('<button class="row-action btn btn-primary">Save Changes</button>')
    ]
]);

echo partial('partials/delete-modal', [
    'modalTitle' => 'Deleting Table Row',
    'message' => 'Are you sure you want to delete this row?',
]);
?>