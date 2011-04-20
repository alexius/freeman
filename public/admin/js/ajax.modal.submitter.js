function createModalAjaxWindow(clickClass, modalWindowId, formId,
                               closeButtonName, spinnerLink)
{
    $(clickClass).click( function ()
    {
        var href = $(this).attr('href');
        $(modalWindowId).dialog({
            resizable: false,
            height:500,
            width: 450,
            modal: true,
            buttons: {
                "Ок": function() {
                    $(modalWindowId + ' .errors .notification-message')
                            .html('<img class="spinner" ' +
                                    'src="' + spinnerLink + '">');
                    $(formId).attr('action', href);
                    $(formId).ajaxSubmit({
                        success:    function(responseText) {
                            $(modalWindowId + ' .errors, .ajax-errors').html('');
                            var ans_json = responseText;
                            if (ans_json.error == 'true')
                            {
                                $(modalWindowId + ' .error .notification-message')
                                        .html(ans_json.error_message);
                                $(modalWindowId + ' .error').fadeTo(0, 400);
                                $(modalWindowId + ' .error').show();
                            }

                            if (ans_json.formMessages){
                                $(modalWindowId +  + ' .ajax-errors').remove();
                                $.each(ans_json.formMessages, function (i, item)
                                {
                                    var err = '<ul class="ajax-errors input-notification error png_bg">';
                                    $.each(item, function (erri, errText){
                                        err += '<li>' + errText + '</li>';

                                    });
                                    err += '</ul>';
                                    $('#' + i).parent().append(err);
                                });
                            }

                            if (ans_json.message) {
                                $(modalWindowId + ' .success .notification-message ')
                                        .html(ans_json.message);

                            }
                            return false;
                        }
                });
                    return false;
                },
                closeButtonName: function() {
                        $( this ).dialog( "close" );
                    return false;
                }
            }
        });
        return false;
    });
}