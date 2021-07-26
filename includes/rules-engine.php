
<?php


add_action( 'init', 'badgeos_vendorlikes_load_triggers');
function badgeos_vendorlikes_load_triggers() {
	$woo_vendor_likes_trigger = 'badgeos_vendor_likes_trigger';
	add_action( $woo_vendor_likes_trigger, 'badgeos_vendor_likes_trigger_event', 10, 20 );
	
}
add_filter( 'badgeos_activity_triggers', 'badgeos_vendor_likes_activity_triggers'  );
function badgeos_vendor_likes_activity_triggers( $triggers ) {

	$triggers[ 'badgeos_vendor_likes_trigger' ] = __( 'Vendor High Fives', 'badgeos-vendor-likes' );
	return $triggers;
}
function vendorliked ($user_id) {
	global $wpdb, $blog_id;
	//badgeos_post_log_entry( null, $user_id, null,'vendor like triggered '.$user_id);
	$user_data = get_user_by( 'id', $user_id );
	$trigger_data = $wpdb->get_results( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE ( meta_key = '_badgeos_trigger_type' or meta_key = '_point_trigger_type' or meta_key = '_deduct_trigger_type' or meta_key = '_rank_trigger_type' ) AND meta_value = 'badgeos_vendor_likes_trigger'");
	if( $trigger_data ) {
		foreach( $trigger_data as $data ) {		
			//badgeos_post_log_entry( null, $user_id, null,'get post id'. $data->post_id);	
			$achievement_id =  $data->post_id;
			$user_crossed_max_allowed_earnings = badgeos_achievement_user_exceeded_max_earnings( $user_id, $achievement_id );
			//badgeos_post_log_entry( null, $user_id, null,'vendor user_crossed_max_allowed_earnings '. $user_crossed_max_allowed_earnings);	
			if ( ! $user_crossed_max_allowed_earnings ) {
				$trigger = $data->meta_value;
				$minimum_activity_count = absint( get_post_meta( $achievement_id, '_badgeos_count', true ) );
				$productlikecount = get_user_vendor_likes_count($user_id);
				
				$new_count = badgeos_update_user_trigger_count( $user_id, $trigger, $blog_id );
				//badgeos_post_log_entry( null, $user_id, null,'vendorlikecount '. $productlikecount.'minimum_activity_count '. $minimum_activity_count);	
			
				if ($productlikecount >= $minimum_activity_count) {
					badgeos_post_log_entry( null, $user_id, null, sprintf( __( '%1$s triggered %2$s (%3$dx)', 'product-likes' ), $user_data->user_login, $trigger, $new_count ) );
					//$userdeserves = badgeos_user_deserves_step(true, $user_id, $achievement_id, $trigger, 0, array());
					$args = array(
						'user_id' => $user_id,
						'achievement_id' => $achievement_id,
						);	
					$useralreadyearned = badgeos_get_user_achievements( $args );
					/*
					foreach ($useralreadyearned as $earnedachievement) {
						$title = get_the_title($earnedachievement->ID);
						badgeos_post_log_entry( null, $user_id, null, 'user already earned : '.$title );
					}
					*/	
					if (empty($useralreadyearned)) { 
						badgeos_award_achievement_to_user( $achievement_id, $user_id );		
					}			
				}
			}
			$parent_achievement = badgeos_get_parent_of_achievement( $achievement_id );
			if (!empty($parent_achievement->ID)) {
				
				$children =  badgeos_get_achievements( array( 'children_of' => $parent_achievement->ID));
				foreach ( $children as $sibling ) {
					
					// If this is the current step, we're good to go and skip
					if ( $sibling->ID == $achievement_id ) {
						
					} else {
					
						$step_requirements = badgeos_get_step_requirements( $sibling->ID);
						$userdeserves = badgeos_user_deserves_step(true, $user_id, $sibling->ID, 'step', 0, array());
						
						$args = array(
							'user_id' => $user_id,
							'achievement_id' =>  $sibling->ID,
							);
						$useralreadyearned = badgeos_get_user_achievements( $args );
						/*
						foreach ($useralreadyearned as $earnedachievement) {
							$title = get_the_title($earnedachievement->ID);
							badgeos_post_log_entry( null, $user_id, null, 'user already earned : '.$title );
						}
						*/							
						if ($userdeserves && empty($useralreadyearned)) {  
							badgeos_award_achievement_to_user( $sibling->ID, $user_id, $step_requirements['achievement_type'],0,array() );							
						}
					}
				}

				$userdeserves = badgeos_user_deserves_step(true, $user_id, $parent_achievement->ID, 'badges', 0, array());
				$args = array(
					'user_id' => $user_id,
					'achievement_id' => $parent_achievement->ID,
					);
					//'site_id' => 1,
					//'achievement_type' => false,
					//'since' => 0,
				$useralreadyearned = badgeos_get_user_achievements( $args );
				/*
				foreach ($useralreadyearned as $earnedachievement) {
					$title = get_the_title($earnedachievement->ID);
					badgeos_post_log_entry( null, $user_id, null, 'user already earned : '.$title );
				}
				*/					
				if ($userdeserves && empty($useralreadyearned)) { 
					badgeos_maybe_award_additional_achievements_to_user( $parent_achievement->ID, $user_id,'badges',0,array() );							
				}
			}
		}
	}
	
}
function get_user_vendor_likes_count($user_id) {
	global $wpdb;
	$vendor_likes_count = 0;
	$table_name = $wpdb->prefix . "vendor_likes";
	//badgeos_post_log_entry( null, $user_id, null, 'user  : '.$user_id."  ".$table_name );
	if (!empty($user_id)) {
		$results = $wpdb->get_results("SELECT DISTINCT vendor_trackid FROM $table_name WHERE user_id = $user_id");
		if ( $results ) {
			$vendor_likes_count = count($results);
			badgeos_post_log_entry( null, $user_id, null, 'high five count  : '.count($results));
		}
	}
	return $vendor_likes_count;
}



/**
* Ajax Call Vendors Likes
*/
function add_vendor_likes()
{
    // $ppp = (isset($_POST["ppp"])) ? $_POST["ppp"] : 3;
    $likes = (isset($_POST['likes'])) ? $_POST['likes'] : '';
    $vendor_id = (isset($_POST['vendor'])) ? $_POST['vendor'] : '';
    $user_id = (isset($_POST['user'])) ? $_POST['user'] : '';
    $date_time = date('d-m-y h:i:s');

    //DB And Table Name
    global $wpdb;
    $vendor_table_name = $wpdb->prefix . "vendor_likes";

    $ip_address = "";
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    }else{
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }

	
    header("Content-Type: text/html");

    if(!empty($user_id) && $user_id != 0)
    {
        $results = $wpdb->get_results("SELECT vendor_trackid FROM $vendor_table_name WHERE vendor_id = $vendor_id AND user_id = $user_id");
        if (count($results)> 0)
        {
			$vendor_total_likes = strval(get_post_meta($vendor_id, 'vendor_like' , true ));
			die($vendor_total_likes.' <i class="far fa-thumbs-up"></i>');
        }
        else
        {   
            $check_total_likes = get_post_meta($vendor_id, 'vendor_like' , true );
            $check_total_likes = $check_total_likes + 1;
            $vendor_likes = update_post_meta($vendor_id, 'vendor_like' , $check_total_likes );
            //$vendor_total_likes = get_post_meta($vendor_id, 'vendor_like' , true );
            $vendor_total_likes = strval($check_total_likes);
            $wpdb->insert($vendor_table_name, array(
                                    'user_id' => $user_id, 
                                    'vendor_id' => $vendor_id,
                                    'date_time' => $date_time
								   ));
			vendorliked($user_id);		           		   
            die($vendor_total_likes.' <i class="far fa-thumbs-up"></i>');
        }
    }
    else
    {   
       
        $results = $wpdb->get_results("SELECT vendor_trackid FROM $vendor_table_name WHERE vendor_id = $vendor_id AND ip_address = $ip_address AND user_id IS NULL");
        if (count($results)> 0)
        {
			$vendor_total_likes = strval(get_post_meta($vendor_id, 'vendor_like' , true ));
			die($vendor_total_likes.' <i class="far fa-thumbs-up"></i>');
        }
        else{ 
            $check_total_likes = get_post_meta($vendor_id, 'vendor_like' , true );
            $check_total_likes = $check_total_likes + 1;
            $vendor_likes = update_post_meta($vendor_id, 'vendor_like' , $check_total_likes);
            //$vendor_total_likes = get_post_meta($vendor_id, 'vendor_like' , true );
            $vendor_total_likes = strval($check_total_likes);

            $wpdb->insert($vendor_table_name, array( 
                                    'vendor_id' => $vendor_id,
                                    'ip_address' => $ip_address,
                                    'date_time' => $date_time
                                   ));
		 //die($vendor_total_likes);
		 
		 die($vendor_total_likes.' <i class="far fa-thumbs-up"></i>');
        }
    }
}

