<?php
//theme support
add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );
add_image_size("slider", 402, 510, true);
add_image_size("services", 40, 40, true);

add_action('admin_menu','remove_default_post_type');

function remove_default_post_type() {
    remove_menu_page('edit.php');
}

function posttype_admin_css() {
    global $post_type;
    $post_types = array('places','countries', 'continents', 'post', 'page');
    if(in_array($post_type, $post_types))
    echo '<style type="text/css">#post-preview, #view-post-btn{display: none;}</style>';
}
add_action( 'admin_head-post-new.php', 'posttype_admin_css' );
add_action( 'admin_head-post.php', 'posttype_admin_css' );

add_action( 'init', 'my_website_add_rewrite_tag' );
function my_website_add_rewrite_tag() {

	//add_rewrite_rule( '^([^/]*)/alle/?','index.php?continents=$matches[1]','top' );
    add_rewrite_rule( '^([^/]*)/([^/]*)/([^/]*)/?','index.php?places=$matches[3]','top' );
    add_rewrite_rule( '^([^/]*)/([^/]*)/?','index.php?countries=$matches[2]','top' );

}

function na_parse_request( $query ) {

    if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
        return;
    }

    if ( ! empty( $query->query['name'] ) ) {
        $query->set( 'post_type', array( 'post', 'page', 'continents' ) );
    }
}
add_action( 'pre_get_posts', 'na_parse_request' );

add_filter( 'post_type_link', 'my_website_filter_post_type_link', 1, 4 );
function my_website_filter_post_type_link( $post_link, $post, $leavename, $sample ) {
    switch( $post->post_type ) {

        case 'continents':

                $post_link = home_url( user_trailingslashit( $post->post_name ) );

            break;

        case 'countries':

                $continent = get_post( get_post_meta($post->ID, '_continent', true) );
                
                if(get_post_meta($post->ID, '_continent', true) == "0") $continent->post_name = 'undefined';
                
                $post_link = home_url( user_trailingslashit( $continent->post_name . '/' . $post->post_name ) );

            break;

        case 'places':

                $country = get_post( get_post_meta($post->ID, '_country', true) );

                $continent = get_post( get_post_meta(get_post_meta($post->ID, '_country', true), '_continent', true) );
                
                if(get_post_meta($post->ID, '_country', true) == "0") $country->post_name = 'undefined';
                if(get_post_meta(get_post_meta($post->ID, '_country', true), '_continent', true) == "0") $continent->post_name = 'undefined';
                
                $post_link = home_url( user_trailingslashit( $continent->post_name . '/' . $country->post_name . '/' . $post->post_name ) );

            break;

    }

    return $post_link;
}


add_action( 'phpmailer_init', 'send_smtp_email' );
function send_smtp_email( PHPMailer $phpmailer ) {
	$phpmailer->isSMTP();
	$phpmailer->Host       = SMTP_HOST;
	$phpmailer->SMTPAuth   = SMTP_AUTH;
	$phpmailer->Port       = SMTP_PORT;
	$phpmailer->Username   = SMTP_USER;
	$phpmailer->Password   = SMTP_PASS;
	$phpmailer->SMTPSecure = SMTP_SECURE;
	$phpmailer->From       = SMTP_FROM;
	$phpmailer->FromName   = SMTP_NAME;
}

add_action('wp_print_scripts', 'enqueueScriptsFix', 100);
add_action('wp_print_styles', 'enqueueStylesFix', 100);
 
function enqueueScriptsFix() {
    if (!is_admin()) {
        if (!empty($_SERVER['HTTPS'])) {
            global $wp_scripts;
            foreach ((array) $wp_scripts->registered as $script) {
                if (stripos($script->src, 'http://', 0) !== FALSE)
                    $script->src = str_replace('http://', 'https://', $script->src);
            }
        }
    }
}

function enqueueStylesFix() {
    if (!is_admin()) {
        if (!empty($_SERVER['HTTPS'])) {
            global $wp_styles;
            foreach ((array) $wp_styles->registered as $script) {
                if (stripos($script->src, 'http://', 0) !== FALSE)
                    $script->src = str_replace('http://', 'https://', $script->src);
            }
        }
    }
}

