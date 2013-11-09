<?php


//=============================================
// Define constants
//=============================================
if (!defined('INBOUND_FORMS')) {
    define('INBOUND_FORMS', plugin_dir_url(__FILE__));
}
if (!defined('INBOUND_FORMS_PATH')) {
    define('INBOUND_FORMS_PATH', plugin_dir_path(__FILE__));
}
if (!defined('INBOUND_FORMS_BASENAME')) {
    define('INBOUND_FORMS_BASENAME', plugin_basename(__FILE__));
}
if (!defined('INBOUND_FORMS_ADMIN')) {
    define('INBOUND_FORMS_ADMIN', get_bloginfo('url') . "/wp-admin");
}

if (!defined('INBOUND_LABEL')) {
define( 'INBOUND_LABEL', str_replace( ' ', '_', strtolower( 'Inbound Now' ) ) );
}

require_once( 'shortcodes-includes.php' );

/*  InboundNow Shortcodes Class
 *  --------------------------------------------------------- */
if (!class_exists('InboundShortcodes')) {
class InboundShortcodes {
  static $add_script;
/*  Contruct
 *  --------------------------------------------------------- */
  static function init() {
    self::$add_script = true;
    add_action('admin_enqueue_scripts', array( __CLASS__, 'loads' ));
    add_action('init', array( __CLASS__, 'shortcodes_tinymce' ));
    add_action( 'wp_enqueue_scripts',  array(__CLASS__, 'frontend_loads')); // load styles
  }

/*  Loads
 *  --------------------------------------------------------- */
  static function loads($hook) {
    global $post;
    if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'page-new.php' || $hook == 'page.php' ) {
      wp_enqueue_style('inbound-shortcodes', INBOUND_FORMS.'css/shortcodes.css');
      wp_enqueue_script('jquery-ui-sortable' );
      wp_enqueue_script('inbound-shortcodes-plugins', INBOUND_FORMS . 'js/shortcodes-plugins.js');
      wp_enqueue_script('inbound-shortcodes', INBOUND_FORMS . 'js/shortcodes.js');
      wp_localize_script( 'inbound-shortcodes', 'inbound_shortcodes', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'inbound_shortcode_nonce' => wp_create_nonce('inbound-shortcode-nonce') ) );
      // Forms CPT only
      if (  ( isset($post) && 'inbound-forms' == $post->post_type ) || ( isset($_GET['post_type']) && $_GET['post_type']=='inbound-forms' ) ) {
         wp_enqueue_style('inbound-forms-css', INBOUND_FORMS . 'css/form-cpt.css');
         wp_enqueue_script('inbound-forms-cpt-js', INBOUND_FORMS . 'js/form-cpt.js');
         wp_localize_script( 'inbound-forms-cpt-js', 'inbound_forms', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'inbound_shortcode_nonce' => wp_create_nonce('inbound-shortcode-nonce'), 'form_cpt' => 'on' ) );
      }
      // Check for active plugins and localize
      $plugins_loaded = array();
      if (is_plugin_active('landing-pages/landing-pages.php')) {
      array_push($plugins_loaded, "landing-pages");
      }
      if (is_plugin_active('cta/wordpress-cta.php')) {
      array_push($plugins_loaded, "cta");
      }
      if (is_plugin_active('leads/wordpress-leads.php')) {
      array_push($plugins_loaded, "leads");
      }
      wp_localize_script( 'inbound-shortcodes', 'inbound_load', array( 'image_dir' => INBOUND_FORMS, 'inbound_plugins' => $plugins_loaded, 'pop_title' => 'Insert Shortcode' ));
      if (isset($post)&&$post->post_type=='inbound-forms')
      {
      require_once( 'shortcodes-fields.php' );
      add_action( 'admin_footer',  array(__CLASS__, 'inbound_forms_header_area'));

      }

      //add_action('admin_head', array( __CLASS__, 'shortcodes_admin_head' ));
    }
  }

  static function frontend_loads() {
      wp_enqueue_style('inbound-shortcodes', INBOUND_FORMS.'css/frontend-render.css');
  }

// Currently off
  static function shortcodes_admin_head() { ?>
  <script type="text/javascript">
  /* <![CDATA[ */
  // Load inline scripts var freshthemes_theme_dir = "<?php // echo INBOUND_FORMS; ?>", test = "<?php // _e('Insert Shortcode', INBOUND_LABEL); ?>";
  /* ]]> */
  </script>
 <?php }

