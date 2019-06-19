<?php

	class gol_wordpressActionsLogs{
		public function __construct(){
			
			$this->menuPageTitle = "Wordpress actions logs options";
			$this->menuPageLabel = "WAL Options";
			
			//add menu page
			add_action( 'admin_menu', array( $this, 'admin_menu_page' ) );

			add_action("add_meta_boxes", array($this, 'add_post_meta_box'));

			//add_action("post_updated", array($this, 'register_log'), 10, 3);
			add_action('updated_post_meta', array($this, 'register_log_meta'), 10, 4);
			
			add_action('gol_wal_register_log', array($this, 'register_log'), 10, 4);

	
			//do_action( "updated_{$meta_type}_meta", int $meta_id, int $object_id, string $meta_key, mixed $_meta_value )

			add_action( 'admin_enqueue_scripts', array($this, 'gol_wal_inject_style'));
			
		}

		public function gol_wal_inject_style(){
			//get options
			wp_enqueue_style('gol_wal_flag', plugin_dir_url( __FILE__ ) . '/css/flag.css', false);
		}

		public function admin_menu_page(){
			add_menu_page( $this->menuPageTitle, $this->menuPageLabel, 'manage_options', get_class($this), array($this,'wal_options_page'), 'dashicons-admin-tools', 100 );
		}

		public function wal_options_page(){
			?>
			<div class="wrap">
				<h1><?php echo $this->menuPageTitle; ?></h1>
				<div class="wrap">
					
				</div>
			</div>
			<?php
		}

		function add_post_meta_box_markup(){

			$key = 'gol_wal_post_updated';

			$meta_content = get_post_meta(get_the_ID(), $key, true);


			$pattern = '/\[(.*?)\[/m';
			$replacement = '<span class="gol_wal_flag ${1}">';
			$meta_content = preg_replace($pattern, $replacement, $meta_content);

			$pattern = '/\]\]/m';
			$replacement = '</span>';
			$meta_content = preg_replace($pattern, $replacement, $meta_content);


			echo '<div id="gol_wal_meta_box_log">' . $meta_content . '</div>';

		}

		function add_post_meta_box(){
			add_meta_box('wal_post_meta_box', __('Actions logs','gol_wal'), array($this, 'add_post_meta_box_markup'), 'offre_emploi', "normal", "high", null);
		}

		function register_log_meta($meta_id, $post_id, $meta_key, $meta_value){


			if( $meta_key == "type_de_contrat" ){

				$meta_key_logs = 'gol_wal_post_updated';

				$user = wp_get_current_user();
				$user_id = $user->data->ID;
				$user_name = $user->data->display_name;

				$generate_log = sprintf(__("<span class='line'>L'utilisateur [user[%s (id:%s)]] a changer [what[%s]] en [value[%s]]</span>"), $user_name, $user_id, $meta_key, $meta_value);
					
				$previous_logs = get_post_meta($post_id, $meta_key_logs, true);
				$new_logs = $generate_log . $previous_logs;

				update_post_meta($post_id, 'gol_wal_post_updated', $new_logs, $previous_logs);			
			}
		}

		function register_log( $post_id, $type, $what, $for){

			if( !$post_id ){
				return;
			}

			$meta_key_logs = 'gol_wal_post_updated';

			//get previous logs
			$previous_logs = get_post_meta($post_id, $meta_key_logs, true);

			//try get user
			$user = wp_get_current_user();
			$user_id = "";
			$user_name = "";
			if($user){
				if(isset($user->data->ID)){
					$user_id = $user->data->ID;
				}
				if(isset($user->data->display_name)){
					$user_name = $user->data->display_name;
				}
			}

			if( in_array($type, array("notification")) ){

				$generate_log = sprintf(
					__("L'utilisateur [user[%s (id:%s)]] a déclencher [what[%s]] pour [for[%s]]<br/>","gol_wal"), 
					$user_name, 
					$user_id, 
					$what, 
					$for
				);


			}
			
			//register log
			if( $generate_log ){
				$new_logs = $generate_log . $previous_logs;
				update_post_meta($post_id, 'gol_wal_post_updated', $new_logs, $previous_logs);			
			}
		}

		

		function register_log2($post_ID, $post_after, $post_before){
			/*
			$key = 'gol_wal_post_updated';

			$user = wp_get_current_user();
			$user_id = $user->data->ID;
			$user_name = $user->data->display_name;


			pre($post_after);

			pre($post_before);

			die();

			$previous_content = get_post_meta($post_ID, $key, true);

			$meta_content = sprintf(__("\rL'utilisateur %s ((id:%s)) a mis à jour le post %s","gol_wal"), $user_name, $user_id, $post_before->ID) . $previous_content;
			
			update_post_meta($post_ID, 'gol_wal_post_updated', $meta_content, $previous_content);
			*/
		}

	}


