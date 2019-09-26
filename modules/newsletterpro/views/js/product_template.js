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

NewsletterPro.namespace('components.ProductTemplate');
NewsletterPro.components.ProductTemplate = function ProductTemplate(cfg)
{
	if (!(this instanceof ProductTemplate))
		return new ProductTemplate(cfg);

	var box = NewsletterPro,
		self = this,
		columns = 0,
		columnsMatch = null;

	this.cfg = cfg;
	this.header = this.cfg.header;

	columnsMatch = this.cfg.template.match(/\<\!--\s+?\{columns=(\d+)\}\s+?--\>/);

	if (columnsMatch) {
		if (columnsMatch.length > 1) {
			columns = Number(columnsMatch[1]);

			if (isNaN(columns)) {
				columns = 0;
			}
		}
	}

	this.columns = columns;

	this.template = this.cfg.template.replace(/\{columns=\d+\}|<!--\s+\{columns=\d+\}\s+-->|<!-- start header -->[\s\S]*?<!-- end header -->/g, '');

	if (this.isTemplate()) {
		this.tree = box.components.ProductTemplateTree(this.template);
	} else {
		this.tree = box.components.ProductTemplateTree(this.template, false);
	}

};

NewsletterPro.components.ProductTemplate.prototype = {
	html: function()
	{
		return this.tree.get('np-product');
	},

	isTemplate: function()
	{
		if (this.getHeader('content') === 'template') {
			return true;
		} else {
			return false;
		}
	},

	hasHeader: function(name)
	{
		if (this.header.hasOwnProperty(name)) {
			return true;
		} else {
			return false;
		}
	},

	getHeader: function(name)
	{
		if (this.hasHeader(name)) {
			return this.header[name];
		}
	},
};

NewsletterPro.namespace('components.ProductTemplateTree');
NewsletterPro.components.ProductTemplateTree = function ProductTemplateTree(string, needParse) {

	if (!(this instanceof ProductTemplateTree))
		return new ProductTemplateTree(string, needParse);

	needParse = typeof needParse === 'undefined' ? true : false;

	this.string = string;
	this.storage = {};

	if (needParse) {
		this.init(this.string);
	} else {
		this.storage['np-product'] = string;
	}
};

NewsletterPro.components.ProductTemplateTree.prototype = {
	init: function(string) {
        var i,
            len = string.length,
            current,
            next,
            prev,
            depth = 0,
            nodeName,
            nodeValue,
            nodeIndex = 0,
            endIndex = 0,
            openIndex = 0;

        for (i = 0; i < len; i++) {
            current = string[i];
            next = string[i + 1];
            prev = string[i - 1];

            if (current === '{' && next === '{') {
                nodeName = string.substr(nodeIndex, i - nodeIndex).replace(/\s+|\:/g, '');

                if (depth == 0) {
                    rootName = nodeName;
                }

                nodeIndex = i + 2;
                openIndex = nodeIndex;

                depth++;
            } else if (current === '}' && next === '}' && string[i + 2] === ',') {

                nodeIndex = i + 3;
                endIndex = nodeIndex

                nodeValue = string.substr(openIndex, i - openIndex).trim();

                if (depth > 1) {
                    this.add(nodeName, nodeValue);
                }

                depth--;
            } else if (current === '}' && next === '}') {
                depth--;
            }
        }

	},

	parse: function(func) {
		var keys = Object.keys(this.storage);
		for (var i = 0, len = keys.length; i < len; i++) {
			func(keys[i], this.storage[keys[i]]);
		}
	},

    add: function(name, value) {
        this.storage[name] = value;
    },
    get: function(name) {
        if (this.has(name)) {
            return this.storage[name];
        }
    },
    getRender: function(name, vars) {
    	if (this.has(name)) {
    		vars = vars || {};

    		var box = NewsletterPro,
    			value = this.get(name);

    		return new box.components.ProductRender(value, vars).render();
    	}
    },
    has: function(name) {
        return this.storage.hasOwnProperty(name);
    },
};

