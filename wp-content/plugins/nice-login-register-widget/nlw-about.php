<?php

delete_transient( 'nlw_teaser_page_redirect' );
?>
<link href='http://fonts.googleapis.com/css?family=Exo' rel='stylesheet' type='text/css'>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<style>

#sp-wrapper{
	font-size: 13px;
	/*font-family: 'Exo', sans-serif;*/
	max-width: 1080px;
	min-height: 600px;
	margin: 30px auto;
	border: 1px solid #D9D9D9;
	border-radius: 5px;
	/* IE10 Consumer Preview */ 
	background-image: -ms-linear-gradient(top, #D9D9D9 0%, #FFFFFF 250px, #D9D9D9 300%);
	
	/* Mozilla Firefox */ 
	background-image: -moz-linear-gradient(top, #D9D9D9 0%, #FFFFFF 250px, #D9D9D9 300%);
	
	/* Opera */ 
	background-image: -o-linear-gradient(top, #D9D9D9 0%, #FFFFFF 250px, #D9D9D9 300%);
	
	/* Webkit (Safari/Chrome 10) */ 
	background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #D9D9D9), color-stop(0.2, #FFFFFF), color-stop(3, #D9D9D9));
	
	/* Webkit (Chrome 11+) */ 
	background-image: -webkit-linear-gradient(top, #D9D9D9 0%, #FFFFFF 250px, #D9D9D9 300%);
	
	/* W3C Markup, IE10 Release Preview */ 
	background-image: linear-gradient(to bottom, #D9D9D9 0%, #FFFFFF 250px, #D9D9D9 300%);
	
	position: relative;
}
#sp-wrapper h3{
	font-family: 'Exo', sans-serif;
	line-height: 14px;
	font-size: 17px;
	color: #D98A1C;
}
#logo{
	text-align: center;
	border-bottom: 1px dashed #3FA9F5;
	margin: 0 15px;	
}
#teaser-content, .sp-footer{
	padding: 20px 10px;
}
.sp-footer{
	border-top: 1px dashed #3FA9F5;
	margin: 0 15px;
}
#main-features li{
	float: left;
	width: 30%;
	padding-right: 34px;
	height: 65px;
	margin-left: 8px;
}
#main-features li.third-col{
	padding-right: 0;
}
#main-features li i{
	padding: 0 5px;
	font-size: 2em;
	line-height: 0.75em;
	color: #D98A1C;
}

