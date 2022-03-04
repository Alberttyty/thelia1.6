function updateProgess(){
	if($('#ebp_progress input[name="debut"]').val()>0){
		$.ajax({
			type:"POST",
			data: $('#ebp_progress').serialize(),
			url:$('#ebp_progress').attr('action'),
			success: function(data){$("#progress").html($(data).find('#progress').html());updateProgess();}
	        });
	}
}

$(document).ready(function(){
	
	updateProgess();
	
});