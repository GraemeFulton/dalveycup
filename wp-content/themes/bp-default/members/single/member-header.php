<?php

/**
 * BuddyPress - Users Header
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php do_action( 'bp_before_member_header' ); ?>

<div id="item-header-avatar">
	<a href="<?php bp_displayed_user_link(); ?>">

		<?php bp_displayed_user_avatar( 'type=full' ); ?>

	</a>
</div><!-- #item-header-avatar -->

<div id="item-header-content">

	<h2>
		<a href="<?php bp_displayed_user_link(); ?>"><?php bp_displayed_user_fullname(); ?></a>
	</h2>

              <h4 class="header-membernumber">Membership Number: <?php do_action('print_user_membership_number')?></h4>

	<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>

	<?php do_action( 'bp_before_member_header_meta' ); ?>

<h3>
		<a style="background:rgba(0, 97, 48, 1); padding:5px;border-radius:8px;color:#eaeaea; border:1px solid #555555;float:left;"href="<?php bp_displayed_user_link(); ?>media"><?php bp_displayed_user_fullname(); ?>'s Gallery</a>
	</h3>

	<div id="item-meta">

		<?php if ( bp_is_active( 'activity' ) ) : ?>


		<?php endif; ?>

		<div id="item-buttons">

			<?php do_action( 'bp_member_header_actions' ); ?>

		</div><!-- #item-buttons -->

		<?php
		/***
		 * If you'd like to show specific profile fields here use:
		 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
		 */
		 do_action( 'bp_profile_header_meta' );

		 ?>

	</div><!-- #item-meta -->

</div><!-- #item-header-content -->

<?php do_action( 'bp_after_member_header' ); ?>
<?php if ( is_user_logged_in() && bp_is_my_profile() && ( !bp_current_action() || bp_is_current_action( 'just-me' ) ) )
	locate_template( array( 'activity/post-form.php'), true );?>
<?php do_action( 'template_notices' ); ?>