add_action ('init', 'safeRedirectBack');
function safeRedirectBack() {
	$redirect = false;

	if ( ! function_exists( 'post_exists' ) ) {
	    require_once( ABSPATH . 'wp-admin/includes/post.php' );
	}


    if(isset($_POST['step']) && $_POST['step'] == 'emailform' && isset($_POST['email']) && $_POST['email'] != '' && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $email = $_POST['email'];
        $to = get_option('admin_email', 'steve.zivanovic@gmail.com');
        $subject = 'New E-Mail';
        $body = '<p>New E-Mail Received: '.htmlspecialchars($email).'</p>';
        $headers = array('Content-Type: text/html; charset=UTF-8');
         
        $mailresult = wp_mail( $to, $subject, $body, $headers );

        wp_safe_redirect('?show=message');
        exit();
    } 

	if(isset($_POST['step']) && ($_POST['step'] == 'first' || $_POST['step'] == 'second' || $_POST['step'] == 'third')) {

		$where = $_POST['where'];
		$duration = $_POST['duration'];
		$count = $_POST['count'];

		if($where=="" || $duration=="" || $count=="") $redirect = true;

		$durationCheck = array('1-2 Wochen', '3-4 Wochen', '5+ Wochen', 'Weiß nicht');
		$countCheck = array('1-2 Personen', '3-4 Personen', '5-6 Personen', '7+ Personen');

		if(!in_array($duration, $durationCheck) || !in_array($count, $countCheck)) $redirect = true;
		else {
			if (strpos($where, ' | ') !== false) {
			    $where = explode(" | ", $where);
			    if(count($where) > 2) $redirect = true;
			    if(!post_exists($where[0]) || !post_exists($where[1])) $redirect = true;
			} else {
				if(!post_exists($where)) $redirect = true;
			}
		}
	}
	if(!$redirect && isset($_POST['step']) && ($_POST['step'] == 'second' || $_POST['step'] == 'third')) {
		$allowed = array('1', '2', '3', '4');

		$answers = array();
		$asks = array();
		$args = array(
		    'post_type' => 'questions',
		    'posts_per_page' => -1,
		    'orderby' => array('date' => 'ASC')
		);

		$custom_query = new WP_Query( $args );
		$questioncount = $custom_query->post_count;
		if ($custom_query->have_posts()) :
			while ($custom_query->have_posts()) : $custom_query->the_post();
				$answer1 = get_post_meta( get_the_ID(), '_answer1', true );
				$answer2 = get_post_meta( get_the_ID(), '_answer2', true );
				$answer3 = get_post_meta( get_the_ID(), '_answer3', true );
				$answer4 = get_post_meta( get_the_ID(), '_answer4', true );
				$idQuestion = get_the_ID();

				array_push($answers, array($answer1, $answer2, $answer3, $answer4));
				array_push($asks, array($idQuestion));
			endwhile; wp_reset_postdata();
		endif;

		$answer = array();
		$ask = array();
		for($i = 1; $i <= $questioncount; $i++) {

			if(!in_array($_POST['question'.$i], $allowed)) $redirect = true;
			else {
				$answer[$i] = $_POST['question'.$i];
				$ask[$i] = $_POST['ask'.$i];
			}
		}

		foreach ($answer as $key => $value) {
			if($answers[$key-1][$value-1] == "") $redirect = true;
		}

		foreach ($ask as $key => $value) {
			if($asks[$key-1][0] != $value) $redirect = true;
		}
	}
	if(!$redirect && isset($_POST['step']) && ($_POST['step'] == 'third')) {
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$useremail = $_POST['useremail'];
		$usertelefon = $_POST['usertelefon'];

		if($fname=="" || $lname=="" || $useremail=="" || $usertelefon=="" || !(filter_var($useremail, FILTER_VALIDATE_EMAIL))) $redirect = true;
	}

	if(!$redirect) return;

	$location = get_bloginfo( 'wpurl' );
	$location = preg_replace('/\?.*/', '', $location);
	wp_safe_redirect($location.'?formerror=1');
	exit();
}

function add_opengraph_doctype($output) {
    return $output . '
    xmlns="https://www.w3.org/1999/xhtml"
    xmlns:og="https://ogp.me/ns#"
    xmlns:fb="http://www.facebook.com/2008/fbml"';
}
add_filter('language_attributes', 'add_opengraph_doctype');

	function facebook_open_graph() {
	    global $post;
	    if (is_singular()) {

			if($excerpt = $post->post_excerpt) {
	 			$excerpt = strip_tags($post->post_excerpt);
				$excerpt = str_replace("", "'", $excerpt);
	        } else {
	            $excerpt = get_bloginfo('description');
			}

		    echo '<meta property="og:title" content="' . get_the_title() . '"/>';
			echo '<meta property="og:description" content="' . $excerpt . '"/>';
		    echo '<meta property="og:type" content="website"/>';
		    echo '<meta property="og:url" content="' . get_permalink() . '"/>';
		    echo '<meta property="og:site_name" content="'.get_bloginfo('name').'"/>';

		    echo '<meta name="twitter:card" content="summary_large_image" />';
			echo '<meta name="twitter:site" content="@'.get_bloginfo('name').'" />';
			echo '<meta name="twitter:creator" content="@think.ba" />';
			echo '<meta name="twitter:url" content="' . get_permalink() . '"/>';
			echo '<meta name="twitter:title" content="' . get_the_title() . '"/>';
			echo '<meta name="twitter:description" content="' . $excerpt . '"/>';

		    if(!has_post_thumbnail( $post->ID )) {
		    $default_image=get_bloginfo('template_directory').'/images/home-background.jpg';
		    echo '<meta property="og:image" content="' . $default_image . '"/><meta name="twitter:image" content="' . $default_image . '" />';
		    } else {
		        $thumbnail_src = get_the_post_thumbnail_url( $post->ID );
		        echo '<meta property="og:image" content="' . esc_attr( $thumbnail_src ) . '"/><meta name="twitter:image" content="' . esc_attr( $thumbnail_src ) . '" />';
		    }

		    echo "
		";
		} else if(is_home()) {
	        $excerpt = get_bloginfo('description');

		    echo '<meta property="og:title" content="' . get_bloginfo('name') . '"/>';
			echo '<meta property="og:description" content="' . $excerpt . '"/>';
		    echo '<meta property="og:type" content="website"/>';
		    echo '<meta property="og:url" content="' . get_bloginfo( 'wpurl' ) . '"/>';
		    echo '<meta property="og:site_name" content="'.get_bloginfo('name').'"/>';

		    echo '<meta name="twitter:card" content="summary_large_image" />';
			echo '<meta name="twitter:site" content="@'.get_bloginfo('name').'" />';
			echo '<meta name="twitter:creator" content="@think.ba" />';
			echo '<meta name="twitter:url" content="' . get_bloginfo( 'wpurl' ) . '"/>';
			echo '<meta name="twitter:title" content="' . get_bloginfo('name') . '"/>';
			echo '<meta name="twitter:description" content="' . $excerpt . '"/>';

		    $default_image=get_bloginfo('template_directory').'/images/home-background.jpg';
		    echo '<meta property="og:image" content="' . $default_image . '"/><meta name="twitter:image" content="' . $default_image . '" />';

		    echo "
		";
		}
	}
