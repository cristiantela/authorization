
	var logged = "<?php echo json_encode($loggedUser); ?>";
	if(logged == "false"){
		$('#formulario').fadeIn();
	}else if(logged == "true"){
		$('#usercard').fadeIn();
	}
