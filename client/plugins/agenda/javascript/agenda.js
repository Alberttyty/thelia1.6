$(document).ready(function() {
	$('.date')
		.datePicker({createButton:false})
		.bind(
			'click',
			function()
			{
				$(this).dpDisplay();
				this.blur();
				return false;
			}
		);
	$('input[name="datedebut"]').bind(
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if (d) {
				d = new Date(d);
				$('input[name="datefin"]').dpSetStartDate(d.addDays(0).asString());
			}
		}
	);
	$('input[name="datefin"]').bind(
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if (d) {
				d = new Date(d);
				$('input[name="datedebut"]').dpSetEndDate(d.addDays(0).asString());
			}
		}
	);
});