<table class="highlight">
    <thead>
    <tr>
        <?php
            foreach ($tableColumns as $column) {
                echo '<th>' . $column . '</th>';
            }
        ?>

        <th class="actions-column">
            <button class="btn-floating waves-effect waves-light blue row-add" data-modal-title="Creating row">
                <i class="material-icons">add</i>
            </button>
        </th>

    </tr>
    </thead>
    <tbody class="table-body">
    <?php
        foreach ($tableData as $i => $row) {
            echo '<tr class="data-row">';

            foreach ($tableColumns as $column) {
                echo '<td>' . $row->$column . '</td>';
            }

            echo '<td class="actions-column row-data" data-row="'. htmlspecialchars(json_encode($row->asArray())) .'">

                    <button class="btn-floating waves-effect waves-light blue row-edit" data-modal-title="Editing row">
                        <i class="material-icons">edit</i>
                    </button>
                    
                    <button class="btn-floating waves-effect waves-light red modal-trigger"  data-target="rowDelete'.$i.'">
                        <i class="material-icons">delete</i>
                    </button>
                    <div id="rowDelete'.$i.'" class="modal">
                    <div class="modal-content">
                        <h4>Deleting Table Row</h4>
                        <p>Are you sure you want to delete this row?</p>
                    </div>
                    <div class="modal-footer">
                        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
                        <a href="'. base_url() . '/toolkit/database/delete?id=' . $row->id . '&tableName='.$tableName.'" class="waves-effect waves-red btn-flat">Delete</a>
                    </div>
                </div>
                  </td>
              </tr>
              ';
        }
    ?>

    </tbody>
</table>
<div id="rowActionModal" class="modal modal-fixed-footer" data-action="" data-csrf="<?php echo csrf_token() ?>" data-table="<?php echo $tableName?>">

    <div class="modal-content fixed">
        <h4 class="modal-title"></h4>
        <div id="jsoneditor"></div>
    </div>

    <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
        <button class="row-action btn btn-primary" >Save Changes</button>
    </div>
</div>

<footer class="row s12 container center fixed">
    <?php echo $pagination->getPagination(1, 5) ?>
</footer>