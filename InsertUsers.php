<?php
require_once('wp-load.php' );

$DB_USER= 'root';
$DB_NAME='dalveycup';
$DB_PASS='';
$DB_HOST='localhost';
$wpdb = new wpdb( $DB_USER, $DB_PASS, $DB_NAME, $DB_HOST);

//echo "connected";

global $wpdb;

$textfile= 'names.txt';

readfromFile($wpdb, $textfile);

function readfromFile($wpdb, $textfile){
$names = file($textfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
echo "<h2>Dalvey Cup Members</h2>";
echo "<p>This is a list of current members.</p>
    <p>Each member has been assigned their Dalvey Cup Membership Number, and
    also a Login ID.</p><p> The Login ID is made up of each member's surname and membership number, and is used to log into the
    Dalvey Cup website.</p><p> Users can change their display names to whatever they want, but the Login ID is what they always use this
    Login ID to enter the site.</p>";
// fill the array
$arr = array();
foreach($names as $name) {
    $arr[] = $name;
}

foreach($arr as $key=>$value){
    $key=$key+1;
   
    print "<br>$key: $value<br>";

    prepareUser($wpdb,$value, $key);
 
}

}

function prepareUser($wpdb,$name, $id){
//////////////////////////////
  //  echo $name."<br>";
$display_name=$name;
$user_id=$id;
////////////////////////////////
if (strpos($display_name,'member') !== false) {
    $user_login=$display_name;
}
else{
list($fname, $lname) = split(' ', $display_name,2);
$user_login=$lname.$user_id;
}
$user_nicename= strtolower($user_login);

$user_pass='$P$B/GtwplXsA.UdgtMxw4vC4OElusqzR/';
$user_email='change@email.com';
$user_url='';

echo "<b>Member ID</b>: ".$user_id."<br><b>Login ID</b>: ".$user_login."<br> ";
echo "<br>dn: ".$display_name." userid: ".$user_id." nicename: ".$user_nicename." loginwith: ".$user_login." <br>";

insertUser($wpdb,$user_id,$user_login ,$user_pass ,$user_email ,$user_url ,$user_nicename,$display_name  );
}




function insertUser($wpdb,$user_id,$user_login,$user_pass,$user_email,$user_url,$user_nicename,$display_name){

    $insertUser = 
     "INSERT INTO wp_users (ID,user_login, user_pass, user_email, user_url, user_nicename, display_name, user_registered) 
       VALUES ('$user_id', '$user_login', '$user_pass', '$user_email', '$user_url', '$user_nicename', '$display_name', '2013-11-12 17:35:34')";
  $wpdb->query( $insertUser );

  //$user_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $wpdb->users WHERE user_email = %s", $user_email));

  $my_user = new WP_User( $user_id );
  $my_user->add_role( "Subscriber" );
  echo "complete";


     echo "<br>loggin in";
     wp_logout();

          wp_set_current_user( $user_id, $user_login );
          wp_set_auth_cookie( $user_id );
          do_action( 'wp_login', $user_login );

  wp_logout();
  echo "<br>loggedout"; 
}


?>