add_action( 'template_redirect', function() {
    if ( is_singular( 'vendor' ) ) {
        global $wp_query;
        $page = ( int ) $wp_query->get( 'page' );
        if ( $page > 1 ) {
            // convert 'page' to 'paged'
            $wp_query->set( 'page', 1 );
            $wp_query->set( 'paged', $page );
        }
        // prevent redirect
        remove_action( 'template_redirect', 'redirect_canonical' );
    }
}, 0 ); // on priority 0 to remove 'redirect_canonical' added with priority 10

function my_pagination_link( $label = NULL, $dir = 'next', WP_Query $query = NULL ) {
    if ( is_null( $query ) ) {
        $query = $GLOBALS['wp_query'];
    }
    $max_page = ( int ) $query->max_num_pages;
    // only one page for the query, do nothing
    if ( $max_page <= 1 ) {
        return;
    }
    $paged = ( int ) $query->get( 'paged' );
    if ( empty( $paged ) ) {
        $paged = 1;
    }
    $target_page = $dir === 'next' ?  $paged + 1 : $paged - 1;
    // if 1st page requiring previous or last page requiring next, do nothing
    if ( $target_page < 1 || $target_page > $max_page ) {
        return;
    }
    if ( null === $label ) {
        $label = __( 'Next Page &raquo;' );
    }

    $label = preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label );
    printf( '<a href="%s">%s</a>', get_pagenum_link( $target_page ), esc_html( $label ) );
}
//add_options_page( 'Vendor Spotlight Page', 'Vendor Spotlight Page', 'manage_options','vendor_spotlight_settings', 'vendor_spotlight_settings_callback' );
add_action( 'admin_menu', 'vendor_spotlight_settings' );
function vendor_spotlight_settings() {
	add_submenu_page( 'edit.php?post_type=vendor', 'Vendor Spotlight Page', 'Vendor Spotlight Page', 'manage_options', 'vendor_spotlight_settings', 'vendor_spotlight_settings_page');
}