add_action( 'wp_head', 'facebook_open_graph', 5 );

add_filter( 'document_title_parts', function ( $title ) {

    if ( is_home() || is_front_page() )
        unset($title['tagline']);

    return $title;

}, 10, 1 );

$new_general_setting = new new_general_setting();

class new_general_setting {
    function new_general_setting( ) {
        add_filter( 'admin_init' , array( &$this , 'register_fields' ) );
    }
    function register_fields() {
        register_setting( 'general', 'phone_number', 'esc_attr' );
        add_settings_field('phone_number', '<label for="phone_number">'.__('Phone Number' , 'phone_number' ).'</label>' , array(&$this, 'fields_html') , 'general' );

		register_setting( 'general', 'home_title', 'esc_attr' );
        add_settings_field('home_title', '<label for="home_title">'.__('Home Title' , 'home_title' ).'</label>' , array(&$this, 'fields_html_title') , 'general' );

        register_setting( 'general', 'home_subtitle', 'esc_attr' );
        add_settings_field('home_subtitle', '<label for="home_subtitle">'.__('Home Subtitle' , 'home_subtitle' ).'</label>' , array(&$this, 'fields_html_subtitle') , 'general' );

        register_setting( 'general', 'home_image', 'esc_attr' );
        add_settings_field('home_image', '<label for="home_image">'.__('Home Image URL' , 'home_image' ).'</label>' , array(&$this, 'fields_html_home_image') , 'general' );

        register_setting( 'general', 'services_title', 'esc_attr' );
        add_settings_field('services_title', '<label for="services_title">'.__('Services Title' , 'services_title' ).'</label>' , array(&$this, 'fields_html_services_title') , 'general' );

        register_setting( 'general', 'question_title', 'esc_attr' );
        add_settings_field('question_title', '<label for="question_title">'.__('Questions Title' , 'question_title' ).'</label>' , array(&$this, 'fields_html_question_title') , 'general' );

		register_setting( 'general', 'wait_title', 'esc_attr' );
        add_settings_field('wait_title', '<label for="wait_title">'.__('Waiting Results Title' , 'wait_title' ).'</label>' , array(&$this, 'fields_html_wait_title') , 'general' );

        register_setting( 'general', 'wait_subtitle', 'esc_attr' );
        add_settings_field('wait_subtitle', '<label for="wait_subtitle">'.__('Waiting Results Subtitle' , 'wait_subtitle' ).'</label>' , array(&$this, 'fields_html_wait_subtitle') , 'general' );

        register_setting( 'general', 'results_found', 'esc_attr' );
        add_settings_field('results_found', '<label for="results_found">'.__('Results Found Title' , 'results_found' ).'</label>' , array(&$this, 'fields_html_results_found') , 'general' );

        register_setting( 'general', 'results_found_subtitle', 'esc_attr' );
        add_settings_field('results_found_subtitle', '<label for="results_found_subtitle">'.__('Results Found Subtitle' , 'results_found_subtitle' ).'</label>' , array(&$this, 'fields_html_results_found_subtitle') , 'general' );

        register_setting( 'general', 'results_found_who', 'esc_attr' );
        add_settings_field('results_found_who', '<label for="results_found_who">'.__('Results Found -> Who Text' , 'results_found_who' ).'</label>' , array(&$this, 'fields_html_results_found_who') , 'general' );

        register_setting( 'general', 'button_title', 'esc_attr' );
        add_settings_field('button_title', '<label for="button_title">'.__('Button Title' , 'button_title' ).'</label>' , array(&$this, 'fields_html_button_title') , 'general' );

        register_setting( 'general', 'button_subtitle', 'esc_attr' );
        add_settings_field('button_subtitle', '<label for="button_subtitle">'.__('Button Subtitle' , 'button_subtitle' ).'</label>' , array(&$this, 'fields_html_button_subtitle') , 'general' );

        register_setting( 'general', 'final_title', 'esc_attr' );
        add_settings_field('final_title', '<label for="final_title">'.__('Final Message Title' , 'final_title' ).'</label>' , array(&$this, 'fields_html_final_title') , 'general' );

        register_setting( 'general', 'final_subtitle', 'esc_attr' );
        add_settings_field('final_subtitle', '<label for="final_subtitle">'.__('Final Message Subtitle' , 'final_subtitle' ).'</label>' , array(&$this, 'fields_html_final_subtitle') , 'general' );
    }
    function fields_html() {
        $value = get_option( 'phone_number', '' );
        echo '<input class="regular-text" type="text" id="phone_number" name="phone_number" value="' . htmlspecialchars($value) . '" />';
    }

	function fields_html_title() {

        $value = get_option( 'home_title', '' );
        echo '<input class="regular-text" type="text" id="home_title" name="home_title" value="' . htmlspecialchars($value) . '" />';
    }

    function fields_html_subtitle() {

        $value = get_option( 'home_subtitle', '' );
        echo '<input class="regular-text" type="text" id="home_subtitle" name="home_subtitle" value="' . htmlspecialchars($value) . '" />';
    }

    function fields_html_services_title() {

        $value = get_option( 'services_title', '' );
        echo '<input class="regular-text" type="text" id="services_title" name="services_title" value="' . htmlspecialchars($value) . '" />';
    }

    function fields_html_question_title() {

        $value = get_option( 'question_title', '' );
        echo '<input class="regular-text" type="text" id="question_title" name="question_title" value="' . htmlspecialchars($value) . '" />';
    }

