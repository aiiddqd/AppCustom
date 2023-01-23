$(document).ready(function () {

    $('.conv-close').click(function (e) {
        fsAjax(
            {
                action: 'conversation_change_status',
                status: 3,
                conversation_id: getGlobalAttr('conversation_id'),
                folder_id: getQueryParam('folder_id')
            },
            laroute.route('conversations.ajax'),
            function (response) {
                if (typeof (response.status) != "undefined" && response.status == 'success') {
                    if (typeof (response.redirect_url) != "undefined") {
                        window.location.href = response.redirect_url;
                    } else {
                        window.location.href = '';
                    }
                } else if (typeof (response.msg) != "undefined") {
                    showFloatingAlert('error', response.msg);
                } else {
                    showFloatingAlert('error', Lang.get("messages.error_occured"));
                }
                loaderHide();
            });
    });


});
