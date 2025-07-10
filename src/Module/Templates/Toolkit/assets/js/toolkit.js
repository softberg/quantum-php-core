class ToolkitLogs {
    constructor() {
        this.$logsIframe = $('.logs-iframe');
    }

    handleLogFileClick(event) {
        const $target = $(event.currentTarget);
        const file = $target.data('file');

        this.$logsIframe.attr('src', `${window.location.origin}/toolkit/logs/view?logFile=${file}`);
        $target.siblings().removeClass('active');
        $target.addClass('active');
    }

    events() {
        $('.logFile-item').on('click', this.handleLogFileClick.bind(this));
    }

    init() {
        this.events();
    }
}

class ToolkitEmails {
    constructor() {
        this.$deleteModal = $('#deleteModal');
        this.$confirmDelete = $('#confirmDelete');
    }

    handleDeleteClick(event) {
        event.stopPropagation();
        const $target = $(event.currentTarget);
        const deleteUrl = $target.data('delete-url');
        this.$confirmDelete.attr('href', deleteUrl);
        this.$deleteModal.modal('open');
    }

    events() {
        $('.delete-trigger').on('click', this.handleDeleteClick.bind(this));
    }

    init() {
        this.events();
    }
}

class ToolkitDatabase {
    constructor() {
        this.$jsonEditorContainer = $('#jsoneditor');
        this.$createTableModal = $('#createTableModal');
        this.$rowActionModal = $('#rowActionModal');
        this.$rowDeleteModal = $('#rowDelete');
        this.$modalConfirm = $('#modal-confirm');
    }

    openModal(modal, actionUrl, title, data = {}, options = { mode: 'form', search: false }) {
        modal.data('action', actionUrl);
        modal.find('.modal-title').text(title);

        this.$jsonEditorContainer.empty();
        window.editor = new JSONEditor(this.$jsonEditorContainer[0], options);
        window.editor.set(data);

        modal.modal('open');
    }

    submitForm(modal, additionalFields = {}) {
        const $form = $('<form>', { method: 'POST', action: modal.data('action') });

        $form.append($('<input>', {
            type: 'hidden',
            name: 'data',
            value: JSON.stringify(window.editor.get())
        }));

        for (const [name, value] of Object.entries(additionalFields)) {
            $form.append($('<input>', { type: 'hidden', name, value }));
        }

        $('body').append($form);
        $form.submit();
    }

    handleTableItemClick(event) {
        const $target = $(event.currentTarget);
        const table = $target.data('name');

        $('.table-iframe').attr('src', `${window.location.origin}/toolkit/database/view?table=${table}`);
        $target.siblings().removeClass('active');
        $target.addClass('active');
    }

    handleRowAddOrCreate(event) {
        const $target = $(event.currentTarget);
        const isTableCreate = $target.hasClass('create-table');
        const modal = isTableCreate ? this.$createTableModal : this.$rowActionModal;
        const actionUrl = '/toolkit/database/create';
        const title = isTableCreate ? 'Creating Table' : $target.data('modal-title');

        let json = {};
        let options = { mode: 'tree', search: false };

        if (!isTableCreate) {
            const rowData = $('.row-data').first().data('row');
            options.mode = rowData ? 'form' : 'tree';

            if (rowData) {
                for (const key of Object.keys(rowData)) {
                    if (key !== 'id') {
                        json[key] = '';
                    }
                }
            }

            const tableName = modal.data('table');
            modal.data('original-name', tableName);
        } else {
            modal.removeData('original-name');
        }

        this.openModal(modal, actionUrl, title, json, options);
    }

    handleRowEdit(event) {
        const data = { ...$(event.currentTarget).parent().data('row') };
        const title = $(event.currentTarget).data('modal-title');

        this.$rowActionModal.data('id', data.id);
        delete data.id;

        this.openModal(this.$rowActionModal, '/toolkit/database/update', title, data);
    }

    handleRowDelete(event) {
        const deleteUrl = $(event.currentTarget).data('url');
        this.$modalConfirm.attr('href', deleteUrl);
        this.$rowDeleteModal.modal('open');
    }

    handleRowAction() {
        const originalName = this.$rowActionModal.data('original-name');

        const additionalFields = {
            table: this.$rowActionModal.data('table'),
            rowId: this.$rowActionModal.data('id'),
            'csrf-token': this.$rowActionModal.data('csrf')
        };

        if (originalName) {
            additionalFields.originalName = originalName;
        }

        this.submitForm(this.$rowActionModal, additionalFields);
    }

    handleSubmitTable() {
        this.submitForm(this.$createTableModal, {
            table: this.$createTableModal.find('#tableName').val(),
            'csrf-token': this.$createTableModal.data('csrf')
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
