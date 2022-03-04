$(document).ready(function() {
	
	Date.format = 'yyyy-mm-dd';
	$('.date').datePicker({createButton:false,startDate:'1996-01-01'})
	.bind(
		'dateSelected',
		function(e,selectedDate,td)
		{
			var date = new Date(selectedDate);
			date.setHours(12); 
			var cible = $(this).attr('for');
			if($('#'+cible).val()!=date.toISOString().substr(0,10)){
				$('#'+cible).val(date.toISOString().substr(0,10));
				$(this).closest('form').submit();	
			}
		}
	)
	.bind(
			'click',
			function()
			{
				var cible = $(this).attr('for');
				$(this).dpSetSelected($('#'+cible).val());
				$(this).dpDisplay();
				//this.blur();
				return false;
			}
	);
		/*.bind(
			'click',
			function()
			{
				$(this).dpDisplay();
				this.blur();
				return false;
			}
		).bind('change',function(){
			$(this).closest('form').find('input').each(function(index,element) {
            	var madate=$(element).val().split("/");
				$(element).val("20"+madate[2]+"-"+madate[1]+"-"+madate[0]);    
            });
			$(this).closest('form').submit();
		});*/
	
});