add_action( 'admin_init', 'vendor_spotlight_settings_init' );
function vendor_spotlight_settings_init(  ) {
    register_setting( 'VendorSpotlight', 'vendor_spotlight_settings' );
    add_settings_section(
        'vendor_spotlight_section',
        __( '', 'wordpress' ),
        'vendor_spotlight_settings_callback',
        'VendorSpotlight'
    );

    add_settings_field(
        'single_vendor_main_title',
        __( 'Main Title (use [vendor] as vendor name placeholder)', 'wordpress' ),
        'single_vendor_main_title_render',
        'VendorSpotlight',
        'vendor_spotlight_section'
    );

    add_settings_field(
        'single_vendor_secondary_title',
        __( 'Secondary Title', 'wordpress' ),
        'single_vendor_secondary_title_render',
        'VendorSpotlight',
        'vendor_spotlight_section'
    );
    add_settings_field(
        'single_vendor_header',
        __( 'Default Header Background', 'wordpress' ),
        'single_vendor_header_render',
        'VendorSpotlight',
        'vendor_spotlight_section'
    );
}
function single_vendor_main_title_render() {
	$single_vendor_main_title = get_option( 'single_vendor_main_title');
	?><input type="text" name="single_vendor_main_title" value="<?php echo $single_vendor_main_title; ?>" size="100%" ><?php
}
function single_vendor_secondary_title_render() {
	$single_vendor_secondary_title = get_option( 'single_vendor_secondary_title');
	?><input type="text" name="single_vendor_secondary_title" value="<?php echo $single_vendor_secondary_title; ?>" size="100%" ><?php
}
function single_vendor_header_render() {
	$single_vendor_header = get_option( 'single_vendor_header');
	if( $image = wp_get_attachment_image_src( $single_vendor_header ) ) { 
        echo '
        <div class="header-background-upl-img"><p><img class="header-background-upl" src="' . $image[0] . '" style="max-height: 100px;" /></p></div>
        <input type="hidden" name="upload_image_id" value="' . $v_profile_pic . '" id="upload_image_id_fld">
        <p>        
        <button class="button button-primary header-background-upl">Add Header Background</button>
        <button class="button header-backgroundrmv">Remove Header Background</button></p>';
    } else {
        echo '
        <div class="header-background-upl-img"></div>
        <input type="hidden" name="upload_image_id" value="" id="upload_image_id_fld">
        <p>
        <button class="button button-primary header-background-upl">Add Header Background</button>
        <button class="button header-backgroundrmv" style="display:none">Remove Header Background</button>
        </p>';
        
    }
	?>
	<script type="text/javascript">
        jQuery(document).ready(function($) {
            
            // on upload button click
            $('body').on( 'click', '.header-background-upl', function(e){
        
                e.preventDefault();
        
                var button = $(this),
                custom_uploader = wp.media({
                    title: 'Insert image',
                    library : {
                        // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                        type : 'image'
                    },
                    button: {
                        text: 'Use this image' // button label text
                    },
                    multiple: false
                }).on('select', function() { // it also has "open" and "close" events
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('.header-background-upl-img').html('<img src="' + attachment.url + '" style="max-height: 100px">');
                    $('#upload_image_id_fld').val(attachment.id);
                    $('.header-backgroundrmv').show();
                }).open();
        
            });
        
            // on remove button click
            $('body').on('click', '.header-backgroundrmv', function(e){
        
                e.preventDefault();
        
                var button = $(this);
                $('.header-background-upl-img').html('<p></p>');
                $('#upload_image_id_fld').val(''); // emptying the hidden field
                $('.header-backgroundrmv').hide();
                
            });

        });
    </script>
<?php
}
function vendor_spotlight_settings_callback() {
    //echo __( 'Vendor Spotlight Page Settings', 'wordpress' );
    
}

