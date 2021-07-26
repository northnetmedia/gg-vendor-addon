<?php


/**
 * Register a Vendors Post Type. 
 */

function vendor_init() {
    $labels = array(
        'name'               => _x( 'Vendors', 'post type general name', 'your-plugin-textdomain' ),
        'singular_name'      => _x( 'Vendor', 'post type singular name', 'your-plugin-textdomain' ),
        'menu_name'          => _x( 'Vendors', 'admin menu', 'your-plugin-textdomain' ),
        'name_admin_bar'     => _x( 'Vendor', 'add new on admin bar', 'your-plugin-textdomain' ),
        'add_new'            => _x( 'Add New', 'Vendor', 'your-plugin-textdomain' ),
        'add_new_item'       => __( 'Add New Vendor', 'your-plugin-textdomain' ),
        'new_item'           => __( 'New Vendor', 'your-plugin-textdomain' ),
        'edit_item'          => __( 'Edit Vendor', 'your-plugin-textdomain' ),
        'view_item'          => __( 'View Vendor', 'your-plugin-textdomain' ),
        'all_items'          => __( 'All Vendors', 'your-plugin-textdomain' ),
        'search_items'       => __( 'Search Vendors', 'your-plugin-textdomain' ),
        'parent_item_colon'  => __( 'Parent Vendors:', 'your-plugin-textdomain' ),
        'not_found'          => __( 'No vendors found.', 'your-plugin-textdomain' ),
        'not_found_in_trash' => __( 'No vendors found in Trash.', 'your-plugin-textdomain' )
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __( 'Description.', 'your-plugin-textdomain' ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'vendor' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_logo'         => 'dashicons-groups',
        'menu_position'      => null,
        'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
    );

    register_post_type( 'vendor', $args );
}

/**
 * Callback Vendors list
 */
function show_vendors_list_in_product($post){
     
    $vendor_name = get_post_meta($post->ID, 'vendor_name' , true );
    //echo 'vendor'.$vendor_name;

     //Vendor Lists
     $dropdown_args = array(
        'post_type'        => 'vendor',
        'posts_per_page'   => -1,
        'orderby'=> 'title', 'order' => 'ASC', 'post_status' => array('publish', 'pending', 'draft', 'private')  
     );
     $loop = get_posts($dropdown_args);


     // Use nonce for verification
     wp_nonce_field( 'vendor_product_info_meta_box_nonce', 'vendor_product_info_meta_box_nonce' );

    echo '<p><label><b>Select the Vendor :</b></label></p>';
    echo '<select name="vendor_name" id="vendor_list">';
            if(!empty($loop)) {
                echo '<option value="0">---Choose---</option>';
                foreach ($loop as $value) 
                {   
                    $selected = '';
                    $vendor_id = $value->ID;
                    if($vendor_name == $vendor_id){
                        $selected = 'selected';
                    }
                    echo '<option value="'.$vendor_id.'" '.$selected.'>'.$value->post_title.'</option>';    
                }
            }
    echo '</select>';
}



/**
 * Saving Metaboxes For Vendors list
 */
add_action( 'save_post', function($post_id) {

    if ( ! isset( $_POST['vendor_product_info_meta_box_nonce'] ) ||
    ! wp_verify_nonce( $_POST['vendor_product_info_meta_box_nonce'], 'vendor_product_info_meta_box_nonce' ) )
        return;
        
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    
    if (!current_user_can('edit_post', $post_id))
        return;

    if(!empty($_POST['vendor_name'])){
        $data=$_POST['vendor_name']; //make sanitization more strict !!
        update_post_meta($post_id, 'vendor_name', $data );
    }
    else{
        update_post_meta($post_id, 'vendor_name', '' );
    }        
});
/**
* Ajax Call Vendor Followers
*/
function add_vendor_followers()
{
    // $ppp = (isset($_POST["ppp"])) ? $_POST["ppp"] : 3;
    $followers = (isset($_POST['vendor_followers'])) ? $_POST['vendor_followers'] : '';
    $vendor_id = (isset($_POST['vendor'])) ? $_POST['vendor'] : '';

    //DB And Table Name
    global $wpdb;
    $vendor_table_name = $wpdb->prefix . "vendor_followers";

    $ip_address = "";
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    }else{
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }

    header("Content-Type: text/html");
    $user_id = get_current_user_id();

    if(!empty($user_id) && $user_id != 0)
    {
        $results = $wpdb->get_results("SELECT vendor_trackid FROM $vendor_table_name WHERE vendor_id = $vendor_id AND user_id = $user_id");
        if (count($results)> 0) {

        } else  {   
            
            $vendor_followers = update_post_meta($vendor_id, 'vendor_followers' , $followers );
            
            $wpdb->insert($vendor_table_name, array(
                                    'user_id' => $user_id, 
                                    'vendor_id' => $vendor_id,
                                    'date_time' => $date_time
								   ));
        }
    }
    else
    {   
       
        $results = $wpdb->get_results("SELECT vendor_trackid FROM $vendor_table_name WHERE vendor_id = $vendor_id AND ip_address = $ip_address AND user_id IS NULL");
        if (count($results)> 0) {

        } else{ 
            $vendor_followers = update_post_meta($vendor_id, 'vendor_followers' , $followers );

            $wpdb->insert($vendor_table_name, array( 
                                    'vendor_id' => $vendor_id,
                                    'ip_address' => $ip_address,
                                    'date_time' => $date_time
                                   ));
        }
    }
    
    $vendor_total_followers = get_post_meta($vendor_id, 'vendor_followers' , true );
   
    $out = '<button class="vendor-follow" data-vendor="'.$vendor_id.'" data-followers="'.$vendor_total_followers.'">
    <p><i class="icon-follow-on"></i> '.$vendor_total_followers.'</p>
    </button>'; 
    die($out);
}


