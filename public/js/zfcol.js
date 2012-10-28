$(document).ready(function() {
	
    var oTable = $('#datatable').dataTable({
        "sPaginationType": "full_numbers"
	
    });
    oTable.$('tr:odd').css('backgroundColor', '#fff');
	
});