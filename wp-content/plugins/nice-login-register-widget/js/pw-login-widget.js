jQuery(document).ready(function($){
	
	
	$(".sp-flipping-link").click(function(){
		$(this).parents(".sp-main-div").children(".sp-login-header").hide().empty();
		$(this).parents('div [class*="sp-widget-"]').hide();
		var clicked = $(this).attr('href');
		switch (clicked){
		case '#sp-register':
			$(this).parents('div [class*="sp-widget-"]').siblings('.sp-widget-register-div').show();
			break;
		case '#lost-pass':
			$(this).parents('div [class*="sp-widget-"]').siblings('.sp-widget-lost_pass-div').show();
			break;
		case '#sp-login':
			$(this).parents('div [class*="sp-widget-"]').siblings('.sp-widget-login-div').show();
			break;
			default:
				return true;
		}
		return false;
	});
	
	
	
	//back-end widget options
	
	
	$("div.widgets-holder-wrap").on("click", ".sp-tabs-menu > li", function(){
		if ($(this).hasClass("sp-active")){
			return false;
		}
		$(this).addClass("sp-active");
		$(this).siblings("li").removeClass("sp-active");
		
		var sp_options_content = $(this).parents("div.sp-widget-back-end-div").children(".sp-options-content");
		sp_options_content.children("div").hide();
		if ($(this).children("span").hasClass("sp_switch_logged_out")){
			sp_options_content.find("div:nth-child(1)").show();
		}else if ($(this).children("span").hasClass("sp_switch_logged_in")){
			sp_options_content.find("div:nth-child(2)").show();
		}
	});
	
	$("div.widgets-holder-wrap").on("change", "input[id*='remember_me'] , input[id*='border']", function(){
		
			$(this).siblings("p").slideToggle();
		
	});
	
	$("div.widgets-holder-wrap").on("change", ".merge-tags-select", function(){
		$(this).next("textarea").val( $(this).next("textarea").val() + "{" + $(this).val() + "}" ) ;
		$(this).val('');
		return false;
	});
	
	
	
});