//making the meta box (Note: meta box != custom meta field)
function show_custom_meta_box_vendor_details() {
    add_meta_box(
        'custom-meta-icon',       // $id
        'Vendor Details',                  // $title
        'display_custom_meta_box_vendor_details',  // $callback
        'vendor',                 // $page
        'normal',                  // $context
        'high'                     // $priority
    );
}
add_action('add_meta_boxes', 'show_custom_meta_box_vendor_details');
function display_custom_meta_box_vendor_details() {
    global $post;
		
    $v_website_url = get_post_meta( $post->ID, 'v_website_url', true );
    $v_video_url = get_post_meta( $post->ID, 'v_video_url', true );
    $v_mission = get_post_meta( $post->ID, 'v_mission', true );
    $v_profile_pic = get_post_meta( $post->ID, 'v_profile_pic', true );
    $image_src = wp_get_attachment_url( $v_profile_pic );
    $v_logo = get_post_meta( $post->ID, 'v_logo', true );
    $logo_image_src = wp_get_attachment_url( $v_logo );
    $v_vendor_details = get_post_meta( $post->ID, 'v_vendor_details', true );
    $v_main_title = get_post_meta( $post->ID, 'v_main_title', true );
    $v_secondary_title = get_post_meta( $post->ID, 'v_secondary_title', true );
    $v_header_image = get_post_meta( $post->ID, 'v_header_image', true );
    $header_image_src = wp_get_attachment_url( $v_header_image );
    ?>    
    <div>
    <label>Website url</label><br>
    <input type="url" name="v_website_url" value="<?php echo $v_website_url; ?>" size="100%" >
    </div>   
    <div>
    <label>Video url</label><br>
    <input type="url" name="v_video_url" value="<?php echo $v_video_url; ?>" size="100%" >
    </div>  
    <div>
    <label>Mission</label><br>
    <textarea name="v_mission" rows="5" cols="100%"><?php echo $v_mission; ?></textarea>
    </div>  
    <div>
    <label>Vendor Profile</label>
    <br>
    <?php if( $image = wp_get_attachment_image_src( $v_profile_pic ) ) { 
        echo '
        <div class="vendor-icon-upl-img"><p><img class="vendor-icon-upl" src="' . $image[0] . '" style="max-height: 100px;" /></p></div>
        <input type="hidden" name="upload_image_id" value="' . $v_profile_pic . '" id="upload_image_id_fld">
        <p>        
        <button class="button button-primary vendor-icon-upl">Add Profile Pic</button>
        <button class="button vendor-iconrmv">Remove Profile Pic</button></p>';
        } else {
        echo '
        <div class="vendor-icon-upl-img"></div>
        <input type="hidden" name="upload_image_id" value="" id="upload_image_id_fld">
        <p>
        <button class="button button-primary vendor-icon-upl">Add Profile Pic</button>
        <button class="button vendor-iconrmv" style="display:none">Remove Profile Pic</button>
        </p>';
        
        }
    ?>
    </div>  
    <div>
    <label>Vendor Carousel Image</label>
    <br>
    <?php if( $image = wp_get_attachment_image_src( $v_logo ) ) { 
        echo '
        <div class="vendor-logo-upl-img"><p><img class="vendor-logo-upl" src="' . $image[0] . '" style="max-height: 100px;" /></p></div>
        <input type="hidden" name="upload_logo_id" value="' . $v_logo . '" id="upload_logo_id_fld">
        <p>        
        <button class="button button-primary vendor-logo-upl">Add Carousel Image</button>
        <button class="button vendor-logormv">Remove Carousel Image</button></p>';
        } else {
        echo '
        <div class="vendor-logo-upl-img"></div>
        <input type="hidden" name="upload_logo_id" value="" id="upload_logo_id_fld">
        <p>
        <button class="button button-primary vendor-logo-upl">Add Carousel Image</button>
        <button class="button vendor-logormv" style="display:none">Remove Carousel Image</button>
        </p>';
        
        }
    ?>
    </div>  
    <div>
    <label>Vendor Details</label><br>
    <textarea name="v_vendor_details" rows="10" cols="100%"><?php echo $v_vendor_details; ?></textarea>
    </div>
      
    <div>
    <label>Main Title </label><br>
    <input type="text" name="v_main_title" value="<?php echo $v_main_title; ?>" size="100%" >
    <br>(use [vendor] as vendor name placeholder)
    </div>  
    <div>
    <label>Secondary Title</label><br>
    <input type="text" name="v_secondary_title" value="<?php echo $v_secondary_title; ?>" size="100%" >
    </div>
    <div>
    <label>Header Image</label>
    <br>
    <?php if( $image = wp_get_attachment_image_src( $v_header_image ) ) { 
        echo '
        <div class="vendor-header-upl-img"><p><img class="vendor-header-upl" src="' . $image[0] . '" style="max-height: 100px;" /></p></div>
        <input type="hidden" name="upload_header_id" value="' . $v_header_image . '" id="upload_header_id_fld">
        <p>        
        <button class="button button-primary vendor-header-upl">Add Header Image</button>
        <button class="button vendor-headerrmv">Remove Header Image</button></p>';
        } else {
        echo '
        <div class="vendor-header-upl-img"></div>
        <input type="hidden" name="upload_header_id" value="" id="upload_header_id_fld">
        <p>
        <button class="button button-primary vendor-header-upl">Add Header Image</button>
        <button class="button vendor-headerrmv" style="display:none">Remove Header Image</button>
        </p>';
        
        }
    ?>
    </div>  
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            
            // on upload button click
            $('body').on( 'click', '.vendor-icon-upl', function(e){
        
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
                    $('.vendor-icon-upl-img').html('<img src="' + attachment.url + '" style="max-height: 100px">');
                    $('#upload_image_id_fld').val(attachment.id);
                    $('.vendor-iconrmv').show();
                }).open();
        
            });
        
            // on remove button click
            $('body').on('click', '.vendor-iconrmv', function(e){
        
                e.preventDefault();
        
                var button = $(this);
                $('.vendor-icon-upl-img').html('<p></p>');
                $('#upload_image_id_fld').val(''); // emptying the hidden field
                $('.vendor-iconrmv').hide();
                
            });

            $('body').on( 'click', '.vendor-logo-upl', function(e){
        
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
                    $('.vendor-logo-upl-img').html('<img src="' + attachment.url + '" style="max-height: 100px">');
                    $('#upload_logo_id_fld').val(attachment.id);
                    $('.vendor-logormv').show();
                }).open();

            });

            // on remove button click
            $('body').on('click', '.vendor-logormv', function(e){

                e.preventDefault();

                var button = $(this);
                $('.vendor-logo-upl-img').html('<p></p>');
                $('#upload_logo_id_fld').val(''); // emptying the hidden field
                $('.vendor-logormv').hide();
                
            });

            $('body').on( 'click', '.vendor-header-upl', function(e){
        
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
                    $('.vendor-header-upl-img').html('<img src="' + attachment.url + '" style="max-height: 100px">');
                    $('#upload_header_id_fld').val(attachment.id);
                    $('.vendor-headerrmv').show();
                }).open();

            });

            // on remove button click
            $('body').on('click', '.vendor-headerrmv', function(e){

                e.preventDefault();

                var button = $(this);
                $('.vendor-header-upl-img').html('<p></p>');
                $('#upload_header_id_fld').val(''); // emptying the hidden field
                $('.vendor-headerrmv').hide();
                
            });
        });
    </script>
