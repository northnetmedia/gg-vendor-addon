<?php

/**
* Vendor List Slide Shortcode In Homepage and Shop Page
*/
add_shortcode('Vendor-Listing', 'vendor_list_init');
function vendor_list_init($atts){
    $args = array(
        'post_type'      => 'vendor',
        'order' => 'ASC', 
        'posts_per_page' => 4,          
    );
    $loop = get_posts($args);

   $html = '<div class="vendor-list-section owl-carousel" id="vendor-slide-list-wrapper">';
            if(!empty($loop)) 
            {
                foreach ($loop as $data) 
                {
                    $html .='<div class="vendor-slide-section">
                                <img src="'.get_the_post_thumbnail_url($data->ID).'">
                             </div>';
                } 
            }
    $html .='</div>';
    return $html;
}



add_shortcode('Vendor-Listing-byID', 'vendor_list_shortcode_init');
function vendor_list_shortcode_init($atts){
    global $wpdb;
    $html = '';
    if (!empty($atts['vendor'])) {
        $args = array(
            'post_type' => 'product',
            'order' => 'ASC',
            'posts_per_page' => -1,    
            'meta_query' => array(
                'meta_value' => array(
                    'key' => 'vendor_name', 
                    'value' => $atts['vendor'],
                )
            )
        );
    } else {
        $args = array(
            'post_type' => 'product',
            'order' => 'ASC'
        );
    }
    $loop = get_posts($args);
    $html = '<div class="vendor-list-products-section" id="products-wrapper">';
    if(!empty($loop))  {
        foreach ($loop as $value)  {
            $html .=  productloop($value->ID);            
        } 
    }
    $html .= '</div>';
 //print_r($html);
    return $html;
}


/**
* Vendor List in About the vendors page
*/
add_shortcode('Vendors-List-by-Impact', 'vendors_list_init');
function vendors_list_init(){
    $args = array(
        'post_type'      => 'vendor',
        'order' => 'DESC', 
        'posts_per_page' => -1,
        'meta_query'     =>
        array(
            'key'     => 'vendor_impacts',
        ),       
    );
    $loop = get_posts($args);

    $html = '<div class="vendors-list">
            ';
            if(!empty($loop)) 
            {
                foreach ($loop as $data) 
                {  
                    $impacts = get_post_meta( $data->ID, 'vendor_impacts', true );
                    $html .= '<div class="product-content">
                    			<div class="container">
                    				<div class="row">
                    					<div class="col-sm-3">
                                			<h4><a href="'.get_permalink($data->ID).'">'.$data->post_title.'</a></h4>
                             			</div>
                             			<div class="col-sm-9">
                             				<div class="row">';
							                    if(!empty($impacts))
							                    {
							                        foreach ($impacts as $key => $value) 
							                        {
							                           $html .= '<div class="col-sm-4">
							                           				<h4>'.get_the_title($value).'</h4>
							                                        <p>'.get_the_excerpt($value).'</p>
							                                    </div>';
							                        } 
							                    }
                    							$html .= '
                    						</div>';
                    						$html .= '<a class="vendorLink" href="'.get_permalink($data->ID).'">Read More</a>';
                    		  $html .= '</div>
                    				</div>
                    			</div>
                    		</div>';
                }
            }
    $html .='
            </div>';
    return $html;
}

