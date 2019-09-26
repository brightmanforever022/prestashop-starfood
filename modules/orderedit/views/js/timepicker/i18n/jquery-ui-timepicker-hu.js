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
/* Hungarian translation for the jQuery Timepicker Addon */
/* Written by Vas Gábor */
(function($) {
	$.timepicker.regional['hu'] = {
		timeOnlyTitle: 'Válasszon időpontot',
		timeText: 'Idő',
		hourText: 'Óra',
		minuteText: 'Perc',
		secondText: 'Másodperc',
		millisecText: 'Milliszekundumos',
		microsecText: 'Ezredmásodperc',
		timezoneText: 'Időzóna',
		currentText: 'Most',
		closeText: 'Kész',
		timeFormat: 'HH:mm',
		amNames: ['de.', 'AM', 'A'],
		pmNames: ['du.', 'PM', 'P'],
		isRTL: false
	};
	$.timepicker.setDefaults($.timepicker.regional['hu']);
})(jQuery);
