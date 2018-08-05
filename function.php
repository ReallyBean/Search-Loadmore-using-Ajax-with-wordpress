<?php

function product_load_more_scripts() {

	global $wp_query; 

	wp_register_script( 'product_loadmore', get_stylesheet_directory_uri() . '/assets/js/productloadmore.js', array(), false, true);

	wp_localize_script( 'product_loadmore', 'product_loadmore_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
		'posts' => json_encode( $wp_query->query_vars ), // everything about your loop is here
		'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
		'search_keyword' => get_search_query() ? get_search_query() : '',
		'max_page' => $wp_query->max_num_pages
	) );

 	wp_enqueue_script( 'product_loadmore' );
}

add_action( 'wp_enqueue_scripts', 'product_load_more_scripts', 99 );

function product_loadmore_ajax_handler(){

	global $wp_query;
	// prepare our arguments for the query
	$args = json_decode( stripslashes( $_POST['query'] ), true );
	$args['paged'] = $_POST['page'] + 1; // we need next page to be loaded
	$args['s'] = $_POST['s'];
	$args['posts_per_page'] = 16;
	$args['post_status'] = 'publish';

	// it is always better to use WP_Query but not here
	query_posts( $args );
 	$response = [
 		'data' => "",
 		'is_last_page' => ( $wp_query->max_num_pages == $args['paged'] ),
 		'found_posts' => $wp_query->found_posts
 	];
	if( have_posts() ) :
 
		// run the loop
		while( have_posts() ): the_post();
 
 			ob_start();
			// look into your theme code how the posts are inserted, but you can use your own HTML of course
			// do you remember? - my example is adapted for Twenty Seventeen theme
			get_template_part( 'template-parts/product') + "'";
			// for the test purposes comment the line above and uncomment the below one
			// the_title();
			$response['data'] .= ob_get_clean(); 
 
 
		endwhile;
 
	endif;
	echo json_encode($response);
	die; // here we exit the script and even no wp_reset_query() required!
}

add_action('wp_ajax_loadmore', 'product_loadmore_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_loadmore', 'product_loadmore_ajax_handler'); // wp_ajax_nopriv_{action}