function vendor_spotlight_settings_page(  ) {
	if (isset($_POST['_wpnonce'])) {
        update_option('single_vendor_main_title', $_POST['single_vendor_main_title']);
        update_option('single_vendor_secondary_title', $_POST['single_vendor_secondary_title']);
        update_option('single_vendor_header', $_POST['upload_image_id']);
    } 
    ?>
    <form action='' method='post'>

        <h2>Vendor Spotlight Page Settings</h2>

        <?php
        settings_fields( 'VendorSpotlight' );
        do_settings_sections( 'VendorSpotlight' );
		wp_nonce_field( 'wpshout_vendor_spotlight_settings_action' );
        submit_button();
        ?>

    </form>
    <?php
	
} ?>
<?php


add_action( 'init', 'badgeos_vendorlikes_load_triggers');
function badgeos_vendorlikes_load_triggers() {
	$woo_vendor_likes_trigger = 'badgeos_vendor_likes_trigger';
	add_action( $woo_vendor_likes_trigger, 'badgeos_vendor_likes_trigger_event', 10, 20 );
	
}
add_filter( 'badgeos_activity_triggers', 'badgeos_vendor_likes_activity_triggers'  );
function badgeos_vendor_likes_activity_triggers( $triggers ) {

	$triggers[ 'badgeos_vendor_likes_trigger' ] = __( 'Vendor High Fives', 'badgeos-vendor-likes' );
	return $triggers;
}
function vendorliked ($user_id) {
	global $wpdb, $blog_id;
	//badgeos_post_log_entry( null, $user_id, null,'vendor like triggered '.$user_id);
	$user_data = get_user_by( 'id', $user_id );
	$trigger_data = $wpdb->get_results( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE ( meta_key = '_badgeos_trigger_type' or meta_key = '_point_trigger_type' or meta_key = '_deduct_trigger_type' or meta_key = '_rank_trigger_type' ) AND meta_value = 'badgeos_vendor_likes_trigger'");
	if( $trigger_data ) {
		foreach( $trigger_data as $data ) {		
			//badgeos_post_log_entry( null, $user_id, null,'get post id'. $data->post_id);	
			$achievement_id =  $data->post_id;
			$user_crossed_max_allowed_earnings = badgeos_achievement_user_exceeded_max_earnings( $user_id, $achievement_id );
			//badgeos_post_log_entry( null, $user_id, null,'vendor user_crossed_max_allowed_earnings '. $user_crossed_max_allowed_earnings);	
			if ( ! $user_crossed_max_allowed_earnings ) {
				$trigger = $data->meta_value;
				$minimum_activity_count = absint( get_post_meta( $achievement_id, '_badgeos_count', true ) );
				$productlikecount = get_user_vendor_likes_count($user_id);
				
				$new_count = badgeos_update_user_trigger_count( $user_id, $trigger, $blog_id );
				//badgeos_post_log_entry( null, $user_id, null,'vendorlikecount '. $productlikecount.'minimum_activity_count '. $minimum_activity_count);	
			
				if ($productlikecount >= $minimum_activity_count) {
					badgeos_post_log_entry( null, $user_id, null, sprintf( __( '%1$s triggered %2$s (%3$dx)', 'product-likes' ), $user_data->user_login, $trigger, $new_count ) );
					//$userdeserves = badgeos_user_deserves_step(true, $user_id, $achievement_id, $trigger, 0, array());
					$args = array(
						'user_id' => $user_id,
						'achievement_id' => $achievement_id,
						);	
					$useralreadyearned = badgeos_get_user_achievements( $args );
					/*
					foreach ($useralreadyearned as $earnedachievement) {
						$title = get_the_title($earnedachievement->ID);
						badgeos_post_log_entry( null, $user_id, null, 'user already earned : '.$title );
					}
					*/	
					if (empty($useralreadyearned)) { 
						badgeos_award_achievement_to_user( $achievement_id, $user_id );		
					}			
				}
			}
			$parent_achievement = badgeos_get_parent_of_achievement( $achievement_id );
			if (!empty($parent_achievement->ID)) {
				
				$children =  badgeos_get_achievements( array( 'children_of' => $parent_achievement->ID));
				foreach ( $children as $sibling ) {
					
					// If this is the current step, we're good to go and skip
					if ( $sibling->ID == $achievement_id ) {
						
					} else {
					
						$step_requirements = badgeos_get_step_requirements( $sibling->ID);
						$userdeserves = badgeos_user_deserves_step(true, $user_id, $sibling->ID, 'step', 0, array());
						
						$args = array(
							'user_id' => $user_id,
							'achievement_id' =>  $sibling->ID,
							);
						$useralreadyearned = badgeos_get_user_achievements( $args );
						/*
						foreach ($useralreadyearned as $earnedachievement) {
							$title = get_the_title($earnedachievement->ID);
							badgeos_post_log_entry( null, $user_id, null, 'user already earned : '.$title );
						}
						*/							
						if ($userdeserves && empty($useralreadyearned)) {  
							badgeos_award_achievement_to_user( $sibling->ID, $user_id, $step_requirements['achievement_type'],0,array() );							
						}
					}
				}

				$userdeserves = badgeos_user_deserves_step(true, $user_id, $parent_achievement->ID, 'badges', 0, array());
				$args = array(
					'user_id' => $user_id,
					'achievement_id' => $parent_achievement->ID,
					);
					//'site_id' => 1,
					//'achievement_type' => false,
					//'since' => 0,
				$useralreadyearned = badgeos_get_user_achievements( $args );
				/*
				foreach ($useralreadyearned as $earnedachievement) {
					$title = get_the_title($earnedachievement->ID);
					badgeos_post_log_entry( null, $user_id, null, 'user already earned : '.$title );
				}
				*/					
				if ($userdeserves && empty($useralreadyearned)) { 
					badgeos_maybe_award_additional_achievements_to_user( $parent_achievement->ID, $user_id,'badges',0,array() );							
				}
			}
		}
	}
	
}
function get_user_vendor_likes_count($user_id) {
	global $wpdb;
	$vendor_likes_count = 0;
	$table_name = $wpdb->prefix . "vendor_likes";
	//badgeos_post_log_entry( null, $user_id, null, 'user  : '.$user_id."  ".$table_name );
	if (!empty($user_id)) {
		$results = $wpdb->get_results("SELECT DISTINCT vendor_trackid FROM $table_name WHERE user_id = $user_id");
		if ( $results ) {
			$vendor_likes_count = count($results);
			badgeos_post_log_entry( null, $user_id, null, 'high five count  : '.count($results));
		}
	}
	return $vendor_likes_count;
}



/**
* Ajax Call Vendors Likes
*/
function add_vendor_likes()
{
    // $ppp = (isset($_POST["ppp"])) ? $_POST["ppp"] : 3;
    $likes = (isset($_POST['likes'])) ? $_POST['likes'] : '';
    $vendor_id = (isset($_POST['vendor'])) ? $_POST['vendor'] : '';
    $user_id = (isset($_POST['user'])) ? $_POST['user'] : '';
    $date_time = date('d-m-y h:i:s');

    //DB And Table Name
    global $wpdb;
    $vendor_table_name = $wpdb->prefix . "vendor_likes";

    $ip_address = "";
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    }else{
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }

	
    header("Content-Type: text/html");

    if(!empty($user_id) && $user_id != 0)
    {
        $results = $wpdb->get_results("SELECT vendor_trackid FROM $vendor_table_name WHERE vendor_id = $vendor_id AND user_id = $user_id");
        if (count($results)> 0)
        {
			$vendor_total_likes = strval(get_post_meta($vendor_id, 'vendor_like' , true ));
			die($vendor_total_likes.' <i class="far fa-thumbs-up"></i>');
        }
        else
        {   
            $check_total_likes = get_post_meta($vendor_id, 'vendor_like' , true );
            $check_total_likes = $check_total_likes + 1;
            $vendor_likes = update_post_meta($vendor_id, 'vendor_like' , $check_total_likes );
            //$vendor_total_likes = get_post_meta($vendor_id, 'vendor_like' , true );
            $vendor_total_likes = strval($check_total_likes);
            $wpdb->insert($vendor_table_name, array(
                                    'user_id' => $user_id, 
                                    'vendor_id' => $vendor_id,
                                    'date_time' => $date_time
								   ));
			vendorliked($user_id);		           		   
            die($vendor_total_likes.' <i class="far fa-thumbs-up"></i>');
        }
    }
    else
    {   
       
        $results = $wpdb->get_results("SELECT vendor_trackid FROM $vendor_table_name WHERE vendor_id = $vendor_id AND ip_address = $ip_address AND user_id IS NULL");
        if (count($results)> 0)
        {
			$vendor_total_likes = strval(get_post_meta($vendor_id, 'vendor_like' , true ));
			die($vendor_total_likes.' <i class="far fa-thumbs-up"></i>');
        }
        else{ 
            $check_total_likes = get_post_meta($vendor_id, 'vendor_like' , true );
            $check_total_likes = $check_total_likes + 1;
            $vendor_likes = update_post_meta($vendor_id, 'vendor_like' , $check_total_likes);
            //$vendor_total_likes = get_post_meta($vendor_id, 'vendor_like' , true );
            $vendor_total_likes = strval($check_total_likes);

            $wpdb->insert($vendor_table_name, array( 
                                    'vendor_id' => $vendor_id,
                                    'ip_address' => $ip_address,
                                    'date_time' => $date_time
                                   ));
		 //die($vendor_total_likes);
		 
		 die($vendor_total_likes.' <i class="far fa-thumbs-up"></i>');
        }
    }
}

