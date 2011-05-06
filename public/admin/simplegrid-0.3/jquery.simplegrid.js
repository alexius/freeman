/*
 * SipmleGrid  0.3b - jQuery Grid
 * Copyright (c) 2009, Fedir Petryk, savalon@ukr.net
 * Dual licensed under the MIT and GPL licenses
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * Date: 2010-05-02
 */

(function ($) {
	$.fn.simplegrid = function(options) {
		
		// настройки по умолчанию
		var options = jQuery.extend({
            
            // action urls
			url: "", 	save_url: "",  edit_url: "",   delete_url: "",
            
			fulledit: false,
			inlineedit: false,		
			select: false,
			selectlink: '',
			width: "100%",
			height: "100%",
			colNames: [],
			colModel :[],
			sortdir: 'asc',
			sortcol: "",
			page: 1,
			rows: 10,
			total_rows: 0,
			savedRow: [],
			filters: false,	
			filters_values: {},
			actions_type: 'edit',
			actions_bind: '',
            inline_add: false,
            customButtons: [],
            button_add_html: '<a class="btn ui-state-default ui-corner-all binded-href" ><span class="ui-icon ui-icon-circle-plus"></span>Добавить</a>'
		},options);

        var liWrapper = '<li class="icon-link-grid ui-state-default ui-corner-all">';
        var ulWrapper = '<ul class="icons-buttons ui-widget">';
        
        var saveButton = '<a id = "save_row" class = "tooltip"><span class="ui-icon ui-icon-disk"></span></a>';
        var calncelButton = '<a id = "remove_row" class = "tooltip"><span class="ui-icon ui-icon-disk ui-icon-wrench"></span></a>';
        var tableSelector = 'table.grid-table';
        var addTrSelector = ' tbody tr.current';
        
		// Инициализируем табличку
		$(this).html('<table class = "grid-table" width = "' + options.width +
					'" height = "' + options.height + '"></table>');	
		
		// заголовки
		$(tableSelector).html('<thead><tr class = "grid-headers"></tr></thead>');
		
		// фильты, если включены
		if (options.filters == true)
        {
			$(tableSelector + ' thead').append('<tr class = "grid-filters"></tr>');
		}
		
		// само тело данных
		$(tableSelector).append('<tbody></tbody>');
		
		// панель нафигации
		$(this).append('<div class = "navigation"></div>');
		
		// строим загoловки
		var buildHeader = function (colNames, colModel, sortcol, sortdir){
			
			/*
			 * TODO
			 * сортировка для нескольких полей
			 * фиксированя ширина, высота или % соотношение
			 */
			
			$.each(colNames, function(i, item) 
            {	
				var sort_class = colModel[i].name; 
				var th_width = '';
				
				if (!sortcol || sortcol == "")
                {
					if (i == 0)
                    {
						sort_class = '';
					}
				}
				else if (colModel[i].name == sortcol)
                {
					sort_class = colModel[i].name + '-' + sortdir;
				}
				
				if (colModel[i].width)
                {
					th_width = 'width = "' + colModel[i].width + '"';
				}
				// добавление сортировки в заголовок
				$(tableSelector + ' tr:first').append('<th class = "header" ' + 
						th_width +' class = "' + sort_class + '">' 
						+ item + '</th>');
				
				// добавление фильтров в заголовок
				if (options.filters == true)
                {
					var filter = colModel[i].filter;
					var filter_html = '';
					var col_name = colModel[i].name;
					// определеяем тип филтра
					if (filter == 'range')
                    {
						var filter_html = 'От: <input class = "grid-filter-range" name = "from-' +
						col_name + '" size = "1"/> ' + 
							'До: <input class = "grid-filter-range" name = "to-' + 
							col_name + '" size = "1"/>';
					}
					else if (filter == 'name')
					{
						var filter_html = 'Имя: <input class = "grid-filter-range" name = "' +
						col_name + '" size = "10"/>';
					}
                    else if (filter == 'custom')
                    {
                        var cFilter = (colModel[i].filter_params.name);
                        var cParams = colModel[i].filter_params.params; 
                        var funcCall = cFilter + "('" + cParams  + "', '" + col_name + "');";
                        var filter_html = eval(funcCall);

                    }
					
					$(tableSelector + ' tr.grid-filters').append('<th>' + filter_html + '</th>');
					

				}
			});
			
			// Тип работы с таблицей 
			if (options.actions_type == 'select')
            {
				$(tableSelector + ' tr:first').append('<th width = "40px">' 
						+ 'Количество</th>');
			}
				
			$(tableSelector + ' tr:first').append('<th width = "40px">' 
					+ 'Действия</th>');
				
			if (options.filters == true && options.actions_type == 'edit')
            {
				$(tableSelector + ' tr.grid-filters').append('<th width = "40px"></th>');
			}
            else if (options.actions_type == 'select' && options.filters == true)
            {
				$(tableSelector + ' tr.grid-filters').append('<th width = "40px"></th>');
				$(tableSelector + ' tr.grid-filters').append('<th width = "40px"></th>');
			}
			
			// вешаем на фильтр ивент сабмита 13 и обновление таблици
			var $th = $(tableSelector + ' tr.grid-filters th');
			$th.keydown( function (event)
            {
				if (event.keyCode == 13)
                {
					$('input', $th).each( function (i, item) 
                    { 
						var filter_val = $(item).val(); 
						var filter_name = $(item).attr('name');
						
						// сохраняем все фильтры в массив
						options.filters_values[filter_name] = filter_val.replace(/(^\s+)|(\s+$)/g, "");
					});		
                    
                    $('select option:selected', $th).each( function (i, item) 
                    { 
                        var filter_val = $(item).val(); 
                        var filter_name = $(item).attr('name');
                        
                        // сохраняем все фильтры в массив
                        options.filters_values[filter_name] = filter_val.replace(/(^\s+)|(\s+$)/g, "");
                    });    	
                    // обновляем данные 
					requestData();
				}
			});		
		};
		
		// восстановление строки
		var restoreRow = function (row_id)
        {
			if (row_id >= 0)
            {
				var $row = $('.grid-table tbody tr#' + row_id);
				$row.removeClass('rowedit').bind('click', rowClick);			
	        	$('td', $row).each(	function(i, cell) 
                {
		        	$(cell).html($('input', cell).val());
		        });	  
			} 
            else 
            {
				var $row = $('.grid-table tbody tr.rowedit');
				$row.removeClass('rowedit').bind('click', rowClick);
				$('td', $row).each(	function(i, cell) 
                {
				    $(cell).html($('input', cell).val());
				});
			}
		};
		
		// ивент нажатия на строку, полное редактирование
		function rowClick() 
        {
			restoreRow();
			if (!$(this).is('.rowedit')){
				$(this).unbind('click', rowClick).addClass('rowedit')
    				.keydown( function (event){
    					if (event.keyCode === 13)
                        {
    						restoreRow(this.id);
    						
    						/*
    						 * TODO
    						 * сохранение в базу
    						 */
    						
    					} else if (event.keyCode === 27){
    						restoreRow(this.id);
    					}
					}
    			);
	        	$('td', this).each(	function(i, item) {
	        		$(item).html('<input type="text" value="' + $(this).html() + '" />');
	        	});	      
			}
		}
		
        function checkboxHandler()
        {
            var show = 0;
            var new_row_data = {};     
            var column = $(this).parent().parent().children().index($(this).parent()); 
            if ($(this).is(':checked'))
            {
                show = 1;
            }
            new_row_data[options.colModel[column].name] =  show;     
            new_row_data[options.colModel[0].name] = $(this).parent().parent().find('td:first').html();
            saveRowData(options.save_url, new_row_data);  

        }
        
    // ивент нажатия на ячейку, редактим ячейку и только
		function cellClick() 
        {
            if (options.inline_add == true)
            {
                $(tableSelector + ' tbody tr.new_row').remove();       
            }
            
		    var params_length = options.colModel.length;  
                                    
			// получаем айди кликнутой строки
			var parent_id = $(this).parents('tr').attr('id');
			
			// получаем айди предыдущей строки (если такова была)
			var $prev_table = $(tableSelector + ' tbody tr.current');
			var prev_id = $prev_table.attr('id'); 
			
			// получаем объект тр кликнутой строки
			var $cells = $(tableSelector + ' tbody tr#' + parent_id);
			
			// если мы перешли на другую строку, то снимаем поля инпутов
			// и снимаем класс current
			if (prev_id != parent_id)
            {
				$(tableSelector + ' tbody tr.current td.cell-edit')
					.each(	function(i, item) 
                    {
					$(item).html($('input', item).val());
					$(item).bind('click', cellClick);
				});
				$(tableSelector + ' tbody tr.current').removeClass('current');
			}
			
			$($cells).addClass('current');

			if ($(this).is('.cell-edit'))
            {
				$(this).unbind('click', cellClick);
                var column = $(this).parent().children().index(this);
				options.savedRow[options.colModel[column].name] = $(this).html();

	        	$(this).html('<input class="text-input" style = "width:98%;" type="text" value="' + $(this).html() + '" />');
                $('input', this).focus();
	        	$('input', this).keydown( function (event)
                {	        		 
					if (event.keyCode === 13)
                    {
					    var do_save = true;	
						// восстановление ячейки в обычный вид    	
						var new_row_data = {};
						$('td', $cells).each(function(i, item) 
                        {
						    var $new_row = $('input', item); 
                            if ($new_row.val() == '' && options.colModel[i].required == true)
                            {
                                messages('error', '<span>Внимание!</span>Поле не может быть пустым' );
                                $(this).focus();          
                                do_save = false;
                            }							
						});
                        if (do_save == true)
                        {
                        $('td', $cells).each( function(i, item) 
                        {
                            var $new_row = $('input', item); 
                            if (i == 0)
                            {
                                new_row_data[options.colModel[0].name] = $(item).html();;
                            }
                            else if (i < params_length)
                            { 
                                if ($new_row.val() != '' && $new_row.parent().hasClass('cell-edit'))
                                {
                                    $(item).html($new_row.val());
                                    $(item).bind('click', cellClick); 
                                    new_row_data[options.colModel[i].name] = $new_row.val();                            
                                } 
                            }                                                              
                            });
                            
						    if (new_row_data && do_save == true)
                            {
							    saveRowData(options.save_url, new_row_data);
						    }
                            $(tableSelector + ' tbody tr.current').removeClass('current');
                            $(tableSelector + ' tbody tr.new_row').remove();   	
                        }					
					} 
                    else if (event.keyCode === 27)
                    {
                        var new_row_data = {};        
                        $('td', $cells)
                            .each(    function(i, item) {
                                
                                if (i == 0)
                                {
                                    new_row_data[options.colModel[0].name] = $(item).html();;
                                }
                                else if (i < params_length)
                                {
                                    $(item).html(options.savedRow[options.colModel[i].name]);
                                    $(item).bind('click', cellClick); 
                                                                                               
                                }
                        });              
                        $(tableSelector + ' tbody tr.current').removeClass('current');
					}
				}
			)};
		}
		
		// строим нашу таблицу с данными
		var buildTable = function (json, colModel)
        {
			var tbody = '';
			$.each(json, function(i, item) 
            {
				if (i != 'total')
                {
				    tbody += '<tr id = "' + i + '">';
				    var k = 0;
				    $.each(item, function(j, col) 
                    {
					    var width_td = '';
					    var edit_class = '';
					    if (options.fulledit === false)
                        {
						    if (colModel[k].celledit === true)
                            {
							    edit_class = ' class = "cell-edit" ';
						    }
					    }
					    
					    if (colModel[k].index === true){
						    index =  col;
					    }
					    
                        if (colModel[k].type == 'checkbox')
                        {           
                            var checked = '';
                            if (col == 1)
                            {
                                checked = 'checked = "checked"';    
                            }
                            
                            tbody += '<td style = "white-space:pre;overflow:hidden;" class = "checkbox" ><input ' 
                                + checked + ' type = "checkbox" value = "' + col + '"></td>';     
                        }
                        else if (colModel[k].type == 'radio')
                        {
                            var checked = '';
                            if (col == 1)
                            {
                                checked = 'checked = "checked"'; 
                            }
                            tbody += '<td style = "white-space:pre;overflow:hidden;" class = "radio"><input ' 
                                + checked + ' type = "radio" value = "' + col + '" name = "default"></td>';     
                        }
                        else
                        {
                            tbody += '<td style = "white-space:pre;overflow:hidden;" ' 
                                + edit_class + ' >' + col + '</td>';    
                        }
                        k++;				
				    });
				    
				    if (options.actions_type == 'select')
                    {
					    tbody += '<td width = "40px" class = "grid-quantity">' +
					    '<input type = "text" name = "quantity" size = "4" value = "1"></td>';
					    
				    }
				    
				    tbody += '<td width = "60px" class = "grid-actions">';
                    tbody += ulWrapper;

				    if (options.actions_type == 'select')
                    {
                        tbody += liWrapper;
					    tbody += '<a class = "" rel = "'
                                + index + '"><span class="ui-icon ui-icon-cart"></span></a> ';
                        tbody += '</li>';
				    }
				    else if (options.actions_type == 'edit')
                    {
					    if (options.edit_url != '')
                        {
                            tbody += liWrapper;
						    tbody += '<a class="tooltip" href = "' +
                                    options.edit_url +
							        index + '"><span class="ui-icon ui-icon-wrench"></span></a> ';
                            tbody += '</li>';
					    }
			    
					    if (options.delete_url != '')
                        {
                            tbody += liWrapper;
						    tbody += '<a rel = "' + index +
                                    '" class = ""><span class="ui-icon ui-icon-trash"></span></a> ';
                            tbody += '</li>';
					    }
                        
                        if (options.customButtons.length > 0)
                        {
                            $.each(options.customButtons, function (i, item){
                                tbody += liWrapper;
                                tbody += '<a href = "' + item.link + index +
                                        '" "class = "binded-href btn_no_text btn ui-state-default ui-corner-all">'
                                   + item.html + '</a> ';
                                 tbody += '</li>';
                            })
                        }
					    
					    tbody += '</ul></td>';
				    }
				    
				    tbody += '</tr>';
				} 
                else if (i == 'total') 
                {
					options.total_rows = item;
				}
				
			});
			
			// вставялем построеный хтмл данных в табилцу
			$(tableSelector + ' tbody').html(tbody);
			
			// Если включена выборка(select), биндим чекбоксы
			if (options.actions_type == 'select')
            {
				$("table.grid-table tbody a.add-to-order").bind('click', options.actions_bind);
			}
			
			// биндим редактинг ячеек
			$(tableSelector + ' tbody tr td.cell-edit').bind('click', cellClick);
            // биндим checkbox хендлер
            $(tableSelector + ' tbody tr td.checkbox input').bind('click', checkboxHandler);        
            $(tableSelector + ' tbody tr td.radio input').bind('click', checkboxHandler);        
			
            if (options.delete_url != '')
            {
                $('.delete-link').bind('click', rowDelete);          
            }
            
			// ф-я подсетвки
			$(tableSelector + ' tbody tr')
				.hover(	function() {
			        	$(this).addClass('hover-row');
			        }, function() {
			        	$(this).removeClass('hover-row');
			    });
		
			//ф-я перенаправления при селекте
			if (options.select == 'link')
			{
				$('.grid-table tbody tr').click(function() 
                {
					ids = $('td:first',this).html();
		        	window.location.replace(options.selectlink + ids);
		        });	
			}
			
			// если включено полное редактирование, биндим ф-я дли эдита на все трки
			if (options.fulledit === true)
            {
				$('.grid-table tbody tr').bind('click', rowClick);
			}
            
            $(tableSelector + ' tbody tr:odd').addClass('odd');
            $(tableSelector + ' tbody tr:even').addClass('even');                          
		};
		
        // удаление строки
        /* TODO
            Сделать добавления диаологово окна удаления
        */
        var rowDelete = function ()
        {
            
            var $href = $(this);
            var id = $href.attr('rel');
            
            $("#confirm").dialog({
                bgiframe: true,
                autoOpen: false,
                resizable: false,
                height:140,
                modal: true,
                overlay: {
                    backgroundColor: '#000',
                    opacity: 0.5
                },
                buttons: {
                    'Да': function() {
                        $.ajax({url:options.delete_url + id,type:"POST",dataType:"json",
                complete:function(state,st) { 
                    if(st=="success") 
                    {
                        var answer = eval("("+state.responseText+")");
                        if (answer.error == 'false')
                        {
                            messages('success', '<span>Данные удалены!</span><br>' );
                            $href.parent().parent().remove();
                        }
                        else
                        {
                            messages('error', '<span>Ошибка!</span>' + answer.error);
                        }
                    }
                    
                    else
                    {
                        messages('error', '<span>Ошибка!</span>Удаление не удалось.' );
                    }                                                   
                }
            });
                     $(this).dialog('close');
                      $(this).dialog('destroy');           
                    },
                    'Нет': function() {
                        $(this).dialog('close');
                    }
                }
            });
    
            $('#confirm').dialog('open'); 

            return false;  
        }
        
		// чередующееся выделение для трки парные \ не парные
		var alternateRowColors = function($table) 
        {
			    $('tbody tr:odd', $table).removeClass('even').addClass('odd');
			    $('tbody tr:even', $table).removeClass('odd').addClass('even');
		};
		
		// сортировка на стороне сервера (в коментах локальная)
		var initSorting = function (sortable_name, sortdir) 
        {
			
	        $('.' + sortable_name + '-' + sortdir).addClass('clickable').hover(function() 
            {
	        	$(this).addClass('hover');
	        }, 
            function() 
            {
	        	$(this).removeClass('hover');
	        })
            .click(function() 
            {
	        	var newDirection = 1;
	        	if (sortdir == 'asc')
                {
	        		old_sort = sortdir;
	        		sortdir = 'desc';
	        		options.sortdir = 'desc'; 	        		
	        	}
	        	else 
                {
	        		old_sort = sortdir;
	        		sortdir = 'asc';
	        		newDirection = -1;
	        		options.sortdir = 'asc';
	        	}
	        	
				var parametrs = {sortdir: options.sortdir, sortcol:options.sortcol,
						page: options.page, rows: options.rows};
				
				if (options.filters === true)
				{
					$.each(options.filters_values, function(i, item) 
                    {
						parametrs[i] = item;
					});
					parametrs['filters'] = true;
				}
				
				$.ajax({url:options.url,type:"POST",dataType:"json", 
					data: (parametrs), 
					complete:function(JSON,st) 
                    { 
						if(st=="success") 
                        {
							var answer = eval("("+JSON.responseText+")");
							options.total_rows = Math.ceil(answer.total_rows / options.rows);
							buildTable(answer.grid_data, options.colModel);
							$(tableSelector + ' tbody tr:odd').addClass('odd');
							$(tableSelector + ' tbody tr:even').addClass('even');
						}
					}
				});
	        	
	       	 	$('th.' + sortable_name + '-' + old_sort)
       	 		.removeClass(sortable_name + '-' + old_sort)
       	 		.addClass(sortable_name + '-' + sortdir);
       	 	
	        });
		};
		
		// панель нафигации
		var navigation = function (){
			$('.navigation')
				.append('<div class = "pager-nav" id = "pager"></div>');
            var navi = ulWrapper;
            navi += liWrapper
                + '<a title="Предыдущая страница" class="prev">'
				+ '<span class="ui-icon ui-icon-arrowthick-1-w"></span></a>';
            navi += '</li>';

            navi += liWrapper;
            navi += '<a title="Следующая страница" class="next">' +
						'<span class="ui-icon ui-icon-arrowthick-1-e"></span><a>';
            navi += '</li>';

            navi += liWrapper;
            navi += '<a title="Обновить" id="renew_grid">'
				+ '<span class="ui-icon ui-icon-refresh"></span></a>';
            navi += '</li>';

            navi += '</ul>';
            
            navi += '<div><input class = "text-input pager-info pagedisplay" type = "text">';
            navi += '<select name="rows" class="pagesize">'
                   + '<option value = "10">10 строк</option>'
                   + '<option value = "30">30 строк</option>'
                   + '<option value = "50">50 строк</option>'
                   + '</select></div>';

			$('.navigation div.pager-nav').append(navi);
			$('.navigation .pager-info')
				.val(options.page + ' / ' + options.total_rows);
			
            $(".pagesize").change(function(){
                var rows = $('select[@name=rows] option:selected').val();
                options.rows = rows; 
               // $('.navigation .pager-info')
             //       .val(options.page + ' / ' + options.total_rows);
                requestData();   
            });
            
			$(".navigation a.next").click(function(){
				if (options.page < options.total_rows) {
					options.page++;				
					requestData();
				}
			});
			
			$(".navigation a.prev").click(function(){
				if (options.page > 1) {
					options.page--;
					requestData();
				}
			});
            
            if (options.inline_add == true)
            {
                $('.navigation #pager').append('<div id = "add_buton">' + options.button_add_html + '</div>');      
                $(".navigation #add_buton").click(function()
                {
                     if (!$(tableSelector + ' tbody tr.current').length)
                     {
                         var td_html = '<td class="cell-edit" style="white-space: pre; overflow: hidden;">'
                         var input_text = '<input type="text" value="" style="width: 98%;">';
                         var input_checkbox = '<input type="checkbox" value="1">';
                         
                         var tr_html = '<tr class = "current new_row">';
                         $.each(options.colModel, function (i, item) 
                         {
                            if (i == 0)
                                tr_html += '<td></td>';    
                            else
                            {
                                tr_html += td_html;    
                                if (item.type == 'checkbox' && item.celledit == true)
                                {
                                    tr_html += input_checkbox + '</td>'; 
                                }   
                                else if (item.celledit == true)
                                {
                                    tr_html += input_text + '</td>'; 
                                }
                                else
                                {
                                    tr_html += '</td>';     
                                }
                            }
                         });
                         tr_html += '<td class = "grid-actions"></td></tr>';
                         $('.grid-table tbody').append(tr_html);
                         addSaveButton();
                         addCancelRowButton();
                         // binding keylogger for saving
                         $(tableSelector + ' tbody tr.current td.cell-edit input').keydown( function (event)
                         {                     
                            if (event.keyCode === 13)
                            {
                                var res = collectAndSave();
                                if (res)
                                {
                                    $(tableSelector + ' tbody tr.current').removeClass('current');  
                                }
                                else
                                {
                                    messages('error', '<span>Ошибка!</span>Невозможно выполнить сохранение' );
                                }
                                     
                            } 
                            else if (event.keyCode === 27)
                            {                      
                                $(tableSelector + ' tbody tr.current').remove();
                            }
                     }); 
                } 
            });
		};
		}
        
        var addSaveButton = function ()
        {
            $(addTrSelector + ' td.grid-actions .icons-buttons')
                    .append(liWrapper + saveButton + '</li>');
            $('#save_row').bind('click', collectAndSave);        
        }
 
        var addCancelRowButton = function ()
        {
            $(addTrSelector + ' td.grid-actions .icons-buttons')
                    .append(liWrapper + calncelButton + '</li>');
            $('#remove_row').click( function()
            {
                $(addTrSelector).remove();    
            });        

        }
         
         
        var collectAndSave = function ()
        {
            $cells = $(addTrSelector); 
            var do_save = true;    
            // восстановление ячейки в обычный вид        
            var new_row_data = {};  
            
            // собираем данные и проверяем на заполненость
            $('td', $cells).each(    
            function(i, item)
            {          
                var $new_row = $('input', item);   
                if ($new_row.val() != '' && $new_row.parent().hasClass('cell-edit'))
                {
                    if ($new_row.attr('type') == 'checkbox')
                    {
                        if  ($new_row.is(':checked'))
                        {
                            new_row_data[options.colModel[i].name] = 1    
                        }
                        else
                        {
                            new_row_data[options.colModel[i].name] = 0;
                        }
                    }
                    else
                    {
                        new_row_data[options.colModel[i].name] = $new_row.val();                                
                    }
                    
                } 
                else if ($new_row.val() == '')
                {
                    messages('attention', '<span>Внимание!</span>Поле не может быть пустым' );
                    do_save = false;
                }
            });

            if ( do_save == true )
            {     
                //new_row_data['id'] = '';       
                saveRowData(options.save_url, new_row_data); 
                setTimeout(function() { requestData(); }, 1000);      
                return true;   
            }   
            else
            {
                return false;
            }                 
        }   
        
        // Ресторим поле строки в зависимости от типа инпут поля
        var restoreField = function (type, value) 
        {
            if (type == "checkbox")
            {
                var checked = '';
                if (value == 0)
                {
                    checked = 'checked = "checked"';   
                }
                return '<input type="checkbox" value="' + value + '" ' + checked + ' >'; 
            }
            else
            {
                return value;
            }
        }
        
        // Биндим хендле в зависимости от типа инпут поля       
        var bindFieldHandler = function (item, type)
        {
            if (type == "checkbox")
            {
                $(item).bind('click', checkboxHandler);                     
            }
            else
            {
                $(item).bind('click', cellClick);     
            }            
        }
        
		// ф-я запросов для навигации
		var requestData = function()
        {
			var parametrs = {sortdir: options.sortdir, sortcol:options.sortcol,
					page: options.page, rows: options.rows};
			
			if (options.filters === true)
			{
				$.each(options.filters_values, function(i, item) {
					parametrs[i] = item;
				});
				parametrs['filters'] = true;
			}
			messages('information', '<span>Загрузка. </span>Подождите пожалуйста!' );
			$.ajax({url:options.url,type:"POST",dataType:"json", 
				data: (parametrs), 
				complete:function(JSON,st) { 
					if(st=="success") {
                        if (JSON.responseText)
                        {
						    var answer = eval("("+JSON.responseText+")");
						    options.total_rows = Math.ceil(answer.total_rows / options.rows);
						    buildTable(answer.grid_data, options.colModel);
						    $('.navigation .pager-info')
							    .val(options.page + ' / ' + options.total_rows);
                            messages(1, '<span>Данные загружены!</span><br>' );
                            addIconHover();
						}
                        else
                        {
                            messages(4, '<span>Нет данных.</span>' );  
                        }
                    }                                                   
				}
			});
		};
		
		// ф-я сохранения строки
		var saveRowData = function(save_url, row_data)
        {
            messages('information', '<span>Сохранение</span>Подождите пожалуйста!' );
			$.post(save_url,    row_data, 
				function(JSON,st) 
                {
					if(st=="success") 
                    {
						if (JSON == 'ok')
                        {
                            messages('success', '<span>Операция выполнена успешно</span>Изменения сохранены!' );
			     	 	} 
                        else
                        {
                            if ($('#no_permission').hasClass('no_permission'))
                            {		     		   
                                messages('error', '<span>Ошибка сохранения!</span>У вас нет прав для выполнения указаного действия');
                            }
                            else if (JSON)
                            {
                                var answer = eval("("+JSON+")");
                                var error = '';    
                                $.each(answer, function (i, item) 
                                {
                                    
                                    if (item != '')
                                    {
                                        error = i + ' - ' +  item;
                                    }
                                })
                                messages('error', '<span>Ошибка сохранения!</span>' + error  );
                            }
                            else 
                            {
                                messages('error', '<span>Ошибка сохранения!</span>'  );
                            }
		     	    	}
					}
				}
			);
		};

		var bindCheckboxHandlers = function ()
        {
			$("table.grid-table tbody a.add-to-order").click(
					function()
					{
						var insert = true;
						var row = $(this).parent().parent();
						var pid = $('td', row).filter(':nth-child(1)').html();
						
						var price = $('td', row).filter(':nth-child(5)').html();
						var qnt = $('td', row).filter(':nth-child(6)').html();
						var qnt_val = $(qnt).val();
						
						$('.order-products tbody tr').each(function (i, item) 
                        {
							var added_pid = $('td', item).html();
							if (added_pid == pid){
								insert = false;
								var cur_qnt = $('td', item).filter(':nth-child(4)').html();
								var cur_sum = $('td', item).filter(':nth-child(5)').html();
							}
						});
						
						if (insert == true)
                        {
							var name = $('td', row).filter(':nth-child(2)').html();
							var model = $('td', row).filter(':nth-child(3)').html();
							var manf = $('td', row).filter(':nth-child(4)').html();
							var tr = '<tr><td>' + pid + '</td>'
								+ '<td>' + manf + ' ' + name + ' ' + model + '</td>'
								+ '<td>' + price + '</td>'
								+ '<td>' + qnt +  '</td>' 
								+ '<td>' + (price * qnt_val) + '</td></tr>';
							$('.order-products tbody').append(tr);
						}
					}
				);
		}
        
        var messages = function(type, text)
        {
            var notice = notification(type, text);
            $('.notifications').css('display', 'none').html(notice).fadeIn("slow");
            setTimeout(function() 
            { 
                $('.notifications').fadeOut('slow', function()
                {
                   
                });
            }, 5000);            
        }

        var addIconHover = function()
        {
            $('table.grid-table .icon-link-grid').unbind('hover');
            $('.icon-link-grid').hover(
                function() { $(this).addClass('ui-state-hover'); },
                function() { $(this).removeClass('ui-state-hover'); }
		    );
        }
		// ф-я возрата 
		return this.each( function() 
        {                            
			buildHeader(options.colNames, options.colModel, 
					options.sortcol, options.sortdir);
			messages('information', '<span>Загрузка. </span>Подождите пожалуйста!' );
			$.ajax({url:options.url,type:"POST",dataType:"json", 
				data: ({sortdir: options.sortdir, sortcol:options.sortcol,
					page: options.page, rows: options.rows}), 
				complete:function(JSON,st) 
                { 
					if(st=="success") 
                    {
						if (JSON.responseText)
                        {
							var answer = eval("("+JSON.responseText+")");
                            //var answer = JSON.responseText;
                             
                            if (answer.error == 'false')
                            {
							    options.total_rows = Math.ceil(answer.total_rows / options.rows);
							    buildTable(answer.grid_data, options.colModel);
							    initSorting(options.sortcol, options.sortdir);

                            }
						}

                        navigation();
                        addIconHover();
                        messages('information', '<span>' + answer.message + '</span><br>' );
					}    
				}
			});			
			
			$(window).unload(function () 
            {
				$(this).unbind("*");
			});
		});
	};
})(jQuery);

var customfilter_price = function (cur, col_name)
{
    var select_html = 'От: <input class = "grid-filter-range" name = "from-' +
                        col_name + '" size = "3"/> ' + 
                            'До: <input class = "grid-filter-range" name = "to-' +
                            col_name + '" size = "3"/>';
                            
    select_html += 'Валюта: <select name = "price">';
    eval("var obj1=("+cur+")");
    $.each(obj1, function (i, item){
        select_html += '<option name = "price" value = "' + i + '">' + item + '</option>';  
    });
    select_html += '</select>';
    return select_html;
}