<?php 
/**
 * Admin Page for Devshark Frontend Plugin
 * text domain: DEVSHARK_FRONTEND_TEXTDOMAIN
 * prefix: DEVSHARK_FRONTEND_PREFIX
 */

/**
 * Register settings
 */
 
$stylesFields = [
	[
		"name" => "category-color",
		"title" => "Category Color",
		"type" => "color" , 
		"placeholder" => "Enter your color"
	],
	[
		"name" => "category-hover-color",
		"title" => "Category Hover Color",
		"type" => "color" , 
		"placeholder" => "Enter your color"
	]
	,
	[
		"name" => "category-background-color",
		"title" => "Category Background Color",
		"type" => "color" , 
		"placeholder" => "Enter your color"
	],
	[
		"name" => "category-background-hover-color",
		"title" => "Category Background Hover Color",
		"type" => "color" , 
		"placeholder" => "Enter your color"
	]
	,
	[
		"name" => "category-border-color",
		"title" => "Category Border Color",
		"type" => "color" , 
		"placeholder" => "Enter your color"
	],
	[
		"name" => "category-border-hover-color",
		"title" => "Category Border Hover Color",
		"type" => "color" , 
		"placeholder" => "Enter your color"
	],
	[
		"name" => "category-font-size",
		"title" => "Category Font Size",
		"type" => "number" , 
		"placeholder" => "Enter Your Font Size (px)"
	],
	[
		"name" => "category-font-size-mobile",
		"title" => "Category Font Size ( Mobile )",
		"type" => "number" , 
		"placeholder" => "Enter Your Font Size (px)"
	], 
	
	// Pagination
	
	[
		"name" => "pagination-color",
		"title" => "Pagination Color",
		"type" => "color" , 
		"placeholder" => "Enter your color"
	],
	[
		"name" => "pagination-hover-color",
		"title" => "Pagination Hover Color",
		"type" => "color" , 
		"placeholder" => "Enter your color"
	]
	,
	[
		"name" => "pagination-background-color",
		"title" => "Pagination Background Color",
		"type" => "color" , 
		"placeholder" => "Enter your color"
	],
	[
		"name" => "pagination-background-hover-color",
		"title" => "Pagination Background Hover Color",
		"type" => "color" , 
		"placeholder" => "Enter your color"
	]
	,
	[
		"name" => "pagination-border-color",
		"title" => "Pagination Border Color",
		"type" => "color" , 
		"placeholder" => "Enter your color"
	],
	[
		"name" => "pagination-border-hover-color",
		"title" => "Pagination Border Hover Color",
		"type" => "color" , 
		"placeholder" => "Enter your color"
	],
	[
		"name" => "pagination-font-size",
		"title" => "Pagination Font Size",
		"type" => "number" , 
		"placeholder" => "Enter Your Font Size (px)"
	],
	[
		"name" => "pagination-font-size-mobile",
		"title" => "Pagination Font Size ( Mobile )",
		"type" => "number" , 
		"placeholder" => "Enter Your Font Size (px)"
	]
];
function devshark_frontend_register_settings() {
	global $stylesFields;
    // Register API URL
    register_setting( 
        'devshark_frontend_options',  // Option group
        DEVSHARK_FRONTEND_PREFIX . '-api-url',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default'           => '',
        )
    );
    
    // Register API Key
    register_setting( 
        'devshark_frontend_options',
        DEVSHARK_FRONTEND_PREFIX . '-api-key',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );
	
	    // Register API Key
    register_setting( 
        'devshark_frontend_options',
        DEVSHARK_FRONTEND_PREFIX . '-webs-slug',
        array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );
	
	register_setting( 
        'devshark_frontend_options',
        DEVSHARK_FRONTEND_PREFIX . '-portfolio_category',
        array(
            'type'              => 'array',
            
            'default'           => [],
        )
    );
	
	foreach($stylesFields as $field){
		register_setting( 
			'devshark_frontend_options',
			DEVSHARK_FRONTEND_PREFIX . '-' . $field["name"],
			 array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
	}
}
add_action( 'admin_init', 'devshark_frontend_register_settings' );


/**
 * Add admin menu
 */
function devshark_frontend_add_admin_menu() {
    add_menu_page( 
        __( 'Devshark Frontend Settings', DEVSHARK_FRONTEND_TEXTDOMAIN ), 
        __( 'Devshark Frontend', DEVSHARK_FRONTEND_TEXTDOMAIN ), 
        'manage_options', 
        'devshark-frontend-settings', 
        'devshark_frontend_settings_page',
        'dashicons-admin-generic',
        81
    );
}
add_action( 'admin_menu', 'devshark_frontend_add_admin_menu' );

