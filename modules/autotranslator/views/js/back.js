/**
*  @author    Amazzing
*  @copyright Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)*
*/

var ajax_action_path = window.location.href.split('#')[0]+'&ajax=1',
	bulk_click = [],
	blockAjax = false;

$(document).ready(function(){

	$('select[name="at_ct"]').on('change', function(){
        var ct = $(this).val(),
			hideLang = ct == 'theme' || ct == 'module';
		// themes and modules always translated from EN
		$('select[name="at_lang"]').toggleClass('hidden', hideLang);
		$('.special-param, .special-option').addClass('hidden');
		$('.special-param.'+ct+', .special-option.'+ct).removeClass('hidden');
		$('.order-by').val('id').change();
    });

	$('.order-way-label').on('click', function(){
		$(this).removeClass('active').siblings('.order-way-label').addClass('active');
		$('input.order-way').val($('.order-way-label.active').data('way')).change();
	});

    $('.overwrite_existing').on('change', function(){
    	var overwrite = $(this).prop('checked') ? 1 : 0,
    		params = 'ajax_action=saveOwerwriteOption&overwrite_existing='+overwrite;
    	$.ajax({url: ajax_action_path+'&'+params});
    });

	$(document).on('click', 'a[href="#"]', function(e){
		e.preventDefault();
	}).on('click', '.go-to-page', function(e){
		updateList($(this).data('page'));
	}).on('change', '.update-list', function(e){
		updateList(1);
	}).on('click', 'a.translate', function(){
		var content_type = $('select[name="at_ct"]').val(),
			identifier = $(this).closest('tr').data('identifier'),
			from = $('[name="at_lang"]').val(),
			to = $(this).attr('data-to');
		if (to == 'all'){
			bulk_click = [];
			$(this).closest('li').siblings().find('a.translate').each(function(){
				bulk_click.push($(this));
			});
			if (bulk_click.length) {
				to = bulk_click[0].attr('data-to');
			} else {
				return false;
			}
		}
		autoTranslate(content_type, identifier, from, to);
	}).on('click', '.btn.stop', function(){
		bulk_click = [];
	}).on('click', '.chk-action', function(){
		var $i = $(this).find('i'),
			$checkboxes = $('input.item-checkbox');
		if ($i.hasClass('icon-check-sign')){
			$checkboxes.prop('checked', true);
		} else if ($i.hasClass('icon-check-empty')){
			$checkboxes.prop('checked', false);
		} else if ($i.hasClass('icon-random')){
			$checkboxes.each(function(){
				$(this).prop('checked', !$(this).prop('checked'));
			});
		}
	}).on('click', '.bulk-translate', function(){
		var to = $(this).data('to'),
			a_selector = to == 'all' ? 'a.translate:not([data-to="all"])' : 'a.translate[data-to="'+to+'"]';
		$('input.item-checkbox:checked').each(function(){
			$(this).closest('tr').find(a_selector).each(function(){
				bulk_click.push($(this));
			});
		});
		if (bulk_click.length){
			bulk_click[0].click();
			$('html, body').animate({
				scrollTop: bulk_click[0].closest('tr').offset().top	- 150
			}, 700);
		}
	});

	function updateList(p) {
		var data = 'ajax_action=callResourseList&'+$('.list-params').serialize()+'&'+$('.list-pagination').serialize()+'&p='+p,
			response = function(r) {
				if ('list_html' in r) {
					$('.dynamic-list').html(utf8_decode(r.list_html));
				}
			};
		$('.dynamic-list').find('table').addClass('loading');
		ajaxRequest(data, response);
	}

	function autoTranslate(content_type, identifier, from, to){
		var $tr = $('tr[data-identifier="'+identifier+'"]'),
			$actionBtn = $tr.find('.t-text'),
			data = {
				content_type: content_type,
				identifier: identifier,
				from: from,
				to: to,
				ajax_action : 'autoTranslate',
				location : $('select[name="location"]').val(),
				overwrite_existing : $('input[name="overwrite_existing"]').prop('checked') ? 1 : 0,
			},
			response = function(r) {
				if (r.hasError){
					prependErrors($tr.closest('.dynamic-list'), r.errors);
				} else {
					$tr.find('.ajax-response').addClass('ok').html('<i class="icon-check"></i> '+utf8_decode(r.response));
					$('.day-stats').html(r.stats_data.day);
					$('.month-stats').html(r.stats_data.month);
				}
				bulk_click.shift();
				if (bulk_click.length){
					var identifier = bulk_click[0].closest('tr').attr('data-identifier'),
						to = bulk_click[0].attr('data-to');
					autoTranslate(content_type, identifier, from, to);
				}
			};
		$actionBtn.addClass('loading');
		ajaxRequest (data, response);
	}

	function prependErrors($el, err) {
		var $err = $('<div class="thrown-error">'+utf8_decode(err)+'</div>'),
			errTxt = $err.text(),
			repeated = false;
		$el.find('.thrown-error').each(function(){
			var $repContainer = $(this).find('.repeat'),
				repTxt = $repContainer.text();
			if ($.trim($(this).text()) == $.trim(errTxt+' '+repTxt)) {
				if (!$repContainer.length) {
					$(this).find('.alert').append(' <span class="repeat">2</span>');
				} else {
					$repContainer.html(parseInt(repTxt) + 1);
				}
				repeated = true;
				return false;
			}
		});
		if (!repeated) {
			$el.prepend($err);
		}
	}
});

function ajaxRequest (data, response) {
	if (blockAjax) {
		alert('Please wait');
		return;
	}
	blockAjax = true;
	$.ajax({
		type: 'POST',
		url: ajax_action_path,
		data: data,
		dataType : 'json',
		success: function(r) {
			console.dir(r);
			blockAjax = false;
			$('.loading').removeClass('loading');
			response(r);
		},
		error: function(r) {
			blockAjax = false;
			$('.loading').removeClass('loading');
			console.warn($(r.responseText).text() || r.responseText);
		}
	});
}


function utf8_decode (utfstr) {
	var res = '';
	for (var i = 0; i < utfstr.length;) {
		var c = utfstr.charCodeAt(i);
		if (c < 128){
			res += String.fromCharCode(c);
			i++;
		} else if((c > 191) && (c < 224)) {
			var c1 = utfstr.charCodeAt(i+1);
			res += String.fromCharCode(((c & 31) << 6) | (c1 & 63));
			i += 2;
		} else {
			var c1 = utfstr.charCodeAt(i+1);
			var c2 = utfstr.charCodeAt(i+2);
			res += String.fromCharCode(((c & 15) << 12) | ((c1 & 63) << 6) | (c2 & 63));
			i += 3;
		}
	}
	return res;
}
/* since 2.7.0 */
