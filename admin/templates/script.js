$(document).click(function(){
	$('.settings').hide();
	$('#contextmenu').trigger('menu.hide');
});
$(document).ready(function(){
	var table = $('table.table'),
		menu = $('#contextmenu'),
		doc = $(this);

	//сортировка - в разработке
	//var $tabs = $( ".pagination_pages ul" ).tabs();
	/*$('table.sortable tbody').sortable({
		update: function( event, ui ) {
			var m = ui.item.closest('table').data('module');
			var id = n = ui.item.data('id');
			var n = ui.item.data('sorting');
			var prev = ui.item.prev('tr').data('sorting');
			var next = ui.item.next('tr').data('sorting');
			//alert(prev+' '+n+' '+next);
			//$.get('/admin.php', {'u':'sorting','m':m,'id':id,'n':n,'prev':prev,'next':next},function(data){if(data) alert(data)});
		},
		//connectWith: ".pagination_pages table tbody"
		//handle: ".sorting"
	});
	//$('table.sortable tbody').draggable();
	//сортировка с пагинатором
	/*$('.pagination_pages tbody').droppable({
		accept: "table.sortable tbody tr",
		hoverClass: "ui-state-hover",
		drop: function( event, ui ) {
			alert(1);
			/*var $item = $( this );
			var $list = $( $item.find( "a" ).attr( "href" ) )
			.find( ".connectedSortable" );

			ui.draggable.hide( "slow", function() {
				$tabs.tabs( "option", "active", $tab_items.index( $item ) );
				$( this ).appendTo( $list ).show( "slow" );
			});
		}
	});    */

	//tooltip
	$('td a.edit',table).attr('title','редактировать запись');
	$('td a.delete',table).attr('title','удалить запись');
	$('td span.level',table).attr('title','для перемещение нажмите и передвиньте в нужное место');
	$('td span.sorting',table).attr('title','для перемещение нажмите и передвиньте в нужное место');
	$('td a.js_display',table).attr('title','показать/скрыть на сайте');
	$('td.post',table).attr('title','двойной клик для редактирования');
	$('td img.img',table).attr('title','просмотр картинки');

	//контекстное меню на таблице
	$(table).on('contextmenu','tr',function(e){
		var tr = $(this);
		if (tr.hasClass('head')) return;
		if (tr.find('a:hover').length>0) return;
		tr.addClass('active').siblings('.active').removeClass('active');
		menu.css({left:e.pageX, top:e.pageY}).data('caller',tr).fadeIn(100);
		$('.boolean',menu).each(function(){
			var m = $(this),
				a = $('b',m),
				name = m.data('name'),
				key = m.data('key'),
				c = $('td[data-name='+name+'] a.js_boolean',tr).hasClass(key+'_1');
			a.toggleClass(key+'_0',!c).toggleClass(key+'_1',c);
		});
		return false;
	});
	//скрыть меню
	menu.on('menu.hide',function(){
		$(this).hide();
		$('tr.active',table).removeClass('active');
	//редактировать
	}).on('click','.edit',function(){
		menu.trigger('menu.hide').data('caller').find('a.open').click();
		return false;
	//вкладки
	}).on('click','.tabs',function(){
		menu.trigger('menu.hide').data('caller').find('a.open').click();
	//boolean
	}).on('click','.boolean',function(){
		var m = $(this),
			name = m.data('name'),
			key = m.data('key');
		menu.data('caller').find('td[data-name='+name+'] a.js_boolean').click();
		$('b',m).toggleClass(key+'_0').toggleClass(key+'_1');
		return false;
	//удалить
	}).on('click','.delete',function(){
		menu.trigger('menu.hide').data('caller').find('a.delete').click();
		return false;
	});




	$('.style_menu select').attr('size',($(window).height()-200)/16);
	//смена фона
	$('.sprite.settings2').click(function(){
		$('div.settings').slideToggle('fast');
		return false;
	});
	//смена фона
	$('.abc a,.color a').click(function(){
		var i = $(this).attr('class');
		$('body').removeClass('a-style b-style c-style g-style').addClass(i+'-style');
		$.get('/admin.php', {'u':'style','style':i});
		return false;
	});
	//смена размера
	$('.size a').click(function(){
		var i = $(this).attr('class');
		$('body').removeClass('b-size m-size s-size').addClass(i+'-size');
		$.get('/admin.php', {'u':'style','size':i});
		return false;
	});


	//дерево из формы
	doc.on('change','.form select[name^="nested_sets"]',function(){
		var s = $(this),
			form = s.closest('.form');
		if (s.attr('name')=='nested_sets[parent]') {
			var parent = this.value || 0;
			$('select[name="nested_sets[previous]"]',form).html('<option value="0">В конце списка</option>').append(s.find('option[data-parent='+parent+']').clone());
		}
		$('input[name="nested_sets[on]"]',form).val(1);
		return false;

	//отправка формы
	}).on('click','.js_submit',function(){
		$(this).closest('form').submit();
		return false;

	//отправка формы аджаксом (дизайн)
	}).on('click','.js_submit_style',function(){
		var form = $(this).closest('form'),
			url = form.attr('action');
		if (window.editor) editor.save();
		form.ajaxSubmit({
			url:		'/admin.php'+url,
			success:	function (data){
				$('.message').html(data).show().fadeOut(2000);
			},
			error:	function(xhr,txt,err){
				alert('Ошибка ('+txt+(err&&err.message ? '/'+err.message : '')+')');
			}
		});
		return false;

	//показать сеополя
	}).on('click','.seo-optimization a',function(){
		$(this).parent('div').next('div').slideToggle('fast');
		return false;

	//multicheckbox
	}).on('change','.multicheckbox input',function(){
		$(this).closest('li').find('input').prop('checked',this.checked);

	//закладки
	}).on('click','.bookmarks a',function(){
		var a = $(this),
			form = a.closest('form'),
			i = a.data('i');
		a.parent().addClass('active').siblings().removeClass('active');
		$('.tab',form).hide().filter('[data-i='+i+']').show().find(':input:visible:enabled').first().focus();

	//открыть форму редактирования
	}).on('click','.table .open',function(){
		$('#window').remove();
		var opener = $(this),
			m = table.data('module'),
			id = opener.closest('tr').data('id'),
			url = opener.attr('href');
		//подниматься наверх при открытии формы
		$('html').animate({scrollTop:0});
		$.get(
			url,
			{'m':m,'u':'form','id':id},
			function(data){ //alert(data);
				$(data).appendTo('body').find('.form').trigger('form.open');
			}
		);
		return false;

	//обработчик события "закрытие формы"
	}).on('form.close','.form',function(){
		if (!$(this).data('changed') || confirm('Некоторые поля были изменены. Вы действительно хотите закрыть форму редактирования?')) {
			$('#window').remove();
			$('#overlay').removeClass('display');
		}

	//обработчик нажатия на ссылку (проверка наличия изменений в форме)
	}).on('click','#body a',function(){
		var form = $('.form');
		return !form.length || !form.data('changed') || confirm('Некоторые поля были изменены. Вы действительно хотите закрыть форму редактирования и перейти по ссылке?');

	//обработчик события "открытие формы"
	}).on('form.open','.form',function(){
		$('a.delete',this).attr('title','удалить');
		$('.sortable',this).sortable();
		$('#overlay').addClass('display');
		if (location.hash) $('.bookmarks a[href='+location.hash+']',this).click();
		$(this).draggable({handle:'.form_head,.form_footer'}).find(':input:visible:enabled').first().focus();

	//обработчик изменения данных в форме
	}).on('input','.form',function(){
		$(this).data('changed',true);

	//отправка формы редактирования
	}).on('form.submit','.form',function(e,close){
		var form = $(this).trigger('form.disable'),
			id = form.prop('id').substr(4),
			url = form.attr('action');
		if (close.sa) $('input[name*="nested_sets"]',form).val(1); //учитываем вложенность при сохранить как
		//обновить текстареа, так как он обновляется при отправке формы
		form.find('.tinymce textarea').each(function(){
			$(this).val(tinyMCE.get($(this).attr('id')).getContent());
		});
		form.ajaxSubmit({
			iframe:		true,
			url			: url+'&id='+(close.sa ? 'new&save_as='+id : id), //имитируем создание новой записи когда сохранить как
			dataType:	'json',
			success:	function (data){
				if (data) { //alert(data);
					//обновление загруженый файлов
					if (data.files) {
						for (var key in data.files) form.find('.files[data-i="'+key+'"]').replaceWith(data.files[key]);
						$('.files.simple ul').sortable();
					}
					//генерация seo-полей
					if (data.seo) {
						form.find('input[name="seo"]').prop('checked',false);
						for (var i in data.seo) form.find('input[name="'+i+'"]').val(data.seo[i]);
					}
					//успешный запрос
					if (data.error==0) {						form.find('.error').hide();
						form.find('.success').show().fadeOut(3000);
						if (data.tr) {
							//обновить
							if (id > 0 && !close.sa) {
								$('tr[data-id='+id+']',table).html(data.tr);
							//добавить новый
							} else {
								table.append(data.tr);
								form.attr('id','form'+data.id).find('span[data-name="id"]').text(data.id);
							}
						}
						if (data.table) $('#table').replaceWith(data.table);
						form.trigger('form.enable').data('changed',false);
						if (close.yep) form.trigger('form.close');
					}
					//ошибка запроса
					else {
						form.find('.error').show().html(data.error);
						form.find('.button input').removeAttr('disabled').parent(".button").removeClass('disabled');
					}
				} else alert('Ошибка отправки формы');
			},
			error:	function(xhr,txt,err){
				alert('Ошибка ('+txt+(err&&err.message ? '/'+err.message : '')+')');
			}
		});

	}).on('form.disable','.form',function(){
		$(this).find('.form_footer .button input').prop('disabled',true);
		$(this).find('.form_footer .button').addClass('disabled');

	}).on('form.enable','.form',function(){
		$(this).find('.form_footer .button input').prop('disabled',$('.loading',this).length>0);
		$(this).find('.form_footer .button').removeClass('disabled');

	//закрыть форму редактирования
	}).on('click','.form .close',function(){
		$(this).closest('.form').trigger('form.close');
		return false;

	//нажатие на кнопку для отправки формы
	}).on('click','.form .form_footer .button',function() {
		var submit = $(this),
			close = {'yep': submit.hasClass('close_form'),'sa': submit.hasClass('save_as')};
		!submit.prop('disabled') && submit.closest('form').trigger('form.submit',close);
		return false;
	});

	//если форма загружена со страницей
	$('.form').trigger('form.open');

	//БЫСТРОЕ РЕДАКТИРОВАНИЕ ===================================================
	table.on('dblclick','td.post',function(){
		sendRequest = true;
		var td = $(this);
		if (!td.has('input').length) {
			var m = table.data('module'),
				id = td.parent('tr').data('id'),
				width = td.width(),
				name = td.data('name'),
				value = td.html();
			var i = td.width(width).html('<input value="'+value.replace(/["]/g,'&quot;')+'" />').find('input').focus().width(width-6).data('value',value).get(0);
			i.setSelectionRange && i.setSelectionRange(0,value.length);
		}
	//нажатие на клавиши
	}).on('keydown','td input',function(e) {
		var i = $(this);
		//Enter или Tab
		if (e.which==13 || e.which==9) {
			sendRequest = false;
			e.preventDefault();
			var td = i.closest('td'),
				tr = td.closest('tr'),
				eq = td.index(),
				next;
			switch (e.which) {
				case 9:
					next = e.shiftKey ? td.prevAll('.post').first() : td.nextAll('.post').first();
					if (next.length == 0) next = e.shiftKey ? tr.prev().find('.post').last() : tr.next().find('.post').first();
					break;
				case 13:
					next = e.shiftKey ? tr.prev().children().eq(eq) : tr.next().children().eq(eq);
					break;
			}
			applyChanges(i);
			next.trigger('dblclick');
			return false;
		//Esc
		} else if (e.keyCode==27) {
			sendRequest = false;
			e.preventDefault();
			i.closest('td').html(i.data('value')).width('auto');
			return false;
		}
	//потеря фокуса инпутом
	}).on('blur','.table td input',function() {
		if (sendRequest) applyChanges($(this));
	});

	//функция применения изменений для быстрого редактирования
	function applyChanges(i) {
		var td = i.closest('td'),
			m = table.data('module'),
			name = td.data('name'),
			id = td.closest('tr').data('id'),
			value = i.val(),
			oldVal = i.data('value');
		td.html(value).width('auto');
		if (value!=oldVal) $.get('/admin.php', {'m':m,'u':'post','id':id,'name':name,'value':value});
	}

	//ПЕРЕКЛЮЧАТЕЛИ ============================================================
	table.on('click','.js_boolean',function(){
		var a = $(this),
			m = table.data('module'),
			id = a.closest('tr').data('id'),
			name = a.closest('td').data('name'),
			key = a.closest('td').data('key'),
			value = a.hasClass(key+'_1') ? 0 : 1;
		$.get('/admin.php', {'m':m,'u':'post','id':id,'name':name,'value':value});
		a.toggleClass(key+'_0').toggleClass(key+'_1');
		return false;
	});

	//ОКНО УДАЛЕНИЯ ============================================================
	//нажатие на "кнопку"
	$('#dialog').on('click','a',function() {
		var dialog = $('#dialog');
		if ($(this).hasClass('red')) dialog.trigger('dialog.execute');
		dialog.trigger('dialog.hide');
		return false;

	//скрытие диалога
	}).on('dialog.hide',function(){
		$(this).hide().data({target:'', path:''});
		$('#overlay').removeClass('dialog');

	//показ диалога
	}).on('dialog.show',function(){
		$('#overlay').addClass('dialog');
		$(this).show();

	//выполнение диалога
	}).on('dialog.execute',function(){
		var dialog = $(this),
			target = dialog.data('target').hide(),
			path = dialog.data('path');
		$.get(path, {},
			function (data) {
				if (data!=1) {
					target.show();
					alert(data);
				} else {
					if (dialog.data('callback')) dialog.data('callback').call(target);
					else target.remove();
				}
			}
		);
	});

	//обработчик нажатия кнопок Enter или Esc
	doc.on('keydown',function(e){
		if (e.keyCode==13 || e.keyCode==27) {
			//нажатие Esc или Enter при открытом диалоге
			if ($('#dialog').is(':visible')) {
				//Enter
				if (e.keyCode==13) {
					$('#dialog').trigger('dialog.execute').trigger('dialog.hide');
					return false;
				//Esc
				} else if (e.keyCode==27) {
					$('#dialog').trigger('dialog.hide');
				}
			//нажатие Esc при открытой форме
			} else if ($('.form').length) {
				//Esc
				if (e.keyCode==27) {
					$('.form').trigger('form.close');
				}
			}
		}
	});

	//УДАЛЕНИЕ id
	table.on('click','tr td .delete',function(){
		var tr = $(this).closest('tr'),
			m = table.data('module'),
			id = tr.data('id'),
			data = {
				target: tr,
				path: '/admin.php?u=delete&type=id&m='+m+'&id='+id
			};
		$('#dialog').trigger('dialog.show').data(data);
		return false;
	});

	//ФАЙЛЫ ====================================================================
	//клик по инпуту
	/*$(document).on('click','.file_multi li .img',function(){
		$(this).siblings('input[type=file]').trigger('click');
	});
	//обновление информации о выбранном файле mysql
	doc.on('change','.files.mysql .add_file input',function(){
		var n = $(this).val().replace(/.*\\(.*)/, "$1").replace(/.*\/(.*)/, "$1");
		$(this).closest('.files').find('.load').text('Выбрано: '+n);
	});*/
	//изменение состояния файлового инпута file
	doc.on('change','.files.file .add_file input',function(){
		var box = $(this).closest('.data').find('.img');
		//удаляем инпут с подгруженным файлом и заменяем на пустой чтобы картинка не отправлялась при отправке формы
		this.outerHTML = this.outerHTML;
		upload(box,this.files[0]);
	//изменение состояния общего файлового инпута file_multi
	}).on('change','.files.file_multi .add_file input',function(){
		upload_multi ($(this).closest('.files'),this.files);
	//изменение состояния индивидуального файлового инпута file_multi
	}).on('change','.files.file_multi li input[type=file]',function(){		var box = $(this).closest('li').find('.img');
		//удаляем инпут с подгруженным файлом и заменяем на пустой чтобы картинка не отправлялась при отправке формы
		this.outerHTML = this.outerHTML;
		upload(box,this.files[0]);

	//удаление file_multi
	}).on('click','.files.file_multi .delete',function(){
		$(this).closest('li').remove();
		return false;
	//удаление file
	}).on('click','.files.file .delete',function(){
		var box = $(this).closest('.files');
		$('img',box).prop('src','/admin/templates/no_img.png');
		$('.img input',box).val('');
		return false;
	//удаление simple
	}).on('click','.files.simple .delete',function(){
		var arr = $(this).closest('li').find('.img').attr('href').split('/'),
			data = {
				path: '/admin.php?u=delete&type=file&m='+arr[2]+'&id='+arr[3]+'&key='+arr[4]+'&file='+arr[5],
				target: $(this).closest('li')
			};
		$('#dialog').trigger('dialog.show').data(data);
		return false;
	//удаление mysql
	}).on('click','.files.mysql .delete',function(){
		var arr = $(this).closest('.files').find('.img').data('img').split('/'),
			data = {
				path: '/admin.php?u=delete&type=key&m='+arr[2]+'&id='+arr[3]+'&key='+arr[4],
				target: $(this).closest('.desc'),
				callback: function(){
					this.closest('.files').find('img').prop('src','/admin/templates/no_img.png');
					this.closest('.files').find('.img input').val('');
					this.remove();
				}
			};
		$('#dialog').trigger('dialog.show').data(data);
		return false;
	//загрузка перемещением file
	}).delegate('.files.file .img, .files.file_multi .img',{
		dragenter: function() {
			//$(this).addClass('highlighted');
			return false;
		},
		dragover: function() {
			return false;
		},
		dragleave: function() {
			//$(this).removeClass('highlighted');
			return false;
		},
		drop: function(e) {
			var img = $(this),
				box = img.closest('.files'),
				dt = e.originalEvent.dataTransfer;
			if (box.hasClass('file')) {
				upload(img,dt.files[0]);
			} else {
				upload_multi(box,dt.files);
			}
			return false;
		}
	});
	//загрузка картинки
	function upload(uploadItem,file) {
		if (file) {
			var img = $('img',uploadItem).prop({src:''}),
				reader = new FileReader(),
				//отключаем возможность отправки формы до загрузки всех изображений
				form = uploadItem.addClass('loading').trigger('form.disable');
			$('.progress',uploadItem).remove();
			var bar = $('<div class="progress" rel="0">загрузка</div>').appendTo(uploadItem);
			$(reader).load(function(e){
				var path = '';
				 // Отсеиваем не картинки
				if (!file.type.match(/image.*/)) {
					if (path=='' && file.type.match(/text.*/))	path = '/admin/templates/icons/doc.png';
					if (path=='' && file.type.match(/.*word/))	path = '/admin/templates/icons/doc.png';
					if (path=='' && file.type.match(/.*excel/))	path = '/admin/templates/icons/xls.png';
					if (path=='' && file.type.match(/.*pdf/))	path = '/admin/templates/icons/pdf.png';
					if (path=='' && file.name.match(/.*zip/))	path = '/admin/templates/icons/zip.png';
					if (path=='' && file.name.match(/.*rar/))	path = '/admin/templates/icons/zip.png';
					if (path=='') path = '/admin/templates/icons/blank.png';
				}
				else path = e.target.result;
				//alert(file.type+' '+path);
				img.prop({src:path});
				uploadItem.prop({file: file});
				new uploaderObject({
					file:		file,
					url:		'/ajax.php?file=uploader',
					fieldName:	'temp',
					onprogress: function(percents) {
						var value = bar.width() * (percents/100 - 1);
						bar.attr('rel', percents).text(percents+'%').css('background-position', value+'px center');
					},
					oncomplete: function(done, data) {
						if (done && data) {
							$('input[type="hidden"]',uploadItem).val(data);
							bar.text('загружено');
						}
						 else {
							alert(this.lastError.text);
						}
						//включаем возможность отправки формы
						uploadItem.removeClass('loading').trigger('form.enable');
					}
				});
			});
			reader.readAsDataURL(file);
		}
	}
	function upload_multi (box,files) {
		var n = 0,
			ul = $('ul',box),
			key = box.data('i');
		$('li',ul).each(function(){
			var i = $(this).data('i');
			if (i > n) n = i;
		});
		$.each(files, function(i, file) {
			n++;
			var name = file.name.split('.',file.name.split('.').length-1),
				li = $('<li/>').data('i',n).attr('title','для изменения последовательности картинок переместите блок в нужное место').appendTo(ul),
				img = '<div class="img"><span>&nbsp;</span><img src="" /><input type="hidden" name="'+key+'['+n+'][temp]" /></div>';
			img+='<a href="#" class="sprite delete" title="удалить"></a>';
			img+='<div>'+file.name+'</div><input class="input" name="'+key+'['+n+'][name]" value="'+name+'"/><br/><label><input name="'+key+'['+n+'][display]" type="checkbox" checked="checked" value="1" /><span>показывать</span></label>';
			$(img).appendTo(li);
			upload(li.find('.img'),file);
		});
		$('.sortable',box).sortable();
		$('.data input',box).replaceWith('<input type="file" multiple="multiple" title="выбрать файл" />');
	}

	//ПОИСК ====================================================================
	$('#filter .sprite.search').click(function(){
		var url = $(this).attr('href'),
			search = $(this).parent('div').find('input').blur().val();
		top.location = url+search;
		return false;
	});

});


hs.lang = {
	cssDirection: 'ltr',
	loadingText : 'Загрузка...',
	loadingTitle : 'Нажмите для отмены',
	focusTitle : 'Нажмите, чтобы вынести на передний план',
	fullExpandTitle : 'Увеличить до реального размера (f)',
	creditsText : 'Powered by <i>Highslide JS</i>',
	creditsTitle : 'Go to the Highslide JS homepage',
	previousText : 'Предыдущее',
	nextText : 'Следующее',
	moveText : 'Передвинуть',
	closeText : 'Закрыть',
	closeTitle : 'Закрыть (esc)',
	resizeTitle : 'Изменить размер',
	playText : 'Слайдшоу',
	playTitle : 'Слайдшоу (пробел)',
	pauseText : 'Пауза',
	pauseTitle : 'Пауза (пробел)',
	previousTitle : 'Предыдущее (стрелка влево)',
	nextTitle : 'Следующее (стрелка вправо)',
	moveTitle : 'Передвинуть',
	fullExpandText : '1:1',
	number: 'Изображение %1 из %2',
	restoreTitle : 'Нажмите для закрытия изображения, нажмите и передвиньте курсор для перемещения. Используйте стрелки для следующего или предыдущего изображения.'
},

hs.graphicsDir = '/plugins/highslide/graphics/';
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.outlineType = 'rounded-black';
hs.wrapperClassName = 'dark borderless controls-in-heading';
hs.fadeInOut = true;
hs.showCredits = false;
hs.dimmingOpacity = 0.75;
hs.allowMultipleInstances = false;
hs.headingEval = 'this.a.title';

if (hs.addSlideshow) {
	hs.addSlideshow({
		interval: 3000,
		repeat: true,
		useControls: true,
		fixedControls: true,
		overlayOptions: {
			opacity: .75,
			position: 'bottom center',
			hideOnMouseOut: true
		}
	});
}