add_action( 'template_redirect', function() {
    if ( is_singular( 'vendor' ) ) {
        global $wp_query;
        $page = ( int ) $wp_query->get( 'page' );
        if ( $page > 1 ) {
            // convert 'page' to 'paged'
            $wp_query->set( 'page', 1 );
            $wp_query->set( 'paged', $page );
        }
        // prevent redirect
        remove_action( 'template_redirect', 'redirect_canonical' );
    }
}, 0 ); // on priority 0 to remove 'redirect_canonical' added with priority 10

function my_pagination_link( $label = NULL, $dir = 'next', WP_Query $query = NULL ) {
    if ( is_null( $query ) ) {
        $query = $GLOBALS['wp_query'];
    }
    $max_page = ( int ) $query->max_num_pages;
    // only one page for the query, do nothing
    if ( $max_page <= 1 ) {
        return;
    }
    $paged = ( int ) $query->get( 'paged' );
    if ( empty( $paged ) ) {
        $paged = 1;
    }
    $target_page = $dir === 'next' ?  $paged + 1 : $paged - 1;
    // if 1st page requiring previous or last page requiring next, do nothing
    if ( $target_page < 1 || $target_page > $max_page ) {
        return;
    }
    if ( null === $label ) {
        $label = __( 'Next Page &raquo;' );
    }

    $label = preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label );
    printf( '<a href="%s">%s</a>', get_pagenum_link( $target_page ), esc_html( $label ) );
}
//add_options_page( 'Vendor Spotlight Page', 'Vendor Spotlight Page', 'manage_options','vendor_spotlight_settings', 'vendor_spotlight_settings_callback' );
add_action( 'admin_menu', 'vendor_spotlight_settings' );
function vendor_spotlight_settings() {
	add_submenu_page( 'edit.php?post_type=vendor', 'Vendor Spotlight Page', 'Vendor Spotlight Page', 'manage_options', 'vendor_spotlight_settings', 'vendor_spotlight_settings_page');
}