/**
* Vendor and its detail  Listing Slider Shortcode In Homepage
*/
add_shortcode('Vendor-Detail-Listing', 'vendor_detail_list_init');
function vendor_detail_list_init(){
    global $wpdb;
    $args = array(
        'post_type'      => 'vendor',
        'order' => 'ASC', 
        'posts_per_page' => -1,          
    );
    $loop = get_posts($args);
    $table_name = $wpdb->prefix . "vendor_likes";   

   $html = '<div class="vendor-detail-list-section owl-carousel" id="vendor-detail-slide-list-wrapper">';
            if(!empty($loop)) 
            {
                foreach ($loop as $data) 
                {   
                    $details = get_post_meta( $data->ID,'v_vendor_details', true);
                    $vendor_profile_image = get_post_meta( $data->ID,'v_profile_pic', true);
                    $vendor_followers = get_post_meta($data->ID,'vendor_followers', true);
                    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($data->ID), 'large');
                    $large_image_url = !empty($large_image_url) ? $large_image_url : '';
                    $vendor_likes = get_post_meta($data->ID, 'vendor_like' , true); 
                    $user_id = get_current_user_id();
                    $results = $wpdb->get_results("SELECT trackid FROM $table_name WHERE vendor_id = $data->ID AND user_id = $user_id");
                   
                    $html .='<div class="vendor-detail-slide-section">
                                <div class="vendor-first-col">
                                    <img src="'.$large_image_url[0].'">                                     
                                    <div class="vendor-fb-like">
                                        <button class="vendor-like thumbid-'.$data->ID.'" data-vendor="'.$data->ID.'" data-likes="'.$vendor_likes.'" data-user="'.$user_id.'">';
                                            if (count($results)> 0) {
                                                $icon = '<i class="icon-highfive-on"></i>';
                                            }  else {
                                                $icon = '<i class="icon-highfive-off"></i>';
                                            }
                                        $html .= '<div class="likes-vendor-add-'.$data->ID.'">';
                                                $initial_value = 0;
                                                if(!empty($vendor_likes))  {
                                                    $html .= $vendor_likes; 
                                                }  else  { 
                                                    $html .= $initial_value;
                                                }
                                        $html .= ' '.$icon.'</div>';
                                        $html .= '</button>
                                    </div>                                  
                                    <div class="ven-followers vendor-followers-'.$data->ID.'">
                                        <button class="vendor-follow" data-vendor="'.$data->ID.'" data-followers="'.$vendor_followers.'">';
                                          $initial_value = 0;
                                          if(!empty($vendor_followers))
                                          {
                                            $html .= '<p><i class="icon-follow-on"></i> '.$vendor_followers.'</p>'; 
                                          }
                                          else
                                          { 
                                            $html .= '<p><i class="icon-follow-off"></i> '.$initial_value.'</p>';
                                          }
                                          $html .= '</button>';
                         $html .= '</div>
                                </div>

                                <div class="vendor-second-col">';
                                    if( !empty( $details ) ):
                                        $html .=  '<p>'.$details.'</p>';
                                    endif; 
                                    $html .='<a href="'.get_the_permalink($data->ID).'">Learn More</a>
                                </div>

                                <div class="vendor-third-col">';
                                    if( !empty( $vendor_profile_image ) ):
                                        $html .='<img src="'.esc_url($vendor_profile_image['url']).'"/>';
                                    endif; 
                       $html .= '</div>
                         </div>';
                } 
            }
    $html .='</div>';
    return $html;
}
/**
* Vendor List in Category page - Shop Single
*/
add_shortcode('List-Of-Vendors', 'list_of_vendors_init');
function list_of_vendors_init(){
    $args = array(
        'post_type'      => 'vendor',
		'orderby' => 'post_name',
        'order' => 'ASC', 
        'posts_per_page' => -1,          
    );
    $loop = get_posts($args);

    //$category = get_queried_object();
    //$cat_slug = $category->slug;
    //$cat_id = $category->term_id;

   $html = '<div class="sidebar-list-section">
            <ul>';
            if(!empty($loop)) 
            {
                foreach ($loop as $data) 
                {   
                    //$token = $cat_slug.','.$data->ID;
                    $vendor_token = $data->ID;
                    $url = home_url( '/' ).'vendor/'.$data->post_name;
                    //$html .='<li><a href="'.home_url( '/' ).'product-category/'.$cat_slug.'?vendor_token='.$vendor_token.'">'.$data->post_title.'</a></li>';
                    $html .='<li><a href="'.$url.'">'.$data->post_title.'</a></li>';
                } 
            }
    $html .='</ul>
            </div>';
    return $html;
}