	function fields_html_home_image() {

        $value = get_option( 'home_image', '' );
        echo '<input class="regular-text" type="text" id="home_image" name="home_image" value="' . htmlspecialchars($value) . '" />';
    }

	function fields_html_wait_title() {

        $value = get_option( 'wait_title', '' );
        echo '<input class="regular-text" type="text" id="wait_title" name="wait_title" value="' . htmlspecialchars($value) . '" />';
    }
    function fields_html_wait_subtitle() {

        $value = get_option( 'wait_subtitle', '' );
        echo '<input class="regular-text" type="text" id="wait_subtitle" name="wait_subtitle" value="' . htmlspecialchars($value) . '" />';
    }

    function fields_html_results_found() {

        $value = get_option( 'results_found', '' );
        echo '<input class="regular-text" type="text" id="results_found" name="results_found" value="' . htmlspecialchars($value) . '" />';
    }

	function fields_html_results_found_subtitle() {

        $value = get_option( 'results_found_subtitle', '' );
        echo '<input class="regular-text" type="text" id="results_found_subtitle" name="results_found_subtitle" value="' . htmlspecialchars($value) . '" />';
    }
    function fields_html_results_found_who() {

        $value = get_option( 'results_found_who', '' );
        echo '<input class="regular-text" type="text" id="results_found_who" name="results_found_who" value="' . htmlspecialchars($value) . '" />';
    }

	function fields_html_button_title() {

        $value = get_option( 'button_title', '' );
        echo '<input class="regular-text" type="text" id="button_title" name="button_title" value="' . htmlspecialchars($value) . '" />';
    }
    function fields_html_button_subtitle() {

        $value = get_option( 'button_subtitle', '' );
        echo '<input class="regular-text" type="text" id="button_subtitle" name="button_subtitle" value="' . htmlspecialchars($value) . '" />';
    }

    function fields_html_final_title() {

        $value = get_option( 'final_title', '' );
        echo '<input class="regular-text" type="text" id="final_title" name="final_title" value="' . htmlspecialchars($value) . '" />';
    }
    function fields_html_final_subtitle() {

        $value = get_option( 'final_subtitle', '' );
        echo '<input class="regular-text" type="text" id="final_subtitle" name="final_subtitle" value="' . htmlspecialchars($value) . '" />';
    }
}

add_action( 'add_meta_boxes', 'page_type_remove_meta_box', 100);

function page_type_remove_meta_box() {
	//remove_meta_box( 'pageparentdiv', 'page', 'side' );
}

function styles_and_scripts() {
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '3.3.6' );
	wp_enqueue_style( 'slick', get_template_directory_uri() . '/slick/slick.css' );
	wp_enqueue_style( 'slick-theme', get_template_directory_uri() . '/slick/slick-theme.css' );
	wp_enqueue_style( 'main', get_template_directory_uri() . '/style.css' );

	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '3.3.6', true );
	wp_enqueue_script( 'slick', get_template_directory_uri() . '/slick/slick.min.js', array(), false, true );
	wp_enqueue_script( 'custom', get_template_directory_uri() . '/js/custom.js', array(), false, true );

	wp_enqueue_style( 'font-awesome', '//use.fontawesome.com/releases/v5.0.6/css/all.css' );
	wp_enqueue_style( 'fonts', '//fonts.googleapis.com/css?family=Bitter:400,400i,700|Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i' );
}

add_action( 'wp_enqueue_scripts', 'styles_and_scripts' );

register_sidebar( array(
'name' => 'Footer 1',
'id' => 'footer-1',
'description' => 'Appears in the footer area 1',
'before_widget' => '<div class="col-md-2 col-sm-3 col-xs-6">',
'after_widget' => '</div>',
'before_title' => '<h4>',
'after_title' => '</h4>',
) );
register_sidebar( array(
'name' => 'Footer 2',
'id' => 'footer-2',
'description' => 'Appears in the footer area 2',
'before_widget' => '<div class="col-md-2 col-sm-3 col-xs-6">',
'after_widget' => '</div>',
'before_title' => '<h4>',
'after_title' => '</h4>',
) );
register_sidebar( array(
'name' => 'Footer 3',
'id' => 'footer-3',
'description' => 'Appears in the footer area 3',
'before_widget' => '<div class="col-md-2 col-sm-2 col-xs-12">',
'after_widget' => '</div>',
'before_title' => '<h4>',
'after_title' => '</h4>',
) );

register_nav_menu( 'primary', __( 'Primary Menu', 'youdiscover-primary-menu' ) );

function create_custom_post_types() {
	register_post_type( 'services',
			array(
			'labels' => array(
					'name' => __( 'Services' ),
					'singular_name' => __( 'Service' ),
			),
			'public' => true,
			'has_archive' => false,
			'menu_icon'   => 'dashicons-admin-tools',
			'publicly_queryable' => false,
			'supports' => array(
					'title',
					'editor',
					'thumbnail'
			)
	));

	register_post_type( 'questions',
			array(
			'labels' => array(
					'name' => __( 'Questions' ),
					'singular_name' => __( 'Question' ),
			),
			'public' => true,
			'has_archive' => false,
			'menu_icon'   => 'dashicons-info',
			'publicly_queryable' => false,
			'supports' => array(
					'title'
			)
	));

	register_post_type( 'continents',
			array(
			'labels' => array(
					'name' => __( 'Continents' ),
					'singular_name' => __( 'Continent' ),
			),
			'public' => true,
			'has_archive' => false,
			'menu_icon'   => 'dashicons-admin-site',
			'supports' => array(
					'title',
					'editor',
					'thumbnail'
			)
	));

	register_post_type( 'countries',
			array(
			'labels' => array(
					'name' => __( 'Countries' ),
					'singular_name' => __( 'Country' ),
			),
			'public' => true,
			'has_archive' => false,
			'menu_icon'   => 'dashicons-admin-site',
			'supports' => array(
					'title',
					'editor',
					'thumbnail'
			)
	));

	register_post_type( 'places',
			array(
			'labels' => array(
					'name' => __( 'Places' ),
					'singular_name' => __( 'Place' ),
			),
			'public' => true,
			'has_archive' => false,
			'menu_icon'   => 'dashicons-admin-site',
			'supports' => array(
					'title',
					'editor',
					'thumbnail'
			),
			'taxonomies'   => array(
				'category'
			)
	));
}