<?php
}

add_action( 'save_post','display_custom_meta_box_vendor_details_save');
function display_custom_meta_box_vendor_details_save( $post_id) {
    
    if ( empty( $post_id ) || empty( $_POST ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( is_int( wp_is_post_revision( $post_id ) ) ) return;
    if ( is_int( wp_is_post_autosave( $post_id ) ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    if ( get_post_type( $post_id ) != 'vendor' ) return;
    if (isset( $_POST[ '_inline_edit' ]))   return;
    /*if (check_admin_referer('bulk-posts'))   return; */
    if (isset($_POST['v_website_url'])) { update_post_meta( $post_id, 'v_website_url', $_POST['v_website_url'] ); }
    if (isset($_POST['v_video_url'])) { update_post_meta( $post_id, 'v_video_url', $_POST['v_video_url'] ); }
    if (isset($_POST['v_mission'])) { update_post_meta( $post_id, 'v_mission', $_POST['v_mission'] ); }
    if (isset($_POST['upload_image_id'])) {  update_post_meta( $post_id, 'v_profile_pic', $_POST['upload_image_id'] ); }
    if (isset($_POST['v_vendor_details'])) { update_post_meta( $post_id, 'v_vendor_details', $_POST['v_vendor_details'] ); }
    if (isset($_POST['upload_logo_id'])) { update_post_meta( $post_id, 'v_logo', $_POST['upload_logo_id'] ); }
    if (isset($_POST['upload_header_id'] )) { update_post_meta( $post_id, 'v_header_image', $_POST['upload_header_id'] ); }
    if (isset($_POST['v_secondary_title'])) { update_post_meta( $post_id, 'v_secondary_title', $_POST['v_secondary_title'] ); }
    if (isset($_POST['v_main_title'])) { update_post_meta( $post_id, 'v_main_title', $_POST['v_main_title'] ); }
    
    $termid = get_post_meta( $post_id, 'v_term_id', true );
    $args = array('name' => get_the_title($post_id));
    if (empty($termid)) {
        $existingterm = term_exists(get_the_title($post_id),'pa_vendors');
        //$termid =  wp_insert_term(get_the_title($post_id),'pa_vendors',array());
        if (empty($existingterm)) {
            $termid =  wp_insert_term(get_the_title($post_id),'pa_vendors',array());
            $termid = $termid['term_id'];
        } else {
            $termid = $existingterm['term_id'];
        } 

        update_post_meta( $post_id, 'v_term_id', $termid); 
    } else {
        wp_update_term( $termid, 'pa_vendors', $args);
    }
}
?>