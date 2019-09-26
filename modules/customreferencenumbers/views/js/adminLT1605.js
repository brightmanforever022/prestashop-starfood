/*
* 2015 IT Present
*
*  @author IT Present <acvikota@gmail.com>
*  @copyright 2015 IT Present
*/
$(document).ready(function(){
	counterResetDate = $("#counter_reset_date").datepicker({dateFormat: 'yy-mm-dd'});
	$("#counter_reset_date").prop("readonly", true);
	if($("#reference_number_format").val().indexOf("{RANDOM_NUMBER}") > -1){displayFields("#random_number_length",true);}else{displayFields("#random_number_length",false);}
	if($("#reference_number_format").val().indexOf("{RANDOM_ALPHABETIC}") > -1){displayFields("#random_alphabetic_length",true);}else{displayFields("#random_alphabetic_length",false);}
	if($("#reference_number_format").val().indexOf("{RANDOM_ALPHANUMERIC}") > -1){displayFields("#random_alphanumeric_length",true);}else{displayFields("#random_alphanumeric_length",false);}
	if($("#reference_number_format").val().indexOf("{COUNTER}") > -1){
		displayFields("#counter_start_at",true);displayFields("#counter_increment_by",true);displayFields("label[for='counter_reset_interval']:eq(0)",true);displayFields("label[for='counter_reset_now']:eq(0)",true);displayFields("label[for='counter_reference_length_in_use']:eq(0)",true);
		if($("label[for='counter_reference_length_in_use']:eq(0)").parent().find("input:checked").val() == 1){displayFields("#counter_reference_length",true);}else{displayFields("#counter_reference_length",false);}
		if($("label[for='counter_reset_interval']:eq(0)").parent().find("input:checked").val() == 3){displayFields("#counter_reset_number",true);}else{displayFields("#counter_reset_number",false);}
		if($("label[for='counter_reset_interval']:eq(0)").parent().find("input:checked").val() == 4){displayFields("#counter_reset_date",true);}else{displayFields("#counter_reset_date",false);}
		if($("label[for='counter_reference_length_in_use']:eq(0)").parent().find("input:checked").val() == 1){
			nextCounterValue = displayReferenceLength($("#counter_reference_length option:selected").val(), counterActualValue);
		}else{
			nextCounterValue = counterActualValue;
		}
		$("#counter_start_at").parent().append("<p class=\'help-block\'>Next counter value: " + nextCounterValue + "</p>")
	}else{
		displayFields("#counter_start_at",false);displayFields("#counter_increment_by",false);displayFields("label[for='counter_reset_interval']:eq(0)",false);displayFields("label[for='counter_reset_now']:eq(0)",false);displayFields("label[for='counter_reference_length_in_use']:eq(0)",false);displayFields("#counter_reset_number",false);displayFields("#counter_reset_date",false);displayFields("#counter_reference_length",false);
	}
	$("#reference_number_format").bind("keyup change", function(e) {
		if(this.value.indexOf("{RANDOM_NUMBER}") > -1){displayFields("#random_number_length",true);}else{displayFields("#random_number_length",false);}
		if(this.value.indexOf("{RANDOM_ALPHABETIC}") > -1){displayFields("#random_alphabetic_length",true);}else{displayFields("#random_alphabetic_length",false);}
		if(this.value.indexOf("{RANDOM_ALPHANUMERIC}") > -1){displayFields("#random_alphanumeric_length",true);}else{displayFields("#random_alphanumeric_length",false);}
		if(this.value.indexOf("{COUNTER}") > -1){
			displayFields("#counter_start_at",true);displayFields("#counter_increment_by",true);displayFields("label[for='counter_reset_interval']:eq(0)",true);displayFields("label[for='counter_reset_now']:eq(0)",true);displayFields("label[for='counter_reference_length_in_use']:eq(0)",true);
			if($("label[for='counter_reference_length_in_use']:eq(0)").parent().find("input:checked").val() == 1){displayFields("#counter_reference_length",true);}else{displayFields("#counter_reference_length",false);}
			if($("label[for='counter_reset_interval']:eq(0)").parent().find("input:checked").val() == 3){displayFields("#counter_reset_number",true);}else{displayFields("#counter_reset_number",false);}
			if($("label[for='counter_reset_interval']:eq(0)").parent().find("input:checked").val() == 4){displayFields("#counter_reset_date",true);}else{displayFields("#counter_reset_date",false);}
		}else{
			displayFields("#counter_start_at",false);displayFields("#counter_increment_by",false);displayFields("label[for='counter_reset_interval']:eq(0)",false);displayFields("label[for='counter_reset_now']:eq(0)",false);displayFields("label[for='counter_reference_length_in_use']:eq(0)",false);displayFields("#counter_reset_number",false);displayFields("#counter_reset_date",false);displayFields("#counter_reference_length",false);
		}
	});
	$("label[for='counter_reference_length_in_use']:eq(0)").parent().find("input").click(function(){
		if($(this).is(":checked") && $(this).val() == 1){displayFields("#counter_reference_length",true);}else{displayFields("#counter_reference_length",false);}
	});
	$("label[for='counter_reset_interval']:eq(0)").parent().find("input").click(function(){
		if($(this).is(":checked") && $(this).val() == 3){displayFields("#counter_reset_number",true);}else{displayFields("#counter_reset_number",false);}
		if($(this).is(":checked") && $(this).val() == 4){displayFields("#counter_reset_date",true);}else{displayFields("#counter_reset_date",false);}
	});
	$("#counter_reference_length").bind("keyup change", function(){
		if(counterActualValue != ""){
			nextCounterValue = displayReferenceLength($(this).val(), counterActualValue);
			$("#counter_start_at").parent().find(".help-block:eq(1)").remove();
			$("#counter_start_at").parent().append("<p class=\'help-block\'>Next counter value: " + nextCounterValue + "</p>");
		}
	});
	$(".customreferencenumbers").on("submit", function(){
		errorMessage = "";
		if($("#reference_number_format").val().indexOf("{COUNTER}") > -1 && (!isNumber($("#counter_start_at").val()) || $("#counter_start_at").val() == "" || !isNumber($("#counter_increment_by").val()) || $("#counter_increment_by").val() == "" || $("#counter_increment_by").val() < 1 || $("#counter_start_at").val() < 1)){
			if(errorMessage.length > 0){
				errorMessage += ", "
			}
			errorMessage += numberReferenceFormatError;
		}
		if($("#reference_number_format").val().indexOf("{COUNTER}") > -1 && ($("label[for='counter_reset_interval']:eq(0)").parent().find("input:checked").val() == 3) && (!isNumber($("#counter_reset_number").val()) || $("#counter_reset_number").val() == "" || $("#counter_reset_number").val() < 1)){
			if(errorMessage.length > 0){
				errorMessage += ", "
			}
			errorMessage += counterResetNumberError;
		}
		if($("#reference_number_format").val().indexOf("{COUNTER}") > -1 && ($("label[for='counter_reset_interval']:eq(0)").parent().find("input:checked").val() == 4) && ($("#counter_reset_date").val() == "" || $("#counter_reset_date").val() == 0 || (counterResetDate.datepicker( "getDate" ) <= new Date()))){
			if(errorMessage.length > 0){
				errorMessage += ", "
			}
			errorMessage += counterResetDateError;
		}
		if($("#reference_number_format").val() == ""){
			errorMessage += numberReferenceFormatEmptyError;
		}
		if(errorMessage.length > 0){
			alert(errorMessage);
			return false;
		}
		return true;
	});
});
function displayFields(identification,isDisplay){
	if(isDisplay){
		$(identification).parents(".form-group").show();
	}else{
		$(identification).parents(".form-group").hide();
	}
}
function displayReferenceLength(counterReferenceLengthSelected, counterActualValue){
	nextCounterValue = "";
	if(counterReferenceLengthSelected > counterActualValue.toString().length){
		for (i = 0; i < (counterReferenceLengthSelected - counterActualValue.toString().length); i++){
			nextCounterValue += "0";
		}
	nextCounterValue += counterActualValue;
	}else{
		nextCounterValue += counterActualValue;
	}
	return nextCounterValue;
}
function isNumber(value) {
	if ((undefined === value) || (null === value)){
		return false;
	}
	if (typeof value == "number") {
		return true;
	}
	return !isNaN(value - 0);
}