/*  TinyMCE
 *  --------------------------------------------------------- */
  static function shortcodes_tinymce() {
    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
      return;

    if ( get_user_option('rich_editing') == 'true' ) {
      add_filter( 'mce_external_plugins', array( __CLASS__, 'add_rich_plugins' ) );
      add_filter( 'mce_buttons', array( __CLASS__, 'register_rich_buttons' ) );
    }
  }

  static function add_rich_plugins( $plugins ) {
    $plugins['InboundShortcodes'] = INBOUND_FORMS . 'js/tinymce.js';
    return $plugins;
  }

  static function register_rich_buttons( $buttons ) {
    array_push( $buttons, "|", 'InboundShortcodesButton' );
    return $buttons;
  }

  static function inbound_forms_header_area()
  {
    global $post;
    $post_id = $post->ID;
    $post_title = get_the_title( $post_id );
    $popup = trim(get_post_meta($post->ID, 'inbound_shortcode', true));
    $form_serialize = get_post_meta($post->ID, 'inbound_form_values', true);
    $short_shortcode = "";
    $shortcode = new InboundShortcodesFields( 'forms' );

      if ( empty ( $post ) || 'inbound-forms' !== get_post_type( $GLOBALS['post'] ) )
          return; ?>
  <div id="entire-form-area">
  <div id="cpt-form-shortcode"><?php echo $popup;?></div>
  <div id="cpt-form-serialize"><?php echo $form_serialize;?></div>
   <div id="short_shortcode_form">
    Shortcode: <input type="text" class="regular-text code short-shortcode-input" readonly="readonly" id="shortcode" name="shortcode" value='[inbound_forms id="<?php echo $post_id;?>" name="<?php echo $post_title;?>"]'>
   </div>
      <div id="inbound-shortcodes-popup">

          <div id="inbound-shortcodes-wrap">
              <div id="inbound-shortcodes-form-wrap">
                  <div id="inbound-shortcodes-form-head">
                      <?php echo $shortcode->popup_title; ?>
                      <?php $shortcode_id = strtolower(str_replace(array(' ','-'),'_', $shortcode->popup_title));  ?>
                  </div>
                  <form method="post" id="inbound-shortcodes-form">
                      <input type="hidden" id="inbound_current_shortcode" value="<?php echo $shortcode_id;?>">
                      <table id="inbound-shortcodes-form-table">
                          <?php echo $shortcode->output; ?>
                          <tbody style="display:none;">
                              <tr class="form-row" style="text-align: center;">
                                  <?php if( ! $shortcode->has_child ) : ?><td class="label">&nbsp;</td><?php endif; ?>
                                  <td class="field" style="width:500px;"><a href="#" id="inbound_insert_shortcode" class="button-primary inbound-shortcodes-insert"><?php _e('Insert Shortcode', INBOUND_LABEL); ?></a></td>
                              </tr>
                          </tbody>
                      </table>
                  </form>
              </div>

              <div id="inbound-shortcodes-preview-wrap">
                  <div id="inbound-shortcodes-preview-head">
                      <?php _e('Shortcode Preview', INBOUND_LABEL); ?>
                  </div>
                  <?php if( $shortcode->no_preview ) : ?>
                      <div id="inbound-shortcodes-nopreview"><?php _e('Shortcode has no preview', INBOUND_LABEL); ?></div>
                  <?php else : ?>
                      <iframe src='<?php echo INBOUND_FORMS; ?>preview.php?sc=' width="285" scrollbar='true' frameborder="0" id="inbound-shortcodes-preview"></iframe>
                  <?php endif; ?>
              </div>
              <div class="clear"></div>
          </div>

      </div>
      <div id="popup-controls">
          <a href="#" id="inbound_insert_shortcode_two" class="button-primary inbound-shortcodes-insert-two"><?php _e('Insert Shortcode', INBOUND_LABEL); ?></a>
          <a href="#" id="shortcode_cancel" class="button inbound-shortcodes-insert-cancel">Cancel</a>
          <a href="#" id="inbound_save_form" style="display:none;" class="button">Save As New Form</a>
      </div>
    </div>

      <script type="text/javascript">
      jQuery(document).ready(function($) {

          jQuery('.child-clone-row').first().attr('id', 'row-1');
          setTimeout(function() {
                  jQuery('#inbound-shortcodes-form input:visible').first().focus();
          }, 500);

      //jQuery("body").on('click', '.child-clone-row', function () {
         // jQuery(".child-clone-row").toggle();
         // jQuery(this).show();
      //});
      });
  </script>

      <?php
  }
}
}
/*  Initialize InboundNow Shortcodes
 *  --------------------------------------------------------- */
InboundShortcodes::init();

?>