add_action( 'init', 'create_custom_post_types' );

add_action( 'admin_init', 'add_meta_boxes' );
function add_meta_boxes() {
    add_meta_box( 'country_continent_metabox', 'Continent', 'continents_field', 'countries', 'side' );
    add_meta_box( 'place_country_metabox', 'Country', 'countries_field', 'places', 'side' );

    add_meta_box( 'country_featured_metabox', 'Featured', 'featured_fields', 'countries', 'side' );
    
    $types = array( 'continents', 'countries', 'places' );

    foreach( $types as $type ) {
      add_meta_box('details_meta', 'Header Details', 'custom_details_meta', $type, 'normal', 'high');
	}

	add_meta_box('questions_meta', 'Answers', 'custom_questions_meta', 'questions', 'normal', 'high');
}

function featured_fields() {
	global $post;
	$featured = get_post_meta( $post->ID, '_featured', true );
?>
    <input type="hidden" name="validate_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
    <p>
    	<label for="customtitle">Featured</label>
    	<input type="checkbox" name="featured" id="featured" value="1" <?php echo ($featured) ? 'checked' : '' ?>>
	</p>
<?php
}

function custom_questions_meta() {
	global $post;
	$answer1 = get_post_meta( $post->ID, '_answer1', true );
	$answer2 = get_post_meta( $post->ID, '_answer2', true );
	$answer3 = get_post_meta( $post->ID, '_answer3', true );
	$answer4 = get_post_meta( $post->ID, '_answer4', true );
?>
    <input type="hidden" name="validate_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
    <p>
    	<label for="answer1">Answer 1</label>
    	<input style="width: 100%" type="text" name="answer1" id="answer1" value="<?php echo htmlspecialchars($answer1) ?>">
	</p>
    <p>
    	<label for="answer2">Answer 2</label>
    	<input style="width: 100%" type="text" name="answer2" id="answer2" value="<?php echo htmlspecialchars($answer2) ?>">
	</p>
    <p>
    	<label for="answer3">Answer 3</label>
    	<input style="width: 100%" type="text" name="answer3" id="answer3" value="<?php echo htmlspecialchars($answer3) ?>">
	</p>
    <p>
    	<label for="answer4">Answer 4</label>
    	<input style="width: 100%" type="text" name="answer4" id="answer4" value="<?php echo htmlspecialchars($answer4) ?>">
	</p>



<?php
	wp_enqueue_media();
    $meta_keys = array('first_answer','second_answer','third_answer','fourth_answer');

    foreach($meta_keys as $key => $meta_key){
        $image_meta_val=get_post_meta( $post->ID, $meta_key, true);
        ?>
        <div class="custom_postimage_wrapper" id="<?php echo $meta_key; ?>_wrapper" style="max-width:300px;margin-bottom:20px;margin-top:20px;">
            <p><img src="<?php echo ($image_meta_val!=''?wp_get_attachment_image_src( $image_meta_val, 'full')[0]:''); ?>" style="max-width:100%;display: <?php echo ($image_meta_val!=''?'block':'none'); ?>" alt=""></p>
            <a class="addimage button" onclick="custom_postimage_add_image('<?php echo $meta_key; ?>');"><?php _e('Set Image for answer '.($key+1),'yourdomain'); ?></a><br>
            <a class="removeimage" style="color:#a00;cursor:pointer;display: <?php echo ($image_meta_val!=''?'block':'none'); ?>" onclick="custom_postimage_remove_image('<?php echo $meta_key; ?>');"><?php _e('Remove Image','yourdomain'); ?></a>
            <input type="hidden" name="<?php echo $meta_key; ?>" id="<?php echo $meta_key; ?>" value="<?php echo $image_meta_val; ?>" />
        </div>
    <?php } ?>
    <script>
    function custom_postimage_add_image(key){

        var $wrapper = jQuery('#'+key+'_wrapper');

        custom_postimage_uploader = wp.media.frames.file_frame = wp.media({
            title: '<?php _e('select image','yourdomain'); ?>',
            button: {
                text: '<?php _e('select image','yourdomain'); ?>'
            },
            multiple: false
        });
        custom_postimage_uploader.on('select', function() {

            var attachment = custom_postimage_uploader.state().get('selection').first().toJSON();
            var img_url = attachment['url'];
            var img_id = attachment['id'];
            $wrapper.find('input#'+key).val(img_id);
            $wrapper.find('img').attr('src',img_url);
            $wrapper.find('img').show();
            $wrapper.find('a.removeimage').show();
        });
        custom_postimage_uploader.on('open', function(){
            var selection = custom_postimage_uploader.state().get('selection');
            var selected = $wrapper.find('input#'+key).val();
            if(selected){
                selection.add(wp.media.attachment(selected));
            }
        });
        custom_postimage_uploader.open();
        return false;
    }

    function custom_postimage_remove_image(key){
        var $wrapper = jQuery('#'+key+'_wrapper');
        $wrapper.find('input#'+key).val('');
        $wrapper.find('img').hide();
        $wrapper.find('a.removeimage').hide();
        return false;
    }
    </script>
<?php
}

