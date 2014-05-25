<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
		<?php if ( current_theme_supports( 'bp-default-responsive' ) ) : ?><meta name="viewport" content="width=device-width, initial-scale=1.0" /><?php endif; ?>
		<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<?php bp_head(); ?>
		<?php wp_head(); ?>
                <?php header("HTTP/1.1 200 OK");?>
	</head>

	<body <?php body_class(); ?> id="bp-default">
      <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
<a class="navbar-brand" href="<?php bloginfo('url')?>"><?php bloginfo('name')?></a>        </div>
        <div class="navbar-collapse collapse">
         <?php /* Primary navigation */
   wp_nav_menu( array(
        'menu'              => 'Dalvey',
        'theme_location'    => 'primary',
        'depth'             => 2,
        'container'         => false,
        'container_class'   => 'collapse navbar-collapse navbar-ex1-collapse',
        'menu_class'        => 'nav navbar-nav',
        'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
        'walker'            => new wp_bootstrap_navwalker())
    );
if ( bp_loggedin_user_id() ) : 
    echo '<a id="my-profile-button"class="button" href="'.bp_loggedin_user_domain().'profile">My Profile</a>';
	echo '&nbsp;&nbsp;<a id="my-profile-button"class="button" href="'.home_url().'/log-out">Log Out</a>';
	else:
	echo '<a id="my-profile-button"class="button" href="'.home_url().'/login">Member Login</a>';
	echo '&nbsp;&nbsp;<a id="my-profile-button"class="button" href="'.home_url().'/register">Register</a>';
	
	endif;
?>
          
			<div id="search-bar" role="search">
                            <div id="main_search">
                            <?php get_search_form(); ?>
                            </div>
			</div><!-- #search-bar -->
			<br>
			
        </div><!--/.nav-collapse -->
      </div>
    </div>
		<?php do_action( 'bp_before_header' ); ?>

		<div id="header">
		<?php if(is_front_page() ) {
            //echo do_shortcode("[metaslider id=31]"); 
		}?>       
		</div><!-- #header -->

		<?php do_action( 'bp_after_header'     ); ?>
		<?php do_action( 'bp_before_container' ); ?>

		<div id="container">