add_shortcode('Vendors-List-Boxed', 'vendors_list_box_init');
function vendors_list_box_init(){
    global $wpdb;
    $args = array(
        'post_type'      => 'vendor',
        'order' => 'ASC', 
        'posts_per_page' => -1,          
    );
    $loop = get_posts($args);
    $table_name = $wpdb->prefix . "vendor_likes";
    $html = '<div class="vendor-list-products-section" id="products-wrapper"><div class="container"><div class="row">';
            if(!empty($loop)) 
            {
                foreach ($loop as $value) 
                {
                    $vendor_likes = get_post_meta($value->ID, 'vendor_like' , true); 
                    $user_id = get_current_user_id();
                    $vendor_id = $value->ID;
                    $vendor_name = get_the_title($vendor_id); 
                    $results = $wpdb->get_results("SELECT trackid FROM $table_name WHERE vendor_id = $vendor_id AND user_id = $user_id");
                    
                    $html .='<div class="col-sm-3">
                            <div class="vendor_wrap">
                                <div class="vendor-fb-like">
                                    <button class="vendor-like thumbid-'.$value->ID.'" data-vendor="'.$value->ID.'" data-likes="'.$vendor_likes.'" data-user="'.$user_id.'">';
                                        if (count($results)> 0) {
                                            $icon = '<i class="icon-highfive-on"></i>';
                                        }  else {
                                            $icon = '<i class="icon-highfive-off"></i>';
                                        }
                                    $html .= '<div class="likes-vendor-add-'.$value->ID.'">';
                                            $initial_value = 0;
                                            if(!empty($vendor_likes))  {
                                                $html .= $vendor_likes; 
                                            }  else  { 
                                                $html .= $initial_value;
                                            }
                                    $html .= ' '.$icon.'</div>';
                                    $html .= '</button>
                                </div>
                                <img src="'.get_the_post_thumbnail_url($value->ID).'">
                                <p class="vendor-name">'.$vendor_name.'</p>
                                <br>
                               <a class="vendorLink" href="'.get_the_permalink($value->ID).'">Learn More</a>
                             </div></div>';
                } 
            }
    $html .='</div></div></div>';
    return $html;
}
/**
* Vendor and its detail  Listing Slider Shortcode In Homepage
*/
add_shortcode('Vendor-Detail-slider-Listing', 'vendor_detail_slider_list_init');
function vendor_detail_slider_list_init(){
    global $wpdb;
    $args = array(
        'post_type'      => 'vendor',
        'order' => 'ASC', 
        'posts_per_page' => -1,          
    );
    $loop = get_posts($args);
    $table_name = $wpdb->prefix . "vendor_likes";
   $html = '<div class="vendor-detail-slider-list-section owl-carousel" id="vendor-detail-slider-list-wrapper">';
            if(!empty($loop)) 
            {
                foreach ($loop as $data) 
                {   
                    //$details = get_field('v_vendor_details', $data->ID);
                    //$vendor_profile_image = get_field('v_profile_pic', $data->ID);
                    $vendor_followers = get_post_meta($data->ID,'vendor_followers', true);
                    $image = get_the_post_thumbnail_url($data->ID);
                    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($data->ID), 'large');
                    $large_image_url = !empty($large_image_url[0]) ? $large_image_url[0] : '';
                    $vendor_likes = get_post_meta($data->ID, 'vendor_like' , true); 
                    $user_id = get_current_user_id();
                    $results = $wpdb->get_results("SELECT trackid FROM $table_name WHERE vendor_id = $data->ID AND user_id = $user_id");
                   
                    //print_r($large_image_url );
                    $html .='<div class="vendor-detail-slider-section">
                            
                    <div class="vendor-col">
                        <a href="'.get_permalink($data->ID).'"><img style="object-fit: contain; position: absolute; height:300px; left:0px;" src="'.$large_image_url.'"></a>
                        <div class="vendor-fb-like">
                            <button class="vendor-like thumbid-'.$data->ID.'" data-vendor="'.$data->ID.'" data-likes="'.$vendor_likes.'" data-user="'.$user_id.'">';
                                
                                if (count($results)> 0) {
                                    $icon = '<i class="icon-highfive-on"></i>';
                                }  else {
                                    $icon = '<i class="icon-highfive-off"></i>';
                                }
                                $html .= '<div class="likes-vendor-add-'.$data->ID.'">';
                                        $initial_value = 0;
                                        if(!empty($vendor_likes))  {
                                            $html .= $vendor_likes; 
                                        }  else  { 
                                            $html .= $initial_value;
                                        }
                                $html .= ' '.$icon.'</div>';
                            $html .= '</button>
                        </div>
                        <div class="ven-followers vendor-followers-'.$data->ID.'">
                            <button class="vendor-follow" data-vendor="'.$data->ID.'" data-followers="'.$vendor_followers.'">';
                                $initial_value = 0;
                                if(!empty($vendor_followers))
                                {
                                $html .= '<p><i class="icon-follow-on"></i> '.$vendor_followers.'</p>'; 
                                }
                                else
                                { 
                                $html .= '<p><i class="icon-follow-off"></i> '.$initial_value.'</p>';
                                }
                                $html .= '</button>';
                    $html .= '</div>
                
                    </div>
                </div>';
                } 
            }
    $html .='</div>';
    return $html;
}
/**
* Vendor and its detail  on Product Details
*/
add_shortcode('Vendor-Featured-Image-Round', 'vendor_image_round');
function vendor_image_round(){
    global $product;
   // var_dump($product->get_id());  
    if ( empty( $product->get_id() ) ) return '';  
    $vendor_id = get_post_meta($product->get_id() , 'vendor_name' , true );
    $vendor_name = get_the_title($vendor_id);
    $image = get_the_post_thumbnail_url($vendor_id);
    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($vendor_id), 'large');
    $large_image_url = !empty($large_image_url) ? $large_image_url : array(''); 
    //var_dump(empty($large_image_url)); 
    $html ='<div class="vendor-profile-image">'; 
    $html .='<a href="'.get_permalink($vendor_id).'"><img src="'.$large_image_url[0].'" alt="'.$vendor_name.'"></a>';
    $html .='</div>';
    return $html;
}

