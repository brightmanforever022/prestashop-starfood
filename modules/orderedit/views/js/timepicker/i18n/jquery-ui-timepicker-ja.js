/**
 * OrderEdit
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2015 silbersaiten
 * @version   1.0.0
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */
/* Japanese translation for the jQuery Timepicker Addon */
/* Written by Jun Omae */
(function($) {
	$.timepicker.regional['ja'] = {
		timeOnlyTitle: '時間を選択',
		timeText: '時間',
		hourText: '時',
		minuteText: '分',
		secondText: '秒',
		millisecText: 'ミリ秒',
		microsecText: 'マイクロ秒',
		timezoneText: 'タイムゾーン',
		currentText: '現時刻',
		closeText: '閉じる',
		timeFormat: 'HH:mm',
		amNames: ['午前', 'AM', 'A'],
		pmNames: ['午後', 'PM', 'P'],
		isRTL: false
	};
	$.timepicker.setDefaults($.timepicker.regional['ja']);
})(jQuery);
