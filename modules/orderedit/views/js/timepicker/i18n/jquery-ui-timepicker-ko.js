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
/* Korean translation for the jQuery Timepicker Addon */
/* Written by Genie */
(function($) {
	$.timepicker.regional['ko'] = {
		timeOnlyTitle: '시간 선택',
		timeText: '시간',
		hourText: '시',
		minuteText: '분',
		secondText: '초',
		millisecText: '밀리초',
		microsecText: '마이크로',
		timezoneText: '표준 시간대',
		currentText: '현재 시각',
		closeText: '닫기',
		timeFormat: 'tt h:mm',
		amNames: ['오전', 'AM', 'A'],
		pmNames: ['오후', 'PM', 'P'],
		isRTL: false
	};
	$.timepicker.setDefaults($.timepicker.regional['ko']);
})(jQuery);