add_shortcode('Vendor-Featured-Image-Polaroid', 'vendor_image_polaroid');
function vendor_image_polaroid()
{
    global $product;

    if (empty($product->get_id())) return '';
    $vendor_id = get_post_meta($product->get_id(), 'vendor_name', true);
    $vendor_name = get_the_title($vendor_id);
    $vendor_profile_image = get_post_meta($vendor_id, 'v_logo', true);
    $carousel = wp_get_attachment_url($vendor_profile_image);

    $html = '<div class="polaroid"><span>';
    if (!empty($vendor_profile_image)) :
        $html .= '<img src="' . esc_url($carousel) . '" alt="' . $vendor_name . '" /> ' . $vendor_name;
    endif;
    $html .= '</span></div>';
    return $html;
}

/**
* Vendor name excerpt and high  on Product Details
*/
add_shortcode('Vendor-Details-Prod', 'vendor_details_for_proddetails');
function vendor_details_for_proddetails(){
    global $product,$wpdb;
   // var_dump($product->get_id());  
    if ( empty( $product->get_id() ) ) return '';  
    $vendor_id = get_post_meta($product->get_id() , 'vendor_name' , true );
    if ( empty( $vendor_id ) ) return '';  
    $vendor_name = get_the_title($vendor_id);    
    if ( empty( $vendor_name ) ) return '';  
    $image = get_the_post_thumbnail_url($vendor_id);
    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($vendor_id), 'large');
    $large_image_url = !empty($large_image_url) ? $large_image_url : '';  
    $vendor_followers = get_post_meta($vendor_id,'vendor_followers', true);
    $user_id = get_current_user_id();
    $vendor_likes = get_post_meta($vendor_id, 'vendor_like' , true); 
    $table_name = $wpdb->prefix . "vendor_likes";
    $results = $wpdb->get_results("SELECT trackid FROM $table_name WHERE vendor_id = $vendor_id AND user_id = $user_id");
    $alreadyliked = (count($results)> 0) ? "-on" : "-off" ;
	$about = get_post_meta($vendor_id, 'v_vendor_details', true) ? get_post_meta($vendor_id, 'v_vendor_details', true) : get_post_meta($vendor_id, 'v_mission', true);
    $impacts = get_post_meta($vendor_id, 'impact_elements' , true );
    $html ='<div class="vendor-profile-details">'; 
    //$html .='<h5>'.$vendor_name.'</h5>';
    $html .='<div class="vendorexcerpt">'. wp_strip_all_tags(get_the_excerpt($vendor_id)).'</div>';
    //$html .='<p><a href="'.get_permalink($vendor_id).'" class="vendorLink">Learn More about '.$vendor_name.'</a></p>';
    $html .='<div class="vendorexcerpt">';
	$html .= '<p class="vendordetails">' . $about . '</p>';
    $html .='<div class="vendorhighfive "><button class="vendor-like thumbid-'.$vendor_id.'" data-vendor="'.$vendor_id.'" data-likes="'.$vendor_likes.'" data-user="'.$user_id.'"><div class="likes-vendor-add-'.$vendor_id.'">'.$vendor_likes.' 
	<i class="icon-highfive'.$alreadyliked.'"></i></div></button> Give a High Five</div>';
    $html .='<div class="vendorfollow"><div class="vendor-followers-'.$vendor_id.'" style="display:inline-block;"><button class="vendor-follow" data-vendor="'.$vendor_id.'" data-followers="'.$vendor_followers.'">'.$vendor_followers.' <i class="icon-follow'.$alreadyliked.'"></i></button></div>  Follow</div>';
    if (!empty($impacts)) {
        $html .='<div><br/>';
        foreach ($impacts as $data) {
            $specific_impact_statement = get_post_meta( $data, 'specific_impact_statement', true );
            $image_id = get_post_meta( $data, '_impact_icon', true );
            $image_src = wp_get_attachment_url( $image_id );
            $html .='<img src="'.$image_src.'" class="pd-impact-img" alt="'.$specific_impact_statement.'" title="'.$specific_impact_statement.'" style="max-width: 75px;" /> &nbsp; ';
        }
        $html .='</div>';
    }
    $html .='</div>';
    $html .='</div>';
    return $html;
}

