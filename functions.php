<?php
/**
*	functions.php
*
*	The theme's functions and definitions
*/


/**
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*	1.0 - Define constants
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*/
define( 'THEMEROOT', get_stylesheet_directory_uri() );
define( 'IMAGES', THEMEROOT . '/images' );
define( 'SCRIPTS', THEMEROOT . '/js' );
define( 'FRAMEWORK', get_template_directory() . '/framework'  );
define( 'FAVE_FUNCTION', get_template_directory() . '/inc'  );
define( 'FAVE_ADMIN', get_template_directory() . '/admin'  );

/**
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*	2.0 - Load the framework
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*/
require_once( FRAMEWORK . '/init.php' );


/**
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*	3.0 - Set up the content width value based on the theme's design.
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*/
if ( ! isset( $content_width ) ) {
	$content_width = 800;
}


/**
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*	4.0 - Set up theme default and register various supported features.
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*/
if ( ! function_exists( 'magazilla_setup' ) ) {
	function magazilla_setup() {
		/**
		*	Make the theme available for translation.
		*/
		$lang_dir = THEMEROOT . '/languages';
		load_theme_textdomain( 'favethemes', $lang_dir );

		/**
		*	Add support for post formats. 
		*/
		add_theme_support( 'post-formats',
			array(
				'gallery',
				'quote',
				'video',
				'audio'
			)
		);

		/**
		*	Add support for automatic feed links. 
		*/
		add_theme_support( 'automatic-feed-links' );

		/**
		*	Add support for post thumbnails. 
		*/
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 672, 372, true );
		add_image_size( 'single-big-size', 1170, 600, true );

		/**
		*	Register nav menus. 
		*/
		register_nav_menus(
			array(
				'main-menu' => __( 'Main Menu', 'favethemes' ),
				'top-menu' => __( 'Top Menu', 'favethemes' ),
				'footer-menu' => __( 'Footer Menu', 'favethemes' ),
				'mobile-menu' => __( 'Mobile Menu', 'favethemes' )
			)
		);

		//remove gallery style css
		add_filter( 'use_default_gallery_style', '__return_false' );


	}

	add_action( 'after_setup_theme', 'magazilla_setup' );

}


/*-----------------------------------------------------------------------------------*/
/*	Thumbnails
/*-----------------------------------------------------------------------------------*/
add_image_size('gal-thumb', 120, 90, true);
//small height, 1 col wide
add_image_size('gal-big',  800, 0, true);


/*
	Menu Icons
*/
if ( ! function_exists( 'magazilla_add_menu_icons_styles' ) ) {
	function magazilla_add_menu_icons_styles(){?>
	 
	<style type="text/css">
	#adminmenu .menu-icon-video div.wp-menu-image:before {
	content: "\f126";
	}
	#adminmenu .menu-icon-gallery div.wp-menu-image:before{
		content: "\f161";
	}
	</style>
	 
	<?php
	}
	add_action( 'admin_head', 'magazilla_add_menu_icons_styles' );
}


/*-----------------------------------------------------
 * Insert ads after spefic paragraph of single post content.
 *-----------------------------------------------------*/

if ( ! function_exists( 'favethemes_insert_post_ads' ) ) {

	add_filter( 'the_content', 'favethemes_insert_post_ads' );

	function favethemes_insert_post_ads( $content ) {

		global $ft_option;

		$article_inline_state = $ft_option['article_inline_state'];
		$ad_code = $ft_option['article_ad_inline'];
		$ad_position_content = $ft_option['ad_position_content'];
		$paragraph_id = $ft_option['paragraph_no'];


		if ( !empty( $ad_code) && $article_inline_state != 0 ) {

			switch( $ad_position_content ) {

				case 'left':
					$ad_code = '<div class="favethemes-content-ad-inline-left">'.$ad_code.'</div>';
					break;

				case 'right':
					$ad_code = '<div class="favethemes-content-ad-inline-right">'.$ad_code.'</div>';
					break;

				default:
					$ad_code = '<div class="favethemes-content-ad-inline">'.$ad_code.'</div>';
					break;
			}

			if ( is_single() && ! is_admin() ) {
				return favethemes_insert_after_paragraph( $ad_code, $paragraph_id, $content );
			}
		} // End Ad inline

		
		return $content;
	}
}
 
