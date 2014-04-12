	function on_login_complete(success){
		//var success = tera.result;
		if(success == "success"){
			$('#login-title').removeClass('red');
			$('#login-title').removeClass('black');
			$('#login-title').addClass('green');
			$('#login-failure').css('opacity', '0');

			setTimeout(function(){
				//called when success
				proceed_after_login();
			}, 200);
		}
		else if(success == "unknown_account"){
			//do some red-flavored alert
			display_error("ID or password does not match.");
		}
		else if(success == "hacker"){
			//I hate these people.
			display_error("Injection detected. I will ban your IP :)");
		}
		else{
			display_error("Unknown error. please contact to me :)");
		}
	}
	function display_error(text){
		$('#login-title').addClass('red');
		$('#login-title').removeClass('black');
		$('#login-failure').css('opacity', '1');
		$('#login-failure').text(text);	
	}

	function proceed_after_login(){
		$('#login-wrap').fadeOut(400, function(){
			$('#login-wrap').remove();
			$.ajax({
				url: '/fragments/filelist.php',
				success: function(result){
					var ro = $(result);
					var ob = $('#background');
            		ro.hide().appendTo("body").fadeIn(4000, function(){
            			ob.remove();
            		});
				}
			});
		});
	}
	function login(){
		//make an AJAX call to core
		var form_data = $('#login-form').serialize();
		$.ajax({
			type:"POST",
			url:"./_core/",
			data:form_data,
			success:on_login_complete,
			error:function(){
				display_error("Unknown network error occurred.");
			}
		});
	}

	$(document).ready(function(){
		$('input[type=password]').keyup(function(e){
			//enter is 13
			if(e.keyCode == 13)
				login();
		});

		$('#btn-login').click(function(){
			login();
		});
		$('#btn-register').click(function(){
			window.location.href = "./?register";
		});
	});