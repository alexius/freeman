/*
 * tableman  0.3 - Table manipulation plugin
 * Copyright (c) 2011, Fedir Petryk, savalon@ukr.net
 * Dual licensed under the MIT and GPL licenses
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * Date: 2011-03-14
 */

(function ($) {
	$.fn.tableman = function(options) {

		// настройки по умолчанию
		var options = jQuery.extend({

            // action urls
			column_delete_url: "",
			row_delete_url: "",
			field_save_url: ""
		},
			options);

	//	var value = '';

		var spinner = '<img src="/images/tableman/spinner.gif">';
        var saveButton = '<a class="save-row tooltip-edit" title="Зберегти"><img src="/images/tableman/icon_save.png"></a>';
		var editButton = '<a class="edit-row tooltip" title="Редагувати рядок"><img src="/images/tableman/edit_icon.gif"></a>';
        var cancelButton =  '<a class="cancel-row tooltip-edit" title="Відмінити зміни"><img src="/images/tableman/restore.jpg"></a>';
        var deleteRowButton = '<img src="/images/tableman/red-cross-icon.jpg">';
		var deleteColumnButton = '<img src="/images/tableman/red-cross-icon.jpg">';

		// Инициализируем табличку
		var $table = $(this);


		// дописываем кнопки
		var deleteButton = function (table)
        {
			$('th', table).each(function(i, cell)
			{
				$(cell).html('<div class="field-value">' + $(cell).html()
						+ '</div><div><a rel="' + (i) + '" class="delete-column tooltip" title="Видалити стовпчик">'
						+ deleteColumnButton + '</a></div>');

			});
			$('thead tr', table).append('<th>&nbsp;</th>');


			$('tbody tr td', table).each(function(i, cell)
			{
				$(cell).html('<div class="field-value">' + $(cell).html() + '</div>');
			});

			$('tbody tr', table).each(function(i, cell)
			{
				$(cell).append('<td style="width:50px;"><div style="display:inline;">'
						+ editButton + '</div><div style="display:inline;">' +
							'<a rel="' + (i+1) + '" class="delete-row tooltip" title="Видалити рядок">'
								+ deleteRowButton + '</a></div></td>');
			});

			$('a.edit-row', table).click(function(item) {
				var rowNum = $(this).parent().parent().parent().attr('title');

				$('tbody tr[title="' + rowNum + '"] td', table).each(function(i, cell){
					$(cell, table).trigger('dblclick');
				});
			});
			
			$('a.delete-column', table).click(function(){
				var $columnClick = this;
				var td = $(this).parent().parent();
				
				var fieldContent = $(td).html();
				$(td).append(spinner);

				$.ajax({
					url:options.column_delete_url + 'tid/' + $(table).attr('id') + '/column/' + $columnClick.rel,
					type:"POST",
					dataType:"json",
					data: '',
					complete:function(html,st) {
						if(st=="success") {
							var ans_json = eval('(' + html.responseText + ')');
							if (ans_json.error == 'false')
							{

								$('th', table).each(function(i, cell)
								{
									if ($columnClick.rel == i)
									{
										$(cell).remove();
									}
									var columnAId = parseInt($('a.delete-column', cell).attr('rel'));
									if (columnAId > $columnClick.rel)
									{
										$('a.delete-column', cell).attr('rel', columnAId-1);
									}
								});

								$('tr', table).each(function(s, tr)
								{
									$('td', tr).each(function(i, cell){
										if ($columnClick.rel == i){
											$(cell).remove();
										}
									});
								});
							} else if (ans_json.error == 'true') {
								$(td).html(fieldContent);
							}
						}
					}
				});
			});

			$('a.delete-row', table).click(function(){
				var $rowClick = this;
				var rowNum = $(this).parent().parent().parent().attr('title');
				var actField = $(this).parent().parent();
				
				$(actField).append(spinner);

				$.ajax({
					url:options.row_delete_url + 'tid/' + $(table).attr('id') + '/row/' + rowNum,
					type:"POST",
					dataType:"json",
					data: '',
					complete:function(html,st) {
						if(st=="success") {
							$('tr[title="' + rowNum + '"]', table).remove();
						}
					}
				});
			});
		};



		// дописываем кнопки
		var thEdit = function (table)
        {
			$('td, th', table).dblclick(function()
			{
				var editableObject = this;
				var value = $('.field-value', this).html();

				if (!$(this).hasClass('editing'))
				{
					$(this).html('<div class="edit-field" style="display:inline;">' +
						'<input class="field-input" type="text" value="' + value + '"></div>' +
							'<div class="edit-buttons" style="display:inline;">' + saveButton + cancelButton + '</div>');
					$(this).addClass('editing');
				}

			

				$('a.cancel-row', editableObject).click(function(item)
				{
					$('.edit-field', editableObject).remove();
					$('.edit-buttons', editableObject).remove();
					$(editableObject).append('<div class="field-value">' + value + '</div>');
					$(editableObject).removeClass('editing');
				});


				$('a.save-row', editableObject).click(function(item)
				{
					var fieldId = editableObject.id;
					var fieldVal = $('.field-input', editableObject).val();

					$(editableObject).html(spinner);

					$.ajax({url:options.field_save_url + editableObject.id,type:"POST",dataType:"json",
						data: {value:fieldVal},
						complete:function(html,st) {
							if(st=="success") {

								$('.edit-field', editableObject).remove();
								$('.edit-buttons', editableObject).remove();
								$(editableObject).html('<div class="field-value">' + fieldVal + '</div>');
								$(editableObject).removeClass('editing');
							} else {
								return false;
							}
						}
					});


				});
			});
		};

		var addToolTip = function (table)
        {
			$('.tooltip', table).tooltip({
				  track: true,
				  delay: 0,
				  showURL: false,
				  showBody: " - ",
				  fade: 250
			});
		}




				// ф-я возрата
		return this.each( function()
        {
			thEdit(this);
			deleteButton(this);
			addToolTip(this);
		});

		return this;
	};
})(jQuery);