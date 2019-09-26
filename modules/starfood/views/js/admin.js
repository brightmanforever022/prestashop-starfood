/**
 * 
 */
$(function(){
	$('#starfoodQPEModal').on('hidden.bs.modal', function (e) {
		
        $('#starfoodQPEModalBody').html('Loading...');
        
	});
    
    $("#starfoodQPEModal").on("click","#file-upload-button", function(){
            $('.loading').show();
           window.setTimeout( checkImgupload, 10000 );
        });
	/*$('#starfoodQPEModal').on('submit', 'form', function(e){
		e.preventDefault();
		var formData = $(this).serialize();
		var formUrl = $(this).attr('action');
		$('#starfoodQPEModalBody').html('Loading...');
		$.ajax({
			url: formUrl,
			dataType: 'json',
			type: 'POST',
			data: formData,
			success: function(response){
				if(response.reload){
					location.reload();
					return;
				}
				$('#starfoodQPEModalBody').html(response.html);
				$('#starfoodQPEModal').modal();
			}
		});
		return false;
	});*/
	
	$('table.product').on('click', 'td', function(e){
		if( $(this).hasClass('row-selector') || $(this).find('a.list-action-enable').length
				|| (e.originalEvent.target.tagName == 'A') 
		){
			return;
		}
		var productId = parseInt( $(this).parent('tr').find('td.row-selector input[type="checkbox"]').val() );
		$('#starfoodQPEModal').modal({
		      escapeClose: false,
              clickClose: false,
              showClose: false
		});
		$.ajax({
			url: currentIndex+'&token='+adToken+'&ajax=1&action=quick_edit&id_product='+productId,
			dataType: 'json',
			type: 'GET',
			success: function(response){
				$('#starfoodQPEModalBody').html(response.html);
                //runningJS();
			}
		});
	});
	$('#starfoodQPEModal').on('change', '#id_measure_unit, #unit_value, #id_liquid_density', function(){
		starfoodCalculateWeight();
	})
});
function starfoodCalculateWeight(){
	var unitValue = $('#unit_value').val();
	unitValue = parseFloat(unitValue);
	if( isNaN(unitValue) ){
		return;
	}

	var weightConverter = null;
	var measureUnitInfo = $('#id_measure_unit option:selected').data('info');
	
	if( typeof measureUnitInfo != 'object'){
		return;
	}
	
	var measureAbsolute = parseFloat(measureUnitInfo.measure_abs);
	if( isNaN(measureAbsolute) ){
		return;
	}
	
	if( measureUnitInfo.liquid ){
		var liquidDensityInfo = $('#id_liquid_density option:selected').data('info');
			
		if( typeof liquidDensityInfo != 'object' ){
			return;
		}
		var density = parseFloat(liquidDensityInfo.density);
		if( isNaN(density) ){
			return;
		}

		var weight = unitValue * measureAbsolute * density;
	}
	else{
		var weight = unitValue * measureAbsolute;
	}
	
	
	$('#weight').val(weight);
}
function checkImgupload(){
    
    if($("#file-success").length){
            id_product = $('#product_id').val();
            $.ajax({
			url: currentIndex+'&token='+adToken+'&ajax=1&action=quick_edit&id_product='+id_product,
			dataType: 'json',
			type: 'GET',
			success: function(response){
				$('#starfoodQPEModalBody').html(response.html);
                //runningJS();
			}
		});
        }  
}
function refreshImagePositions(imageTable)
{
	var reg = /_[0-9]$/g;
	var up_reg  = new RegExp("imgPosition=[0-9]+&");

	imageTable.find("tbody tr").each(function(i,el) {
		$(el).find("td.positionImage").html(i + 1);
	});
	imageTable.find("tr td.dragHandle a:hidden").show();
	imageTable.find("tr td.dragHandle:first a:first").hide();
	imageTable.find("tr td.dragHandle:last a:last").hide();
}