add_action( 'admin_init', 'vendor_spotlight_settings_init' );
function vendor_spotlight_settings_init(  ) {
    register_setting( 'VendorSpotlight', 'vendor_spotlight_settings' );
    add_settings_section(
        'vendor_spotlight_section',
        __( '', 'wordpress' ),
        'vendor_spotlight_settings_callback',
        'VendorSpotlight'
    );

    add_settings_field(
        'single_vendor_main_title',
        __( 'Main Title (use [vendor] as vendor name placeholder)', 'wordpress' ),
        'single_vendor_main_title_render',
        'VendorSpotlight',
        'vendor_spotlight_section'
    );

    add_settings_field(
        'single_vendor_secondary_title',
        __( 'Secondary Title', 'wordpress' ),
        'single_vendor_secondary_title_render',
        'VendorSpotlight',
        'vendor_spotlight_section'
    );
    add_settings_field(
        'single_vendor_header',
        __( 'Default Header Background', 'wordpress' ),
        'single_vendor_header_render',
        'VendorSpotlight',
        'vendor_spotlight_section'
    );
}
function single_vendor_main_title_render() {
	$single_vendor_main_title = get_option( 'single_vendor_main_title');
	?><input type="text" name="single_vendor_main_title" value="<?php echo $single_vendor_main_title; ?>" size="100%" ><?php
}
function single_vendor_secondary_title_render() {
	$single_vendor_secondary_title = get_option( 'single_vendor_secondary_title');
	?><input type="text" name="single_vendor_secondary_title" value="<?php echo $single_vendor_secondary_title; ?>" size="100%" ><?php
}
function single_vendor_header_render() {
	$single_vendor_header = get_option( 'single_vendor_header');
	if( $image = wp_get_attachment_image_src( $single_vendor_header ) ) { 
        echo '
        <div class="header-background-upl-img"><p><img class="header-background-upl" src="' . $image[0] . '" style="max-height: 100px;" /></p></div>
        <input type="hidden" name="upload_image_id" value="' . $v_profile_pic . '" id="upload_image_id_fld">
        <p>        
        <button class="button button-primary header-background-upl">Add Header Background</button>
        <button class="button header-backgroundrmv">Remove Header Background</button></p>';
    } else {
        echo '
        <div class="header-background-upl-img"></div>
        <input type="hidden" name="upload_image_id" value="" id="upload_image_id_fld">
        <p>
        <button class="button button-primary header-background-upl">Add Header Background</button>
        <button class="button header-backgroundrmv" style="display:none">Remove Header Background</button>
        </p>';
        
    }
	?>
	<script type="text/javascript">
        jQuery(document).ready(function($) {
            
            // on upload button click
            $('body').on( 'click', '.header-background-upl', function(e){
        
                e.preventDefault();
        
                var button = $(this),
                custom_uploader = wp.media({
                    title: 'Insert image',
                    library : {
                        // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                        type : 'image'
                    },
                    button: {
                        text: 'Use this image' // button label text
                    },
                    multiple: false
                }).on('select', function() { // it also has "open" and "close" events
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('.header-background-upl-img').html('<img src="' + attachment.url + '" style="max-height: 100px">');
                    $('#upload_image_id_fld').val(attachment.id);
                    $('.header-backgroundrmv').show();
                }).open();
        
            });
        
            // on remove button click
            $('body').on('click', '.header-backgroundrmv', function(e){
        
                e.preventDefault();
        
                var button = $(this);
                $('.header-background-upl-img').html('<p></p>');
                $('#upload_image_id_fld').val(''); // emptying the hidden field
                $('.header-backgroundrmv').hide();
                
            });

        });
    </script>
<?php
}
function vendor_spotlight_settings_callback() {
    //echo __( 'Vendor Spotlight Page Settings', 'wordpress' );
    
}

function vendor_spotlight_settings_page(  ) {
	if (isset($_POST['_wpnonce'])) {
        update_option('single_vendor_main_title', $_POST['single_vendor_main_title']);
        update_option('single_vendor_secondary_title', $_POST['single_vendor_secondary_title']);
        update_option('single_vendor_header', $_POST['upload_image_id']);
    } 
    ?>
    <form action='' method='post'>

        <h2>Vendor Spotlight Page Settings</h2>

        <?php
        settings_fields( 'VendorSpotlight' );
        do_settings_sections( 'VendorSpotlight' );
		wp_nonce_field( 'wpshout_vendor_spotlight_settings_action' );
        submit_button();
        ?>

    </form>
    <?php
	
}
?>