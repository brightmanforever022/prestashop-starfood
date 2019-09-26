

$(function(){
	var dateElem = '<div class="form-group">'
		+'<label class="control-label col-lg-3">'+noteReminderDateLabelTrns+'</label>'
		+'<div class="col-lg-9">'
		+'<input id="notereminder_date" class="datepicker" name="notereminder_date" type="text">'
		+'</div></div>';
	$('#txt_msg').parents('.form-group').after(dateElem);

});