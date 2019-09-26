// ----------------------------------------------------------------------------
//
//  File:   				functions.js
//  Purpose:				Commun functions for all pages
//  Author: 				Store Commander http://www.storecommander.com
//
// ----------------------------------------------------------------------------

// ----------------------------------------------------------------------------
//
//  Function:   priceFormat
//  Purpose:		Convert float to price format ########.##
//  Arguments:	float: price to convert
//
// ----------------------------------------------------------------------------

	function priceFormat(i) 
	{
		i=noComma(i.toString());
		if(isNaN(i)) { i = 0.00; }
		var minus = '';
		if(i < 0) { minus = '-'; }
		i = Math.abs(i);
		i = parseInt((i + .005) * 100);
		i = i / 100;
		s = new String(i);
		if(s.indexOf('.') < 0) { s += '.00'; }
		if(s.indexOf('.') == (s.length - 2)) { s += '0'; }
		s = minus + s;
		return s;
	}

// ----------------------------------------------------------------------------
//
//  Function:   priceFormat4Dec
//  Purpose:            Convert float to price format ########.####
//  Arguments:  float: price to convert
//
// ----------------------------------------------------------------------------

	function priceFormat4Dec(i) 
	{
		i=noComma(i.toString());
		if(isNaN(i)) { i = 0.0000; }
		var minus = '';
		if(i < 0) { minus = '-'; }
		i = Math.abs(i);
		i = parseInt((i + .00005) * 10000);
		i = i / 10000;
		s = new String(i);
		if(s.indexOf('.') < 0) { s += '.0000'; }
		if(s.indexOf('.') == (s.length - 2)) { s += '000'; }
		if(s.indexOf('.') == (s.length - 3)) { s += '00'; }
		if(s.indexOf('.') == (s.length - 4)) { s += '0'; }
		s = minus + s;
		return s;
	}

// ----------------------------------------------------------------------------
//
//  Function:   priceFormat6Dec
//  Purpose:		Convert float to price format ########.######
//  Arguments:	float: price to convert
//
// ----------------------------------------------------------------------------

	function priceFormat6Dec(i) 
	{
		i=noComma(i.toString());
		if(isNaN(i)) { i = 0.000000; }
		var minus = '';
		if(i < 0) { minus = '-'; }
		i = Math.abs(i);
		i = parseInt((i + .0000005) * 1000000);
		i = i / 1000000;
		s = new String(i);
		if(s.indexOf('.') < 0) { s += '.000000'; }
		if(s.indexOf('.') == (s.length - 2)) { s += '00000'; }
		if(s.indexOf('.') == (s.length - 3)) { s += '0000'; }
		if(s.indexOf('.') == (s.length - 4)) { s += '000'; }
		if(s.indexOf('.') == (s.length - 5)) { s += '00'; }
		if(s.indexOf('.') == (s.length - 6)) { s += '0'; }
		s = minus + s;
		return s;
	}
	
// ----------------------------------------------------------------------------
//
//  Function:   noComma
//  Purpose:		Convert , to .
//  Arguments:	str: string to convert to float
//
// ----------------------------------------------------------------------------

	function noComma(s)
	{
		return s.toString().replace(',','.')*1;
	}
	
// ----------------------------------------------------------------------------
//
//  Function:   in_array
//  Purpose:		Check if val exists in array
//  Arguments:	val: value to check, array: array 
//
// ----------------------------------------------------------------------------

	function in_array(val, array) {
		for(var i = 0, l = array.length; i < l; i++) {
			if(array[i] == val) {
				return true;
			}
		}
		return false;
	}
	
	
