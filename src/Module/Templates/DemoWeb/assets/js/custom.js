class Custom {
    constructor() {
        this.timeOut = null;

        this.initTabs();
        this.initPlugins();
        this.events();
    }

    initPlugins() {
        $(".dropdown-trigger").dropdown();
        $('textarea#content').characterCounter();
        $('.modal').modal();
        $('.sidenav').sidenav();

        this.easyMarkdownEditor();
    }

    easyMarkdownEditor() {
        const el = document.querySelector('#content');

        if (el) {
            new EasyMDE({
                element: document.getElementById('content'),
                height: "300px",
                maxHeight: "300px",
            });
        }
    }

    modalTrigger(e) {
        $('#modal-confirm').attr('href', $(e.currentTarget).data('url'));
        $('.modal').modal('open');
    }

    visibilityIcon(e) {
        if ($(e.currentTarget).hasClass('on')) {
            $('.off').removeClass('hide');
            $(e.currentTarget).parent('.input-field').find('input[type=text]').attr('type', 'password');

        } else {
            $('.on').removeClass('hide');
            $(e.currentTarget).parent('.input-field').find('input[type=password]').attr('type', 'text');
        }

        $(e.currentTarget).addClass('hide');
    }

    search(e) {
        if (this.timeOut) {
            clearTimeout(this.timeOut);
        }

        this.timeOut = setTimeout(() => {
            $(e.currentTarget).closest('form.form-search').submit();
        }, 1000)
    }

    initTabs() {
        $('.account-tabs').tabs();
        $('#account_password').removeClass('hide');
    }

    tabSwitcher() {
        $('.material-alert').remove();
    }

    copyCommand(event) {
        let button = jQuery(event.currentTarget);
        let container = button.closest('.cli-command');
        let codeElement = container.find('code');
        let codeText = codeElement.text().trim();

        navigator.clipboard.writeText(codeText)
            .then(() => {
                button.find('.copy-content').hide();
                button.find('.done-icon').show();

                setTimeout(() => {
                    button.find('.done-icon').hide();
                    button.find('.copy-content').show();
                }, 2000);

                let tooltip = $('<span class="copy-tooltip">Copied!</span>');
                button.append(tooltip);
                setTimeout(() => {
                    tooltip.addClass('show');
                }, 10);

                setTimeout(() => {
                    tooltip.remove();
                }, 1500);
            })
            .catch(err => {
                console.error("Failed to copy:", err);
            });
    }

    events() {
        $(document).on('click', '.modal-trigger', this.modalTrigger.bind(this));
        $(document).on('click', '.visibility-icon', this.visibilityIcon.bind(this));
        $(document).on('input', '.search-bar', this.search.bind(this));
        $(document).on('click', '.account-tabs .tab', this.tabSwitcher.bind(this));
        $(document).on('click', '.copy-btn', this.copyCommand.bind(this));
    }
}

jQuery(document).ready(function ($) {
    new Custom();
});