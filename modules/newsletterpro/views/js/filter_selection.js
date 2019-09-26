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

;(function($){
	NewsletterPro.namespace('components.FilterSelection');
	NewsletterPro.components.FilterSelection = function FilterSelection(cfg)
	{
		if (!(this instanceof FilterSelection))
			return new FilterSelection(cfg);

		this.customers = cfg.customers;
		this.visitors = cfg.visitors;
		this.visitors_np = cfg.visitors_np;
		this.added = cfg.added;

		this.customers_apply_callback =  cfg.customers_apply_callback;
		this.visitor_apply_callback = cfg.visitor_apply_callback;
		this.visitor_np_apply_callback = cfg.visitor_np_apply_callback;
		this.added_apply_callback = cfg.added_apply_callback;

		this.clearfilters = function()
		{
			this.customers.clear.trigger('click');

			if (typeof this.visitors !== 'undefined')
				this.visitors.clear.trigger('click');

			if (typeof this.visitors_np !== 'undefined')
				this.visitors_np.clear.trigger('click');

			this.added.clear.trigger('click');
		};

		this.parseFilters = function(list, filter, func)
		{
			for (var filterName in list)
			{
				var item = list[filterName];

				for (var filterNameSelection in filter)
				{
					var ids = filter[filterNameSelection];

					func(filterName, item, filterNameSelection, ids);
				}
			}
		};

		this.applyFilters = function(filters)
		{
			for (var listName in filters)
			{
				var filter = filters[listName];

				switch(listName)
				{
					case 'customers':
						this.parseFilters(this.customers, filter, function(filterName, item, filterNameSelection, ids){

							switch(filterName)
							{
								case 'groups':
								case 'languages':
								case 'shops':
								case 'gender':
								case 'subscribed':
									if (filterName === filterNameSelection)
									{
										item.mark(ids);
									}
								break;
							}
						});

						this.customers_apply_callback();

						break;

					case 'visitors':
						if (typeof this.visitors !== 'undefined')
						{
							this.parseFilters(this.visitors, filter, function(filterName, item, filterNameSelection, ids){
								switch(filterName)
								{
									case 'shops':
									case 'subscribed':
										if (filterName === filterNameSelection)
										{
											item.mark(ids);
										}
									break;
								}
							});

							this.visitor_apply_callback();
						}
						break;

					case 'visitors_np':
						if (typeof this.visitors_np !== 'undefined')
						{
							this.parseFilters(this.visitors_np, filter, function(filterName, item, filterNameSelection, ids){
								switch(filterName)
								{
									case 'languages':
									case 'shops':
									case 'gender':
									case 'subscribed':
									case 'filter_by_interest':
										if (filterName === filterNameSelection)
										{
											item.mark(ids);
										}
									break;
								}
							});

							this.visitor_np_apply_callback();
						}
						break;

					case 'added':
							this.parseFilters(this.added, filter, function(filterName, item, filterNameSelection, ids){
								switch(filterName)
								{
									case 'languages':
									case 'shops':
									case 'csv_name':
									case 'subscribed':
										if (filterName === filterNameSelection)
										{
											item.mark(ids);
										}
									break;
								}
							});

							this.added_apply_callback();
						break;
				}
			}
		};

		this.getFilters = function()
		{
			var filters = {};

			filters['customers'] = this.getCustomersFilter();

			if (typeof this.visitors !== 'undefined')
				filters['visitors'] = this.getVisitorsFilter();

			if (typeof this.visitors_np !== 'undefined')
				filters['visitors_np'] = this.getVisitorNpFilter();

			filters['added'] = this.getAddedFilter();

			return filters;
		};

		this.getCustomersFilter = function()
		{
			var filters = {};
			for(var filterName in this.customers)
			{
				var item = this.customers[filterName];

				switch(filterName)
				{
					case 'groups':
					case 'languages':
					case 'shops':
					case 'gender':
					case 'subscribed':
						filters[filterName] = item.getSelected();
						break;
				}
			}
			return filters;
		};

		this.getVisitorsFilter = function()
		{
			var filters = {};
			for(var filterName in this.visitors)
			{
				var item = this.visitors[filterName];

				switch(filterName)
				{
					case 'shops':
					case 'subscribed':
						filters[filterName] = item.getSelected();
						break;
				}

				// make the rest of cases
			}
			return filters;
		};

		this.getVisitorNpFilter = function()
		{
			var filters = {};
			for(var filterName in this.visitors_np)
			{
				var item = this.visitors_np[filterName];
			
				switch(filterName)
				{
					case 'languages':
					case 'shops':
					case 'gender':
					case 'subscribed':
					case 'filter_by_interest':
						filters[filterName] = item.getSelected();
						break;
				}

				// make the rest of cases
			}
			return filters;
		};

		this.getAddedFilter = function()
		{
			var filters = {};
			for(var filterName in this.added)
			{
				var item = this.added[filterName];

				switch(filterName)
				{
					case 'languages':
					case 'shops':
					case 'csv_name':
					case 'subscribed':
						filters[filterName] = item.getSelected();
						break;
				}

				// make the rest of cases
			}
			return filters;
		};
	};

}(jQueryNewsletterProNew));