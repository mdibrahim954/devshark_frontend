<?php 

/**
 *   Enqueue the scripts and styles for the plugin
 */

function devshark_admin_enqueue_assets() {
    // Enqueue CSS
    wp_enqueue_style( 
        DEVSHARK_FRONTEND_PREFIX . '-styles', 
        plugin_dir_url( __FILE__ ) . 'assets/style.css', 
        array(), 
        '1.0.0' 
    );

    // Enqueue JS
    // wp_enqueue_script( 
    //     DEVSHARK_FRONTEND_PREFIX . '-scripts', 
    //     plugin_dir_url( __FILE__ ) . 'js/devshark-frontend-scripts.js', 
    //     array( 'jquery' ), 
    //     '1.0.0', 
    //     true 
    // );
}

add_action( 'admin_enqueue_scripts', 'devshark_admin_enqueue_assets' );


function devshark_frontend_enqueue_assets() {
	
	wp_enqueue_script(DEVSHARK_FRONTEND_PREFIX . "-portfolio-script", 
        plugin_dir_url( __FILE__ ) . 'assets/portfolio.js', 
        array('jquery'), 
        '1.0', 
        true
    );
	
	
	wp_enqueue_style(DEVSHARK_FRONTEND_PREFIX . "-portfolio-script", 
        plugin_dir_url( __FILE__ ) . 'assets/front-end-style.css', 
        '1.0', 
        true
    );
    
    // Pass AJAX URL and nonce to JavaScript
    wp_localize_script(DEVSHARK_FRONTEND_PREFIX . "-portfolio-script", 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce( DEVSHARK_FRONTEND_PREFIX . "-portfolio_list" ),
		"prefix" => DEVSHARK_FRONTEND_PREFIX
    ));
}

add_action( 'wp_enqueue_scripts', 'devshark_frontend_enqueue_assets' );