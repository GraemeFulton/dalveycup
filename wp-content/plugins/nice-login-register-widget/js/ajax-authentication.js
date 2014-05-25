jQuery(document).ready(function($){
	

	$(".widget_pw_login_widget").on("click", "input[type='submit']", function(){
		
			
			var mainDiv = $(this).parents('.sp-main-div');
			if (mainDiv.length > 0 && mainDiv.children('.is_ajax_authentication').length==1){
				var hidden = mainDiv.children('.is_ajax_authentication');
				if (hidden.attr('value')=='0'){
					return true;
				}
			}
			
			var login_header = mainDiv.children('.sp-login-header');
			
			//ssl check
			var force_ssl_login = mainDiv.find(".force_ssl_login");
			if (force_ssl_login.length>0 && force_ssl_login.val()=='true' && document.location.protocol!='https:' ){
				login_header.children(".sp-ssl-requires-msg").slideDown(100);
				return false;
			}
			
			var loadingDiv = mainDiv.children(".sp-loading-img");
			loadingDiv.show();
			mainDiv.children("div:not('.sp-loading-img')").css('opacity', '0.4');
			var form = $(this).parents("form");

			login_header.hide();
			login_header.empty();
			
			var inputs = form.find('input');
			if (inputs.length > 0){
				var jsonObj = { };
				var redirect_exec = false;
				inputs.each(function(index){
					var name = $(this).attr('name');
					jsonObj[name] = $(this).val();
					
				});
				$.post(ajax_object.ajax_url, jsonObj, function(data) {
					
					//nonce check
					if (data=='-1'){
						login_header.html("<div id='login_error'>"+pwLogWi_messages.ajax_request_fails+"</div>");
						return;
					}
					
					var JSon;
					
					try{
						JSon = $.parseJSON(data);
					}catch(e){
						login_header.html("<div id='login_error'>"+pwLogWi_messages.ajax_unknown_error+"</div>");
						return;
					}
					
					
					if (data=='0' || JSon.operation == 'redirect'){
						redirect_exec = true;
						//location.href = JSon.redirect_to;
						location.reload(true);
						
					}else{
						login_header.html(JSon.message);
					}
					
					
				}).always(function(){
					if (!redirect_exec){
						loadingDiv.hide();
						login_header.slideDown(100);
						mainDiv.children("div:not('.sp-loading-img')").css('opacity', '1');
					}
				});
				
			}
			return false;
			
		});

});