function custom_details_meta() {
	global $post;
	$customtitle = get_post_meta( $post->ID, '_customtitle', true );
	$customsubtitle = get_post_meta( $post->ID, '_customsubtitle', true );
?>
    <input type="hidden" name="validate_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
    <p>
    	<label for="customtitle">Custom Title</label>
    	<input style="width: 100%" type="text" name="customtitle" id="customtitle" value="<?php echo htmlspecialchars($customtitle) ?>">
	</p>
	<p>
		<label for="customtitle">Custom Subtitle</label>
    	<input style="width: 100%" type="text" name="customsubtitle" id="customsubtitle" value="<?php echo htmlspecialchars($customsubtitle) ?>">
    </p>
<?php
}

function continents_field() {
    global $post;
    $selected_continent = get_post_meta( $post->ID, '_continent', true );
    $all_continents = get_posts( array(
        'post_type' => 'continents',
        'numberposts' => -1,
        'orderby' => 'post_title',
        'order' => 'ASC'
    ) );
    ?>
    <input type="hidden" name="validate_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
	    <table class="form-table">
	    	<tr valign="top">
	    		<th scope="row">
	    			<label for="continents">Continents</label>
	    		</th>
	    		<td>
	    			<select name="continent">
	    				<option value="0">-</option>
		    			<?php foreach ( $all_continents as $continent ) : ?>
		        		<option value="<?php echo $continent->ID; ?>"<?php echo ( $continent->ID == $selected_continent ) ? ' selected="selected"' : ''; ?>><?php echo $continent->post_title; ?></option>
		    			<?php endforeach; ?>
	    			</select>
	    		</td>
	    	</tr>
    	</table>
<?php
}

function countries_field() {
    global $post;
    $selected_country = get_post_meta( $post->ID, '_country', true );
    $all_countries = get_posts( array(
        'post_type' => 'countries',
        'numberposts' => -1,
        'orderby' => 'post_title',
        'order' => 'ASC'
    ) );
    ?>
    <input type="hidden" name="validate_nonce" value="<?php echo wp_create_nonce( basename( __FILE__ ) ); ?>" />
	    <table class="form-table">
	    	<tr valign="top">
	    		<th scope="row">
	    			<label for="continents">Countries</label>
	    		</th>
	    		<td>
	    			<select name="country">
	    				<option value="0">-</option>
		    			<?php foreach ( $all_countries as $country ) : ?>
		        		<option value="<?php echo $country->ID; ?>"<?php echo ( $country->ID == $selected_country ) ? ' selected="selected"' : ''; ?>><?php echo $country->post_title; ?></option>
		    			<?php endforeach; ?>
	    			</select>
	    		</td>
	    	</tr>
    	</table>
<?php
}


add_action( 'save_post', 'save_continent_field' );
function save_continent_field( $post_id ) {

    // only run this for countries and places
    if ( 'questions' != get_post_type( $post_id ) && 'continents' != get_post_type( $post_id ) && 'countries' != get_post_type( $post_id ) && 'places' != get_post_type( $post_id ) )
        return $post_id;

    // verify nonce
    if ( (empty( $_POST['validate_nonce'] ) || !wp_verify_nonce( $_POST['validate_nonce'], basename( __FILE__ ) )) )
        return $post_id;

    // check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // check permissions
    if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;

    if(isset($_POST['continent']))
    	update_post_meta( $post_id, '_continent', $_POST['continent'] );

	if(isset($_POST['country']))
    	update_post_meta( $post_id, '_country', $_POST['country'] );

	if(isset($_POST['customtitle']))
    	update_post_meta( $post_id, '_customtitle', $_POST['customtitle'] );

	if(isset($_POST['customsubtitle']))
    	update_post_meta( $post_id, '_customsubtitle', $_POST['customsubtitle'] );

	if('countries' == get_post_type( $post_id ))
		update_post_meta( $post_id, '_featured', $_POST['featured'] );

	if(isset($_POST['answer1']))
		update_post_meta( $post_id, '_answer1', $_POST['answer1'] );

	if(isset($_POST['answer2']))
		update_post_meta( $post_id, '_answer2', $_POST['answer2'] );

	if(isset($_POST['answer3']))
		update_post_meta( $post_id, '_answer3', $_POST['answer3'] );

	if(isset($_POST['answer4']))
		update_post_meta( $post_id, '_answer4', $_POST['answer4'] );

    $meta_keys = array('first_answer','second_answer','third_answer','fourth_answer');
    foreach($meta_keys as $meta_key){
        if(isset($_POST[$meta_key]) && intval($_POST[$meta_key])!=''){
            update_post_meta( $post_id, $meta_key, intval($_POST[$meta_key]));
        }else{
            update_post_meta( $post_id, $meta_key, '');
        }
    }

    if ( ! wp_is_post_revision( $post_id ) ) {

        remove_action( 'save_post', 'save_continent_field' );

        wp_update_post( array(
            'ID' => $post_id,
            'post_name' => ''
        ));

        add_action( 'save_post', 'save_continent_field' );

    }
}

class My_Social_Widget extends WP_Widget {
    function My_Social_Widget() {
        $widget_ops = array( 'classname' => 'social_widget', 'description' => __( "Social Widget" ) );
        $this->WP_Widget('my_social_widget', __('Social'), $widget_ops);
    }

