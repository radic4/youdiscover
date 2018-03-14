<?php get_template_part( 'templates/menu' ); ?>
<div class="banner" style="background-image: url('<?php echo (get_option( 'home_image', '' )!="") ? get_option( 'home_image', '' ) : get_bloginfo('template_directory').'/images/home-background.jpg'; ?>');">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row wizard" id="formwizard">
                        <div class="wizard-danke">
                            <img src="<?php echo get_bloginfo('template_directory'); ?>/images/danke-check.png" alt="check">
<?php

foreach ($answers as $key => $value) {
	$answers[$key] = stripslashes_deep($value);
}

foreach ($asks as $key => $value) {
	$asks[$key] = stripslashes_deep($value);
}

global $wpdb;
$table = registrations;
$data = array(
    'where'    => stripslashes_deep($where),
	'duration'    => stripslashes_deep($duration),
	'count'    => stripslashes_deep($count),
	'answers'    => serialize($answers),
	'asks'    => serialize($asks),
	'fname'    => stripslashes_deep($fname),
	'lname'    => stripslashes_deep($lname),
	'useremail'    => stripslashes_deep($useremail),
	'usertelefon'    => stripslashes_deep($usertelefon),
	'created' => current_time('mysql', 1)
);
$format = array(
    '%s','%s','%s','%s','%s','%s','%s','%s','%s'
);
$success=$wpdb->insert( $table, $data, $format );
if($success) {

	$to = get_option('admin_email', 'steve.zivanovic@gmail.com');
	$subject = 'New registration';
	$body = '<p>New registration on '.get_bloginfo('name').'</p><p><a href="'.get_bloginfo( 'wpurl' ).'/wp-admin" target="_blank">Go to administration</a></p>';
	$headers = array('Content-Type: text/html; charset=UTF-8');
	 
	$mailresult = wp_mail( $to, $subject, $body, $headers );

	echo '<h2>'.get_option( 'final_title', '' ).'</h2><h3>'.get_option( 'final_subtitle', '' ).'</h3>' ; 
} else {
	echo '<h2>Fehler!</h2><h3>Etwas ist schief gelaufen.</h3>';
}
?>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<?php get_template_part( 'templates/services' ); ?>

<?php get_template_part( 'templates/slider' ); ?>