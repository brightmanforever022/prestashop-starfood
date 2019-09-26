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

NewsletterPro.namespace('modules.categoriesTree');
NewsletterPro.modules.categoriesTree = ({
	dom: null,
	box: null,

	global: function(name, value) 
	{
		window[name] = value;
	},

	init: function(box) 
	{
		var self = this;

		var readyToExpand = true;
		var needCheckAll = false;
		var needUncheckAll = false;
		var interval = null;
		var intervalCheck = null;
		var id_tree = 0;
		var arrayCatToExpand = new Array();
		var id_category_root = 0;
		var sendNewsletters;

		self.box = box;

		self.ready(function(dom){

			self.global('buildTreeView', buildTreeView);
			self.global('searchCategory', searchCategory);
			self.global('clickOnCategoryBox', clickOnCategoryBox);

			self.uncheckAllCategories = function(appyFilter) 
			{
				if (typeof appyFilter === 'undefined')
					appyFilter = true;

				return uncheckAllCategories(appyFilter);
			}

			self.setNeedUncheckAll = function(value) 
			{
				needUncheckAll = value;
			}

			function buildTreeView()
			{
				use_shop_context = 0;
				if (buildTreeView.arguments[0] && buildTreeView.arguments[0] == 1)
					use_shop_context = 1;

				$("#categories-treeview").treeview({
					url : ajaxRequestUrl,
					toggle: function () { callbackToggle($(this)); },
					ajax : {
						type: 'POST',
						async: false,
						data: {
							getChildrenCategories : true,
							use_shop_context : use_shop_context,
							selectedCat: selectedCat
						}
					}
				});

				id_category_root = $('#categories-treeview li:first').attr('id');

				$('#categories-treeview li#'+id_category_root+' span').trigger('click');

				$('#categories-treeview li#'+id_category_root).children('div').remove();
				$('#categories-treeview li#'+id_category_root).
					removeClass('collapsable lastCollapsable').
					addClass('last static');

				disabled = $('#categories-treeview li:first input[type=checkbox]').attr('disabled');
				$('#categories-treeview input[type=checkbox]').attr('disabled', disabled);
				$('#expand_all').click( function () {
					expandAllCategories();
					return false;
				});

				$('#collapse_all').click( function () {
					collapseAllCategories();
					return false;
				});

				$('#check_all').click( function () {
					needCheckAll = true;
					checkAllCategories();
					return false;
				});

				$('#uncheck_all').click( function () {
					needUncheckAll = true;
					uncheckAllCategories();
					return false;
				});

				$('.expandable').click(function(){
					disabled = $('#categories-treeview li:first input[type=checkbox]').attr('disabled');
					$('#categories-treeview input[type=checkbox]').attr('disabled', disabled);
				});
			}

			function isModuleSendNewsletter(modules)
			{
				if (typeof NewsletterPro.modules.sendNewsletters !== 'undefined' )
				{
					sendNewsletters = NewsletterPro.modules.sendNewsletters;
					return true;
				}
				return false;
			}

			function callbackToggle(element)
			{
				if (!element.is('.expandable'))
					return false;

				if (element.children('ul').children('li.collapsable').length != 0)
					closeChildrenCategories(element);
			}

			function closeChildrenCategories(element)
			{
				var arrayLevel = new Array();

				if (element.children('ul').find('li.collapsable').length == 0)
					return false;

				element.children('ul').find('li').each(function() {
					var level = $(this).children('span.category_level').html();
					if (arrayLevel[level] == undefined)
						arrayLevel[level] = new Array();

					arrayLevel[level].push($(this).attr('id'));
				});

				for(i=arrayLevel.length-1;i>=0;i--)
				{
					if (arrayLevel[i] != undefined)
						for(j=0;j<arrayLevel[i].length;j++)
						{
							$('#categories-treeview').find('li#'+arrayLevel[i][j]+'.collapsable').children('span.category_label').trigger('click');
							$('#categories-treeview').find('li#'+arrayLevel[i][j]+'.expandable').children('ul').hide();
						}
				}
			}

			function setCategoryToExpand()
			{
				var ret = false;

				id_tree = 0;
				arrayCatToExpand = new Array();
				$('#categories-treeview').find('li.expandable:visible').each(function() {
					arrayCatToExpand.push($(this).attr('id'));
					ret = true;
				});

				return ret;
			}

			function needExpandAllCategories()
			{
				return $('li').is('.expandable');
			}

			function expandAllCategories()
			{
				if (!needExpandAllCategories())
					return;

				if ($('li#'+id_category_root).is('.expandable'))
					$('li#'+id_category_root).children('span.folder').trigger('click');

				readyToExpand = true;
				if (setCategoryToExpand())
					interval = setInterval(openCategory, 10);
			}

			function openCategory()
			{
				readyToExpand = true;

				if (id_tree >= arrayCatToExpand.length && readyToExpand)
				{

					if (!setCategoryToExpand())
					{
						clearInterval(interval);

						interval = null;
						readyToExpand = false;
						if (needCheckAll)
						{
							checkAllCategories();
							needCheckAll = false;
						}
						else if (needUncheckAll)
						{
							uncheckAllCategories();
							needUncheckAll = false;
						}
					}
					else
						readyToExpand = true;
				}

				if (readyToExpand)
				{
					if ($('#categories-treeview').find('li#'+arrayCatToExpand[id_tree]+'.hasChildren').length > 0)
						readyToExpand = false;
					$('#categories-treeview').find('li#'+arrayCatToExpand[id_tree]+'.expandable:visible span.category_label').trigger('click');
					id_tree++;
				}
			}

			function collapseAllCategories()
			{
				closeChildrenCategories($('li#'+id_category_root));
			}

			function checkAllCategories()
			{
				if (needExpandAllCategories())
					expandAllCategories();

				$('input[name="categoryBox[]"]').not(':checked').each(function () {
					$(this).attr('checked', true);
					clickOnCategoryBox($(this), false);
				});

				if (isModuleSendNewsletter())
					sendNewsletters.triggerEvent('clickOnCategoryBox');
			}

			function uncheckAllCategories(appyFilter)
			{
				if (typeof appyFilter === 'undefined')
					appyFilter = true;

				if (needExpandAllCategories())
					expandAllCategories();

				$('input[name="categoryBox[]"]:checked').each(function () { 
					$(this).removeAttr('checked');
					clickOnCategoryBox($(this), false);
				});

				if (isModuleSendNewsletter() && appyFilter)
					sendNewsletters.triggerEvent('clickOnCategoryBox');
			}

			function clickOnCategoryBox(category, oneClick)
			{
				oneClick = typeof oneClick !== 'undefined' ? oneClick : true;

				if (category.is(':checked'))
				{
					$('select#id_category_default').append('<option value="'+category.val()+'">'+(category.val() !=1 ? category.parent().find('span').html() : home)+'</option>');
					updateNbSubCategorySelected(category, true);
					if ($('select#id_category_default option').length > 0)
					{
						$('select#id_category_default').show();
						$('#no_default_category').hide();
					}
				}
				else
				{
					$('select#id_category_default option[value='+category.val()+']').remove();
					updateNbSubCategorySelected(category, false);
					if ($('select#id_category_default option').length == 0)
					{
						$('select#id_category_default').hide();
						$('#no_default_category').show();
					}
				}

				if ($.hasOwnProperty('uniform'))
				{
					 $.uniform.update(category);
				}

				if (oneClick && isModuleSendNewsletter())
					sendNewsletters.triggerEvent('clickOnCategoryBox');

			}

			function updateNbSubCategorySelected(category, add)
			{
				var currentSpan = category.parent().parent().parent().children('.nb_sub_cat_selected');
				var parentNbSubCategorySelected = currentSpan.children('.nb_sub_cat_selected_value').html();

				if (use_radio)
				{
					$('.nb_sub_cat_selected').hide();
					return false;
				}

				if (add)
					var newValue = parseInt(parentNbSubCategorySelected)+1;
				else
					var newValue = parseInt(parentNbSubCategorySelected)-1;

				currentSpan.children('.nb_sub_cat_selected_value').html(newValue);
				currentSpan.children('.nb_sub_cat_selected_word').html(selectedLabel);

				if (newValue == 0)
					currentSpan.hide();
				else
					currentSpan.show();

				if (currentSpan.parent().children('.nb_sub_cat_selected').length != 0)
					updateNbSubCategorySelected(currentSpan.parent().children('input'), add);
			}

			function searchCategory()
			{
				var category_to_check;
				if ($('#search_cat').length)
				{
					$('#search_cat').autocomplete(ajaxRequestUrl+'?searchCategory=1', {
						delay: 100,
						minChars: 3,
						autoFill: true,
						max:20,
						matchContains: true,
						mustMatch:true,
						scroll:false,
						cacheLength:0,
						multipleSeparator:'||',
						formatItem: function(item) 
						{
							return item[1]+' - '+item[0];
						}
					}).result(function(event, item)
					{ 
						parent_ids = getParentCategoriesIdAndOpen(item[1]);
					});
				}
			}

			function getParentCategoriesIdAndOpen(id_category)
			{
				category_to_check = id_category;
				$.ajax({
					type: 'POST',
					url: ajaxRequestUrl,
					async: true,
					dataType: 'json',
					data: 'ajax=true&getParentCategoriesId=true&id_category=' + id_category ,
					success: function(jsonData) {
						for(var i= 0; i < jsonData.length; i++)
							if (jsonData[i].id_category != 1)
								arrayCatToExpand.push(jsonData[i].id_category);
						readyToExpand = true;
						interval = setInterval(openParentCategories, 10);
						intervalCheck = setInterval(checkCategory, 20);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						jAlert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
					}
				});
			}

			function openParentCategories()
			{
				if (id_tree >= arrayCatToExpand.length && !readyToExpand)
				{
					clearInterval(interval);

					interval = null;
					readyToExpand = false;
				}

				if (readyToExpand)
				{
					if ($('li#'+arrayCatToExpand[id_tree]+'.hasChildren').length > 0)
						readyToExpand = false;

					$('li#'+arrayCatToExpand[id_tree]+'.expandable span').trigger('click');
					id_tree++;
				}
			}

			function checkCategory()
			{
				if ($('li#'+category_to_check+' > input[type=checkbox]').prop('checked'))
				{
					clearInterval(intervalCheck);
					intervalCheck = null;
				}
				else
				{
					$('li#'+category_to_check+' > input').attr('checked', true);
					updateNbSubCategorySelected($('li#'+category_to_check+' > input[type=checkbox]'), true);
				}

				if (isModuleSendNewsletter())
					sendNewsletters.triggerEvent('clickOnCategoryBox');
			}
		});

		return this;
	},

	ready: function(func) {
		var self = this;

		$(document).ready(function(){
			self.dom = {};
			func(self.dom)
		});
	},

}.init(NewsletterPro));