// Parent Function that makes the magic happen 
function favethemes_insert_after_paragraph( $insertion, $paragraph_id, $content ) {

	$content_buffer = '';

	$content_parts = preg_split('/(<p.*>)/U', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

	foreach ($content_parts as $content_part_index => $content_part) {
		if (!empty($content_part)) {

            if ($paragraph_id == $content_part_index / 2) {

            	$content_buffer .= $insertion;
            }
            $content_buffer .=  $content_part;

        }

	}

	$content = $content_buffer;

	return $content;
}

/*-----------------------------------------------------
 * Insert ads top of single post content.
 *-----------------------------------------------------*/

if ( ! function_exists( 'favethemes_insert_post_ads_top' ) ) {

	add_filter( 'the_content', 'favethemes_insert_post_ads_top' );

	function favethemes_insert_post_ads_top( $content ) {

		global $ft_option;

		$article_top_state = $ft_option['article_top_state'];
		$ad_top_code = $ft_option['article_ad_top'];

		// Top Ad
		if ( !empty( $ad_top_code) && $article_top_state != 0 ) {
			
			if ( is_single() && ! is_admin() ) {
				$content = '<div class="favethemes-content-ad-top">'.$ad_top_code.'</div>' . $content;
			}
		}
		
		return $content;
	}
}

/*-----------------------------------------------------
 * Insert ads bottom of single post content.
 *-----------------------------------------------------*/

if ( ! function_exists( 'favethemes_insert_post_ads_bottom' ) ) {

	add_filter( 'the_content', 'favethemes_insert_post_ads_bottom' );

	function favethemes_insert_post_ads_bottom( $content ) {

		global $ft_option;

		$article_bottom_state = $ft_option['article_bottom_state'];
		$ad_bottom_code = $ft_option['article_ad_bottom'];

		// Top Ad
		if ( !empty( $ad_bottom_code) && $article_bottom_state != 0 ) {
			
			if ( is_single() && ! is_admin() ) {
				$content = $content . '<div class="favethemes-content-ad-bottom">'.$ad_bottom_code.'</div>';
			}
		}
		
		return $content;
	}
}

/* --------------------------------------------------------------------------
 * Hex to RGB values
 ---------------------------------------------------------------------------*/

 if ( ! function_exists( 'favethemes_hex2rgb' ) ) {
	 function favethemes_hex2rgb($hex) {

	//$hex = str_replace("#", "", $hex);

	 	$hex = preg_replace("/#/", "", $hex );

	 	$color = array();

	 	if(strlen($hex) == 3) {
	 		$color['r'] = hexdec(substr($hex, 0, 1) );
	 		$color['g'] = hexdec(substr($hex, 1, 1) );
	 		$color['b'] = hexdec(substr($hex, 2, 1) );
	 	} else {
	 		$color['r'] = hexdec(substr($hex, 0, 2) );
	 		$color['g'] = hexdec(substr($hex, 2, 2) );
	 		$color['b'] = hexdec(substr($hex, 4, 4) );
	 	}

	 	return $color;
	 }
}


/* --------------------------------------------------------------------------
 * Open Graph
 ---------------------------------------------------------------------------*/

if ( ! function_exists( '' ) ) { 

	function magzilla_add_opengraph() {
		global $post; // Ensures we can use post variables outside the loop

		// Start with some values that don't change.
		echo "<meta property='og:site_name' content='". get_bloginfo('name') ."'/>"; // Sets the site name to the one in your WordPress settings
		echo "<meta property='og:url' content='" . get_permalink() . "'/>"; // Gets the permalink to the post/page

		if (is_singular()) { // If we are on a blog post/page
	        echo "<meta property='og:title' content='" . get_the_title() . "'/>"; // Gets the page title
	        echo "<meta property='og:type' content='article'/>"; // Sets the content type to be article.
	        if( has_post_thumbnail( $post->ID )) { // If the post has a featured image.
				$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
				echo "<meta property='og:image' content='" . esc_attr( $thumbnail[0] ) . "'/>"; // If it has a featured image, then display this for Facebook
			} 
	    } elseif(is_front_page() or is_home()) { // If it is the front page or home page
	    	echo "<meta property='og:title' content='" . get_bloginfo("name") . "'/>"; // Get the site title
	    	echo "<meta property='og:type' content='website'/>"; // Sets the content type to be website.
	    }

	}


	if ( !defined('WPSEO_VERSION') && !class_exists('NY_OG_Admin')) {
		add_action( 'wp_head', 'magzilla_add_opengraph', 5 );
	}
}



/* --------------------------------------------------------------------------
 
/**
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @since Magzilla 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string Filtered title.
 */
if ( ! function_exists( 'magzilla_wp_title' ) ) {

	function magzilla_wp_title( $title, $sep ) {
		global $paged, $page;

		if ( is_feed() )
			return $title;

		// Add the site name.
		$title .= get_bloginfo( 'name' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title = "$title $sep $site_description";

		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 )
			$title = "$title $sep " . sprintf( __( 'Page %s', 'favethemes' ), max( $paged, $page ) );

		return $title;
	}
	add_filter( 'wp_title', 'magzilla_wp_title', 10, 2 );
}

/**
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*	MagZilla Options link in admin bar
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*/

if ( ! function_exists( '' ) ) {

	function magzilla_admin_render() {
		global $wp_admin_bar;

		$wp_admin_bar->add_menu( array(

			'parent' => false, // use 'false' for a root menu, or pass the ID of parent menu
			'id' => 'smof_options',  // link ID, default ta a sanitized title value
			'title' => __('Magzilla Options', 'favethemes' ), // link title
			'href' => admin_url( 'themes.php?page=optionsframework' ),
			'meta' => false, 

		));

	}
	add_action( 'wp_before_admin_bar_render', 'magzilla_admin_render' );
}


/**
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*	MagZilla Change the columns for the edit CPT screen
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*/
function magzilla_change_columns( $cols ) {
	$cols = array(
			"cb" => '<input type="checkbox" />',
			"title" => __( "Title", "favethemes" ),
			"author" => __( "Author", "favethemes" ),
			"categories" => __( "Categories", "favethemes" ),
			"magzilla_featured" => __( "Featured", "favethemes" ),
			"tags" => __( "Tags", "favethemes" ),
			"comments" => __( "Comments", "favethemes" ),
			"date" => __( "Date", "favethemes" )
			
		);
	return $cols;
}
add_filter( "manage_posts_columns", "magzilla_change_columns" );

function magzilla_custom_columns( $column, $post_id ) {
	global $ft_option;
	
	switch ( $column ) {
		case "magzilla_featured":
			if( get_post_meta( $post_id, 'fave_featured', true ) == 1 ) {
					echo "Yes";
			} else {
				echo "-";
			}
		break;
	}
}

add_action( "manage_posts_custom_column", "magzilla_custom_columns", 10, 2 );



/**
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*	Add filter to posts for featured and reviews
*	----------------------------------------------------------------------------------------------------------------------------------------------------
*/
add_action( 'restrict_manage_posts', 'magzilla_admin_posts_filter_restrict_manage_posts' );

function magzilla_admin_posts_filter_restrict_manage_posts(){
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    
    if ('post' == $type){
        
        $values = array(
            'Featured Posts' => 'fave_featured',
            'Review Posts' => 'fave_review_checkbox',
        );
        ?>
        <select name="magzilla_custom_field">
        <option value=""><?php _e('Filter By Custom Fields', 'favethemes'); ?></option>
        <?php
            $current_v = isset($_GET['magzilla_custom_field'])? $_GET['magzilla_custom_field']:'';
            foreach ($values as $label => $value) {
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value == $current_v? ' selected="selected"':'',
                        $label
                    );
                }
        ?>
        </select>
        <?php
    }
}


