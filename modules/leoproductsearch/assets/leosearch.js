/**
 * @copyright Commercial License By LeoTheme.Com 
 * @email leotheme.com
 * @visit http://www.leotheme.com
 */

var instantSearchQueries = [];
$(document).ready(function()
{
	/* TODO Ids aa blocksearch_type need to be removed*/
	
	if (typeof ajaxsearch != 'undefined' && ajaxsearch && typeof blocksearch_type !== 'undefined' && blocksearch_type){
		var width_ac_results = 	$("#leo_search_query_" + blocksearch_type).parent('form').width();
		$("#leo_search_query_" + blocksearch_type).autocomplete(
			leo_search_url,
			{
				minChars: 3,
				max: 10,
				width: (width_ac_results > 0 ? width_ac_results : 500),
				selectFirst: false,
				scroll: false,
				dataType: "json",
				formatItem: function(data, i, max, value, term) {
					return value;
				},
				parse: function(data) {
					var mytab = new Array();
					for (var i = 0; i < data.length; i++)
						mytab[mytab.length] = { data: data[i], value: '<IMG align = "center" width = "20px" SRC=' + data[i].image_link + '>' + '  ' + data[i].pname };
					return mytab;
				},
				extraParams: {
					ajaxSearch: 1,
					id_lang: id_lang
				}
			}
		)
		.result(function(event, data, formatted) {
			$('#leo_search_query_' + blocksearch_type).val(data.pname);
			document.location.href = data.product_link;
		});
		}
});

function tryToCloseInstantSearch()
{
	if ($('#old_center_column').length > 0)
	{
		$('#center_column').remove();
		$('#old_center_column').attr('id', 'center_column');
		$('#center_column').show();
		return false;
	}
}

function stopInstantSearchQueries()
{
	for(i=0;i<instantSearchQueries.length;i++)
		instantSearchQueries[i].abort();
	instantSearchQueries = new Array();
}
