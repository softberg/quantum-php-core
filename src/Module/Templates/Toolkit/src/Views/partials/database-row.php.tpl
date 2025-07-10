<tr class="data-row">
    <?php foreach ($tableColumns as $column): ?>
        <td><?= $row->$column ?></td>
    <?php endforeach; ?>
    <td class="actions-column row-data" data-row="<?= htmlspecialchars(json_encode($row->asArray())) ?>">
        <button class="btn-floating waves-effect waves-light blue row-edit" data-modal-title="Editing row">
            <i class="material-icons">edit</i>
        </button>
        <button class="btn-floating waves-effect waves-light red delete-trigger"
            data-delete-url="<?php echo base_url(true) . '/database/delete?id=' . $row->id . '&tableName=' . $tableName ?>"
            data-modal-id="rowDelete"
            data-confirm-id="modal-confirm"
            data-modal-title="Deleting row">
            <i class="material-icons">delete</i>
        </button>
    </td>
</tr> 