add_filter( 'parse_query', 'magzilla_posts_filter' );


function magzilla_posts_filter( $query ){
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    if ( 'post' == $type && is_admin() && $pagenow=='edit.php' && isset($_GET['magzilla_custom_field']) && $_GET['magzilla_custom_field'] != '') {
        $query->query_vars['meta_key'] = $_GET['magzilla_custom_field'];
        $query->query_vars['meta_value'] = 1 ;
    }
}

/*-----------------------------------------------------------------------------------*/
/*	Register blog sidebar, footer and custom sidebar
/*-----------------------------------------------------------------------------------*/
	
if( function_exists('register_sidebar') ) {
		register_sidebar(array(
			'name' => 'Default Sidebar',
			'id' => 'default-sidebar',
			'description' => 'Widgets in this area will be shown in the sidebar.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));

		register_sidebar(array(
			'name' => 'Page Sidebar',
			'id' => 'page-sidebar',
			'description' => 'Widgets in this area will be shown in the sidebar of any page.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));

		register_sidebar(array(
			'name' => 'bbpress Sidebar',
			'id' => 'bbpress-sidebar',
			'description' => 'Widgets in this area will be shown in the sidebar of bbpress page.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));

		register_sidebar(array(
			'name' => 'Category Sidebar',
			'id' => 'category-sidebar',
			'description' => 'Widgets in this area will be shown in the sidebar of any category page.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));

		register_sidebar(array(
			'name' => 'Video Sidebar',
			'id' => 'video-sidebar',
			'description' => 'Widgets in this area will be shown in the sidebar for video post type.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));

		register_sidebar(array(
			'name' => 'Gallery Sidebar',
			'id' => 'gallery-sidebar',
			'description' => 'Widgets in this area will be shown in the sidebar for gallery post type.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
		
		register_sidebar(array(
			'name' => 'Author Page Sidebar',
			'id' => 'author-sidebar',
			'description' => 'Widgets in this area will be shown in the sidebar of author page.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));	
		register_sidebar(array(
			'name' => 'Search Page Sidebar',
			'id' => 'search-sidebar',
			'description' => 'Widgets in this area will be shown in the search page.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));	
		register_sidebar(array(
			'name' => 'Footer Col 1',
			'id' => 'footer-col-1',
			'description' => 'Widgets in this area will be shown in the footer col 1.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
		register_sidebar(array(
			'name' => 'Footer Col 2',
			'id' => 'footer-col-2',
			'description' => 'Widgets in this area will be shown in the footer col 2.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));

		register_sidebar(array(
			'name' => 'Footer Col 3',
			'id' => 'footer-col-3',
			'description' => 'Widgets in this area will be shown in the footer col 3.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
		register_sidebar(array(
			'name' => 'Footer Col 4',
			'id' => 'footer-col-4',
			'description' => 'Widgets in this area will be shown in the footer col 4s.',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
		
		
	}
?>
