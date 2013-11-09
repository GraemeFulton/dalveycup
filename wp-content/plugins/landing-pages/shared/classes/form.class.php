<?php

/**
 * Creates Form Shortcode
 */

/*
Usage
[inbound_form fields="First Name, Last Name, Email, Company, Phone" required="Company" textareas="Company"]
*/

//=============================================
// Define constants
//=============================================

if (!class_exists('InboundForms')) {
class InboundForms {
    static $add_script;
    //=============================================
    // Hooks and Filters
    //=============================================
    static function init()  {
        add_shortcode('inbound_form', array(__CLASS__, 'inbound_forms_create'));
        add_action('init', array(__CLASS__, 'register_script'));
        add_action('wp_footer', array(__CLASS__, 'print_script'));
        add_action('wp_footer', array(__CLASS__, 'inline_my_script'));
        add_action( 'init',  array(__CLASS__, 'send_email'));
    }

    // Shortcode params
    static function inbound_forms_create( $atts, $content = null ) { {
    self::$add_script = true;
    $email = get_option('admin_email');
    extract(shortcode_atts(array(
      'id' => '',
      'name' => '',
      'layout' => '',
      'notify' => $email,
      'labels' => '',
      'width' => '',
      'redirect' => '',
      'submit' => 'Submit'
    ), $atts));

    $form_name = $name;
    $form_layout = $layout;
    $form_labels = $labels;
    $form_labels_class = (isset($form_labels)) ? "inbound-label-".$form_labels : 'inbound-label-inline';
    $submit_button = ($submit != "") ? $submit : 'Submit';


    // Check for image in submit button option
    if (preg_match('/\.(jpg|jpeg|png|gif)(?:[\?\#].*)?$/i',$submit_button)) {
      $submit_button_type = 'style="background:url('.$submit_button.') no-repeat;color: rgba(0, 0, 0, 0);border: none;box-shadow: none;';
    } else {
      $submit_button_type = '';
    }

    /* Sanitize width input */
    if (preg_match('/px/i',$width)) {
      $fixed_width = str_replace("px", "", $width);
        $width_output = "width:" . $fixed_width . "px;";
    } elseif (preg_match('/%/i',$width)) {
      $fixed_width_perc = str_replace("%", "", $width);
        $width_output = "width:" . $fixed_width_perc . "%;";
    } else {
      $width_output = "width:" . $width . "px;";
    }

    $form_width = ($width != "") ? $width_output : '';

    //if (!preg_match_all("/(.?)\[(inbound_field)\b(.*?)(?:(\/))?\](?:(.+?)\[\/inbound_field\])?(.?)/s", $content, $matches)) {
    if (!preg_match_all('/(.?)\[(inbound_field)(.*?)\]/s',$content, $matches)) {
      return '';

    } else {

      for($i = 0; $i < count($matches[0]); $i++) {
        $matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
      }
        // matches are $matches[3][$i]['label']
        $clean_form_id = preg_replace("/[^A-Za-z0-9 ]/", '', trim($name));
        $form_id = strtolower(str_replace(array(' ','_'),'-',$clean_form_id));

        $form = '<!-- This Inbound Form is Automatically Tracked -->';
        $form .= '<div id="inbound-form-wrapper" class="">';
        $form .= '<form class="inbound-now-form wpl-track-me" method="post" id="'.$form_id.'" action="" style="'.$form_width.'">';
        $main_layout = ($form_layout != "") ? 'inbound-'.$form_layout : 'inbound-normal';
        for($i = 0; $i < count($matches[0]); $i++) {

        $label = (isset($matches[3][$i]['label'])) ? $matches[3][$i]['label'] : '';
        $clean_label = preg_replace("/[^A-Za-z0-9 ]/", '', trim($label));
        $formatted_label = strtolower(str_replace(array(' ','_'),'-',$clean_label));
        $field_placeholder = (isset($matches[3][$i]['placeholder'])) ? $matches[3][$i]['placeholder'] : '';

        $placeholer_use = ($field_placeholder != "") ? $field_placeholder : $label;

        if ($field_placeholder != "") {
          $form_placeholder = "placeholder='".$placeholer_use."'";
        } else if (isset($form_labels) && $form_labels === "placeholder") {
          $form_placeholder = "placeholder='".$placeholer_use."'";
        } else {
          $form_placeholder = "";
        }

        $description_block = (isset($matches[3][$i]['description'])) ? $matches[3][$i]['description'] : '';
        $required = (isset($matches[3][$i]['required'])) ? $matches[3][$i]['required'] : '0';
        $req = ($required === '1') ? 'required' : '';
        $req_label = ($required === '1') ? '<span class="inbound-required">*</span>' : '';
        $field_name = strtolower(str_replace(array(' ','_'),'-',$label));

        /* Map Common Fields */
        (preg_match( '/Email|e-mail|email/i', $label, $email_input)) ? $email_input = " inbound-email" : $email_input = "";

        // Match Phone
        (preg_match( '/Phone|phone number|telephone/i', $label, $phone_input)) ? $phone_input = " inbound-phone" : $phone_input = "";

        // match name or first name. (minus: name=, last name, last_name,)
        (preg_match( '/(?<!((last |last_)))name(?!\=)/im', $label, $first_name_input)) ? $first_name_input = " inbound-first-name" : $first_name_input =  "";

        // Match Last Name
        (preg_match( '/(?<!((first)))(last name|last_name|last)(?!\=)/im', $label, $last_name_input)) ? $last_name_input = " inbound-last-name" : $last_name_input =  "";

        $input_classes = $email_input . $first_name_input . $last_name_input . $phone_input;

        $type = (isset($matches[3][$i]['type'])) ? $matches[3][$i]['type'] : '';

            $form .= '<div class="inbound-field '.$main_layout.' label-'.$form_labels_class.'">';

        if ($type != 'hidden' && $form_labels != "bottom" || $type === "radio"){
            $form .= '<label class="inbound-label '.$formatted_label.' '.$form_labels_class.' inbound-input-'.$type.'">' . $matches[3][$i]['label'] . $req_label . '</label>';
                    }
        if ($type === 'textarea'){
             $form .=  '<textarea class="inbound-input inbound-input-textarea" name="'.$field_name.'" id="in_'.$field_name.' '.$req.'"/></textarea>';
        } else if ($type === 'dropdown'){
            $dropdown_fields = array();
            $dropdown = $matches[3][$i]['dropdown'];
            $dropdown_fields = explode(",", $dropdown);
            $form .= '<select name="'. $field_name .'" id="">';
            foreach ($dropdown_fields as $key => $value) {
              $drop_val_trimmed =  trim($value);
              $dropdown_val = strtolower(str_replace(array(' ','_'),'-',$drop_val_trimmed));
              $form .= '<option value="'. $dropdown_val .'">'. $value .'</option>';
            }
            $form .= '</select>';
        } else if ($type === 'radio'){
            $radio_fields = array();
            $radio = $matches[3][$i]['radio'];
            $radio_fields = explode(",", $radio);
            // $clean_radio = str_replace(array(' ','_'),'-',$value) // clean leading spaces. finish
            foreach ($radio_fields as $key => $value) {
              $radio_val_trimmed =  trim($value);
              $radio_val =  strtolower(str_replace(array(' ','_'),'-',$radio_val_trimmed));
              $form .= '<span class="radio-'.$main_layout.' radio-'.$form_labels_class.'"><input type="radio" name="'. $field_name .'" value="'. $radio_val .'">'. $radio_val_trimmed .'</span>';
            }
          } else if ($type === 'checkbox'){
            $checkbox_fields = array();

            $checkbox = $matches[3][$i]['checkbox'];
            $checkbox_fields = explode(",", $checkbox);
            // $clean_radio = str_replace(array(' ','_'),'-',$value) // clean leading spaces. finish
            foreach ($checkbox_fields as $key => $value) {
              $checkbox_val_trimmed =  trim($value);
              $checkbox_val =  strtolower(str_replace(array(' ','_'),'-',$checkbox_val_trimmed));
              $form .= '<input class="checkbox-'.$main_layout.' checkbox-'.$form_labels_class.'" type="checkbox" name="'. $field_name .'" value="'. $checkbox_val .'">'.$checkbox_val_trimmed.'<br>';

            }
          } else if ($type === 'html-block'){
              $html = $matches[3][$i]['html'];
              echo $html;
              $form .= "<div>" . $html . "</div>";

          } else if ($type === 'editor'){
            //wp_editor(); // call wp editor
          } else {
              $hidden_param = (isset($matches[3][$i]['dynamic'])) ? $matches[3][$i]['dynamic'] : '';
              $dynamic_value = (isset($_GET[$hidden_param])) ? $_GET[$hidden_param] : '';
              $form .=  '<input class="inbound-input inbound-input-text '.$formatted_label . $input_classes.'" name="'.$field_name.'" '.$form_placeholder.' value="'.$dynamic_value.'" type="'.$type.'" '.$req.'/>';
          }
          if ($type != 'hidden' && $form_labels === "bottom" && $type != "radio"){
              $form .= '<label class="inbound-label '.$formatted_label.' '.$form_labels_class.' inbound-input-'.$type.'">' . $matches[3][$i]['label'] . $req_label . '</label>';
          }

          if ($description_block != "" && $type != 'hidden'){
                $form .= "<div class='inbound-description'>".$description_block."</div>";
          }
               $form .= '</div>';
      }
      // End Loop
      $current_page = get_permalink();
             $form .= '<div class="inbound-field '.$main_layout.' inbound-submit-area">
                      <input type="submit" '.$submit_button_type.' class="button" value="'.$submit_button.'" name="send" id="inbound_form_submit" />
                  </div>
                  <input type="hidden" name="inbound_submitted" value="1">';
           if( $redirect != "") {
            $form .=  '<input type="hidden" id="inbound_redirect" name="inbound_redirect" value="'.$redirect.'">';
           }
            $form .= '<input type="hidden" name="inbound_form_name" value="'.$form_name.'">
                      <input type="hidden" name="inbound_form_id" value="'.$id.'">
                      <input type="hidden" name="inbound_current_page_url" value="'.$current_page.'">
                      <input type="hidden" name="inbound_furl" value="'. base64_encode($redirect) .'">
                      <input type="hidden" name="inbound_notify" value="'. base64_encode($notify) .'">

                  </form>
                  </div>';
      $form = preg_replace('/<br class="inbr".\/>/', '', $form); // remove editor br tags
      return $form;
  }
}
}

    // setup enqueue scripts
    static function register_script() {
    //wp_register_script('preloadify-js', plugins_url('/js/preloadify/jquery.preloadify.js', __FILE__), array('jquery'), '1.0', true);
    //wp_register_style( 'preloadify-css', plugins_url( '/inbound-forms/js/preloadify/plugin/css/style.css' ) );
    }

    // only call enqueue once
    static function print_script() {
    if ( ! self::$add_script )
      return;
    //wp_print_scripts('preloadify-js');
    //wp_enqueue_style( 'preloadify-css' );
     }

    // move to file
    static function inline_my_script() {
      if ( ! self::$add_script )
      return;

      echo '<script type="text/javascript">
          jQuery(document).ready(function($){

          function validateEmail(email) {

              var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
              return re.test(email);
          }
          var parent_redirect = parent.window.location.href;
          jQuery("#inbound_parent_page").val(parent_redirect);


         // validate email
           $("input.inbound-email").on("change keyup", function (e) {
               var email = $(this).val();
                if (validateEmail(email)) {
                  $(this).css("color", "green");
                  $(this).addClass("valid-email");
                  $(this).removeClass("invalid-email");
                } else {
                  $(this).css("color", "red");
                  $(this).addClass("invalid-email");
                  $(this).removeClass("valid-email");
                }
              if($(this).hasClass("valid-email")) {
                   $(this).parent().parent().find("#inbound_form_submit").removeAttr("disabled");
              }
           });

          });
          </script>';

    echo "<style type='text/css'>
      /* Add button style options http://medleyweb.com/freebies/50-super-sleek-css-button-style-snippets/ */
        input.invalid-email {-webkit-box-shadow: 0 0 6px #F8B9B7;
                          -moz-box-shadow: 0 0 6px #f8b9b7;
                          box-shadow: 0 0 6px #F8B9B7;
                          color: #B94A48;
                          border-color: #E9322D;}
        input.valid-email {-webkit-box-shadow: 0 0 6px #B7F8BA;
                    -moz-box-shadow: 0 0 6px #f8b9b7;
                    box-shadow: 0 0 6px #98D398;
                    color: #008000;
                    border-color: #008000;}
            </style>";
    }

    static function send_email(){
      // Cross reference with EDD email class for HTML sends
      // Add PHP processing for lead data
      if(isset($_POST['inbound_submitted']) && $_POST['inbound_submitted'] === '1') {
            $redirect = "";
        if(isset($_POST['inbound_furl']) && $_POST['inbound_furl'] != "") {
            $redirect = base64_decode($_POST['inbound_furl']);
        }
        if(isset($_POST['inbound_notify']) && $_POST['inbound_notify'] != "") {
            $email_to = base64_decode($_POST['inbound_notify']);
        }

          foreach ( $_POST as $field => $value ) {
                if ( get_magic_quotes_gpc() ) {
                    $value = stripslashes( $value );
                }
                $field = strtolower($field);

                if (preg_match( '/Email|e-mail|email/i', $value)) {
                $field = "email";
                }

                if (preg_match( '/(?<!((last |last_)))name(?!\=)/im', $value) && !isset($form_data['first-name'])) {
                $field = "first-name";
                }

                if (preg_match( '/(?<!((first)))(last name|last_name|last)(?!\=)/im', $value) && !isset($form_data['last-name'])) {
                $field = "last-name";
                }

                if (preg_match( '/Phone|phone number|telephone/i', $value)) {
                $field = "phone";
                }

                $form_data[$field] = strip_tags( $value );

            }
            /*
                add_filter( 'wp_mail_from', 'wp_leads_mail_from' );
                function wp_leads_mail_from( $email )
                {
                    return 'david@inboundnow.com';
                }
                // Make Option
                add_filter( 'wp_mail_from_name', 'wp_leads_mail_from_name' );
                function wp_leads_mail_from_name( $name )
                {
                    return 'David';
                }
             */
                // Make Option
                add_filter( 'wp_mail_content_type', 'set_html_content_type' );
                function set_html_content_type() {
                return 'text/html';
                }

           //print_r($form_data); // debug form data

            /* Might be better email send need to test and look at html edd emails */
            if ( isset($form_data['email'])) {
                // DO PHP LEAD SAVE HERE
                $to = $email_to; // admin email or email from shortcode
                $admin_url = get_bloginfo( 'url' ) . "/wp-admin";
                // get the website's name and puts it in front of the subject
                $email_subject = "[" . get_bloginfo( 'name' ) . "] " . $form_data['inbound_form_name'] . " - New Lead Conversion";
                // get the message from the form and add the IP address of the user below it
                $email_message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                  <html>
                    <head>
                      <meta http-equiv="Content-Type" content="text/html;' . get_option('blog_charset') . '" />
                    </head>
                    <body style="margin: 0px; background-color: #F4F3F4; font-family: Helvetica, Arial, sans-serif; font-size:12px;" text="#444444" bgcolor="#F4F3F4" link="#21759B" alink="#21759B" vlink="#21759B" marginheight="0" topmargin="0" marginwidth="0" leftmargin="0">
                      <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" border="0">
                        <tr>';
                $email_message .= "<div style='padding-top: 10px; padding-left: 15px; font-size: 20px; padding-bottom: 10px; background-color:#E0E0E0; border:solid 1px #CECDCA;'>New Conversion on <strong>" . $form_data['inbound_form_name'] ."</strong></div>\n";
                $exclude_array = array('Inbound Redirect', 'Inbound Submitted', 'Inbound Notify', 'Inbound Parent Page', 'Send', 'Inbound Furl' );

                $main_count = 0;
                $url_request = "";
                foreach ($form_data as $key => $value) {
                    //array_push($action_categories, $ctaw_cat->category_nicename);
                    $urlparam = ($main_count < 1 ) ?  "?" : "&";
                    $url_request .= $urlparam . $key . "=" . urlencode($value);
                    $name = str_replace(array('-','_'),' ', $key);
                    $name = ucwords($name);
                    if ( $name === "Inbound Current Page Url" ) {
                      $name = "Converted on Page";
                    }
                    $field_data = ($form_data[$key] != "") ? $form_data[$key] : "<span style='color:#949494; font-size: 10px;'>(Field left blank)</span>";


                    if(!in_array($name, $exclude_array)) {
                    $email_message .= "<div style='border:solid 1px #EBEBEA; padding-top:10px; padding-bottom:10px; padding-left:20px; padding-right:20px;'><strong style='min-width: 120px;display: inline-block;'>".$name . ": </strong>" . $field_data ."</div>\n";
                    }
                    $main_count++;
                }

                $email_message .= "<div style='border:solid 1px #EBEBEA; background-color:#fff; padding-top:10px; padding-bottom:10px; padding-left:20px; padding-right:20px;'><h1><a style='color: #00F;font-size: 20px;' href='".$admin_url."/edit.php?post_type=wp-lead&lead-email-redirect=".$form_data['email']."' target='_blank'>View this Lead</a></h1></div>\n";
                $email_message .= '</tr>
                              </table>
                            </body>
                          </html>';
                if (isset($form_data['first-name']) && isset($form_data['last-name'])) {
                  $from_name = $form_data['first-name'] . " ". $form_data['last-name'];
                } else if (isset($form_data['first-name'])) {
                  $from_name = $form_data['first-name'];
                } else {
                  $from_name = get_bloginfo( 'name' );
                }
                // set the e-mail headers with the user's name, e-mail address and character encoding
                $headers  = "From: " . $from_name . " <" . $form_data['email'] . ">\n";
                $headers .= 'Content-type: text/html';
                // send the e-mail with the shortcode attribute named 'email' and the POSTed data
                wp_mail( $to, $email_subject, $email_message, $headers );
                // and set the result text to the shortcode attribute named 'success'
                //$result = $success;
                // ...and switch the $sent variable to TRUE
                $sent = true;

                //echo "email sent";
                // Do redirect
                //echo $redirect . $url_request;
                if ($redirect != "") {

                wp_redirect( $redirect );
                exit();
                }
            }

        }

    }
  }

}

InboundForms::init();
?>