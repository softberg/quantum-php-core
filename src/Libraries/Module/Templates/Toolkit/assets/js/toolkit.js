jQuery(document).ready(function ($) {
    $('.modal').modal();

    $('.modal-trigger').on('click', function (event) {
        event.stopPropagation();
        let targetModalId = $(this).data('target');
        $('#' + targetModalId).modal('open');
    });

    $('.visibility-icon').on('click', function () {
        if ($(this).hasClass('on')) {
            $('.off').removeClass('hide');
            $(this).parent('.input-field').find('input[type=text]').attr('type', 'password');

        } else {
            $('.on').removeClass('hide');
            $(this).parent('.input-field').find('input[type=password]').attr('type', 'text');
        }

        $(this).addClass('hide');
    });

    $('.collapsible').collapsible();

    $('.collapsible-header').hover(
        function () {
            const icon = $(this).find('.status-icon');
            icon.data('original', icon.text());
            $(icon).parent().addClass("chevron");
            icon.text('chevron_right');
        },
        function () {
            const icon = $(this).find('.status-icon');
            $(icon).parent().removeClass("chevron");
            icon.text(icon.data('original'));
        }
    );

    $('.logFile-item').on('click', function (){
        $('.logs-iframe').attr('src', window.location.origin + "/toolkit/logs/view?logDate=" + $(this).data("file"));
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
    });

    $('.table-item').on('click', function (){
        $('.table-iframe').attr('src', window.location.origin + "/toolkit/database/view?table=" + $(this).data("name"));
        $(this).siblings().removeClass('active');
        $(this).addClass('active');
    });

    let editor;

    function setupModal(modal, action, title, data = {}, options = { mode: 'form', search: false }) {
        modal.data('action', action);
        modal.find('.modal-title').text(title);
        $("#jsoneditor").empty();
        editor = new JSONEditor(document.getElementById("jsoneditor"), options);
        editor.set(data);
        modal.modal('open');
    }

    function submitForm(modal, additionalFields = {}) {
        const $form = $('<form>', { method: 'POST', action: modal.data('action') });

        $form.append($('<input>', { type: 'hidden', name: 'data', value: JSON.stringify(editor.get()) }));

        Object.entries(additionalFields).forEach(([name, value]) => {
            $form.append($('<input>', { type: 'hidden', name, value }));
        });

        $('body').append($form);
        $form.submit();
    }

    $('.row-add').on('click', function(){

        let rowData = $('.row-data').first().data('row');

        let json = {};

        let options = { mode: 'form', search: false };

        if(rowData){
            json = Object.fromEntries(
                Object.keys(rowData)
                    .filter(key => key !== 'id')
                    .map(key => [key, ''])
            );
        }else{
            options.mode = 'tree';
        }

        setupModal($('#rowActionModal'), 'create', $(this).data('modal-title'), json, options);
    });

    $('.row-edit').on('click', function(){
        let data = $(this).parent().data("row");
        let editModal = $('#rowActionModal');
        editModal.data('id', data.id);
        delete data.id;
        setupModal(editModal, 'update', $(this).data('modal-title'), data);
    });

    $('.row-action').on('click', function () {
        let $rowActionModal = $('#rowActionModal');
        submitForm($rowActionModal, {
            table: $rowActionModal.data('table'),
            rowId: $rowActionModal.data('id'),
            'csrf-token': $rowActionModal.data('csrf'),
        });
    });

    $('.create-table').on('click', function () {
        setupModal($('#createTableModal'), 'database/create', 'Creating Table', {}, { mode: 'tree', search: false });
    });

    $('.submit-table').on('click', function () {
        let $createTableModal = $('#createTableModal');
        submitForm($createTableModal, {
            table: $createTableModal.find('#tableName').val(),
            'csrf-token': $createTableModal.data('csrf'),
        });
    });

    $('.sidenav').sidenav();
});


