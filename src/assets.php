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
	
	
	wp_enqueue_style(DEVSHARK_FRONTEND_PREFIX . "-portfolio-style", 
        plugin_dir_url( __FILE__ ) . 'assets/front-end-style.css', 
        '1.0', 
        true
    );
	
	 // Define the inline CSS
    $custom_css = '
        .devshark-portfolio-categories {
            color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-category-color', '#ffffffff' ) . ' !important;
            background-color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-category-background-color', '#ffffff00' ) . ' !important;
            border-color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-category-border-color', '#f39c12' ) . ' !important;
            font-size: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-category-font-size', '14' ) . 'px !important;
        }
        .devshark-portfolio-categories:hover {
            color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-category-hover-color', '#000000' ) . ' !important;
            background-color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-category-background-hover-color', '#f39c12' ) . ' !important;
            border-color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-category-border-hover-color', '#00000000' ) . ' !important;
        }
        .devshark-portfolio-categories.active {
            color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-category-hover-color', '#000000' ) . ' !important;
            background-color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-category-background-hover-color', '#f39c12' ) . ' !important;
            border-color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-category-border-hover-color', '#00000000' ) . ' !important;
        }

        .devshark-pagination-item {
            color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-pagination-color', '#ffffffff' ) . ' !important;
            background-color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-pagination-background-color', '#ffffff00' ) . ' !important;
            border-color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-pagination-border-color', '#f39c12' ) . ' !important;
            font-size: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-pagination-font-size', '14' ) . 'px !important;
        }
        .devshark-pagination-item:hover {
            color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-pagination-hover-color', '#000000' ) . ' !important;
            background-color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-pagination-background-hover-color', '#f39c12' ) . ' !important;
            border-color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-pagination-border-hover-color', '#00000000' ) . ' !important;
        }
        .devshark-pagination-item.active {
            color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-pagination-hover-color', '#000000' ) . ' !important;
            background-color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-pagination-background-hover-color', '#f39c12' ) . ' !important;
            border-color: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-pagination-border-hover-color', '#00000000' ) . ' !important;
        }

        media only screen and (max-width: 600px) {
            .devshark-portfolio-categories {
                font-size: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-category-font-size-mobile', '14' ) . 'px !important;
            }
            .devshark-pagination-item {
                font-size: ' . get_option( DEVSHARK_FRONTEND_PREFIX . '-category-font-size-mobile', '14' ) . 'px !important;
            }
        }
    ';

    // Add the inline CSS to the 'main-style' handle
    wp_add_inline_style( DEVSHARK_FRONTEND_PREFIX . "-portfolio-style", $custom_css );
    
    // Pass AJAX URL and nonce to JavaScript
    wp_localize_script(DEVSHARK_FRONTEND_PREFIX . "-portfolio-script", 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce( DEVSHARK_FRONTEND_PREFIX . "-portfolio_list" ),
		"prefix" => DEVSHARK_FRONTEND_PREFIX
    ));
}

add_action( 'wp_enqueue_scripts', 'devshark_frontend_enqueue_assets' );