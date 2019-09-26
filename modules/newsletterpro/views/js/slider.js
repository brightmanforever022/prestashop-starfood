/**
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
* @license   Do not edit, modify or copy this file
*/

function gkSlider(cfg)
{
	if (!(this instanceof gkSlider))
		return new gkSlider(cfg);

	var self           = this,
		snap           = null,
		min            = null,
		max            = null,
		step           = null,
		position       = null,
		value          = null,
		drabable       = false,
		target         = null,
		move           = null,
		start          = null,
		done           = null,
		values         = [],
		remplaceValues = {},
		mouseSpeed     = 0,
		lastMouseX     = -1,
		prefix         = '',
		currentValue   = 0,
		dom = {};

	init();

	// global 
	self.dom    = dom;

	self.getValue = function () 
	{
		return getValue();
	};

	self.setValue = function (value) 
	{
		setValue(value);
	};

	self.refresh = function() 
	{
		return refresh();
	};

	self.show = function()
	{
		return show();
	};

	self.hide = function()
	{
		return hide();
	};

	// private 
	function init () 
	{
		initDom();
		initConfig();
		initCss();
		initEvents();
		refresh();
	}

	function initConfig()
	{
		snap           = cfg.snap || false;
		min            = parseInt(cfg.min)   || 0;
		max            = cfg.max || parseInt(dom.sliderBar.width() );
		step           = parseInt(cfg.step)  || 1;
		value          = cfg.value || 0;
		prefix         = cfg.prefix || '';
		values         = cfg.values || [];
		remplaceValues = cfg.remplaceValues || {};
		move           = cfg.move;
		start          = cfg.start;
		done           = cfg.done;
	}

	function initDom()
	{
		addDom('target', cfg.target);
		addDom('instance', getTemplate(cfg));
		addDom('sliderController', [dom.instance, '.slider-controller']);
		addDom('sliderBar', [dom.instance, '.slider-bar']);
		addDom('rulesBar', [dom.instance, '.rules-bar']);
		addDom('sliderInfo', getInfoTemplate());
		addDom('sliderInfoInput', [dom.sliderInfo, '.slider-value']);

		dom.sliderBar.prepend(dom.sliderInfo);
		dom.target.empty();
		dom.target.append(dom.instance);
	}

	function initCss()
	{
		dom.rulesBar.css( 'width', rulesBarWidth());
		dom.sliderInfo.css({'position' : 'absolute'});
		dom.instance.css({'position': 'relative', });
		dom.sliderController.css({'position': 'absolute'});
		dom.sliderBar.css({'position': 'absolute'});
		dom.rulesBar.css({'position': 'absolute'});
	}

	function initEvents()
	{
		dom.sliderController.on('mousedown', function(event) {
			startDrag();
			return false;
		});

		dom.sliderController.on('mouseup', function(event) {
			stopDrag();
			return false;
		});

		onMouseUp(function(event){
			if ( drabable == true )
				stopDrag();
			return false;
		});
	}

	function initRules()
	{
		dom.rulesBar.empty();	
		addRulesQuick(values);
	}

	function refreshRules()
	{
		initRules();
	}

	function addDom(name, data)
	{
		if (Object.prototype.toString.call( data ) === '[object Array]')
			dom[name] = data[0].find(data[1]);
		else
			dom[name] = data;
	}

	function rulesBarWidth()
	{
		return ((sliderWidth() / sliderParentWidth()) * 100) + '%';
	}

	function sliderParentWidth()
	{
		return dom.sliderBar.parent().width();
	}

	function getInfoTemplate()
	{
		var template  = '<span class="slider-value-container" style="left:'+(getValueToPosition(value))+'">';
			template += '<span class="slider-value">'+(getInfo())+'</span>';
			template += '</span>';

		return $(template);
	}

	function getTemplate(settings) 
	{
		var id = cfg.id || '',
			className = (typeof cfg.className === 'undefined') ? '' : (typeof cfg.className === 'array' ? cfg.className.join(' ') : cfg.className),
			slider;

		slider  = '<div id="'+id+'" class="slider style-dark '+className+'">';
		slider += '<div class="slider-bar">';
		slider += '<a href="javascript:{}" class="slider-controller">&nbsp;</a>';
		slider += '</div>';
		slider += '<div class="rules-bar">&nbsp;</div>';
		slider += '</div>';

		slider = $(slider);

		slider.css({
			'margin-top': '28px',
			'margin-bottom': '34px',
		});

		return slider;
	}

	function getPercent() 
	{
		var percent = 0;
		if ( position != 0 )
			percent = (( position / sliderBar.width()) ) * 100  ;
		return percent;
	}

	function setPosition(poz) 
	{
		position = poz;
		setLimits();

		value = getPositionToValue();
		setElementsPosition();

		if ( snap && mouseSpeed < 5) 
		{

			$.each(values, function(index, s) {
				if( getValue() > (s - snap) && getValue() < (s + snap) ) 
				{
					snapAtValue(s);
				}
			});
		}
	}

	function snapAtValue(val)
	{
		value = val;
		position = getValueToPosition();
		setElementsPosition();
	}

	function setLimits()
	{
		if ( position < 0 ) 
			position = 0;
		else if(position > sliderWidth()) 
			position = sliderWidth();
	}

	function controllerWidth()
	{
		return dom.sliderController.width();
	}

	function getControllerX()
	{
		return ( getLeft() / sliderWidth() ) * 100;
	}

	function getLeft()
	{
		return position * ((sliderWidth() - controllerWidth()) / sliderWidth());
	}

	function getInfoLeft()
	{
		return getLeft() - (sliderInfoWidth()/2) + (controllerWidth()/2) - 1;
	}

	function sliderInfoWidth()
	{
		return dom.sliderInfo.width();
	}

	function getInfoX()
	{
		return ( getInfoLeft() / dom.sliderBar.width() ) * 100;
	}

	function setElementsPosition()
	{
		dom.sliderController.css({'left' : getControllerX() + '%'});
		dom.sliderInfo.css({'left': getInfoX() + '%' } );
		writeInfo(getValue());
	}

	function writeInfo(value)
	{
		dom.sliderInfoInput.text(value + prefix);
	}

	function getInfo()
	{
		return value + prefix;
	}

	function getRange()
	{
		return (max - min); 
	}

	function getPositionToValue()
	{
		return Math.round( convertPositionToValue(position) );
	}

	function convertPositionToValue(position)
	{
		return (getRange() * position / sliderWidth()) + min;
	}

	function getValueToPosition()
	{
		return convertValueToPosition(value) - convertValueToPosition(min);
	}

	function convertValueToPosition(value)
	{
		return (sliderWidth() *  value / getRange());
	}

	function setValue (val)
	{
		value = val;

		setPosition(getValueToPosition());

		if (cfg.hasOwnProperty('onSetValue'))
			cfg.onSetValue(value, self);
	}

	function refresh() 
	{
		setPosition(getValueToPosition());
		refreshRules();
	}

	function show()
	{
		dom.target.parent().show();
		dom.instance.show();
		refresh();
	}

	function hide()
	{
		dom.target.parent().hide();
	}

	function offsetLeft()
	{
		return dom.sliderBar.offset().left;
	}

	function offsetRight()
	{
		return dom.sliderBar.offset().right;
	}

	function sliderWidth()
	{
		return dom.sliderBar.width();
	}

	function onMouseMove(func)
	{
		$(window).on('mousemove', function(event) {
			func(event);
		});	
	}

	function onMouseUp(func)
	{
		 $(window).on('mouseup', function(event) {
		 	func(event);
		 });
	}

	function startDrag() 
	{
		drabable = true;

		onMouseMove(function(event){
			setPosition(-offsetLeft() + event.pageX);

			if( move != null )
				move( self );

			if( lastMouseX > -1 )
				mouseSpeed = Math.abs(event.pageX - lastMouseX);

			lastMouseX = event.pageX;
		});

		if( start != null )
			start( self );
	}

	function stopDrag() 
	{
		drabable = false;
		$(window).unbind('mousemove');
		if( done != null )
			done( self );
	}

	function getValue()  
	{
		return value;
	}

	function addRule( value ) 
	{
		if( value < min )
			value = min;
		else if( value > max )
			value = max;

		var ctrlWidth = controllerWidth();
		var strlWidth = sliderWidth();
		// position in pixel's 
		var position = (( (strlWidth - ctrlWidth) / ( max - min) ) * (value - min) ) + (ctrlWidth/2) ;
		// position in percent's 
		position = (position / strlWidth ) * 100;

		var template = [
			'<div class="rule-line-'+value+'">',
			'<span class="rule" style="left: '+position+'%;"></span>',
			'<span class="rule-value" style="left: '+position+'%;">'+getValueReplacement(value)+'</span>',
			'</div>'
		];

		template = $(template.join(''));

		var ruleInfo = template.find('.rule-value');

		dom.rulesBar.append(template);
		var left = (position - ( (ruleInfo.width()/2) /  strlWidth ) * 100) + '%';
		ruleInfo.css({'left' : left});
	}

	function addRulesQuick(values)
	{
		var html = [];
		var ctrlWidth = controllerWidth();
		var strlWidth = sliderWidth();
		for (var i in values)
		{
			var value = values[i];

			if( value < min )
				value = min;
			else if( value > max )
				value = max;

			// position in pixel's 
			var position = (( (strlWidth - ctrlWidth) / ( max - min) ) * (value - min) ) + (ctrlWidth/2) ;
			// position in percent's 
			var positionPercent = (position / strlWidth ) * 100;
			var positionPercentCorrection = (( typeof cfg.corectPosition !== 'undefined' ? position + cfg.corectPosition : position ) / strlWidth ) * 100;

			var template = [
				'<div class="rule-line-'+value+'">',
				'<span class="rule" style="left: '+positionPercent+'%;"></span>',
				'<span class="rule-value" style="left: '+positionPercentCorrection+'%;">'+getValueReplacement(value)+'</span>',
				'</div>'
			];

			html.push(template.join(''));
		}

		dom.rulesBar.html(html.join(''));
	}

	function getValueReplacement(value) 
	{
		if (remplaceValues.hasOwnProperty(value))
			return remplaceValues[value];
		return value;
	}

	return this;
}
