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

NewsletterPro.namespace('modules.csv');
NewsletterPro.modules.csv = ({
	dom: null,
	init: function(box) {
		var self = this;

		self.ready(function(dom) {
			dom.nextStep.on('click', function(){
				var listRef = Number($('[name="exportEmailAddresses"]:checked').val());

				$.postAjax({'submit': 'getExportOptions', value: listRef }).done(function(response){
					if (response.success)
					{
						var columns = response.columns;
						
						buildTable(columns);

						dom.listRef.val(listRef);

						showDetails();
					}
					else
						box.alertErrors(response.errors);
				});
			});

			dom.btnBack.on('click', function(){
				hideDetails();
			});

			dom.btnExport.on('click', function(){
				dom.form.submit();
			});

			dom.checkAll.on('click', function(){
				$.each($('[name="export_csv_selected_columns[]"]'), function(i, item){
					$(item).prop('checked', true);
				});
			});

		});

		function showDetails()
		{
			self.dom.container.hide();
			self.dom.exportDetails.show();
		}

		function hideDetails()
		{
			self.dom.container.show();
			self.dom.exportDetails.hide();
		}

		function buildTable(columns)
		{
			self.dom.exportOptions.empty();

			var theadTemplate = '';
			for(var i = 0; i < columns.length; i++)
			{
				var column = columns[i];

				theadTemplate += '\
					<div class="checkbox">\
						<label class="control-label  in-win">\
							<input type="checkbox" name="export_csv_selected_columns[]" value="'+column+'">\
							'+formatColumn(column)+'\
						</label>\
					</div>';
			}

			self.dom.exportOptions.html(theadTemplate);
		}

		function formatColumn(name)
		{
			var exp = name.split('_');
			for(var i = 0; i < exp.length; i++)
			{
				var first = exp[i][0],
					rest = exp[i].slice(1);

				exp[i] = first.toUpperCase() + rest;
			}

			return exp.join(' ');
		}

		return self;
	},

	ready: function(func) {
		var self = this;
		$(document).ready(function(){
			self.dom = {
				btnBack: $('#np-export-email-addresses-back'),
				nextStep: $('#btn-export-email-addresses'),
				container: $('#import-export-container'),
				exportDetails: $('#export-details'),
				exportOptions: $('#np-export-email-options'),
				btnExport: $('#btn-export-csv'),
				form: $('#export-csv-form'),
				listRef: $('#export-csv-list-ref'),
				checkAll: $('#btn-export-csv-checkall'),
			};
			func(self.dom);
		});
	},
}.init(NewsletterPro));