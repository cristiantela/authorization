<?php
	include_once 'Authorization.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Authorization | Mathues</title>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<style type="text/css">
		.formulario{
			width:100%;
			max-width: 330px;
			padding: 15px;
			margin-top: 180px;
		}
		.formulario input#iUser{
			margin-bottom: -1px;
			border-bottom-right-radius: 0;
			border-bottom-left-radius: 0;
		}
		.formulario input#iPassword{
			margin-bottom: 10px;
			border-top-left-radius: 0;
			border-top-right-radius: 0;
		}

	</style>
</head>
<body class="text-center" style="background-color: #f5f5f5;">
	<!-- Card User -->
	<div id="usercard" class="card mx-auto" style="margin-top: 110px;width: 300px; display: none;">
		<div class="card-header">
			<img class="card-img" src="https://www.w3schools.com/bootstrap4/img_avatar3.png">
		</div>
		<div class="card-body">
			<h3 id="username">Lorem Ipsum</h3>
		</div>
		<div class="card-footer">
			<form action="<?php$user?>">
				<button id="logoutButton" type="button" class="btn btn-danger">Deslogar</button>
			</form>
		</div>
	</div>

	<!-- Login Form -->
	<form id="formulario" style="display: none;" class=" mx-auto formulario">
		<h1 class="h3 mb-3 font-weight-normal">Login</h1>
		<label for="iUser" class="sr-only">Your Username</label>
		<input type="text" name="user" id="iUser" class="form-control" placeholder="User" required autofocus>
		<label for="iPassword" class="sr-only">Password</label>
		<input class="form-control" type="password" name="password" id="iPassword" placeholder="Password" required>
		<button id="loginButton" class="btn btn-primary btn-block mt-4" type="submit">Login</button>
	</form>

	<!-- Code Area -->

	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script>
		//Function getCookie by W3School
		function getCookie(cname) {
		  var name = cname + "=";
		  var decodedCookie = decodeURIComponent(document.cookie);
		  var ca = decodedCookie.split(';');
		  for(var i = 0; i <ca.length; i++) {
		    var c = ca[i];
		    while (c.charAt(0) == ' ') {
		      c = c.substring(1);
		    }
		    if (c.indexOf(name) == 0) {
		      return c.substring(name.length, c.length);
		    }
		  }
		  return "";
		}
		function PageLogin () {
			$('#usercard').fadeOut();
			$('#formulario').fadeIn();
		}
		function PageHome () {
			loadUser(getCookie("user"))
			$('#formulario').fadeOut();
			$('#usercard').fadeIn();
		}
		function loadUser(user){
			$('#username').text(user.charAt(0).toUpperCase()+user.slice(1));
		}
		function toggleForm(){
			$('#formulario').fadeOut();
		}
		function checkUser(){
			let token = getCookie("_session")

			if(token == ""){
				$('#formulario').fadeIn();
			}else{
				$.ajax({
					url: 'login.php',
					type: 'POST',
					data: {
						action: "verifyToken"
					},
					success: function(response){
						if(response=="1"){
							PageHome()
						}else{
							PageLogin()
						}
					}
				})
			}
		}
		$(document).ready(function(){
			checkUser();
			$('#formulario').submit(function(event){
				event.preventDefault()
				$.ajax({
					url: 'login.php',
					type: 'POST',
					data: {
						action: "login",
						user: $('#iUser').val(),
						password: $('#iPassword').val()
					},
					success:function(response){
						response = JSON.parse(response)

						if (response.error) {
							alert(response.error)
							return false
						}
						PageHome()
					}
				});
			});
			$('#logoutButton').click(function() {
				$.ajax({
					url: 'login.php',
					type: 'POST',
					data: {
						action: "logout"
					},
					success:function(response){
						PageLogin()
					}
				});
			});
		});
	</script>
</body>
</html>