add_shortcode('Vendor-Name-Prod', 'vendor_name_for_proddetails');
function vendor_name_for_proddetails(){
    global $product,$wpdb;
   // var_dump($product->get_id());  
    if ( empty( $product->get_id() ) ) return '';  
    $vendor_id = get_post_meta($product->get_id() , 'vendor_name' , true );
    if ( empty( $vendor_id ) ) return '';  
    $vendor_name = get_the_title($vendor_id);    
    if ( empty( $vendor_name ) ) return '';  
    $html = '<a href="'.esc_url( get_permalink($vendor_id)).'" style="color: #5C9391;">'.$vendor_name.'</a>';
    return $html;
}

add_shortcode('Vendor-Logo-Prod', 'vendor_logo_for_proddetails');
function vendor_logo_for_proddetails(){
    global $product;
    if ( empty( $product->get_id() ) ) return '';  
    $vendor_id = get_post_meta($product->get_id() , 'vendor_name' , true );
    $vendor_name = get_the_title($vendor_id);
    $image = get_the_post_thumbnail_url($vendor_id);
    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($vendor_id), 'small');
    $large_image_url = !empty($large_image_url) ? $large_image_url : array(''); 
    $html ='<div class="vendor-logo">'; 
    $html .='<img src="'.$large_image_url[0].'" alt="'.$vendor_name.'">';
    $html .='</div>';
    return $html;
}

add_shortcode('single-vendor-highfive', 'get_single_vendor_highfive');
function get_single_vendor_highfive($atts){	
    if ( !isset( $atts['vendor']) ) return '';  
    $vendor_id = $atts['vendor'];
    $user_id = get_current_user_id();
    $vendor_likes = get_post_meta($vendor_id, 'vendor_like' , true); 
    $alreadyliked = ($vendor_likes > 0) ? "-on" : "-off" ; // This is not correct - should be getting only the results for the current user
    $html ='<div class="vendorhighfive "><button class="vendor-like thumbid-'.$vendor_id.'" data-vendor="'.$vendor_id.'" data-likes="'.$vendor_likes.'" data-user="'.$user_id.'"><div class="likes-vendor-add-'.$vendor_id.'">
	<i class="icon-highfive'.$alreadyliked.'"></i>
	</div></button> Give a High Five</div>';
    return $html;
}

add_shortcode('single-vendor-follow', 'get_single_vendor_follow');
function get_single_vendor_follow($atts){
    if ( !isset( $atts['vendor']) ) return '';  
    $vendor_id = $atts['vendor'];
    $vendor_followers = get_post_meta($vendor_id,'vendor_followers', true);
    $user_id = get_current_user_id();
    $html ='<div class="vendorfollow"><div class="vendor-followers-'.$vendor_id.'" style="display:inline-block;"><button class="vendor-follow" data-vendor="'.$vendor_id.'" data-followers="'.$vendor_followers.'"><i class="icon-follow-off"></i></button></div>  Follow</div>';
    return $html;
}
?>