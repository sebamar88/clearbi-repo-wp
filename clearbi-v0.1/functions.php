<?php 

if ( !defined( 'ABSPATH' ) ) exit;


function clearbi_seo_setup(){
	
	//Titulos SEO
	add_theme_support('title-tag');
	
	//Soporte estilos por default de gutenberg en tu tema
    add_theme_support('wp-block-styles');

    //habilitar imagenes destacadas
    add_theme_support('post-thumbnails');

    //agregar tamanios personalizados
    add_image_size('blog', 300, 250, true);
    add_image_size('single', 600, 500, true);
		add_image_size('nose', 555, 370, array( 'left', 'top' ));
		add_image_size('blogpost', 350, 180, array( 'left', 'top' ));
    add_image_size('multiproposito', 1080, 1080, true);
    

	/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				 'width'       => 125,
				 'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array( 'site-title', 'site-description' )
			)
		);
}
add_action('after_setup_theme', 'clearbi_seo_setup');

function replace_core_jquery_version() {
wp_deregister_script( 'jquery' );
// Change the URL if you want to load a local copy of jQuery from your own server.
wp_register_script( 'jquery', "https://code.jquery.com/jquery-3.5.1.min.js", array(), '3.5.1' );
}
add_action( 'wp_enqueue_scripts', 'replace_core_jquery_version' );		
	

function clearbiseo_css() {


		// Font Awesome
		wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array());

		// Bootstrap CSS
		wp_enqueue_style('bootstrapCSS', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css', array());

		//google fonts
		wp_enqueue_style('googleFonts', 'https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Roboto+Slab&display=swap', array());
		wp_enqueue_style('googleFontsMont', 'https://fonts.googleapis.com/css2?family=Montserrat&display=swap', array());

		//Custom CSS
		wp_enqueue_style( 'styles', trailingslashit( get_template_directory_uri() ) . 'style.css', array() );
		
		// Bootstrap JS
		wp_enqueue_script('popperJS', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js', array(),true, true);
		wp_enqueue_script('BootstrapJS', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js', array(),true, true);

		// Custom JS
		wp_enqueue_script('scripts', get_template_directory_uri() . '/assets/js/scripts.js', array('jqueryNew'), true, true);

    }

add_action( 'wp_enqueue_scripts', 'clearbiseo_css', 10 );

// END ENQUEUE PARENT ACTION


function clearbiseo_menus() {
    register_nav_menus(array(
        'header-menu' => 'Header Menu',
        'footer-menu' => 'Footer Menu'
    ));
}

// Filter except 
function excerpt($limit) {
  $excerpt = explode(' ', get_the_excerpt(), $limit);
  if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt).'...';
  } else {
    $excerpt = implode(" ",$excerpt);
  }	
  $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
  return $excerpt;
}
 
function content($limit) {
  $content = explode(' ', get_the_content(), $limit);
  if (count($content)>=$limit) {
    array_pop($content);
    $content = implode(" ",$content).'...';
  } else {
    $content = implode(" ",$content);
  }	
  $content = preg_replace('/[.+]/','', $content);
  $content = apply_filters('the_content', $content); 
  $content = str_replace(']]>', ']]>', $content);
  return $content;
}
/**
 * Add Mime TypesTypes
 */
function bodhi_svgs_upload_mimes( $mimes = array() ) {

	global $bodhi_svgs_options;

	if ( empty( $bodhi_svgs_options['restrict'] ) || current_user_can( 'administrator' ) ) {

		// allow SVG file upload
		$mimes['svg'] = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';

		return $mimes;

	} else {

		return $mimes;

	}

}
add_filter( 'upload_mimes', 'bodhi_svgs_upload_mimes' );


/**
 * Mime Check fix for WP 4.7.1 / 4.7.2
 *
 * Fixes uploads for these 2 version of WordPress.
 * Issue was fixed in 4.7.3 core.
 */
global $wp_version;
if ( $wp_version == '4.7.1' || $wp_version == '4.7.2' ) {
	add_filter( 'wp_check_filetype_and_ext', 'bodhi_svgs_disable_real_mime_check', 10, 4 );
}
function bodhi_svgs_disable_real_mime_check( $data, $file, $filename, $mimes ) {

		$wp_filetype = wp_check_filetype( $filename, $mimes );

		$ext = $wp_filetype['ext'];
		$type = $wp_filetype['type'];
		$proper_filename = $data['proper_filename'];

		return compact( 'ext', 'type', 'proper_filename' );

}
function bodhi_svgs_response_for_svg( $response, $attachment, $meta ) {

	if ( $response['mime'] == 'image/svg+xml' && empty( $response['sizes'] ) ) {

		$svg_path = get_attached_file( $attachment->ID );

		if ( ! file_exists( $svg_path ) ) {
			// If SVG is external, use the URL instead of the path
			$svg_path = $response['url'];
		}

		$dimensions = bodhi_svgs_get_dimensions( $svg_path );

		$response['sizes'] = array(
			'full' => array(
				'url' => $response['url'],
				'width' => $dimensions->width,
				'height' => $dimensions->height,
				'orientation' => $dimensions->width > $dimensions->height ? 'landscape' : 'portrait'
				)
			);

	}

	return $response;

}
add_filter( 'wp_prepare_attachment_for_js', 'bodhi_svgs_response_for_svg', 10, 3 );

function bodhi_svgs_get_dimensions( $svg ) {

	$svg = simplexml_load_file( $svg );

	if ( $svg === FALSE ) {

		$width = '0';
		$height = '0';

	} else {

		$attributes = $svg->attributes();
		$width = (string) $attributes->width;
		$height = (string) $attributes->height;

	}

	return (object) array( 'width' => $width, 'height' => $height );

}

function fix_svg() {
  echo '<style type="text/css">
        .attachment-266x266, .thumbnail img {
             width: 100% !important;
             height: auto !important;
        }
        </style>';
}
add_action( 'admin_head', 'fix_svg' );

//Menu
function register_navwalker(){
	require_once get_template_directory() . '/class-wp-bootstrap-navwalker.php';
	require_once get_template_directory() . '/class-wp-bootstrap-navwalker-movil.php';

}
add_action( 'after_setup_theme', 'register_navwalker' );

function mi_nuevo_mime_type( $existing_mimes ) {
 // añade webp a la lista de mime types
 $existing_mimes['webp'] = 'image/webp';
 // devuelve el array a la funcion con el nuevo mime type
 return $existing_mimes;
}
add_filter( 'mime_types', 'mi_nuevo_mime_type' );