/*
	class magnification4Wordpress{

	public function __construct(){
		
		$this->menuPageTitle = "magnification4Wordpress Options";
		$this->menuPageLabel = "m4W Options";
		
		//add menu page
		add_action( 'admin_menu', array( $this, 'admin_menu_page' ) );
		add_action( 'admin_init', array( $this, 'magnification4Wordpress_settings' ) );
		add_action( 'wp_footer', array( $this, 'magnification4Wordpress_html_ouput') );
		
		//enqueue script
		add_action( 'wp_enqueue_scripts', array( $this, 'magnification4Wordpress_script'), 0 );
		
	}

	public function admin_menu_page(){
		add_menu_page( $this->menuPageTitle, $this->menuPageLabel, 'manage_options', get_class($this), array($this,'magnification4Wordpress_options_page'), 'dashicons-admin-tools', 100 );
	}

	public function magnification4Wordpress_script(){
		
		//get options
		$this->options = get_option( 'm4w_options' );
		wp_register_script( 'magnification4Wordpress-js', plugin_dir_url( __FILE__ ) . '/js/m4w.js', array('jquery'), "1.0", true);	
		wp_localize_script( 'magnification4Wordpress-js', 'm4w_container_id', $this->options['m4w_container_id']);
		wp_localize_script( 'magnification4Wordpress-js', 'm4w_zoom_factor', array($this->options['m4w_zoom_factor']));
		wp_enqueue_script( 'magnification4Wordpress-js' );
	}

	public function magnification4Wordpress_settings(){
		register_setting(
			'm4w_group',
			'm4w_options',
			array( $this, 'sanitize' )
		);
		add_settings_section(
			'm4w_section',
			'Configuration de base :',
			'',
			get_class($this)
		);  
		add_settings_field(
			'm4w_container_id',
			'Container ID',
			array( $this, 'field_container_id' ),
			get_class($this),
			'm4w_section'     
		);      
		add_settings_field(
			'm4w_zoom_factor', 
			'Zooom factor', 
			array( $this, 'field_zoom_factor' ), 
			get_class($this),
			'm4w_section'
		);

	}


	public function sanitize( $input ){
		$new_input = array();
		if( isset( $input['m4w_zoom_factor'] ) )
			$new_input['m4w_zoom_factor'] = floatval($input['m4w_zoom_factor']);
		if( isset( $input['m4w_container_id'] ) )
			$new_input['m4w_container_id'] = sanitize_text_field( $input['m4w_container_id'] );
		return $new_input;
	}


	public function magnification4Wordpress_options_page(){
		?>
		<div class="wrap">
			<h1><?php echo $this->menuPageTitle; ?></h1>
			<?php
				$this->options = get_option( 'm4w_options' );
			?>
			<div class="wrap">
				<form method="post" action="options.php">
				<?php
					// This prints out all hidden setting fields
					settings_fields( 'm4w_group' );
					do_settings_sections( get_class($this) );
					submit_button();
				?>
				</form>
			</div>
		</div>
		<?php
	}

	public function field_container_id(){
		printf(
			'<input type="text" id="m4w_container_id" name="m4w_options[m4w_container_id]" value="%s" />', 
			isset( $this->options['m4w_container_id'] ) ? esc_attr( $this->options['m4w_container_id']) : ''
		);
	}
	public function field_zoom_factor(){
		printf(
			'<input type="text" id="m4w_zoom_factor" name="m4w_options[m4w_zoom_factor]" value="%s" />',
			isset( $this->options['m4w_zoom_factor'] ) ? esc_attr( $this->options['m4w_zoom_factor']) : ''
		);
	}

	public function magnification4Wordpress_html_ouput(){
		
		//get options
		$this->options = get_option( 'm4w_options' );
		
		?>
			<div id="<?php echo $this->options["m4w_container_id"]; ?>">
				<ul>
					<li>
					<a href="#" class="zoom" data-action="zoom">A<sup>+</sup></a>
					</li>
					<li>
					<a href="#" class="normal" data-action="normal">A</a>
					</li>
					<li>
					<a href="#" class="macro" data-action="zoomout">A<sup>-</sup></a>
					</li>
				</ul>
			</div>
		<?php
	}
	}*/