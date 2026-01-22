<?php 

/**
 * create a function for showing portfolio items
 */

function devshark_frontend_display_portfolio_items() {
    // Verify nonce
	if(!wp_verify_nonce($_GET["nonce"] , DEVSHARK_FRONTEND_PREFIX . "-portfolio_list" )){
		wp_send_json_error( array(
            'message' => 'Request are not send using authonthice source!',
        ) );
		return;
	};
	
	// Api end point
    $portfolio_api_endpoint = '/portfolio';
	
	// Api url 
    $api_url = DEVSHARK_API_URL;
	
	// Api key 
    $api_key = DEVSHARK_API_KEY;
	
	$selected_cat = get_option(DEVSHARK_FRONTEND_PREFIX . '-portfolio_category', []);

    // Validate API settings
    if ( empty( $api_url ) || empty( $api_key ) ) {
        wp_send_json_error( array(
            'message' => 'API URL or API Key not configured.',
        ) );
        return;
    }

    // Get and sanitize page number
    $page_number = isset( $_GET['page_num'] ) ? absint( $_GET['page_num'] ) : 1;
	$devshark_webs = get_option( DEVSHARK_FRONTEND_PREFIX . '-webs-slug', '' );
	$dev_wb_exp = explode( " " , $devshark_webs );
	
	$terms = ( ! empty( $_GET['term_id'] ) && ( ! is_array($_GET['term_id']) ) && intval( $_GET['term_id'] ) !== 0 ) ? intval( $_GET['term_id'] ) : $selected_cat;
    // Build parameters
    $params = array(
        'api_key'  => sanitize_text_field( $api_key ),
        'per_page' => 10,
        'page_num' => $page_number,
		'webs' => !empty($dev_wb_exp[0]) ? $dev_wb_exp[0] : '' ,
		'term_id' => $terms,
    );

    // Build URL
    $url = add_query_arg( $params, trailingslashit( $api_url ) . ltrim( $portfolio_api_endpoint, '/' ) );

    // Request arguments
    $args = array(
        'timeout' => 15,
        'headers' => array(
            'Accept' => 'application/json',
        ),
    );
	
	
	
	if(empty($selected_cat)){
		wp_send_json_error( array(
            'message' => 'Please minimum Select a category!',
        ) );
        return;
	}

    // Make request
    $response = wp_remote_get( $url, $args );

    // Error handling
    if ( is_wp_error( $response ) ) {
        wp_send_json_error( array(
            'message' => 'Request failed: ' . $response->get_error_message(),
        ) );
        return;
    }

    // Get response code
    $response_code = wp_remote_retrieve_response_code( $response );

    if ( $response_code !== 200 ) {
        wp_send_json_error( array(
            'message' => 'API returned error code: ' . $response_code,
        ) );
        return;
    }

    // Get and decode response
    $body = wp_remote_retrieve_body( $response );
	
	$data = json_decode( $body, true );
	$data["__"] = $_GET['term_id'] ;
	$filtered_posts = filtered_posts_by_webs($data['posts'] , get_option( DEVSHARK_FRONTEND_PREFIX . '-webs-slug', '' ) );
	$data['posts'] = $filtered_posts ;
    // Check JSON decode
    if ( json_last_error() !== JSON_ERROR_NONE ) {
        wp_send_json_error( array(
            'message' => 'Invalid JSON response from API',
        ) );
        return;
    }

    // Success
    wp_send_json_success( array(
        'data' => $data,
		"filtered_posts" => $filtered_posts,
    ) );


}



function filtered_posts_by_webs( $posts, $web ) {

    if ( empty( $posts ) || empty( $web ) ) {
        return [];
    }

    $web  = trim( $web );
    $data = [];

    foreach ( $posts as $post ) {

        if ( empty( $post['webs'] ) || ! is_array( $post['webs'] ) ) {
            continue;
        }

        // Extract slugs once
        $slugs = array_column( $post['webs'], 'slug' );

        // Fast lookup + no duplicates
        if ( in_array( $web, $slugs, true ) ) {
            $data[] = $post;
        }
    }

    return $data;
}

