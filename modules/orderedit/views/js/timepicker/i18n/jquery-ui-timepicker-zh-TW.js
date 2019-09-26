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
/* Chinese translation for the jQuery Timepicker Addon */
/* Written by Alang.lin */
(function($) {
	$.timepicker.regional['zh-TW'] = {
		timeOnlyTitle: '選擇時分秒',
		timeText: '時間',
		hourText: '時',
		minuteText: '分',
		secondText: '秒',
		millisecText: '毫秒',
		microsecText: '微秒',
		timezoneText: '時區',
		currentText: '現在時間',
		closeText: '確定',
		timeFormat: 'HH:mm',
		amNames: ['上午', 'AM', 'A'],
		pmNames: ['下午', 'PM', 'P'],
		isRTL: false
	};
	$.timepicker.setDefaults($.timepicker.regional['zh-TW']);
})(jQuery);
