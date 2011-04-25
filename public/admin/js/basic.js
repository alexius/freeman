$(document).ready(function() {

    $( '.menu-scroll' ).scrollFollow( {
        speed: 500,
        offset: 0,
        container: 'body-wrapper'
        //onText: 'Disable Follow',
        //offText: 'Enable Follow'
    } );

/*   $(document).scroll(function(){
       var marginTop = 0 + $(document).scrollTop();
       $('.menu-scroll').css('margin-top',marginTop + 'px');
   });*/


    $( "#hide" ).click(function() {
        hideMenu();
        $.cookie('hidden', null, {'domain' : 'freeman.in', 'path' : '/'});
        $.cookie('hidden', 'true', {'domain' : 'freeman.in', 'path' : '/'});
	    return false;
    });

    $( "#show-menu" ).click(function() {
        showMenu();
        $.cookie('hidden', null, {'domain' : 'freeman.in', 'path' : '/'});
        $.cookie('hidden', 'false', {'domain' : 'freeman.in', 'path' : '/'});
        return false;
    });



	$( "input:submit" ).button();

	  $('.tooltip').tooltip({
          track: true,
          delay: 0,
          showURL: false,
          showBody: " - ",
          fade: 250
     });


    $('.pickdate').datepicker({ dateFormat: 'yy-mm-dd',
           onSelect: function(dateText, inst) {
           $('#pickdate').val(dateText);
         }
    });

	$('.dialog_link, .ui-button, .icon-link').hover(
			function() { $(this).addClass('ui-state-hover'); },
			function() { $(this).removeClass('ui-state-hover'); }
		);

    coloriz_table();

});

function coloriz_table()
{
	var $table = $('.default-table');
	$('tbody tr:odd', $table).removeClass('even').addClass('odd');
	$('tbody tr:even', $table).removeClass('odd').addClass('even');
}

function ajax_requester(url, element, url_redirect)
{
    $.ajax({url:url,type:"POST",dataType:"json",
        data: '',
        complete:function(html,st) {
            if(st=="success") {
            	var ans_json = eval('(' + html.responseText + ')');
            	if (ans_json.error == 'true') {
					$(element + ' .errors').html(ans_json.error_message);
					return false;
				}
				else if (ans_json.error == 'false')
				{
					if (url_redirect){
						redirector(url_redirect);
					} else {

					}
					$(element).dialog('destroy');
					return false;
				}
            } else {
            	return false;
            }
        }
    });
}

function redirector(url)
{
	document.location = url;
}

function isInteger(s) {
	  return (s.toString().search(/^[0-9]+$/) == 0);
	}

function successMessage(text){
	$('.error-wrapper').html('');
	$('.message-wrapper').html('');

	var messageHtml = '<div class="ui-widget message">' +
			'<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">' +
			'<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>'
			+ text + '</p></div></div>';
	$('.message-wrapper').html(messageHtml);

}


function errorMessage(text){
	$('.error-wrapper').html('');
	$('.message-wrapper').html('');

	var messageHtml = '<div class="ui-widget message">' +
			'<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">' +
			'<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>'
			+ text + '</p></div></div>';
	$('.error-wrapper').html(messageHtml);

}

function notification(type, message, display)
{
    var styleClass = '';
    if (type == 'error') {
        styleClass = 'notification error png_bg';
    }
    else if (type == 'success') {
        styleClass = 'notification success png_bg';
    }
    else if (type == 'information') {
        styleClass = 'notification information png_bg';
    }
    else if (type == 'attention') {
        styleClass = 'notification attention png_bg';
    }

    if (display === false){
        var display = 'no-display';
    } else {
        var display = '';
    }


    var template = '<div class="' + styleClass + ' ' + display + '">'
                + '<a href="#" class="close">' +
                    '<img src="/admin/images/icons/cross_grey_small.png" title="" alt="">'
                + '</a>'
                + '<div class="notification-message">' + message + '</div>'
                + '</div>';
    return template;
}

function hideMenu()
{
    var options = {};
    $( "#sidebar-wrapper" ).hide( );
    $( "#sidebar" ).css('background',
       '#f0f0f0 url(\'/admin/images/bg-hidden.gif\') top left repeat-y');
    $( "#sidebar" ).css('width', '60px');
    $('#main-content').css('margin', '0 30px 0 85px');
    $( "#sidebar-hidden" ).show();

    $('.faux').removeClass('faux').addClass('faux-hidden');
}

function showMenu()
{
    var options = {};
    $( "#sidebar-hidden" ).hide(  );
    $('#main-content').css('margin', '0 30px 0 260px');
    $( "#sidebar" ).css('width', '230px');
    $( "#sidebar" ).css('background',
       '#f0f0f0 url(\'/admin/images/bg-body.gif\') top left repeat-y');
    $( "#sidebar-wrapper" ).show(  );

    $('.faux-hidden').removeClass('faux-hidden').addClass('faux');
}