/**
 * Settings page
 */
function devshark_frontend_settings_page() {
    // Check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    // Show success message
    if ( isset( $_GET['settings-updated'] ) ) {
        add_settings_error(
            'devshark_messages',
            'devshark_message',
            __( 'Settings Saved Successfully!', DEVSHARK_FRONTEND_TEXTDOMAIN ),
            'success'
        );
    }
    
    // Display error messages
    settings_errors( 'devshark_messages' );
    ?>
    <div class="dashwrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        
        <form action="options.php" method="post">
            <?php 
            // ✅ CRITICAL - Output security fields
            settings_fields( 'devshark_frontend_options' );
            ?>
            
            <div class="dashSec">
                <div class="dashCollmn">
                    <h3><?php esc_html_e( 'API Integration', DEVSHARK_FRONTEND_TEXTDOMAIN ); ?></h3>
                    
                    <div class="fieldBox">
                        <label class="fieldLabel" for="<?php echo esc_attr( DEVSHARK_FRONTEND_PREFIX ); ?>-api-url">
                            <?php esc_html_e( 'API URL:', DEVSHARK_FRONTEND_TEXTDOMAIN ); ?>
                        </label>
                        <input 
                            type="url" 
                            class="field"
                            placeholder="http://dev-admin.test/wp-json/devshark/v1/"
                            id="<?php echo esc_attr( DEVSHARK_FRONTEND_PREFIX ); ?>-api-url" 
                            name="<?php echo esc_attr( DEVSHARK_FRONTEND_PREFIX ); ?>-api-url" 
                            value="<?php echo esc_attr( get_option( DEVSHARK_FRONTEND_PREFIX . '-api-url', '' ) ); ?>" 
                        />
                        <p class="description">
                            <?php esc_html_e( 'Enter the base URL of your Devshark Backend API', DEVSHARK_FRONTEND_TEXTDOMAIN ); ?>
                        </p>
                    </div>
                    
                    <div class="fieldBox">
                        <label class="fieldLabel" for="<?php echo esc_attr( DEVSHARK_FRONTEND_PREFIX ); ?>-api-key">
                            <?php esc_html_e( 'API Key:', DEVSHARK_FRONTEND_TEXTDOMAIN ); ?>
                        </label>
                        <input 
                            type="text" 
                            class="field"
                            placeholder="<?php esc_attr_e( 'Enter your API key', DEVSHARK_FRONTEND_TEXTDOMAIN ); ?>"
                            id="<?php echo esc_attr( DEVSHARK_FRONTEND_PREFIX ); ?>-api-key" 
                            name="<?php echo esc_attr( DEVSHARK_FRONTEND_PREFIX ); ?>-api-key" 
                            value="<?php echo esc_attr( get_option( DEVSHARK_FRONTEND_PREFIX . '-api-key', '' ) ); ?>" 
                        />
                        <p class="description">
                            <?php esc_html_e( 'Get this from your Devshark Backend admin panel', DEVSHARK_FRONTEND_TEXTDOMAIN ); ?>
                        </p>
                    </div>
					
					<div class="fieldBox">
                        <label class="fieldLabel" for="<?php echo esc_attr( DEVSHARK_FRONTEND_PREFIX ); ?>-api-key">
                            <?php esc_html_e( 'Enter Portfolio Webs slug:', DEVSHARK_FRONTEND_TEXTDOMAIN ); ?>
                        </label>
                        <input 
                            type="text" 
                            class="field"
                            placeholder="<?php esc_attr_e( 'Enter your webs slug', DEVSHARK_FRONTEND_TEXTDOMAIN ); ?>"
                            id="<?php echo esc_attr( DEVSHARK_FRONTEND_PREFIX ); ?>-webs-slug" 
                            name="<?php echo esc_attr( DEVSHARK_FRONTEND_PREFIX ); ?>-webs-slug" 
                            value="<?php echo esc_attr( get_option( DEVSHARK_FRONTEND_PREFIX . '-webs-slug', '' ) ); ?>" 
                        />
                        <p class="description">
                            <?php esc_html_e( 'How to get a web slug: frist login administrator website -> Go to Portfolio -> Webs -> Edit a webs (nasiruddin) -> Copy slug', DEVSHARK_FRONTEND_TEXTDOMAIN ); ?>
                        </p>
                    </div>
					
					<div class="fieldBox">
					
						<?php 
							// API endpoint
							$portfolio_api_endpoint = '/portfolio-category';
							$params = array(
								'api_key' => sanitize_text_field( DEVSHARK_API_KEY ),
							);

							// Build URL
							$url = add_query_arg( $params, trailingslashit( DEVSHARK_API_URL ) . ltrim( $portfolio_api_endpoint, '/' ) );

							// Request arguments
							$args = array(
								'timeout' => 15,
								'headers' => array(
									'Accept' => 'application/json',
								),
							);

							// Make request
							$response = wp_remote_get( $url, $args );

							// Check for errors
							if ( is_wp_error( $response ) ) {
								echo '<p class="error">' . esc_html__( 'Error loading categories', 'devshark-frontend' ) . '</p>';
							} else {
								// Get response code
								$response_code = wp_remote_retrieve_response_code( $response );
								
								if ( $response_code === 200 ) {
									// Get and decode body
									$body = wp_remote_retrieve_body( $response );
									$data = json_decode( $body, true );
									
									// Check if data exists
									if ( ! empty( $data ) && is_array( $data ) ) {
										echo '<div class="portfolio-categories">';
										echo '<h4>' . esc_html__( 'Filter by Category', 'devshark-frontend' ) . '</h4>';
										echo '<div class="portfolio-categories_" >';
										
										foreach ( $data as $category ) {
											$term_id   = isset( $category['term_id'] ) ? absint( $category['term_id'] ) : 0;
											$term_name = isset( $category['name'] ) ? esc_html( $category['name'] ) : '';
											$term_slug = isset( $category['slug'] ) ? esc_attr( $category['slug'] ) : '';
											$count     = isset( $category['count'] ) ? absint( $category['count'] ) : 0;
											
											if ( $term_id && $term_name ) {
												?>
												<label class="category-checkbox">
													<input 
														type="checkbox" 
														name="<?php echo DEVSHARK_FRONTEND_PREFIX; ?>-portfolio_category[]" 
														value="<?php echo $term_id ; ?>" 
														<?php checked( in_array( $term_id , (array) get_option(DEVSHARK_FRONTEND_PREFIX . '-portfolio_category', []) ) ); ?>
														data-term-slug="<?php echo $term_slug; ?>"
													/>
													<?php echo $term_name; ?>
													<span class="count">(<?php echo $count; ?>)</span>
												</label>
												<?php
											}
										}
										echo "</div>";
										
										echo '</div>';
									} else {
										echo '<p>' . esc_html__( 'No categories found', 'devshark-frontend' ) . '</p>';
									}
								} else {
									echo '<p class="error">' . esc_html__( 'Failed to load categories', 'devshark-frontend' ) . '</p>';
								}
							}
						?>
                        
                    </div>
                </div>
                
                <div class="dashCollmn">   
                    <div class="shortcode-box">
						<h3><?php esc_html_e( 'Element Shortcode', DEVSHARK_FRONTEND_TEXTDOMAIN ); ?></h3>
                        <p><?php esc_html_e( 'Use this shortcode to display portfolio:', DEVSHARK_FRONTEND_TEXTDOMAIN ); ?></p>
                        <code>[<?php echo esc_html(DEVSHARK_FRONTEND_PREFIX . "-portfolio-list"); ?>]</code>
                    </div>
					
					
                </div>
				
				<div class="dashCollmn">   
					<div class="fieldBox">
						<h3><?php esc_html_e( 'Style', DEVSHARK_FRONTEND_TEXTDOMAIN ); ?></h3>
                        <div class="devshark-style-container" >
							<?php 
								global $stylesFields ;
								stylesFields($stylesFields);
							?>
						</div>
                    </div>
                </div>
            </div>
            
            <?php submit_button( __( 'Save Changes', DEVSHARK_FRONTEND_TEXTDOMAIN ) ); ?>
        </form>
    </div>
    <?php
}


function stylesFields($fields){
	if(!is_array($fields)){
		return ;
	}
	foreach($fields as $field){
		?>
		<div class="devshark-style-wrap" >
			<label class="fieldLabel" for="<?php echo esc_attr( DEVSHARK_FRONTEND_PREFIX ) . "-" . $field["name"]; ?>">
				<?php esc_html_e( $field["title"], DEVSHARK_FRONTEND_TEXTDOMAIN ); ?>
			</label>
			<input 
				type="<?php echo $field["type"]; ?>" 
				class="field"
				placeholder="<?php esc_attr_e( $field["placeholder"], DEVSHARK_FRONTEND_TEXTDOMAIN ); ?>"
				id="<?php echo esc_attr( DEVSHARK_FRONTEND_PREFIX ) . "-" . $field["name"]; ?>" 
				name="<?php echo esc_attr( DEVSHARK_FRONTEND_PREFIX ) . "-" . $field["name"]; ?>" 
				value="<?php echo esc_attr( get_option( DEVSHARK_FRONTEND_PREFIX . '-' . $field["name"] , '' ) ); ?>" 
				/>
		</div>
		<?php
	}
}