// ----------------------------------------------------------------------------
//
//  Function:   getLinkRewriteFromString
//  Purpose:		Convert string to link_rewrite
//  Arguments:	val: value to convert
//
// ----------------------------------------------------------------------------

	function getLinkRewriteFromString(str)
	{
		str = str.toUpperCase();
		str = str.toLowerCase();
		
		/* Lowercase */
		str = str.replace(/[\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5\u0101\u0103\u0105\u0430]/g, 'a');
        str = str.replace(/[\u0431]/g, 'b');
		str = str.replace(/[\u00E7\u0107\u0109\u010D\u0446]/g, 'c');
		str = str.replace(/[\u010F\u0111\u0434]/g, 'd');
		str = str.replace(/[\u00E8\u00E9\u00EA\u00EB\u0113\u0115\u0117\u0119\u011B\u0435\u044D]/g, 'e');
        str = str.replace(/[\u0444]/g, 'f');
		str = str.replace(/[\u011F\u0121\u0123\u0433\u0491]/g, 'g');
		str = str.replace(/[\u0125\u0127]/g, 'h');
		str = str.replace(/[\u00EC\u00ED\u00EE\u00EF\u0129\u012B\u012D\u012F\u0131\u0438\u0456]/g, 'i');
		str = str.replace(/[\u0135\u0439]/g, 'j');
		str = str.replace(/[\u0137\u0138\u043A]/g, 'k');
		str = str.replace(/[\u013A\u013C\u013E\u0140\u0142\u043B]/g, 'l');
        str = str.replace(/[\u043C]/g, 'm');
		str = str.replace(/[\u00F1\u0144\u0146\u0148\u0149\u014B\u043D]/g, 'n');
		str = str.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8\u014D\u014F\u0151\u043E]/g, 'o');
        str = str.replace(/[\u043F]/g, 'p');
		str = str.replace(/[\u0155\u0157\u0159\u0440]/g, 'r');
		str = str.replace(/[\u015B\u015D\u015F\u0161\u0441]/g, 's');
		str = str.replace(/[\u00DF]/g, 'ss');
		str = str.replace(/[\u0163\u0165\u0167\u0442]/g, 't');
		str = str.replace(/[\u00F9\u00FA\u00FB\u00FC\u0169\u016B\u016D\u016F\u0171\u0173\u0443]/g, 'u');
        str = str.replace(/[\u0432]/g, 'v');
		str = str.replace(/[\u0175]/g, 'w');
		str = str.replace(/[\u00FF\u0177\u00FD\u044B]/g, 'y');
		str = str.replace(/[\u017A\u017C\u017E\u0437]/g, 'z');
		str = str.replace(/[\u00E6]/g, 'ae');
        str = str.replace(/[\u0447]/g, 'ch');
        str = str.replace(/[\u0445]/g, 'kh');
		str = str.replace(/[\u0153]/g, 'oe');
        str = str.replace(/[\u0448]/g, 'sh');
        str = str.replace(/[\u0449]/g, 'ssh');
        str = str.replace(/[\u044F]/g, 'ya');
        str = str.replace(/[\u0454]/g, 'ye');
        str = str.replace(/[\u0457]/g, 'yi');
        str = str.replace(/[\u0451]/g, 'yo');
        str = str.replace(/[\u044E]/g, 'yu');
        str = str.replace(/[\u0436]/g, 'zh');

		/* Uppercase */
		str = str.replace(/[\u0100\u0102\u0104\u00C0\u00C1\u00C2\u00C3\u00C4\u00C5\u0410]/g, 'A');
        str = str.replace(/[\u0411]/g, 'B');
		str = str.replace(/[\u00C7\u0106\u0108\u010A\u010C\u0426]/g, 'C');
		str = str.replace(/[\u010E\u0110\u0414]/g, 'D');
		str = str.replace(/[\u00C8\u00C9\u00CA\u00CB\u0112\u0114\u0116\u0118\u011A\u0415\u042D]/g, 'E');
        str = str.replace(/[\u0424]/g, 'F');
		str = str.replace(/[\u011C\u011E\u0120\u0122\u0413\u0490]/g, 'G');
		str = str.replace(/[\u0124\u0126]/g, 'H');
		str = str.replace(/[\u0128\u012A\u012C\u012E\u0130\u0418\u0406]/g, 'I');
		str = str.replace(/[\u0134\u0419]/g, 'J');
		str = str.replace(/[\u0136\u041A]/g, 'K');
		str = str.replace(/[\u0139\u013B\u013D\u0139\u0141\u041B]/g, 'L');
        str = str.replace(/[\u041C]/g, 'M');
		str = str.replace(/[\u00D1\u0143\u0145\u0147\u014A\u041D]/g, 'N');
		str = str.replace(/[\u00D3\u014C\u014E\u0150\u041E]/g, 'O');
        str = str.replace(/[\u041F]/g, 'P');
		str = str.replace(/[\u0154\u0156\u0158\u0420]/g, 'R');
		str = str.replace(/[\u015A\u015C\u015E\u0160\u0421]/g, 'S');
		str = str.replace(/[\u0162\u0164\u0166\u0422]/g, 'T');
		str = str.replace(/[\u00D9\u00DA\u00DB\u00DC\u0168\u016A\u016C\u016E\u0170\u0172\u0423]/g, 'U');
        str = str.replace(/[\u0412]/g, 'V');
		str = str.replace(/[\u0174]/g, 'W');
		str = str.replace(/[\u0176\u042B]/g, 'Y');
		str = str.replace(/[\u0179\u017B\u017D\u0417]/g, 'Z');
		str = str.replace(/[\u00C6]/g, 'AE');
        str = str.replace(/[\u0427]/g, 'CH');
        str = str.replace(/[\u0425]/g, 'KH');
		str = str.replace(/[\u0152]/g, 'OE');
        str = str.replace(/[\u0428]/g, 'SH');
        str = str.replace(/[\u0429]/g, 'SHH');
        str = str.replace(/[\u042F]/g, 'YA');
        str = str.replace(/[\u0404]/g, 'YE');
        str = str.replace(/[\u0407]/g, 'YI');
        str = str.replace(/[\u0401]/g, 'YO');
        str = str.replace(/[\u042E]/g, 'YU');
        str = str.replace(/[\u0416]/g, 'ZH');

		str = str.toLowerCase();

		str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]/g,'');
		
		str = str.replace(/[\u0028\u0029\u0021\u003F\u002E\u0026\u005E\u007E\u002B\u002A\u002F\u003A\u003B\u003C\u003D\u003E]/g, '');
		str = str.replace(/[\s\'\:\/\[\]-]+/g, ' ');

		// Add special char not used for url rewrite
		str = str.replace(/[ ]/g, '-');
		str = str.replace(/[\/\\"'|,;%]*/g, '');

		return str;
	} 
	
	function getLinkRewriteFromStringLight(str)
	{
		/* Lowercase */
		str = str.replace(/[\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5\u0101\u0103\u0105\u0430]/g, 'a');
        str = str.replace(/[\u0431]/g, 'b');
		str = str.replace(/[\u00E7\u0107\u0109\u010D\u0446]/g, 'c');
		str = str.replace(/[\u010F\u0111\u0434]/g, 'd');
		str = str.replace(/[\u00E8\u00E9\u00EA\u00EB\u0113\u0115\u0117\u0119\u011B\u0435\u044D]/g, 'e');
        str = str.replace(/[\u0444]/g, 'f');
		str = str.replace(/[\u011F\u0121\u0123\u0433\u0491]/g, 'g');
		str = str.replace(/[\u0125\u0127]/g, 'h');
		str = str.replace(/[\u00EC\u00ED\u00EE\u00EF\u0129\u012B\u012D\u012F\u0131\u0438\u0456]/g, 'i');
		str = str.replace(/[\u0135\u0439]/g, 'j');
		str = str.replace(/[\u0137\u0138\u043A]/g, 'k');
		str = str.replace(/[\u013A\u013C\u013E\u0140\u0142\u043B]/g, 'l');
        str = str.replace(/[\u043C]/g, 'm');
		str = str.replace(/[\u00F1\u0144\u0146\u0148\u0149\u014B\u043D]/g, 'n');
		str = str.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8\u014D\u014F\u0151\u043E]/g, 'o');
        str = str.replace(/[\u043F]/g, 'p');
		str = str.replace(/[\u0155\u0157\u0159\u0440]/g, 'r');
		str = str.replace(/[\u015B\u015D\u015F\u0161\u0441]/g, 's');
		str = str.replace(/[\u00DF]/g, 'ss');
		str = str.replace(/[\u0163\u0165\u0167\u0442]/g, 't');
		str = str.replace(/[\u00F9\u00FA\u00FB\u00FC\u0169\u016B\u016D\u016F\u0171\u0173\u0443]/g, 'u');
        str = str.replace(/[\u0432]/g, 'v');
		str = str.replace(/[\u0175]/g, 'w');
		str = str.replace(/[\u00FF\u0177\u00FD\u044B]/g, 'y');
		str = str.replace(/[\u017A\u017C\u017E\u0437]/g, 'z');
		str = str.replace(/[\u00E6]/g, 'ae');
        str = str.replace(/[\u0447]/g, 'ch');
        str = str.replace(/[\u0445]/g, 'kh');
		str = str.replace(/[\u0153]/g, 'oe');
        str = str.replace(/[\u0448]/g, 'sh');
        str = str.replace(/[\u0449]/g, 'ssh');
        str = str.replace(/[\u044F]/g, 'ya');
        str = str.replace(/[\u0454]/g, 'ye');
        str = str.replace(/[\u0457]/g, 'yi');
        str = str.replace(/[\u0451]/g, 'yo');
        str = str.replace(/[\u044E]/g, 'yu');
        str = str.replace(/[\u0436]/g, 'zh');

		/* Uppercase */
		str = str.replace(/[\u0100\u0102\u0104\u00C0\u00C1\u00C2\u00C3\u00C4\u00C5\u0410]/g, 'A');
        str = str.replace(/[\u0411]/g, 'B');
		str = str.replace(/[\u00C7\u0106\u0108\u010A\u010C\u0426]/g, 'C');
		str = str.replace(/[\u010E\u0110\u0414]/g, 'D');
		str = str.replace(/[\u00C8\u00C9\u00CA\u00CB\u0112\u0114\u0116\u0118\u011A\u0415\u042D]/g, 'E');
        str = str.replace(/[\u0424]/g, 'F');
		str = str.replace(/[\u011C\u011E\u0120\u0122\u0413\u0490]/g, 'G');
		str = str.replace(/[\u0124\u0126]/g, 'H');
		str = str.replace(/[\u0128\u012A\u012C\u012E\u0130\u0418\u0406]/g, 'I');
		str = str.replace(/[\u0134\u0419]/g, 'J');
		str = str.replace(/[\u0136\u041A]/g, 'K');
		str = str.replace(/[\u0139\u013B\u013D\u0139\u0141\u041B]/g, 'L');
        str = str.replace(/[\u041C]/g, 'M');
		str = str.replace(/[\u00D1\u0143\u0145\u0147\u014A\u041D]/g, 'N');
		str = str.replace(/[\u00D3\u014C\u014E\u0150\u041E]/g, 'O');
        str = str.replace(/[\u041F]/g, 'P');
		str = str.replace(/[\u0154\u0156\u0158\u0420]/g, 'R');
		str = str.replace(/[\u015A\u015C\u015E\u0160\u0421]/g, 'S');
		str = str.replace(/[\u0162\u0164\u0166\u0422]/g, 'T');
		str = str.replace(/[\u00D9\u00DA\u00DB\u00DC\u0168\u016A\u016C\u016E\u0170\u0172\u0423]/g, 'U');
        str = str.replace(/[\u0412]/g, 'V');
		str = str.replace(/[\u0174]/g, 'W');
		str = str.replace(/[\u0176\u042B]/g, 'Y');
		str = str.replace(/[\u0179\u017B\u017D\u0417]/g, 'Z');
		str = str.replace(/[\u00C6]/g, 'AE');
        str = str.replace(/[\u0427]/g, 'CH');
        str = str.replace(/[\u0425]/g, 'KH');
		str = str.replace(/[\u0152]/g, 'OE');
        str = str.replace(/[\u0428]/g, 'SH');
        str = str.replace(/[\u0429]/g, 'SHH');
        str = str.replace(/[\u042F]/g, 'YA');
        str = str.replace(/[\u0404]/g, 'YE');
        str = str.replace(/[\u0407]/g, 'YI');
        str = str.replace(/[\u0401]/g, 'YO');
        str = str.replace(/[\u042E]/g, 'YU');
        str = str.replace(/[\u0416]/g, 'ZH');

		str = str.toLowerCase();

		str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]/g,'');
		
		str = str.replace(/[\u0028\u0029\u0021\u003F\u002E\u0026\u005E\u007E\u002B\u002A\u002F\u003A\u003B\u003C\u003D\u003E]/g, '');
		str = str.replace(/[\s\'\:\/\[\]-]+/g, ' ');

		// Add special char not used for url rewrite
		str = str.replace(/[ ]/g, '-');
		str = str.replace(/[\/\\"'|,;%]*/g, '');

		return str;
	} 
	
	function getLinkRewriteFromStringLightWithCase(str)
	{
		/* Lowercase */
		str = str.replace(/[\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5\u0101\u0103\u0105\u0430]/g, 'a');
        str = str.replace(/[\u0431]/g, 'b');
		str = str.replace(/[\u00E7\u0107\u0109\u010D\u0446]/g, 'c');
		str = str.replace(/[\u010F\u0111\u0434]/g, 'd');
		str = str.replace(/[\u00E8\u00E9\u00EA\u00EB\u0113\u0115\u0117\u0119\u011B\u0435\u044D]/g, 'e');
        str = str.replace(/[\u0444]/g, 'f');
		str = str.replace(/[\u011F\u0121\u0123\u0433\u0491]/g, 'g');
		str = str.replace(/[\u0125\u0127]/g, 'h');
		str = str.replace(/[\u00EC\u00ED\u00EE\u00EF\u0129\u012B\u012D\u012F\u0131\u0438\u0456]/g, 'i');
		str = str.replace(/[\u0135\u0439]/g, 'j');
		str = str.replace(/[\u0137\u0138\u043A]/g, 'k');
		str = str.replace(/[\u013A\u013C\u013E\u0140\u0142\u043B]/g, 'l');
        str = str.replace(/[\u043C]/g, 'm');
		str = str.replace(/[\u00F1\u0144\u0146\u0148\u0149\u014B\u043D]/g, 'n');
		str = str.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8\u014D\u014F\u0151\u043E]/g, 'o');
        str = str.replace(/[\u043F]/g, 'p');
		str = str.replace(/[\u0155\u0157\u0159\u0440]/g, 'r');
		str = str.replace(/[\u015B\u015D\u015F\u0161\u0441]/g, 's');
		str = str.replace(/[\u00DF]/g, 'ss');
		str = str.replace(/[\u0163\u0165\u0167\u0442]/g, 't');
		str = str.replace(/[\u00F9\u00FA\u00FB\u00FC\u0169\u016B\u016D\u016F\u0171\u0173\u0443]/g, 'u');
        str = str.replace(/[\u0432]/g, 'v');
		str = str.replace(/[\u0175]/g, 'w');
		str = str.replace(/[\u00FF\u0177\u00FD\u044B]/g, 'y');
		str = str.replace(/[\u017A\u017C\u017E\u0437]/g, 'z');
		str = str.replace(/[\u00E6]/g, 'ae');
        str = str.replace(/[\u0447]/g, 'ch');
        str = str.replace(/[\u0445]/g, 'kh');
		str = str.replace(/[\u0153]/g, 'oe');
        str = str.replace(/[\u0448]/g, 'sh');
        str = str.replace(/[\u0449]/g, 'ssh');
        str = str.replace(/[\u044F]/g, 'ya');
        str = str.replace(/[\u0454]/g, 'ye');
        str = str.replace(/[\u0457]/g, 'yi');
        str = str.replace(/[\u0451]/g, 'yo');
        str = str.replace(/[\u044E]/g, 'yu');
        str = str.replace(/[\u0436]/g, 'zh');

		/* Uppercase */
		str = str.replace(/[\u0100\u0102\u0104\u00C0\u00C1\u00C2\u00C3\u00C4\u00C5\u0410]/g, 'A');
        str = str.replace(/[\u0411]/g, 'B');
		str = str.replace(/[\u00C7\u0106\u0108\u010A\u010C\u0426]/g, 'C');
		str = str.replace(/[\u010E\u0110\u0414]/g, 'D');
		str = str.replace(/[\u00C8\u00C9\u00CA\u00CB\u0112\u0114\u0116\u0118\u011A\u0415\u042D]/g, 'E');
        str = str.replace(/[\u0424]/g, 'F');
		str = str.replace(/[\u011C\u011E\u0120\u0122\u0413\u0490]/g, 'G');
		str = str.replace(/[\u0124\u0126]/g, 'H');
		str = str.replace(/[\u0128\u012A\u012C\u012E\u0130\u0418\u0406]/g, 'I');
		str = str.replace(/[\u0134\u0419]/g, 'J');
		str = str.replace(/[\u0136\u041A]/g, 'K');
		str = str.replace(/[\u0139\u013B\u013D\u0139\u0141\u041B]/g, 'L');
        str = str.replace(/[\u041C]/g, 'M');
		str = str.replace(/[\u00D1\u0143\u0145\u0147\u014A\u041D]/g, 'N');
		str = str.replace(/[\u00D3\u014C\u014E\u0150\u041E]/g, 'O');
        str = str.replace(/[\u041F]/g, 'P');
		str = str.replace(/[\u0154\u0156\u0158\u0420]/g, 'R');
		str = str.replace(/[\u015A\u015C\u015E\u0160\u0421]/g, 'S');
		str = str.replace(/[\u0162\u0164\u0166\u0422]/g, 'T');
		str = str.replace(/[\u00D9\u00DA\u00DB\u00DC\u0168\u016A\u016C\u016E\u0170\u0172\u0423]/g, 'U');
        str = str.replace(/[\u0412]/g, 'V');
		str = str.replace(/[\u0174]/g, 'W');
		str = str.replace(/[\u0176\u042B]/g, 'Y');
		str = str.replace(/[\u0179\u017B\u017D\u0417]/g, 'Z');
		str = str.replace(/[\u00C6]/g, 'AE');
        str = str.replace(/[\u0427]/g, 'CH');
        str = str.replace(/[\u0425]/g, 'KH');
		str = str.replace(/[\u0152]/g, 'OE');
        str = str.replace(/[\u0428]/g, 'SH');
        str = str.replace(/[\u0429]/g, 'SHH');
        str = str.replace(/[\u042F]/g, 'YA');
        str = str.replace(/[\u0404]/g, 'YE');
        str = str.replace(/[\u0407]/g, 'YI');
        str = str.replace(/[\u0401]/g, 'YO');
        str = str.replace(/[\u042E]/g, 'YU');
        str = str.replace(/[\u0416]/g, 'ZH');

		str = str.replace(/[^a-zA-Z0-9\s\'\:\/\[\]-]/g,'');
		
		str = str.replace(/[\u0028\u0029\u0021\u003F\u002E\u0026\u005E\u007E\u002B\u002A\u002F\u003A\u003B\u003C\u003D\u003E]/g, '');
		str = str.replace(/[\s\'\:\/\[\]-]+/g, ' ');

		// Add special char not used for url rewrite
		str = str.replace(/[ ]/g, '-');
		str = str.replace(/[\/\\"'|,;%]*/g, '');

		return str;
	} 
	
	function getAccentedLinkRewriteFromString(str)
	{
		str = str.toUpperCase();
		str = str.toLowerCase();
		str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]\\u00A1-\\uFFFF/g,'');
		str = str.replace(/[\u0028\u0029\u0021\u003F\u002E\u0026\u005E\u007E\u002B\u002A\u002F\u003A\u003B\u003C\u003D\u003E]/g, '');
		str = str.replace(/[\s\'\:\/\[\]-]+/g, ' ');

		// Add special char not used for url rewrite
		str = str.replace(/[ ]/g, '-');
		str = str.replace(/[\/\\"'|,;%]*/g, '');

		return str;
	} 
	
	function getAccentedLinkRewriteFromStringLight(str)
	{
		str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]\\u00A1-\\uFFFF/g,'');
		str = str.replace(/[\u0028\u0029\u0021\u003F\u002E\u0026\u005E\u007E\u002B\u002A\u002F\u003A\u003B\u003C\u003D\u003E]/g, '');
		str = str.replace(/[\s\'\:\/\[\]-]+/g, ' ');

		// Add special char not used for url rewrite
		str = str.replace(/[ ]/g, '-');
		str = str.replace(/[\/\\"'|,;%]*/g, '');

		return str;
		return str;
	}

	function sanitizeString(str)
	{
		str = str.replace(/[^\w\s]/gi, '')
		return str;
	}

	function replaceAccentCharacters(str)
	{
		var defaultDiacriticsRemovalMap = [
			{'base':'A', 'letters':/[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g},
			{'base':'AA','letters':/[\uA732]/g},
			{'base':'AE','letters':/[\u00C6\u01FC\u01E2]/g},
			{'base':'AO','letters':/[\uA734]/g},
			{'base':'AU','letters':/[\uA736]/g},
			{'base':'AV','letters':/[\uA738\uA73A]/g},
			{'base':'AY','letters':/[\uA73C]/g},
			{'base':'B', 'letters':/[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g},
			{'base':'C', 'letters':/[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g},
			{'base':'D', 'letters':/[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g},
			{'base':'DZ','letters':/[\u01F1\u01C4]/g},
			{'base':'Dz','letters':/[\u01F2\u01C5]/g},
			{'base':'E', 'letters':/[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g},
			{'base':'F', 'letters':/[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g},
			{'base':'G', 'letters':/[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g},
			{'base':'H', 'letters':/[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g},
			{'base':'I', 'letters':/[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g},
			{'base':'J', 'letters':/[\u004A\u24BF\uFF2A\u0134\u0248]/g},
			{'base':'K', 'letters':/[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g},
			{'base':'L', 'letters':/[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g},
			{'base':'LJ','letters':/[\u01C7]/g},
			{'base':'Lj','letters':/[\u01C8]/g},
			{'base':'M', 'letters':/[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g},
			{'base':'N', 'letters':/[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g},
			{'base':'NJ','letters':/[\u01CA]/g},
			{'base':'Nj','letters':/[\u01CB]/g},
			{'base':'O', 'letters':/[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g},
			{'base':'OI','letters':/[\u01A2]/g},
			{'base':'OO','letters':/[\uA74E]/g},
			{'base':'OU','letters':/[\u0222]/g},
			{'base':'P', 'letters':/[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g},
			{'base':'Q', 'letters':/[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g},
			{'base':'R', 'letters':/[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g},
			{'base':'S', 'letters':/[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g},
			{'base':'T', 'letters':/[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g},
			{'base':'TZ','letters':/[\uA728]/g},
			{'base':'U', 'letters':/[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g},
			{'base':'V', 'letters':/[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g},
			{'base':'VY','letters':/[\uA760]/g},
			{'base':'W', 'letters':/[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g},
			{'base':'X', 'letters':/[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g},
			{'base':'Y', 'letters':/[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g},
			{'base':'Z', 'letters':/[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g},
			{'base':'a', 'letters':/[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g},
			{'base':'aa','letters':/[\uA733]/g},
			{'base':'ae','letters':/[\u00E6\u01FD\u01E3]/g},
			{'base':'ao','letters':/[\uA735]/g},
			{'base':'au','letters':/[\uA737]/g},
			{'base':'av','letters':/[\uA739\uA73B]/g},
			{'base':'ay','letters':/[\uA73D]/g},
			{'base':'b', 'letters':/[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g},
			{'base':'c', 'letters':/[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g},
			{'base':'d', 'letters':/[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g},
			{'base':'dz','letters':/[\u01F3\u01C6]/g},
			{'base':'e', 'letters':/[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g},
			{'base':'f', 'letters':/[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g},
			{'base':'g', 'letters':/[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g},
			{'base':'h', 'letters':/[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g},
			{'base':'hv','letters':/[\u0195]/g},
			{'base':'i', 'letters':/[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g},
			{'base':'j', 'letters':/[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g},
			{'base':'k', 'letters':/[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g},
			{'base':'l', 'letters':/[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g},
			{'base':'lj','letters':/[\u01C9]/g},
			{'base':'m', 'letters':/[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g},
			{'base':'n', 'letters':/[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g},
			{'base':'nj','letters':/[\u01CC]/g},
			{'base':'o', 'letters':/[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g},
			{'base':'oi','letters':/[\u01A3]/g},
			{'base':'ou','letters':/[\u0223]/g},
			{'base':'oo','letters':/[\uA74F]/g},
			{'base':'p','letters':/[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g},
			{'base':'q','letters':/[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g},
			{'base':'r','letters':/[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g},
			{'base':'s','letters':/[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g},
			{'base':'t','letters':/[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g},
			{'base':'tz','letters':/[\uA729]/g},
			{'base':'u','letters':/[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g},
			{'base':'v','letters':/[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g},
			{'base':'vy','letters':/[\uA761]/g},
			{'base':'w','letters':/[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g},
			{'base':'x','letters':/[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g},
			{'base':'y','letters':/[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g},
			{'base':'z','letters':/[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g}
		];

		for(var i=0; i<defaultDiacriticsRemovalMap.length; i++) {
			str = str.replace(defaultDiacriticsRemovalMap[i].letters, defaultDiacriticsRemovalMap[i].base);
		}

		return str;

	}
	

	function isJSON(str) {
		if (str=='' || typeof str!='string') return false;
		if(str.charAt(0)!="{") return false;
		str = str.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@');
		str = str.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']');
		str = str.replace(/(?:^|:|,)(?:\s*\[)+/g, '');
		return (/^[\],:{}\s]*$/).test(str);
	}

	function pausecomp(millis)
	{
		var date = new Date();
		var curDate = null;

		do { curDate = new Date(); }
		while(curDate-date < millis);
	}
	
	function latinise(string)
	{
		var latin_map={"Ã�":"A","Ä‚":"A","áº®":"A","áº¶":"A","áº°":"A","áº²":"A","áº´":"A","Ç�":"A","Ã‚":"A","áº¤":"A","áº¬":"A","áº¦":"A","áº¨":"A","áºª":"A","Ã„":"A","Çž":"A","È¦":"A","Ç ":"A","áº ":"A","È€":"A","Ã€":"A","áº¢":"A","È‚":"A","Ä€":"A","Ä„":"A","Ã…":"A","Çº":"A","á¸€":"A","Èº":"A","Ãƒ":"A","êœ²":"AA","Ã†":"AE","Ç¼":"AE","Ç¢":"AE","êœ´":"AO","êœ¶":"AU","êœ¸":"AV","êœº":"AV","êœ¼":"AY","á¸‚":"B","á¸„":"B","Æ�":"B","á¸†":"B","Éƒ":"B","Æ‚":"B","Ä†":"C","ÄŒ":"C","Ã‡":"C","á¸ˆ":"C","Äˆ":"C","ÄŠ":"C","Æ‡":"C","È»":"C","ÄŽ":"D","á¸�":"D","á¸’":"D","á¸Š":"D","á¸Œ":"D","ÆŠ":"D","á¸Ž":"D","Ç²":"D","Ç…":"D","Ä�":"D","Æ‹":"D","Ç±":"DZ","Ç„":"DZ","Ã‰":"E","Ä”":"E","Äš":"E","È¨":"E","á¸œ":"E","ÃŠ":"E","áº¾":"E","á»†":"E","á»€":"E","á»‚":"E","á»„":"E","á¸˜":"E","Ã‹":"E","Ä–":"E","áº¸":"E","È„":"E","Ãˆ":"E","áºº":"E","È†":"E","Ä’":"E","á¸–":"E","á¸”":"E","Ä˜":"E","É†":"E","áº¼":"E","á¸š":"E","ê�ª":"ET","á¸ž":"F","Æ‘":"F","Ç´":"G","Äž":"G","Ç¦":"G","Ä¢":"G","Äœ":"G","Ä ":"G","Æ“":"G","á¸ ":"G","Ç¤":"G","á¸ª":"H","Èž":"H","á¸¨":"H","Ä¤":"H","â±§":"H","á¸¦":"H","á¸¢":"H","á¸¤":"H","Ä¦":"H","Ã�":"I","Ä¬":"I","Ç�":"I","ÃŽ":"I","Ã�":"I","á¸®":"I","Ä°":"I","á»Š":"I","Èˆ":"I","ÃŒ":"I","á»ˆ":"I","ÈŠ":"I","Äª":"I","Ä®":"I","Æ—":"I","Ä¨":"I","á¸¬":"I","ê�¹":"D","ê�»":"F","ê�½":"G","êž‚":"R","êž„":"S","êž†":"T","ê�¬":"IS","Ä´":"J","Éˆ":"J","á¸°":"K","Ç¨":"K","Ä¶":"K","â±©":"K","ê�‚":"K","á¸²":"K","Æ˜":"K","á¸´":"K","ê�€":"K","ê�„":"K","Ä¹":"L","È½":"L","Ä½":"L","Ä»":"L","á¸¼":"L","á¸¶":"L","á¸¸":"L","â± ":"L","ê�ˆ":"L","á¸º":"L","Ä¿":"L","â±¢":"L","Çˆ":"L","Å�":"L","Ç‡":"LJ","á¸¾":"M","á¹€":"M","á¹‚":"M","â±®":"M","Åƒ":"N","Å‡":"N","Å…":"N","á¹Š":"N","á¹„":"N","á¹†":"N","Ç¸":"N","Æ�":"N","á¹ˆ":"N","È ":"N","Ç‹":"N","Ã‘":"N","ÇŠ":"NJ","Ã“":"O","ÅŽ":"O","Ç‘":"O","Ã”":"O","á»�":"O","á»˜":"O","á»’":"O","á»”":"O","á»–":"O","Ã–":"O","Èª":"O","È®":"O","È°":"O","á»Œ":"O","Å�":"O","ÈŒ":"O","Ã’":"O","á»Ž":"O","Æ ":"O","á»š":"O","á»¢":"O","á»œ":"O","á»ž":"O","á» ":"O","ÈŽ":"O","ê�Š":"O","ê�Œ":"O","ÅŒ":"O","á¹’":"O","á¹�":"O","ÆŸ":"O","Çª":"O","Ç¬":"O","Ã˜":"O","Ç¾":"O","Ã•":"O","á¹Œ":"O","á¹Ž":"O","È¬":"O","Æ¢":"OI","ê�Ž":"OO","Æ�":"E","Æ†":"O","È¢":"OU","á¹”":"P","á¹–":"P","ê�’":"P","Æ¤":"P","ê�”":"P","â±£":"P","ê��":"P","ê�˜":"Q","ê�–":"Q","Å”":"R","Å˜":"R","Å–":"R","á¹˜":"R","á¹š":"R","á¹œ":"R","È�":"R","È’":"R","á¹ž":"R","ÉŒ":"R","â±¤":"R","êœ¾":"C","ÆŽ":"E","Åš":"S","á¹¤":"S","Å ":"S","á¹¦":"S","Åž":"S","Åœ":"S","È˜":"S","á¹ ":"S","á¹¢":"S","á¹¨":"S","Å¤":"T","Å¢":"T","á¹°":"T","Èš":"T","È¾":"T","á¹ª":"T","á¹¬":"T","Æ¬":"T","á¹®":"T","Æ®":"T","Å¦":"T","â±¯":"A","êž€":"L","Æœ":"M","É…":"V","êœ¨":"TZ","Ãš":"U","Å¬":"U","Ç“":"U","Ã›":"U","á¹¶":"U","Ãœ":"U","Ç—":"U","Ç™":"U","Ç›":"U","Ç•":"U","á¹²":"U","á»¤":"U","Å°":"U","È”":"U","Ã™":"U","á»¦":"U","Æ¯":"U","á»¨":"U","á»°":"U","á»ª":"U","á»¬":"U","á»®":"U","È–":"U","Åª":"U","á¹º":"U","Å²":"U","Å®":"U","Å¨":"U","á¹¸":"U","á¹´":"U","ê�ž":"V","á¹¾":"V","Æ²":"V","á¹¼":"V","ê� ":"VY","áº‚":"W","Å´":"W","áº„":"W","áº†":"W","áºˆ":"W","áº€":"W","â±²":"W","áºŒ":"X","áºŠ":"X","Ã�":"Y","Å¶":"Y","Å¸":"Y","áºŽ":"Y","á»´":"Y","á»²":"Y","Æ³":"Y","á»¶":"Y","á»¾":"Y","È²":"Y","ÉŽ":"Y","á»¸":"Y","Å¹":"Z","Å½":"Z","áº�":"Z","â±«":"Z","Å»":"Z","áº’":"Z","È¤":"Z","áº”":"Z","Æµ":"Z","Ä²":"IJ","Å’":"OE","á´€":"A","á´�":"AE","Ê™":"B","á´ƒ":"B","á´„":"C","á´…":"D","á´‡":"E","êœ°":"F","É¢":"G","Ê›":"G","Êœ":"H","Éª":"I","Ê�":"R","á´Š":"J","á´‹":"K","ÊŸ":"L","á´Œ":"L","á´�":"M","É´":"N","á´�":"O","É¶":"OE","á´�":"O","á´•":"OU","á´˜":"P","Ê€":"R","á´Ž":"N","á´™":"R","êœ±":"S","á´›":"T","â±»":"E","á´š":"R","á´œ":"U","á´ ":"V","á´¡":"W","Ê�":"Y","á´¢":"Z","Ã¡":"a","Äƒ":"a","áº¯":"a","áº·":"a","áº±":"a","áº³":"a","áºµ":"a","ÇŽ":"a","Ã¢":"a","áº¥":"a","áº­":"a","áº§":"a","áº©":"a","áº«":"a","Ã¤":"a","ÇŸ":"a","È§":"a","Ç¡":"a","áº¡":"a","È�":"a","Ã ":"a","áº£":"a","Èƒ":"a","Ä�":"a","Ä…":"a","á¶�":"a","áºš":"a","Ã¥":"a","Ç»":"a","á¸�":"a","â±¥":"a","Ã£":"a","êœ³":"aa","Ã¦":"ae","Ç½":"ae","Ç£":"ae","êœµ":"ao","êœ·":"au","êœ¹":"av","êœ»":"av","êœ½":"ay","á¸ƒ":"b","á¸…":"b","É“":"b","á¸‡":"b","áµ¬":"b","á¶€":"b","Æ€":"b","Æƒ":"b","Éµ":"o","Ä‡":"c","Ä�":"c","Ã§":"c","á¸‰":"c","Ä‰":"c","É•":"c","Ä‹":"c","Æˆ":"c","È¼":"c","Ä�":"d","á¸‘":"d","á¸“":"d","È¡":"d","á¸‹":"d","á¸�":"d","É—":"d","á¶‘":"d","á¸�":"d","áµ­":"d","á¶�":"d","Ä‘":"d","É–":"d","ÆŒ":"d","Ä±":"i","È·":"j","ÉŸ":"j","Ê„":"j","Ç³":"dz","Ç†":"dz","Ã©":"e","Ä•":"e","Ä›":"e","È©":"e","á¸�":"e","Ãª":"e","áº¿":"e","á»‡":"e","á»�":"e","á»ƒ":"e","á»…":"e","á¸™":"e","Ã«":"e","Ä—":"e","áº¹":"e","È…":"e","Ã¨":"e","áº»":"e","È‡":"e","Ä“":"e","á¸—":"e","á¸•":"e","â±¸":"e","Ä™":"e","á¶’":"e","É‡":"e","áº½":"e","á¸›":"e","ê�«":"et","á¸Ÿ":"f","Æ’":"f","áµ®":"f","á¶‚":"f","Çµ":"g","ÄŸ":"g","Ç§":"g","Ä£":"g","Ä�":"g","Ä¡":"g","É ":"g","á¸¡":"g","á¶ƒ":"g","Ç¥":"g","á¸«":"h","ÈŸ":"h","á¸©":"h","Ä¥":"h","â±¨":"h","á¸§":"h","á¸£":"h","á¸¥":"h","É¦":"h","áº–":"h","Ä§":"h","Æ•":"hv","Ã­":"i","Ä­":"i","Ç�":"i","Ã®":"i","Ã¯":"i","á¸¯":"i","á»‹":"i","È‰":"i","Ã¬":"i","á»‰":"i","È‹":"i","Ä«":"i","Ä¯":"i","á¶–":"i","É¨":"i","Ä©":"i","á¸­":"i","ê�º":"d","ê�¼":"f","áµ¹":"g","êžƒ":"r","êž…":"s","êž‡":"t","ê�­":"is","Ç°":"j","Äµ":"j","Ê�":"j","É‰":"j","á¸±":"k","Ç©":"k","Ä·":"k","â±ª":"k","ê�ƒ":"k","á¸³":"k","Æ™":"k","á¸µ":"k","á¶„":"k","ê��":"k","ê�…":"k","Äº":"l","Æš":"l","É¬":"l","Ä¾":"l","Ä¼":"l","á¸½":"l","È´":"l","á¸·":"l","á¸¹":"l","â±¡":"l","ê�‰":"l","á¸»":"l","Å€":"l","É«":"l","á¶…":"l","É­":"l","Å‚":"l","Ç‰":"lj","Å¿":"s","áºœ":"s","áº›":"s","áº�":"s","á¸¿":"m","á¹�":"m","á¹ƒ":"m","É±":"m","áµ¯":"m","á¶†":"m","Å„":"n","Åˆ":"n","Å†":"n","á¹‹":"n","Èµ":"n","á¹…":"n","á¹‡":"n","Ç¹":"n","É²":"n","á¹‰":"n","Æž":"n","áµ°":"n","á¶‡":"n","É³":"n","Ã±":"n","ÇŒ":"nj","Ã³":"o","Å�":"o","Ç’":"o","Ã´":"o","á»‘":"o","á»™":"o","á»“":"o","á»•":"o","á»—":"o","Ã¶":"o","È«":"o","È¯":"o","È±":"o","á»�":"o","Å‘":"o","È�":"o","Ã²":"o","á»�":"o","Æ¡":"o","á»›":"o","á»£":"o","á»�":"o","á»Ÿ":"o","á»¡":"o","È�":"o","ê�‹":"o","ê��":"o","â±º":"o","Å�":"o","á¹“":"o","á¹‘":"o","Ç«":"o","Ç­":"o","Ã¸":"o","Ç¿":"o","Ãµ":"o","á¹�":"o","á¹�":"o","È­":"o","Æ£":"oi","ê��":"oo","É›":"e","á¶“":"e","É”":"o","á¶—":"o","È£":"ou","á¹•":"p","á¹—":"p","ê�“":"p","Æ¥":"p","áµ±":"p","á¶ˆ":"p","ê�•":"p","áµ½":"p","ê�‘":"p","ê�™":"q","Ê ":"q","É‹":"q","ê�—":"q","Å•":"r","Å™":"r","Å—":"r","á¹™":"r","á¹›":"r","á¹�":"r","È‘":"r","É¾":"r","áµ³":"r","È“":"r","á¹Ÿ":"r","É¼":"r","áµ²":"r","á¶‰":"r","É�":"r","É½":"r","â†„":"c","êœ¿":"c","É˜":"e","É¿":"r","Å›":"s","á¹¥":"s","Å¡":"s","á¹§":"s","ÅŸ":"s","Å�":"s","È™":"s","á¹¡":"s","á¹£":"s","á¹©":"s","Ê‚":"s","áµ´":"s","á¶Š":"s","È¿":"s","É¡":"g","á´‘":"o","á´“":"o","á´�":"u","Å¥":"t","Å£":"t","á¹±":"t","È›":"t","È¶":"t","áº—":"t","â±¦":"t","á¹«":"t","á¹­":"t","Æ­":"t","á¹¯":"t","áµµ":"t","Æ«":"t","Êˆ":"t","Å§":"t","áµº":"th","É�":"a","á´‚":"ae","Ç�":"e","áµ·":"g","É¥":"h","Ê®":"h","Ê¯":"h","á´‰":"i","Êž":"k","êž�":"l","É¯":"m","É°":"m","á´”":"oe","É¹":"r","É»":"r","Éº":"r","â±¹":"r","Ê‡":"t","ÊŒ":"v","Ê�":"w","ÊŽ":"y","êœ©":"tz","Ãº":"u","Å­":"u","Ç”":"u","Ã»":"u","á¹·":"u","Ã¼":"u","Ç˜":"u","Çš":"u","Çœ":"u","Ç–":"u","á¹³":"u","á»¥":"u","Å±":"u","È•":"u","Ã¹":"u","á»§":"u","Æ°":"u","á»©":"u","á»±":"u","á»«":"u","á»­":"u","á»¯":"u","È—":"u","Å«":"u","á¹»":"u","Å³":"u","á¶™":"u","Å¯":"u","Å©":"u","á¹¹":"u","á¹µ":"u","áµ«":"ue","ê�¸":"um","â±´":"v","ê�Ÿ":"v","á¹¿":"v","Ê‹":"v","á¶Œ":"v","â±±":"v","á¹½":"v","ê�¡":"vy","áºƒ":"w","Åµ":"w","áº…":"w","áº‡":"w","áº‰":"w","áº�":"w","â±³":"w","áº˜":"w","áº�":"x","áº‹":"x","á¶�":"x","Ã½":"y","Å·":"y","Ã¿":"y","áº�":"y","á»µ":"y","á»³":"y","Æ´":"y","á»·":"y","á»¿":"y","È³":"y","áº™":"y","É�":"y","á»¹":"y","Åº":"z","Å¾":"z","áº‘":"z","Ê‘":"z","â±¬":"z","Å¼":"z","áº“":"z","È¥":"z","áº•":"z","áµ¶":"z","á¶Ž":"z","Ê�":"z","Æ¶":"z","É€":"z","ï¬€":"ff","ï¬ƒ":"ffi","ï¬„":"ffl","ï¬�":"fi","ï¬‚":"fl","Ä³":"ij","Å“":"oe","ï¬†":"st","â‚�":"a","â‚‘":"e","áµ¢":"i","â±¼":"j","â‚’":"o","áµ£":"r","áµ¤":"u","áµ¥":"v","â‚“":"x"};
		var final_string = "";
	
		for (var i = 0; i < string.length; i++) {
			var letter = string.charAt(i);
	
			final_string += (latin_map[letter] || letter);
		} 
	
		if(final_string=="")
			final_string = string;
	
		return final_string;
	}
	
	function replaceAll(find, replace, str) {
	  return str.replace(new RegExp(find, 'g'), replace);
	}
	
	
	/*
dtmlXMLLoaderObject.prototype.waitLoadFunction=function(dhtmlObject){
	var once = true;
	this.check=function (){
		if ((dhtmlObject)&&(dhtmlObject.onloadAction != null)){
			if ((!dhtmlObject.xmlDoc.readyState)||(dhtmlObject.xmlDoc.readyState == 4)){
				if (!once)
					return;
				once=false; //IE 5 fix
				if (typeof dhtmlObject.onloadAction == "function"){
					var log="XML Error Loading: "+dhtmlObject.filePath+"\n\nDo you want to open the document?";
					if (confirm(log))
						window.open(dhtmlObject.filePath);
					dhtmlObject.onloadAction(dhtmlObject.mainObject, null, null, null, dhtmlObject);
				}
				if (dhtmlObject.waitCall){
					dhtmlObject.waitCall.call(this,dhtmlObject);
					dhtmlObject.waitCall=null;
				}
			}
		}
	};
	return this.check;
};

    function myErrorHandler(type, desc, erData){
//    	alert('rr');
    }
    dhtmlxError.catchError("LoadXML",myErrorHandler);

	*/
/*
	if (typeof dhtmlXGridObject!='undefined')
		dhtmlXGridObject.prototype._getCookie=function(a,b){
			var tmp = this.getCookie(a);
			if(tmp!="" && tmp!=null)
			{
				tmp = tmp.replace(new RegExp('%7C', 'g'), '|');
				tmp = tmp.replace(new RegExp('%2C', 'g'), ',');
			}
			return(tmp||"||||").split("|")[b]
		};*/
	
	var timeOutModifGrid = null;
	var flagLoadingSettings = false;
	
	function initGridUISettings(gridObj)
	{
		gridObj.enableColumnMove(true);
		gridObj.attachEvent("onResizeEnd",function(){
			if (!flagLoadingSettings)
			{
				clearTimeout(timeOutModifGrid);
				timeOutModifGrid=setTimeout(function(){
					saveGridUISettings(gridObj);
				},1000);
			}
	   	return true;
		});
		gridObj.attachEvent("onAfterSorting",function(){
			if (!flagLoadingSettings)
			{
				clearTimeout(timeOutModifGrid);
				timeOutModifGrid=setTimeout(function(){
					saveGridUISettings(gridObj);
				},1000);
			}
	   	return true;
		});
		gridObj.attachEvent("onAfterCMove",function(){
			if (!flagLoadingSettings)
			{
				clearTimeout(timeOutModifGrid);
				timeOutModifGrid=setTimeout(function(){
					saveGridUISettings(gridObj);
				},1000);
			}
	   	return true;
		});
		gridObj.attachEvent("onColumnHidden",function(){
			if (!flagLoadingSettings)
			{
				clearTimeout(timeOutModifGrid);
				timeOutModifGrid=setTimeout(function(){
					saveGridUISettings(gridObj);
				},1000);
			}
	   	return true;
		});
	}
	
	function saveGridUISettings(gridObj)
	{
		if (gridObj._first_loading==0 && gridObj.getColumnsNum() != 'undefined' && !flagLoadingSettings)
		{		
			var grid_name = gridObj._uisettings_name;
			var cols="";
			var hiddenData="";
			var orderArray=[];
			var orderData="";
			var sizeData="";
			var sortData="";
			for(var i=0 ; i < gridObj.getColumnsNum() ; i++)
			{
				var col_id = gridObj.getColumnId(i);
				if (col_id)
				{
					hiddenData+=col_id+":"+Number(gridObj.isColumnHidden(i))+(i < gridObj.getColumnsNum()-1 ? '-':'');
					orderArray[i]=col_id;
					sizeData+=col_id+":"+gridObj.getColWidth(i)+(i < gridObj.getColumnsNum()-1 ? '-':'');
				}else{
					return false;
				}
			}
			sortData+=gridObj.getSortingState().join(',');
			for(var i=0 ; i < orderArray.length-1 ; i++)
        orderData+=orderArray[i]+":"+i+"-";
			allData=hiddenData+"|"+orderData+"|"+sizeData+"|"+sortData;
			
			ui_settings[grid_name] = allData;
//console.log("SAVE: "+allData);

			$.post("index.php?ajax=1&act=all_uisettings_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(), {"name":grid_name,"data":allData},function(data){});
	 	}
	}

	function loadGridUISettings(gridObj)
	{
		gridObj.enableHeaderMenu();
		flagLoadingSettings=true;
		var grid_name = gridObj._uisettings_name;
		var settingsToLoad="";
		var defaultValue=gridObj.getUserData("", "uisettings");
		var value=ui_settings[grid_name];
		if (typeof defaultValue !='undefined' && defaultValue!="" && defaultValue!=null){
			settingsToLoad=defaultValue;
			ui_settings[grid_name]=defaultValue;
		}/*else if (typeof value !='undefined')
		{
			settingsToLoad=value;
		}*/
		if (settingsToLoad!="" && settingsToLoad!=null)
		{
//console.log("LOAD: "+ui_settings[grid_name]);
				uidata=settingsToLoad.split('|');
		      if (uidata.length==4)
		      {
	    		    var hiddenData=uidata[0];
					var orderData=uidata[1];
					var sizeData=uidata[2];
					var sortData=uidata[3];

					// ORDER
					data=orderData.split('-');
					if (data.length==gridObj.getColumnsNum())
						for(var i=0 ; i < gridObj.getColumnsNum() ; i++)
						{
							cdata=data[i].split(':');
							if (cdata[0]!='')
								gridObj.moveColumn(gridObj.getColIndexById(cdata[0]),cdata[1]);
						}
					// HIDDEN
					data=hiddenData.split('-');
					if (data.length==gridObj.getColumnsNum())
						for(var i=0 ; i < gridObj.getColumnsNum() ; i++)
						{
							cdata=data[i].split(':');
							if (cdata[0]!='')
								gridObj.setColumnHidden(gridObj.getColIndexById(cdata[0]),cdata[1]);
						}
					// SIZE
					data=sizeData.split('-');
					if (data.length==gridObj.getColumnsNum())
						for(var i=0 ; i < gridObj.getColumnsNum() ; i++)
						{
							cdata=data[i].split(':');
							if (cdata[0]!='')
							{
								gridObj.setColWidth(gridObj.getColIndexById(cdata[0]),cdata[1]);
							}
						}
					// SORT
					if (sortData!=""){
						cdata=sortData.split(':');
						if(cdata[0]!=undefined && cdata[0]!="" && cdata[0]!=null)
						{
							data=cdata[0].split(',');
							gridObj.sortRows(data[0],null,data[1]);
							gridObj.setSortImgState(true,data[0],data[1]);
						}
					}
		      }
	 		}
	 	flagLoadingSettings=false;
	}

	var timeOutModifParam = null;
	
	function saveParamUISettings(name, value)
	{
		if(name!=undefined && name!=null && name!=0 && name!="" && value!=undefined)
		{
			ui_settings[name] = value;
			
			clearTimeout(timeOutModifParam);
			timeOutModifParam=setTimeout(function(){
				_saveParamUISettings(name, value);
			},1000);
		}
	}
	
	function _saveParamUISettings(name, value)
	{
		if(name!=undefined && name!=null && name!=0 && name!="" && value!=undefined)
		{
			$.post("index.php?ajax=1&act=all_uisettings_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(), {"name":name,"data":value},function(data){});
		}
	}
	
	function getParamUISettings(name)
	{
		if(name!=undefined && name!=null && name!=0 && name!="")
		{
			if(ui_settings[name]!=undefined)
				return ui_settings[name];
			else
				return null;
		}
		return null;
	}

	// get unique
	Array.prototype.getUnique = function(){
  	var u = {}, a = [];
  	for(var i = 0, l = this.length; i < l; ++i){
     	if(u.hasOwnProperty(this[i])) {
        	continue;
     	}
     	a.push(this[i]);
	     u[this[i]] = 1;
  	}
  	return a;
	}
	
	// UPDATE QUEUE
	var updateQueue = new Array();
	var updatingQueue = false;
	var updateQueueTimer=null;
	
	function playUpdateQueue()
	{
		if(updatingQueue!=true)
		{
			if($.isArray(updateQueue) && updateQueue.length>0)
			{
				updatingQueue = true;
				var limit = updateQueueLimit;

				// Récupération de la première tâche
				var first_action = updateQueue[0];
				var actions = new Array();
				actions[0] = first_action;
				var name = first_action["name"];

				// Récupération des tâches correspondantes au même fichier
				if(updateQueue.length>1)
				{
					var listIndexToDelete = new Array();
					$.each(updateQueue, function (ind, params){
						if(ind>0)
						{
							if(name==params["name"])
							{
								if(actions.length<limit)
								{
									actions[actions.length] = params;
									//updateQueue.splice(ind,1); 
									listIndexToDelete[listIndexToDelete.length] = ind;
									
									if(actions.length==limit)
										return false;
								}
							}
						}
						else
							listIndexToDelete[listIndexToDelete.length] = ind;
							//updateQueue.splice(ind,1); 
					});
					
					var nbToDelete = listIndexToDelete.length;
					if(nbToDelete>0)
					{
						for(var ind=(nbToDelete-1);ind>=0;ind--)
						{
							updateQueue.splice(ind,1); 
						}
					}
				}
				else
					updateQueue.splice(0,1); 

				// Requête Ajax vers le fichier concerné
				if(actions.length>0)
				{
					setLayoutStatusText((updateQueue.length*1+actions.length*1));
					
					$.post('index.php?ajax=1&act='+name+'&id_lang='+SC_ID_LANG,{'rows':JSON.stringify(actions)},function(data){
						updatingQueue = false;
						
						var doEval = false;
						if(data!=undefined && data!=null && data!="" && data!=0)
						{
							if(isJSON(data))
								doEval = true;
							else
							{
								var data_tmp = $.trim(data);
								if(isJSON(data_tmp))
								{
									doEval = true;
									data = data_tmp;
								}
							}
						}
						if(doEval==true)
						{
							data = JSON.parse(data);
							if(data.callback!=undefined && data.callback!=null && data.callback!="" && data.callback!=0)
								eval(data.callback);
						}
						else
						{
							if(data.search("{")>=0)
							{
								var exp = data.split("{");
								data = exp[0];
							}
							dhtmlx.message({text:lang_queueerror_1+"<br/>"+data+"<br/><br/><strong>"+lang_queueerror_2+"</strong><br/><center onclick=\"openQueueLogWindow()\" style=\"text-decoration: underline;\">"+lang_queueerror_3+"</center>",type:'error',expire:-1});
						}
						setLayoutStatusText(updateQueue.length);
						playUpdateQueue();
					});
				}
			}
			else
			{
				setLayoutStatusText();
			}
		}
	}
	
	$(document).ready(function(){
		setInterval(function(){playUpdateQueue()}, 1000);
	});
	
	$(window).bind('beforeunload', function(e) 
	{
		if($.isArray(updateQueue) && updateQueue.length>0)
		{
	        return lang_confirmclose;
		}
	});
	
	/*
	 * params (object) :
	 * (string) name,
	 * (string) row,
	 * (string) action,
	 * (object) params (ex: {nvalue:"val","ovalue":"val",field:"val"}),
	 * (string) callback
	 */
	function addInUpdateQueue(params,grid)
	{
		if(params!=undefined && params!=null && params!="" && params!=0)
		{
			if((params["name"]!=undefined && params["name"]!=null && params["name"]!="" && params["name"]!=0)
				&&
				/*(params["row"]!=undefined && params["row"]!=null && params["row"]!="" && params["row"]!=0)
				&&*/
				(params["action"]!=undefined && params["action"]!=null && params["action"]!="" && params["action"]!=0)
			)
			{
				//updateQueue.push(params);
				var position = updateQueue.length;
				updateQueue[ position ] = params;
				
				setLayoutStatusText(updateQueue.length);
				
				if(grid!=undefined && grid!=null && grid!="" && grid!=0)
					if(params["row"]!=undefined && params["row"]!=null && params["row"]!="" && params["row"]!=0)
						grid.setRowTextBold(params["row"]);
			}
		}
	}
	
	function sendInsert(params,layout)
	{
		if(params!=undefined && params!=null && params!="" && params!=0)
		{
			if((params["name"]!=undefined && params["name"]!=null && params["name"]!="" && params["name"]!=0)
				&&
				(params["row"]!=undefined && params["row"]!=null && params["row"]!="" && params["row"]!=0)
				&&
				(params["action"]!=undefined && params["action"]!=null && params["action"]!="" && params["action"]!=0)
			)
			{
				if(layout!=undefined && layout!=null && layout!="" && layout!=0)
					layout.progressOn();
				
				$.post('index.php?ajax=1&act='+params["name"]+'&action='+params["action"]+'&gr_id='+params["row"]+'&id_lang='+SC_ID_LANG,params["params"],function(data){
					
					var doEval = false;
					if(data!=undefined && data!=null && data!="" && data!=0)
					{
						if(isJSON(data))
							doEval = true;
						else
						{
							var data_tmp = $.trim(data);
							if(isJSON(data_tmp))
							{
								doEval = true;
								data = data_tmp;
							}
						}
					}
					if(doEval==true)
					{
						data = JSON.parse(data);
						if(data.callback!=undefined && data.callback!=null && data.callback!="" && data.callback!=0)
							eval(data.callback);
					}
					else
					{
						if(data.search("{")>=0)
						{
							var exp = data.split("{");
							data = exp[0];
						}
						dhtmlx.message({text:lang_queueerror_4,type:'error',expire:-1});
					}

				});
			}
		}
	}
	
	function setLayoutStatusText(nbTotal)
	{
		if(nbTotal!=undefined && nbTotal!=null && nbTotal!="" && !isNaN(nbTotal) && nbTotal>0)
		{
			$('#layoutstatusqueue').css('display','block');
			$('#layoutstatusqueue span').html(lang_queuetasks+' '+nbTotal);
		}
		else
		{
			$('#layoutstatusqueue').css('display','none');
		}
	}
	
	function openQueueLogWindow()
	{
		if (!dhxWins.isWindow("wAllQueueLogs"))
		{
			wAllQueueLogs = dhxWins.createWindow("wAllQueueLogs", 50, 50, 940, $(window).height()-75);
			wAllQueueLogs.setIcon('lib/img/time.png','../../../lib/img/time.png');
			wAllQueueLogs.setText(lang_queuetaskswindow); //  and cancel modifications
			$.get("index.php?ajax=1&act=all_queuelogs_init",function(data){
					$('#jsExecute').html(data);
				});
		}
	}
