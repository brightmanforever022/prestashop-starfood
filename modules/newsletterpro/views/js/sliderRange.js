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

function gkSliderRange(cfg)
{
	if (!(this instanceof gkSliderRange))
		return new gkSliderRange(cfg);

	var self           = this,
		snap           = null,
		min            = null,
		max            = null,
		step           = null,
		positionMin    = null,
		positionMax    = null,
		valueMin       = null,
		valueMax       = null,
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
		current     = null,
		editable = typeof cfg.editable !== 'undefined' ? cfg.editable : false,
		dom = {};

	init();

	// global 
	self.dom    = dom;

	self.getValueMin = function () 
	{
		return getValueMin();
	};

	self.getValueMax = function () 
	{
		return getValueMax();
	};

	self.setValueMin = function (value) 
	{
		setValueMin(value);
	};

	self.setValueMax = function (value) 
	{
		setValueMax(value);
	};

	self.refresh = function() 
	{
		return refresh();
	};

	self.reset = function(config)
	{
		return reset(config);
	};

	self.show = function()
	{
		return show();
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
		max            = cfg.max || parseInt(dom.sliderBar.width());
		step           = parseInt(cfg.step)  || 1;
		valueMin       = cfg.valueMin || 0;
		valueMax       = cfg.valueMax || 0;
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
		addDom('sliderControllerMax', [dom.instance, '.slider-controller-max']);
		addDom('sliderBar', [dom.instance, '.slider-bar']);
		addDom('rulesBar', [dom.instance, '.rules-bar']);

		addDom('sliderInfo', getInfoTemplate());
		addDom('sliderInfoMax', getInfoTemplate('max'));

		addDom('sliderInfoInput', [dom.sliderInfo, '.slider-value']);
		addDom('sliderInfoInputMax', [dom.sliderInfoMax, '.slider-value-max']);
		addDom('sliderRange', $('<div class="slider-range"></div>'));

		dom.sliderBar.prepend(dom.sliderInfo);
		dom.sliderBar.prepend(dom.sliderInfoMax);
		dom.sliderBar.prepend(dom.sliderRange);

		dom.target.append(dom.instance);
	}

	function initCss()
	{
		dom.rulesBar.css( 'width', rulesBarWidth());
		dom.sliderInfo.css({'position' : 'absolute'});
		dom.sliderInfoMax.css({'position' : 'absolute'});
		dom.instance.css({'position': 'relative', });
		dom.sliderController.css({'position': 'absolute'});
		dom.sliderControllerMax.css({'position': 'absolute'});
		dom.sliderBar.css({'position': 'absolute'});
		dom.rulesBar.css({'position': 'absolute'});
		dom.sliderRange.css({'position': 'absolute'});
	}

	function initEvents()
	{
		dom.sliderController.on('mousedown', function(event) {
			current = {
				controller: dom.sliderController,
				info: dom.sliderInfo,
				infoInput: dom.sliderInfoInput,
				value: valueMin,
				position: positionMin,
			};

			startDrag('min');
			return false;
		});

		dom.sliderController.on('mouseup', function(event) {
			stopDrag();
			return false;
		});

		dom.sliderControllerMax.on('mousedown', function(event) {
			current = {
				controller: dom.sliderControllerMax,
				info: dom.sliderInfoMax,
				infoInput: dom.sliderInfoInputMax,
				value: valueMin,
				position: positionMax,
			};
			startDrag('max');
			return false;
		});

		dom.sliderControllerMax.on('mouseup', function(event) {
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

		$.each(values, function(index, val) {
			addRule( parseInt(val) );
		});
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

	function getInfoTemplate(type)
	{
		type = type || 'min';

		var getvtopmin = getValueToPositionMin(valueMin),
			getvtomax = getValueToPositionMax(valueMax);

		if (isNaN(getvtopmin)) {
			getvtopmin = 0;
		}

		if (isNaN(getvtomax)) {
			getvtomax = 0;
		}

		var template = '';
		switch(type)
		{
			case 'min':

				template += '<span class="slider-value-container" style="left:'+ getvtopmin +'">';
				
				if (editable) {
					template += '<input type="text" class="slider-value np-slider-range-input-text" value="' + getInfoMin() + '">';	
				} else {
					template += '<span class="slider-value">'+(getInfoMin())+'</span>';
				}

				template += '</span>';

			break;

			case 'max':
				template += '<span class="slider-value-container-max" style="left:'+ getvtomax +'">';

				if (editable) {
					template += '<input type="text" class="slider-value-max np-slider-range-input-text" value="' + getInfoMax() + '">';	
				} else {
					template += '<span class="slider-value-max">'+(getInfoMax())+'</span>';
				}
				template += '</span>';

			break;
		}

		var tpl = $(template);

		if (editable) {
			tpl.find('input.slider-value').on('change', function() {
				var val = $(this).val();
				setValueMin(val);
			});

			tpl.find('input.slider-value-max').on('change', function() {
				var val = $(this).val();
				setValueMax(val);
			});
		}

		return tpl;
	}

	function getTemplate(settings) 
	{
		var id = cfg.id || '',
			className = (typeof cfg.className === 'undefined') ? '' : (typeof cfg.className === 'array' ? cfg.className.join(' ') : cfg.className),
			slider;

		slider  = '<div id="'+id+'" class="slider style-dark '+className+'">';
		slider += '<div class="slider-bar">';
		slider += '<a href="javascript:{}" class="slider-controller">&nbsp;</a>';
		slider += '<a href="javascript:{}" class="slider-controller-max">&nbsp;</a>';
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

	function getPercentMin() 
	{
		var percent = 0;
		if ( positionMin != 0 )
			percent = (( positionMin / sliderBar.width()) ) * 100  ;
		return percent;
	}

	function getPercentMax() 
	{
		var percent = 0;
		if ( positionMax != 0 )
			percent = (( positionMax / sliderBar.width()) ) * 100  ;
		return percent;
	}

	function setPositionMin(poz) 
	{
		positionMin = poz;
		setLimitsMin();

		valueMin = getPositionToValueMin();

		setElementsPositionMin();

		if ( snap && mouseSpeed < 5) 
		{
			$.each(values, function(index, s) {
				if( getValueMin() > (s - snap) && getValueMin() < (s + snap) ) 
				{
					snapAtValueMin(s);
				}
			});
		}
	}

	function setPositionMax(poz) 
	{
		positionMax = poz;
		setLimitsMax();

		valueMax = getPositionToValueMax();

		setElementsPositionMax();

		if ( snap && mouseSpeed < 5) 
		{
			$.each(values, function(index, s) {
				if( getValueMax() > (s - snap) && getValueMax() < (s + snap) ) 
				{
					snapAtValueMax(s);
				}
			});
		}
	}

	function snapAtValueMin(val)
	{
		valueMin = val;
		positionMin = getValueToPositionMin();
		setElementsPositionMin();
	}

	function snapAtValueMax(val)
	{
		valueMax = val;
		positionMax = getValueToPositionMax();
		setElementsPositionMax();
	}

	function setLimitsMin()
	{
		if ( positionMin < 0 ) 
		{
			positionMin = 0;
		}
		else if(positionMin > sliderWidth() && !checkLimits()) 
		{

			positionMin = sliderWidth();
		}
		else if (checkLimits())
		{
			positionMin = positionMax;
		}
	}

	function checkLimits()
	{
		return (valueMax <= valueMin && positionMax <= positionMin);
	}

	function setLimitsMax()
	{
		if ( positionMax < 0 && !checkLimits()) 
		{
			positionMax = 0;
		}
		else if(positionMax > sliderWidth())
		{
			positionMax = sliderWidth();
		}
		else if (checkLimits())
		{
			positionMax = positionMin;
		}
	}

	function controllerWidth()
	{
		return dom.sliderController.width();
	}

	function getControllerXMin()
	{
		return ( getLeftMin() / sliderWidth() ) * 100;
	}

	function getControllerXMax()
	{
		return ( getLeftMax() / sliderWidth() ) * 100;
	}

	function getLeftMin()
	{
		return positionMin * ((sliderWidth() - controllerWidth()) / sliderWidth());
	}

	function getLeftMax()
	{
		return positionMax * ((sliderWidth() - controllerWidth()) / sliderWidth());
	}

	function getInfoLeftMin()
	{
		return getLeftMin() - (sliderInfoWidth()/2) + (controllerWidth()/2) - 1;
	}

	function getInfoLeftMax()
	{
		return getLeftMax() - (sliderInfoWidth()/2) + (controllerWidth()/2) - 1;
	}

	function sliderInfoWidth()
	{
		return dom.sliderInfo.width();
	}

	function getInfoXMin()
	{
		return ( getInfoLeftMin() / dom.sliderBar.width() ) * 100;
	}

	function getInfoXMax()
	{
		return ( getInfoLeftMax() / dom.sliderBar.width() ) * 100;
	}

	function getControllerDif()
	{
		return getControllerXMax() - getControllerXMin();
	}

	function setElementsPositionMin()
	{
		dom.sliderController.css({'left' : getControllerXMin() + '%'});
		dom.sliderInfo.css({'left': getInfoXMin() + '%' } );

		dom.sliderRange.css({'left': getControllerXMin() + '%' } );
		dom.sliderRange.css({'width':  getControllerDif() + '%' } );

		writeInfoMin(getValueMin());
	}

	function setElementsPositionMax()
	{
		dom.sliderControllerMax.css({'left' : getControllerXMax() + '%'});
		dom.sliderInfoMax.css({'left': getInfoXMax() + '%' } );

		dom.sliderRange.css({'left': getControllerXMin() + '%' } );
		dom.sliderRange.css({'width':  getControllerDif() + '%' } );

		writeInfoMax(getValueMax());

	}

	function isCurrent()
	{
		if (current != null)
			return true;
		return false;
	}

	function writeInfoMin(value)
	{
		if (editable) {
			dom.sliderInfo.find('input').val(value);
		} else {
			dom.sliderInfo.text(value + prefix);
		}
	}

	function writeInfoMax(value)
	{
		if (editable) {
			dom.sliderInfoMax.find('input').val(value);
		} else {
			dom.sliderInfoMax.text(value + prefix);
		}
	}

	function getInfoMin()
	{

		if (!valueMin) {
			valueMin = 0;
		}

		if (editable) {
			return valueMin;
		} else {
			return valueMin + prefix;
		}

	}

	function getInfoMax()
	{
		if (!valueMax) {
			valueMax = 0;
		}

		if (editable) {
			return valueMax;
		} else {
			return valueMax + prefix;
		}

	}

	function getRange()
	{
		return (max - min); 
	}

	function getPositionToValueMin()
	{
		return Math.round( convertPositionToValue(positionMin) );
	}

	function getPositionToValueMax()
	{
		return Math.round( convertPositionToValue(positionMax) );
	}

	function convertPositionToValue(poz)
	{
		return (getRange() * poz / sliderWidth()) + min;
	}

	function getValueToPositionMin()
	{
		return convertValueToPosition(valueMin) - convertValueToPosition(min);
	}

	function getValueToPositionMax()
	{
		return convertValueToPosition(valueMax) - convertValueToPosition(min);
	}

	function convertValueToPosition(val)
	{
		return (sliderWidth() *  val / getRange());
	}

	function setValueMin( val )
	{
		valueMin = val;
		setPositionMin(getValueToPositionMin());
	}

	function setValueMax( val ) 
	{
		valueMax = val;
		setPositionMax(getValueToPositionMax());
	}

	function refresh() 
	{
		setPositionMin(getValueToPositionMin());
		setPositionMax(getValueToPositionMax());
		initRules();
	}

	function reset(config)
	{
		config = config || {};
		$.each(config, function(i, v){
			cfg[i] = v;
		});

		initConfig();
		refresh();
	}

	function show()
	{
		dom.instance.show();
		refresh();
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

	self.resetPositionMin = function()
	{
		resetPositionMin();
	};

	function resetPositionMin()
	{
		setPositionMin(-offsetLeft());
	}

	self.resetPositionMax = function()
	{
		resetPositionMax();
	};

	function resetPositionMax()
	{
		setPositionMax(-offsetLeft() + sliderWidth());
	}

	function startDrag(type) 
	{
		type = type || 'min';

		drabable = true;

		onMouseMove(function(event){
			switch(type)
			{
				case 'min':
					setPositionMin(-offsetLeft() + event.pageX);
				break;

				case 'max':
					setPositionMax(-offsetLeft() + event.pageX);
				break;
			}

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

	function getValueMin()  
	{
		return valueMin;
	}

	function getValueMax()
	{
		return valueMax;
	}

	function addRule( value ) 
	{
		if( value < min )
			value = min;
		else if( value > max )
			value = max;

		// position in pixel's 
		var position = (( (sliderWidth() - controllerWidth()) / ( max - min) ) * (value - min) ) + (controllerWidth()/2) ;
		// position in percent's 
		position = (position / sliderWidth() ) * 100;

		var template = '<div class="rule-line-'+value+'">';
			template += '<span class="rule" style="left: '+position+'%;"></span>';
			template += '<span class="rule-value" style="left: '+position+'%;">'+getValueReplacement(value)+'</span>';
			template += '</div>';

		template = $(template);

		var ruleInfo = template.find('.rule-value');

		dom.rulesBar.append(template);

		ruleInfo.css({'left' :  (position - ( (ruleInfo.width()/2) /  sliderWidth() ) * 100) + '%'});
	}

	function getValueReplacement(value) 
	{
		if (remplaceValues.hasOwnProperty(value))
			return remplaceValues[value];
		return value;
	}

	return this;
}
