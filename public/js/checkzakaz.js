$('#checkzakaz_submit').on('click', checkzakaz);
function checkzakaz(event){
		event.preventDefault();
		var zakazNum=$('#checkzakaz_input').val();
		$.post('/page/status', {zakaznumber:zakazNum}, checkzakaz_susses);
}

function checkzakaz_susses(data){
	if(data){
		
		$('#body-container').html('');
		$('#body-container').html(data);
            
	}
}