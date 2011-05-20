(function ($)
{
	$.fn.modalAjaxForm = function(options) {

		// настройки по умолчанию
		var options = jQuery.extend({

            // action urls
			clickClass: "",
			modalWindowId: "",
			formId: "",
            closeButtonName: "",
            spinnerLink: ""
		},
			options);
    var closeButtonName = options.closeButtonName;
        
    $(options.clickClass).click( function ()
    {
        var href = $(this).attr('href');
        $(options.modalWindowId).dialog({
            resizable: false,
            height:500,
            width: 450,
            modal: true,
            buttons: [{
                text: "Ок",
                click: function() {
                    $(options.modalWindowId + ' .error .notification-message')
                            .html('<img class="spinner" ' +
                                    'src="' + options.spinnerLink + '">');
                    $(options.formId).attr('action', href);
                    $(options.formId).ajaxSubmit({
                        success:    function(responseText) {
                            $(options.modalWindowId + ' .errors, .ajax-errors').html('');
                            var ans_json = responseText;
                            if (ans_json.error == 'true')
                            {
                                $(options.modalWindowId + ' .error .notification-message')
                                        .html(ans_json.error_message);
                                $(options.modalWindowId + ' .error').fadeTo(0, 400);
                                $(options.modalWindowId + ' .error').show();
                            }

                            if (ans_json.formMessages){
                                $(options.modalWindowId + ' ul.ajax-errors').remove();
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
                                $(options.modalWindowId + ' .success .notification-message ')
                                        .html(ans_json.message);
								$(options.modalWindowId + ' .success').show();

                            }
                            return false;
                            }
                        });
                        return false;
                    }},
                {
                    text: closeButtonName,
                    click: function() {
                            $( this ).dialog( "close" );
                        return false;
                }
                }]
        });
        return false;
    });
    };
})(jQuery);