.key-features{
	margin: 57px 0;
}
.key-features > .feature{
	float: left;
	width: 32%;
	margin-right: 10px;
}
.key-features > #feature-3{
	margin-right: 0;
}
.key-features > .feature i{
	vertical-align: top;
	color: #3FA9F5;
}
.key-features > .feature h3{
	margin-top: 3px;
}
.feature-content{
	display: inline-block;
	width: 75%;
	margin-left: 8px;
}
#superplugin{
	text-align: center;
}
#superplugin img{
	width: 250px;
	height: auto;
}
#lwp-price{
	line-height: 25px;
	font-size: 21px;
	text-align: center;
	padding: 5px;
	position: absolute;
	right: 100px;
	top: 30px;
	width: 100px;
	border: 4px solid #3FA9F5;
	border-radius: 50%;
	transform: rotate(18deg) ;
	-webkit-transform: rotate(18deg) ;
	-moz-transform: rotate(18deg) ;
	-o-transform: rotate(18deg) ;
	-ms-transform: rotate(18deg) ;
}
#lwp-price h3{
	line-height: 27px;
	font-size: 23px;
}
.get-it-now{
	text-align: center;
}
.get-it-now a{
	background: #3FA9F5;
	background-image: -webkit-gradient(linear,left top,left bottom,from(#3FA9F5),to(#f4f4f4));
	background-image: -webkit-linear-gradient(top,#3FA9F5,#f4f4f4);
	background-image: -moz-linear-gradient(top,#3FA9F5,#f4f4f4);
	background-image: -o-linear-gradient(top,#3FA9F5,#f4f4f4);
	background-image: linear-gradient(to bottom,#3FA9F5,#f4f4f4);
	border-color: #bbb;
	color: #333;
	text-shadow: 0 1px 0 #fff;
	font-family: 'Exo', sans-serif;
	line-height: 18px;
	font-size: 20px;
	padding: 10px;
	display: inline-block;
	border: 1px solid #D9D9D9;
	text-decoration: none;
	border-radius: 4px;
}

#sp-wrapper{font-size:13px;max-width:1080px;min-height:600px;margin:30px auto;border:1px solid #d9d9d9;border-radius:5px;background-image:-ms-linear-gradient(top,#d9d9d9 0,#fff 250px,#d9d9d9 300%);background-image:-moz-linear-gradient(top,#d9d9d9 0,#fff 250px,#d9d9d9 300%);background-image:-o-linear-gradient(top,#d9d9d9 0,#fff 250px,#d9d9d9 300%);background-image:-webkit-gradient(linear,left top,left bottom,color-stop(0,#d9d9d9),color-stop(0.2,#fff),color-stop(3,#d9d9d9));background-image:-webkit-linear-gradient(top,#d9d9d9 0,#fff 250px,#d9d9d9 300%);background-image:linear-gradient(to bottom,#d9d9d9 0,#fff 250px,#d9d9d9 300%);position:relative}#sp-wrapper h3{font-family:'Exo',sans-serif;line-height:14px;font-size:17px;color:#d98a1c}#logo{text-align:center;border-bottom:1px dashed #3fa9f5;margin:0 15px}#teaser-content,.sp-footer{padding:20px 10px}.sp-footer{border-top:1px dashed #3fa9f5;margin:0 15px}#main-features li{float:left;width:30%;padding-right:34px;height:65px;margin-left:8px}#main-features li.third-col{padding-right:0}#main-features li i{padding:0 5px;font-size:2em;line-height:.75em;color:#d98a1c}.key-features{margin:57px 0}.key-features>.feature{float:left;width:32%;margin-right:10px}.key-features>#feature-3{margin-right:0}.key-features>.feature i{vertical-align:top;color:#3fa9f5}.key-features>.feature h3{margin-top:3px}.feature-content{display:inline-block;width:75%;margin-left:8px}#superplugin{text-align:center}#superplugin img{width:250px;height:auto}#lwp-price{line-height:25px;font-size:21px;text-align:center;padding:5px;position:absolute;right:100px;top:30px;width:100px;border:4px solid #3fa9f5;border-radius:50%;transform:rotate(18deg);-webkit-transform:rotate(18deg);-moz-transform:rotate(18deg);-o-transform:rotate(18deg);-ms-transform:rotate(18deg)}#lwp-price h3{line-height:27px;font-size:23px}.get-it-now{text-align:center}.get-it-now a{background:#3fa9f5;background-image:-webkit-gradient(linear,left top,left bottom,from(#3fa9f5),to(#f4f4f4));background-image:-webkit-linear-gradient(top,#3fa9f5,#f4f4f4);background-image:-moz-linear-gradient(top,#3fa9f5,#f4f4f4);background-image:-o-linear-gradient(top,#3fa9f5,#f4f4f4);background-image:linear-gradient(to bottom,#3fa9f5,#f4f4f4);border-color:#bbb;color:#333;text-shadow:0 1px 0 #fff;font-family:'Exo',sans-serif;line-height:18px;font-size:20px;padding:10px;display:inline-block;border:1px solid #d9d9d9;text-decoration:none;border-radius:4px}

</style>
<div id="sp-wrapper">

<div id="logo"><a href="http://superplug.in/login-widget-pro/?utm_source=login_widget_pro&utm_medium=nlw_teaser&utm_campaign=LWP" title="Login Widget Pro" target="_blank">
<img class="" alt="login widget pro for wordpress" src="http://superplug.in/wp-content/uploads/2013/11/BannerNLW.png" ></a>
</div>
<div id="lwp-price"><h3>Starts At $19</h3></div>

<div id="teaser-content">
<div id="sp-intro">
<h3>Thanks for choosing Nice Login Widget V 1.3.9</h3>
<p>This free version includes the essential features for effectively contorlling login and registration process on your WordPress site, such as sidebar widget, shortcode based forms and more. <br>
We do our best to review the support forum on WordPress.org and help our users with technical issues. We'll appreciate your rating and review.<br>
If you want to have control over the entire process, customzie the look and feel and attract your users to register to your site, you may want to have a look at the premium version of this plugin, introduced below.</p>
<p>Thank you for your support and for using Nice Login Widget<br>
The SuperPlugin Team</p>
<h3>Upgrade To The Pro Version</h3>
<p>We are thrilled to introduce you with the latest and greatest plugin for turning your visitors to users.<br>
If you operate a website, you should know how hard it is to grab busy visitors’ attention and make them engaged, let alone committed, to your site.<br>
Getting users to come back to your site is the first step in creating a long term and mutually beneficial relationship between you and your visitors.
<br>Such a first step may be registration to your site, which could lead to engaging with your blog, social media and ultimately contacting you or making an online purchase.
</p>
</div>

<div class="get-it-now">
<a href="http://superplug.in/login-widget-pro/?utm_source=login_widget_pro&utm_medium=nlw_teaser&utm_campaign=LWP" target="_blank" >Get The Pro!</a>
</div>

<div class="key-features">
<div id="feature-1" class="feature"><i class="fa fa-users fa-5x"></i>
<div class="feature-content"><h3>Engage Your Users</h3>
Use login widget in any widget area, as a popup and inside content as a shortcode</div>
</div>
<div id="feature-2" class="feature"><i class="fa fa-list-alt fa-5x"></i>
<div class="feature-content"><h3>Actually Use Your User’s List</h3>
Add newsletter subscription option to your registration form, import your users list or just your newsletter subscribers as a .csv file and import them to Mailchimp or any other newsletter service. You can also toggle users preferences and turn non registered users to registered and the other way around. Have users verify their email address and avoid spam registrations.</div>
</div>
<div id="feature-3" class="feature"><i class="fa fa-edit fa-5x"></i>
<div class="feature-content"><h3>Make it Yours</h3>
Control CSS, animation, titles, set custom redirect pages for newly registered users and returning visitors upon login, customize email welcome message with rich text editing.</div>
</div>
<div class="clear"></div>
</div>

<h3>Main Features</h3>
<ul id="main-features" class="fa-ul">
<li><i class="fa-li fa fa-check-square-o fa-5x"></i>Use login widget in any widget area, as a popup and inside content as a shortcode</li>
<li><i class="fa-li fa fa-check-square-o"></i>Trigger popup with a link using a simple class. Once clicked, a popup with the login form inside will appear.</li>
<li class="third-col"><i class="fa-li fa fa-check-square-o"></i>Trigger popup with a menu item. Once clicked, a popup with the login form inside will appear.</li>
<li><i class="fa-li fa fa-check-square-o"></i>Enable/disable Gravatar for logged in users.</li>
<li><i class="fa-li fa fa-check-square-o"></i>Set custom password retrieval page</li>
<li class="third-col"><i class="fa-li fa fa-check-square-o"></i>Set custom email validation page</li>
<li><i class="fa-li fa fa-check-square-o"></i>Set custom form transition effects (only in advanced browsers): regular transition, horizontal rotation, vertical rotation, fade-in/fade-out</li>
<li><i class="fa-li fa fa-check-square-o"></i>Set popup width in pixels</li>
<li class="third-col"><i class="fa-li fa fa-check-square-o"></i>Set custom login button form once logged in: leave as-is/hide login button/replace with login text</li>
<li><i class="fa-li fa fa-check-square-o"></i>Five color schemes: white, light-grey, dark-grey, blue sky and inherit (theme’s CSS takes over) – more styles will be added in the coming versions</li>
<li><i class="fa-li fa fa-check-square-o"></i>Place login widget anywhere inside content using a shortcode</li>
<li class="third-col"><i class="fa-li fa fa-check-square-o"></i>Export users: choose to export all users or just those who subscribed to your newsletter in .csv format to your local machine. Use it for various purposes: import to your newsletter software, CRM etc.</li>
<li><i class="fa-li fa fa-check-square-o"></i>Enable/disable user subscription to your newsletter in login form</li>
<li><i class="fa-li fa fa-check-square-o"></i>Enable checkbox for terms and conditions upon registration</li>
<li class="third-col"><i class="fa-li fa fa-check-square-o"></i>Allow/don’t allow using email as username</li>
<li><i class="fa-li fa fa-check-square-o"></i>Customize your welcome email using built-in rich text editor</li>
<li><i class="fa-li fa fa-check-square-o"></i>Block parts of your content and make available for logged in users only</li>
</ul>
<div class="clear"></div>

<h3>Expect for More</h3>
<p>We are already hard at work on putting more wisdom and power into Login Widget Pro, with the lisence, you get 1 year of future updates and upgrades, and premium support</p>

<div class="get-it-now">
<a href="http://superplug.in/login-widget-pro/?utm_source=login_widget_pro&utm_medium=nlw_teaser&utm_campaign=LWP" target="_blank" >Get The Pro!</a>
</div>

</div>
<div class="sp-footer">
<div id="superplugin"><a href="http://superplug.in" title="SuperPlugin Home"><img src="http://superplug.in/wp-content/uploads/sites/1/2013/09/logo.png" alt=""></a></div>
<div class="clear"></div>
</div>
</div>