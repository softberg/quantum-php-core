class ToolkitLogs {
    handleLogFileClick(event) {
        $('.logs-iframe').attr('src', window.location.origin + '/toolkit/logs/view?logFile=' + $(event.currentTarget).data('file'));
        $(event.currentTarget).siblings().removeClass('active');
        $(event.currentTarget).addClass('active');
    }

    events() {
        $('.logFile-item').on('click', this.handleLogFileClick.bind(this));
    }

    init() {
        this.events();
    }
}

class ToolkitEmails {
    handleDeleteClick(event) {
        event.stopPropagation();
        let deleteUrl = $(event.currentTarget).data('delete-url');
        $('#confirmDelete').attr('href', deleteUrl);
        $('#deleteModal').modal('open');
    }

    events() {
        $('.delete-trigger').on('click', this.handleDeleteClick.bind(this));
    }

    init() {
        this.events();
    }
}

class ToolkitDatabase {
    openModal(modal, actionUrl, title, data, options) {
        if (typeof data === 'undefined') data = {};
        if (typeof options === 'undefined') options = { mode: 'form', search: false };

        modal.data('action', actionUrl);
        modal.find('.modal-title').text(title);

        $('#jsoneditor').empty();

        window.editor = new JSONEditor(document.getElementById('jsoneditor'), options);
        window.editor.set(data);

        modal.modal('open');
    }

    submitForm(modal, additionalFields) {
        if (typeof additionalFields === 'undefined') additionalFields = {};
        let $form = $('<form>', {method: 'POST', action: modal.data('action')});

        $form.append($('<input>', { type: 'hidden', name: 'data', value: JSON.stringify(window.editor.get()) }));

        for (let name in additionalFields) {
            if (additionalFields.hasOwnProperty(name)) {
                $form.append($('<input>', { type: 'hidden', name: name, value: additionalFields[name] }));
            }
        }

        $('body').append($form);

        $form.submit();
    }

    handleTableItemClick(event) {
        $('.table-iframe').attr('src', window.location.origin + '/toolkit/database/view?table=' + $(event.currentTarget).data('name'));
        $(event.currentTarget).siblings().removeClass('active');
        $(event.currentTarget).addClass('active');
    }

    handleRowAddOrCreate(event) {
        let isTableCreate = $(event.currentTarget).hasClass('create-table');

        let modal = isTableCreate ? $('#createTableModal') : $('#rowActionModal');
        let actionUrl = '/toolkit/database/create';
        let title = isTableCreate ? 'Creating Table' : $(event.currentTarget).data('modal-title');
        let json = {};
        let options = { mode: 'tree', search: false };

        if (!isTableCreate) {
            let rowData = $('.row-data').first().data('row');
            options = { mode: rowData ? 'form' : 'tree', search: false };
            if (rowData) {
                for (let key in rowData) {
                    if (rowData.hasOwnProperty(key) && key !== 'id') {
                        json[key] = '';
                    }
                }
            }

            let tableName = modal.data('table');
            modal.data('original-name', tableName);
        } else {
            modal.removeData('original-name');
        }
        this.openModal(modal, actionUrl, title, json, options);
    }

    handleRowEdit(event) {
        let data = $(event.currentTarget).parent().data('row');
        let editModal = $('#rowActionModal');

        editModal.data('id', data.id);

        delete data.id;

        this.openModal(editModal, '/toolkit/database/update', $(event.currentTarget).data('modal-title'), data);
    }

    handleRowDelete(event) {
        let deleteUrl = $(event.currentTarget).data('url');
        $('#modal-confirm').attr('href', deleteUrl);
        $('#rowDelete').modal('open');
    }

    handleRowAction() {
        let $rowActionModal = $('#rowActionModal');
        let originalName = $rowActionModal.data('original-name');
        let additionalFields = {
            table: $rowActionModal.data('table'),
            rowId: $rowActionModal.data('id'),
            'csrf-token': $rowActionModal.data('csrf')
        };

        if (originalName) {
            additionalFields.originalName = originalName;
        }

        this.submitForm($rowActionModal, additionalFields);
    }

    handleSubmitTable() {
        let $createTableModal = $('#createTableModal');

        this.submitForm($createTableModal, {
            table: $createTableModal.find('#tableName').val(),
            'csrf-token': $createTableModal.data('csrf')
        });
    }

    events() {
        $('.table-item').on('click', this.handleTableItemClick.bind(this));
        $('.row-add').on('click', this.handleRowAddOrCreate.bind(this));
        $('.row-edit').on('click', this.handleRowEdit.bind(this));
        $('.row-delete').on('click', this.handleRowDelete.bind(this));
        $('.row-action').on('click', this.handleRowAction.bind(this));
        $('.create-table').on('click', this.handleRowAddOrCreate.bind(this));
        $('.submit-table').on('click', this.handleSubmitTable.bind(this));
    }

    init() {
        this.events();
    }
}

jQuery(document).ready(function ($) {
    window.editor = undefined;
    $('.modal').modal();
    $('.collapsible').collapsible();
    $('.sidenav').sidenav();

    new ToolkitLogs().init();
    new ToolkitEmails().init();
    new ToolkitDatabase().init();
});
