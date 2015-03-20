$(document).ready(function(){
/*	if ($.isFunction($.fn.validate)) $('form.validate').each(function(){
*/
	$('form.validate').submit(function(){
		if ($.isFunction($.fn.validate)) {
			if ($(this).valid()) {}
			else {
					$(this).parents('.window').window_resize();
				}
			}
		}
	});

	//отправка формы ссылкой
	$(document).on("click",'.js_submit',function(){
		if ($(this).hasClass('inactive')) return false;
		$(this).parents('form').submit();
		return false;
	});

	//очитска урл от путстых значений
	$('form.clear_form').submit(function(){
		$(this).find('select,input').each(function(){
		});
	});


	//мультичексбокс
	$(document).on("change",'.multi_checkbox .data input',function(){
		var arr = [];
		var i = 0;
		$(this).parents('.data').find('input:checked').each(function(){
			arr[i] = $(this).val();
			i++;
		});
		$(this).parents('.data').next('input').val(arr);
	});
	//min-max
	$(document).on("change",'.input2 .data input',function(){
		var max = parseInt($(this).parents('.data').find('input:last').val());
		$(this).parents('.data').next('input').val(min+'-'+max);
	});


	//добавление товара в корзину
	$('.js_buy').click(function(){
			product	= $(this).data('id'),
			price	= $(this).data('price'),
			count	= 1,
			total = $('.total',basket),
			basket_count = parseInt(counter.text()),
			basket_total = parseInt(total.text());
		$.getJSON('/ajax.php',{
				action:		'add_product',
				product:	product,
				count:		count
			},function (data) {
				if (data.done){
					total.text(data.total);
				} else alert(data.message);
			}
		);
		//сообщение что товар куплен
		var id = $(this).data('window_id'); //alert(id);
		$('#'+id).window_open();
		//моментальное изменение количества и цены товароы на старнице
		basket_count+= count;
		basket_total+= price * count;
		//количество знаков после запятой
		basket_total = basket_total.toFixed();
		counter.text(basket_count);
		total.text(basket_total);
		$('.full',basket).show();
		$('.empty',basket).hide();
		return false;
	});


	//кнопка открытия окна
	$(document).on('click','.window_open',function(){
		var id = $(this).data('window_id');
		$('#'+id).window_open();
		return false;
	});
	//кнопка закрытия окна
	$(document).on('click','.window_close',function(){
		$('#overlay').removeClass('active');
		$(this).closest('.window').removeClass('active');
		return false;
	});
	//функция открытия окна
	$.fn.window_open = function() {
		//добавляем модальный фон
		if(attr.indexOf('modal') + 1) {
			if ($('#overlay').length==0) $('<div id="overlay">').appendTo('body');
			$('#overlay').addClass('active');
		}
		//центрируем по высоте
		if(attr.indexOf('middle') + 1) {
			var top = 0.5 * ($(window).height() - $(this).outerHeight(true)) + $(window).scrollTop();
			if (top < 0) top = 0;
			$(this).css('top',top+'px');
		}
		$(this).addClass('active');
	};
});