// Create ajax request for get portfolio list 
add_action("wp_ajax_" . DEVSHARK_FRONTEND_PREFIX . "-portfolio_list" ,  "devshark_frontend_display_portfolio_items");
add_action("wp_ajax_nopriv_" . DEVSHARK_FRONTEND_PREFIX . "-portfolio_list" ,  "devshark_frontend_display_portfolio_items" );







/*
* Ajax Portfolio Categories 
*/
function devshark_get_portfolio_categories(){
	
	// endponts 
	$portfolio_api_endpoint = '/portfolio-category';
	
	// params 
	
	// combine params 
	$params = array(
        'api_key'  => sanitize_text_field( DEVSHARK_API_KEY ),
    );
	
	 // Request arguments
    $args = array(
        'timeout' => 15,
        'headers' => array(
            'Accept' => 'application/json',
        ),
    );
	// Build URL
    $url = add_query_arg( $params, trailingslashit( DEVSHARK_API_URL ) . ltrim( $portfolio_api_endpoint, '/' ) );
	
	// Make request
    $response = wp_remote_get( $url, $args );
	
	    // Get and decode response
    $body = wp_remote_retrieve_body( $response );
	
	$data = json_decode( $body, true );
	$filter_cat = filtered_posts_cat($data);
	wp_send_json_success( $filter_cat );
}
// Create ajax request for get portfolio list 
add_action("wp_ajax_" . DEVSHARK_FRONTEND_PREFIX . "-portfolio_categories" ,  "devshark_get_portfolio_categories");
add_action("wp_ajax_nopriv_" . DEVSHARK_FRONTEND_PREFIX . "-portfolio_categories" ,  "devshark_get_portfolio_categories" );



function filtered_posts_cat( $cats ) {

    if ( empty( $cats ) || ! is_array( $cats ) ) {
        return [];
    }

    $selected_cat = (array) get_option(
        DEVSHARK_FRONTEND_PREFIX . '-portfolio_category',
        []
    );

    // If nothing selected, return all cats
    if ( empty( $selected_cat ) ) {
        return $cats;
    }

    $selected_cat = array_map( 'intval', $selected_cat );
    $data         = [];

    foreach ( $cats as $cat ) {
        if ( in_array( (int) $cat['term_id'], $selected_cat, true ) ) {
            $data[] = $cat;
        }
    }

    return $data;
}


/**
* Create a function for show element or portfolio list
	@function DEVSHARK_FRONTEND_PREFIX . portfolios 
	v: 1.0.0
	author: Naim Bhuiya
*/

function devshark_frontend_portfolios (){
     ob_start(); // Start output buffering
	$html = '<div class="' . DEVSHARK_FRONTEND_PREFIX . '-portfolio-root" id="' . DEVSHARK_FRONTEND_PREFIX . '-portfolio-root" >';
		// Header 
		$html .='<div class="devshark_portfolio_categories" id="devshark_portfolio_categories" ></div>';
			// Body 
			$html .= '<div class="' . DEVSHARK_FRONTEND_PREFIX . '-portfolio-list" id="' . DEVSHARK_FRONTEND_PREFIX . '-portfolio-list" >';
				$html .= '<div class="devshark-post-item loading" ><div class="devshark-post-title loading" ></div><div class="devshark-post-excerpt loading" ></div></div>';
				$html .= '<div class="devshark-post-item loading" ><div class="devshark-post-title loading" ></div><div class="devshark-post-excerpt loading" ></div></div>';
				$html .= '<div class="devshark-post-item loading" ><div class="devshark-post-title loading" ></div><div class="devshark-post-excerpt loading" ></div></div>';
			$html .= '</div>';
		// Footer
		 $html .= '<div class="devshark_post_pagination" ></div>';
	$html .= "</div>";
	
	echo $html;
    return ob_get_clean(); // Return buffered content
};
add_shortcode( DEVSHARK_FRONTEND_PREFIX . "-portfolio-list" , "devshark_frontend_portfolios" );