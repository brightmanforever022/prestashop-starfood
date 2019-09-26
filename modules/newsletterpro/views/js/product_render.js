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
* 
* Examples:
* 
* {if isset(var)} yes {else} no {/if}
* {if var > 0} {var} is greater than 0!{/if}
* Signs: <, <=, ==, >= , >, ===, !==, !=
*
**/

NewsletterPro.namespace('components.ProductRender');
NewsletterPro.components.ProductRender = function ProductRender(content, vars)
{
	if (!(this instanceof ProductRender))
		return new ProductRender(content, vars);

	var CONDITIONAL_ISSET       = 1,
		CONDITIONAL_LT          = 2,
		CONDITIONAL_LT_EQ       = 3,
		CONDITIONAL_EQUAL       = 4,
		CONDITIONAL_GT_EQ       = 5,
		CONDITIONAL_GT          = 6,
		CONDITIONAL_EQUAL_E     = 7,
		CONDITIONAL_NOT_EQUAL   = 8,
		CONDITIONAL_NOT_EQUAL_E = 9,
		contentRender;

	content = content.replace(/\&gt;/g, '>');
	content = content.replace(/\&lt;/g, '<');

	this.render = function()
	{
		// render the content exclude the condtional statements
		for(var key in vars)
			setVar(key, displayValue(vars[key]));

		contentRender = content;

		try
		{
			executeConditions(getConditions());
		} 
		catch(e)
		{
			console.error(e.message);
		}

		return contentRender;
	};

	function setVar(key, value) 
	{
		content = content.replace(new RegExp('\{'+key+'\}', 'g'), value);
	}

	function displayValue(value) 
	{
		if (typeof value === 'undefined' || value === null) 
			return '';
		return value;
	}

	function getConditions()
	{
		var regex = /\{if\s(?:\s+)?([^}]+)\}([\s\S]+?)(?:\{\/if\})/g;
		var match = content.match(regex);
		var conditions = [];

		if (match != null)
		{
			var len = match.length;
			if (len > 0)
			{
				for(var i = 0; i < len; i++)
				{
					var match = regex.exec(content),
						type = getType(match[1]),
						condition = match[1];

					conditions.push({
						match: match[0],
						condition: condition,
						content: match[2],
						type: type,
						variable: getRightVariable(condition),
						value: getLeftValue(condition),
						sign: getSignByType(type),
						hasElse: function()
						{
							return /\{else\}/.test(this.match);
						},
						getElse: function()
						{
							if (this.hasElse())
							{
								var match = /\{else\}([\s\S]+)/.exec(this.content);
								if (match != null)
									return match[1];
							}
							return '';
						},
						getIf: function()
						{
							if (this.hasElse())
							{
								var match = /([\s\S]+?)\{else\}/.exec(this.content);
								if (match != null)
									return match[1];
							}
							return this.content;
						}
					});
				}
			}
		}

		return conditions;
	}

	function executeConditions(conditions)
	{
		if (conditions.length > 0)
		{
			for(var i in conditions)
			{
				var condition = conditions[i];
				 evaluate(condition);
			}
		}
	}

	function evaluate(condition)
	{
		var variable = condition.variable,
			value    = condition.value,
			content  = condition.getIf(),
			match    = condition.match;

		switch(condition.type)
		{
			case CONDITIONAL_ISSET:
				if (varExists(condition.variable))
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_LT:
				if (varExists(variable) && getVarValue(variable) < value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_LT_EQ:
				if (varExists(variable) && getVarValue(variable) <= value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_EQUAL:
				if (varExists(variable) && getVarValue(variable) == value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_GT_EQ:
				if (varExists(variable) && getVarValue(variable) >= value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_GT:
				if (varExists(variable) && getVarValue(variable) > value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_EQUAL_E:
				if (varExists(variable) && getVarValue(variable) === value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_NOT_EQUAL:
				if (varExists(variable) && getVarValue(variable) != value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_NOT_EQUAL_E:
				if (varExists(variable) && getVarValue(variable) !== value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;
		}
	}

	function setConditionVar(match, content)
	{
		contentRender = contentRender.replace(match, content);
	}

	function varExists(name)
	{
		if (vars.hasOwnProperty(name))
			return true;
		return false;
	}

	function getVarValue(name)
	{
		if (varExists(name))
			return vars[name];
	}

	function getType(value)
	{
		var type;

		switch(true)
		{
			case /isset/.test(value):
				type = CONDITIONAL_ISSET;
				break;

			case getRegExp('<').test(value):
				type = CONDITIONAL_LT;
				break;

			case getRegExp('<=').test(value):
				type = CONDITIONAL_LT_EQ;
				break;

			case getRegExp('==').test(value):
				type = CONDITIONAL_EQUAL;
				break;

			case getRegExp('>=').test(value):
				type = CONDITIONAL_GT_EQ;
				break;

			case getRegExp('>').test(value):
				type = CONDITIONAL_GT;
				break;

			case getRegExp('===').test(value):
				type = CONDITIONAL_EQUAL_E;
				break;

			case getRegExp('!=').test(value):
				type = CONDITIONAL_NOT_EQUAL;
				break;

			case getRegExp('!==').test(value):
				type = CONDITIONAL_NOT_EQUAL_E;
				break;
		}

		return type;
	}

	function getRegExp(value)
	{
		var rl = '(\\s|\\w+|\"\')';
		return new RegExp(rl+value+rl);
	}

	function getSignByType(type)
	{
		var sign;
		switch(type)
		{
			case CONDITIONAL_ISSET:
				sign = 'isset';
				break;

			case CONDITIONAL_LT:
				sign = '<';
				break;

			case CONDITIONAL_LT_EQ:
				sign = '<=';
				break;

			case CONDITIONAL_EQUAL:
				sign = '==';
				break;

			case CONDITIONAL_GT_EQ:
				sign = '>=';
				break;

			case CONDITIONAL_GT:
				sign = '>';
				break;

			case CONDITIONAL_EQUAL_E:
				sign = '===';
				break;

			case CONDITIONAL_NOT_EQUAL:
				sign = '!=';
				break;

			case CONDITIONAL_NOT_EQUAL_E:
				sign = '!==';
				break;
		}
		return sign;
	}

	function getRightVariable(condition)
	{
		var type = getType(condition);
		if(type == CONDITIONAL_ISSET)
		{
			var regex = new RegExp(getSignByType(type)+'\\((\\w+)\\)');
			var match = regex.exec(condition);
			if (typeof match[1] !== 'undefined')
				return match[1];
		}
		else
			return $.trim(condition.match(/\w+/)[0]);
	}

	function getLeftValue(condition)
	{
		var match = /[=<>]+([^}]+)/.exec(condition);
		if (match !== null)
			return stripQuotes(match[1]);
	}

	function stripQuotes(value)
	{
		value = $.trim(value);

		if (value.length > 0)
		{
			var firstChar = value[0];
			var lastChar = value[value.length - 1];

			if (firstChar == '\'' || firstChar == '"')			
				return value.substr(1, value.length - 2);
		}
		return value;
	}

};