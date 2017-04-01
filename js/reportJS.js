 /* Sales Per Day Total Row Handler */
 var total= 0;
        $(document).ready(function(){

            var $dataRows=$("#sum_table_raport1 tr:not('.totalRand, .randTitlu')");

            $dataRows.each(function() {
                $(this).find('.randTotal').each(function(i){        
                    total+=parseFloat( $(this).html());
                });
            });
            $("#sum_table_raport1 th.totalul").each(function(i){  
                $(this).html(total+" RON");
            });

        });
		
/* Sales Per Day DatePicker Settings */
$('.form_datetime_raport1').datetimepicker({
        language:  'ro',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 0,
        showMeridian: 1,
		minView: 3
    });
	