    function widget( $args, $instance ) {
        extract($args);
        $text = apply_filters( 'widget_text', $instance['text'], $instance );
        $text1 = apply_filters( 'widget_text', $instance['text1'], $instance );
        $text2 = apply_filters( 'widget_text', $instance['text2'], $instance );
        $text3 = apply_filters( 'widget_text', $instance['text3'], $instance );
        $text4 = apply_filters( 'widget_text', $instance['text4'], $instance );
        $text5 = apply_filters( 'widget_text', $instance['text5'], $instance );
        $text6 = apply_filters( 'widget_text', $instance['text6'], $instance );
        echo $before_widget;
        ?>
            <?php if($text!="") { echo $before_title.$text.$after_title;  } ?>
            <ul class="social-list">
            	<?php if($text1!="") { ?><li><a href="<?php echo htmlspecialchars($text1) ?>" target="_blank"><i class="fab fa-facebook"></i> Facebook</a></li><?php } ?>
            	<?php if($text2!="") { ?><li><a href="<?php echo htmlspecialchars($text2) ?>" target="_blank"><i class="fab fa-pinterest"></i> Pinterest</a></li><?php } ?>
            	<?php if($text3!="") { ?><li><a href="<?php echo htmlspecialchars($text3) ?>" target="_blank"><i class="fab fa-instagram"></i> Instagram</a></li><?php } ?>
            	<?php if($text4!="") { ?><li><a href="<?php echo htmlspecialchars($text4) ?>" target="_blank"><i class="fab fa-google"></i> Google+</a></li><?php } ?>
            	<?php if($text5!="") { ?><li><a href="<?php echo htmlspecialchars($text5) ?>" target="_blank"><i class="fab fa-twitter"></i> Twitter</a></li><?php } ?>
            	<?php if($text6!="") { ?><li><a href="<?php echo htmlspecialchars($text6) ?>" target="_blank"><i class="fab fa-linkedin"></i> Linkedin</a></li><?php } ?>
            </ul>
        <?php
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        if ( current_user_can('unfiltered_html') ) {
            $instance['text'] =  $new_instance['text'];
            $instance['text1'] =  $new_instance['text1'];
        	$instance['text2'] =  $new_instance['text2'];
        	$instance['text3'] =  $new_instance['text3'];
        	$instance['text4'] =  $new_instance['text4'];
        	$instance['text5'] =  $new_instance['text5'];
        	$instance['text6'] =  $new_instance['text6'];
        } else {
            $instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) );
            $instance['text1'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text1']) ) );
	        $instance['text2'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text2']) ) );
    	    $instance['text3'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text3']) ) );
	        $instance['text4'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text4']) ) );
        	$instance['text5'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text5']) ) );
    	    $instance['text6'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text6']) ) );
    	}
        return $instance;
    }

    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'text' => '', 'text1' => '', 'text2' => '', 'text3' => '', 'text4' => '', 'text5' => '', 'text6' => '' ) );
        $text = format_to_edit($instance['text']);
        $text1 = format_to_edit($instance['text1']);
        $text2 = format_to_edit($instance['text2']);
        $text3 = format_to_edit($instance['text3']);
        $text4 = format_to_edit($instance['text4']);
        $text5 = format_to_edit($instance['text5']);
        $text6 = format_to_edit($instance['text6']);
?>
		<div class="nav-menu-widget-form-controls">
		<p>
        <label for="<?php echo $this->get_field_id('text'); ?>">Title:</label> <input type="text" class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" value="<?php echo htmlspecialchars($text); ?>" />
        </p>
		<p>
        <label for="<?php echo $this->get_field_id('text1'); ?>">Facebook:</label> <input type="text" class="widefat" id="<?php echo $this->get_field_id('text1'); ?>" name="<?php echo $this->get_field_name('text1'); ?>" value="<?php echo htmlspecialchars($text1); ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id('text2'); ?>">Pinterest:</label> <input type="text" class="widefat" id="<?php echo $this->get_field_id('text2'); ?>" name="<?php echo $this->get_field_name('text2'); ?>" value="<?php echo htmlspecialchars($text2); ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id('text3'); ?>">Instagram:</label> <input type="text" class="widefat" id="<?php echo $this->get_field_id('text3'); ?>" name="<?php echo $this->get_field_name('text3'); ?>" value="<?php echo htmlspecialchars($text3); ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id('text4'); ?>">Google Plus:</label> <input type="text" class="widefat" id="<?php echo $this->get_field_id('text4'); ?>" name="<?php echo $this->get_field_name('text4'); ?>" value="<?php echo htmlspecialchars($text4); ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id('text5'); ?>">Twitter:</label> <input type="text" class="widefat" id="<?php echo $this->get_field_id('text5'); ?>" name="<?php echo $this->get_field_name('text5'); ?>" value="<?php echo htmlspecialchars($text5); ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id('text6'); ?>">Linkedin:</label> <input type="text" class="widefat" id="<?php echo $this->get_field_id('text6'); ?>" name="<?php echo $this->get_field_name('text6'); ?>" value="<?php echo htmlspecialchars($text6); ?>" />
    	</p>
    	</div>
<?php
    }
}


function lbi_widgets_init() {
    register_widget( 'My_Social_Widget' );
}
add_action( 'widgets_init', 'lbi_widgets_init' );

function wpb_remove_schedule_delete() {
    remove_action( 'wp_scheduled_delete', 'wp_scheduled_delete' );
}
add_action( 'init', 'wpb_remove_schedule_delete' );

add_action( 'admin_menu', 'registrations_admin_menu' );
function registrations_admin_menu() {
	add_menu_page( 'Registrations', 'Registrations', 'manage_options', 'my_registrations_page', 'registrations_page', 'dashicons-tickets', 40  );
}

function registrations_page() {
	echo '
	<div class="wrap">
		<h2>Registrations</h2>
	<div style="overflow-x:scroll;">
	<table class="widefat striped" style="min-width:1000px;">
		<tr>
			<th style="text-align: center">SELECT</th>
			<th>ID</th>
			<th>Wohin</th>
			<th>Dauer</th>
			<th>Anzahl</th>
			<th>Questions/Answers</th>
			<th>Vorname</th>
			<th>Nachname</th>
			<th>Email</th>
			<th>Telefonnummer</th>
			<th>Created At</th>
			<th>Action</th>
		</tr>
	';

	global $wpdb;

	if($_GET['show'] == 'all') $perpage = 9999999999;
	else $perpage = 10;

	if($_GET['clear'] == true) $result = $wpdb->get_results("TRUNCATE `registrations`");

	$delete = intval($_GET['delete']);

	if($delete) $result = $wpdb->get_results("DELETE FROM `registrations` WHERE id = $delete");

	$paged = (intval($_GET['paged']) > 0) ? intval($_GET['paged']) : 1;

	$offset = ($paged-1) * $perpage;

	$result = $wpdb->get_results("SELECT count(id) as count FROM registrations");
	$count = $result[0]->count;

	$pages = ceil($count/$perpage);
	if($paged > $pages) $paged = $pages;

	echo '<br><a href="admin.php?page=my_registrations_page&clear=true" onclick="return confirm(\'Are you sure?\')" class="button action">DELETE ALL REGISTRATIONS</a><br><br><a href="admin.php?page=my_registrations_page&show=all" class="button action">SHOW ALL</a>&nbsp;&nbsp;<a href="'.get_bloginfo('template_directory').'/includes/registration_export.php" class="button action">EXPORT ALL</a>&nbsp;&nbsp;<a href="'.get_bloginfo('template_directory').'/includes/registration_export.php?id=" class="button action" id="exportselected">EXPORT SELECTED</a><br><br>';

	if($paged>1)
	echo '<a href="admin.php?page=my_registrations_page&paged='.($paged-1).'" class="button action">Previous</a>&nbsp;&nbsp;';
	if($paged<$pages)
	echo '<a href="admin.php?page=my_registrations_page&paged='.($paged+1).'" class="button action">Next</a>';

	echo '<br><br>';

    $result = $wpdb->get_results("SELECT * FROM registrations ORDER BY `id` DESC LIMIT $perpage OFFSET $offset");
    foreach ($result as $print) {
        echo '
        <tr>
        	<td style="text-align: center"><input type="checkbox" name="exportselect" value="'.htmlspecialchars(stripslashes_deep($print->id)).'" /></td>
        	<td>'.htmlspecialchars(stripslashes_deep($print->id)).'</td>
        	<td>'.htmlspecialchars(stripslashes_deep($print->where)).'</td>
        	<td>'.htmlspecialchars(stripslashes_deep($print->duration)).'</td>
        	<td>'.htmlspecialchars(stripslashes_deep($print->count)).'</td>
        	<td>
        	';

        $answers = unserialize($print->answers);
		$asks = unserialize($print->asks);

		$Qtitle = array();
		$Qanswer = array();

        foreach ($asks as $key => $value) {
        	$Qtitle[$key] = (get_the_title($value)!="") ? get_the_title($value) : 'Question Title is no longer available';
        	$Qanswer[$key] = (get_post_meta( $value, '_answer'.$answers[$key], true )!="") ? get_post_meta( $value, '_answer'.$answers[$key], true ) : 'Question Answer  is no longer available';

        }

        foreach ($Qtitle as $key => $value) {
        	echo stripslashes_deep($Qtitle[$key].'<br>'.$Qanswer[$key].'<br><br>');
        }

        echo '
        	</td>
        	<td>'.htmlspecialchars(stripslashes_deep($print->fname)).'</td>
        	<td>'.htmlspecialchars(stripslashes_deep($print->lname)).'</td>
        	<td>'.htmlspecialchars(stripslashes_deep($print->useremail)).'</td>
        	<td>'.htmlspecialchars(stripslashes_deep($print->usertelefon)).'</td>
        	<td>'.htmlspecialchars(stripslashes_deep($print->created)).'</td>
        	<td><a href="admin.php?page=my_registrations_page&delete='.($print->id).'" class="button action">Delete</a></td>
        </tr>
        ';
    }

	echo '
	</table>
	</div>
	</div>
	';

	echo '<br>';

	if($paged>1)
	echo '<a href="admin.php?page=my_registrations_page&paged='.($paged-1).'" class="button action">Previous</a>&nbsp;&nbsp;';
	if($paged<$pages)
	echo '<a href="admin.php?page=my_registrations_page&paged='.($paged+1).'" class="button action">Next</a>';

?>
<script type="text/javascript">
	jQuery('#exportselected').click(function(e) {
		e.preventDefault();
		var selected = '';
		jQuery('input[name="exportselect"]:checkbox:checked').each(function() {
			if(selected!='') selected = selected + '|';
			selected = selected + jQuery(this).val();
		});
		jQuery(this).attr('href', paramReplace('id', jQuery(this).attr('href'), selected));
		
		window.location.href = jQuery(this).attr('href');
	});

	function paramReplace(name, string, value) {	  
	  var re = new RegExp("[\\?&]" + name + "=([^&#]*)"),
	      delimeter = re.exec(string)[0].charAt(0),
	      newString = string.replace(re, delimeter + name + "=" + value);

	  return newString;
	}
</script>
<?php
}