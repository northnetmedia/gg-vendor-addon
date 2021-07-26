<?php
<<<<<<< HEAD
=======

>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Reign
 */
get_header();
$id = get_the_ID();
$title = get_the_title();
<<<<<<< HEAD
$v_profile_pic = get_post_meta( $id, 'v_profile_pic', true );
$vendor_carousel = get_post_meta( $id, 'v_logo', $id);
$website_url = get_post_meta( $id, 'v_website_url', true );
$video_url = get_post_meta( $id, 'v_video_url', true );
$mission = get_post_meta( $id, 'v_mission', true );
$vendor_details = get_post_meta( $id, 'v_vendor_details', true );
$impacts = get_post_meta( $id, 'vendor_impacts', true );
$carousel = wp_get_attachment_url( $vendor_carousel );
$profile_img = wp_get_attachment_url( $v_profile_pic );
=======
$v_profile_pic = get_post_meta($id, 'v_profile_pic', true);
$vendor_carousel = get_post_meta($id, 'v_logo', $id);
$website_url = get_post_meta($id, 'v_website_url', true);
$video_url = get_post_meta($id, 'v_video_url', true);
$mission = get_post_meta($id, 'v_mission', true);
$vendor_details = get_post_meta($id, 'v_vendor_details', true);
$impacts = get_post_meta($id, 'vendor_impacts', true);
$carousel = wp_get_attachment_url($vendor_carousel);
$profile_img = wp_get_attachment_url($v_profile_pic);
>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe

/*
$vendorcategories = $wpdb->get_results( $wpdb->prepare("SELECT DISTINCT t.name AS product_category, t.term_id
FROM {$wpdb->prefix}posts p 
JOIN {$wpdb->prefix}postmeta pm1 ON pm1.post_id = p.ID AND pm1.meta_key = 'vendor_name' AND pm1.meta_value = %d
LEFT JOIN {$wpdb->prefix}term_relationships AS tr ON tr.object_id = p.ID
JOIN {$wpdb->prefix}term_taxonomy AS tt ON tt.taxonomy = 'product_cat' AND parent <> 0 AND tt.term_taxonomy_id = tr.term_taxonomy_id 
JOIN {$wpdb->prefix}terms AS t ON t.term_id = tt.term_id
WHERE p.post_type = 'product' AND p.post_status = 'publish'",$id)) ;
*/
<<<<<<< HEAD

$header_background = !empty(get_post_meta( $id, 'v_header_image', true )) ? wp_get_attachment_url(get_post_meta( $id, 'v_header_image', true )) : wp_get_attachment_url(get_option('single_vendor_header'));
$maintitle = !empty(get_post_meta( $id, 'v_main_title', true )) ? get_post_meta( $id, 'v_main_title', true ) : get_option('single_vendor_main_title');
$maintitle = str_replace('[vendor]',$title,str_replace('guud','<span style="font-family: \'Playlist Script\'; font-size: 40pt; font-weight: 400;">guud</span>',$maintitle));
$secondarytitle = !empty(get_post_meta( $id, 'v_secondary_title', true )) ? get_post_meta( $id, 'v_secondary_title', true ) : get_option('single_vendor_secondary_title');
?>
<div class="content-wrapper">
<section class="vendor_spotlight templatepageheader" style="background-image: url(<?php echo $header_background; ?>);background-position: bottom center; background-repeat: no-repeat; background-size: cover;  margin-left: calc(50% - 50vw);  margin-right: calc(50% - 50vw);height: 460px; position:relative; top:-60px;"">
    <div class="background-overlay"></div>
    <div class="container" style="padding: 0px 20px;">
        <h1 class="elementor-heading-title elementor-size-default"><?php echo $maintitle; ?></h1>
        <h2><?php echo $secondarytitle; ?></h2>
    </div>
</section>
<div class="carousel">
    <div class="carouselleft">
        <?php 
        if( !empty( $carousel ) ):
            echo '<img src="'.esc_url($carousel).'" alt="'.$title.'" />';
        endif;	?>
        </div>
    <div class="carouselright">
        <h2 class="vendor_mission">"<?php echo $mission; ?>"</h2>
        <img src="/wp-content/uploads/2020/11/yellow-heart.png"  alt="<?php echo $title; ?>" width="100">

=======
$header_background = !empty(get_post_meta($id, 'v_header_image', true)) ? wp_get_attachment_url(get_post_meta($id, 'v_header_image', true)) : wp_get_attachment_url(get_option('single_vendor_header'));
$maintitle = !empty(get_post_meta($id, 'v_main_title', true)) ? get_post_meta($id, 'v_main_title', true) : get_option('single_vendor_main_title');
$maintitle = str_replace('[vendor]', $title, str_replace('guud', '<span style="font-family: \'Playlist Script\'; font-weight: 400;">guud</span>', $maintitle));
$secondarytitle = !empty(get_post_meta($id, 'v_secondary_title', true)) ? get_post_meta($id, 'v_secondary_title', true) : get_option('single_vendor_secondary_title');
?>
<div class="content-wrapper">
    <section class="vendor_spotlight templatepageheader" style="background-image: url(<?php echo $header_background; ?>);background-position: bottom center; background-repeat: no-repeat; background-size: cover;  margin-left: calc(50% - 50vw);  margin-right: calc(50% - 50vw);height: 460px; position:relative; top:-60px;"">
    <div class=" background-overlay">
</div>
<div class="container">
    <h1 class="elementor-heading-title elementor-size-default"><?php echo $maintitle; ?></h1>
    <h2><?php echo $secondarytitle; ?></h2>
</div>
</section>
<div class="carousel">
    <div class="carouselleft">
        <?php
        if (!empty($carousel)) :
            echo '<img src="' . esc_url($carousel) . '" alt="' . $title . '" />';
        endif;    ?>
    </div>
    <div class="carouselright">
        <img src="/wp-content/uploads/2020/11/yellow-heart.png" alt="<?php echo $title; ?>" width="100">
        <h2 class="vendor_mission">"<?php echo $mission; ?>"</h2>
>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe
    </div>
</div>
<div class="vendortop">
    <div class="profileleft">
<<<<<<< HEAD
    <?php  if( !empty( $profile_img ) ): ?>
        <div class="profileimg">
        <img src="<?php echo esc_url($profile_img); ?>" alt="<?php echo $vendor_name; ?>" />
        </div>
   <?php endif;	?>
    </div>
    <div class="profileright">
        <h2>About <?php echo $title; ?></h2>
        <p><?php echo $vendor_details; ?></p>
        <?php foreach ($impacts as $key => $impact) { ?>   
            <?php     
            $image_id = get_post_meta( $impact, '_impact_icon', true );
            $image_src = wp_get_attachment_url( $image_id );
            $specific_impact_statement = get_post_meta( $impact, 'specific_impact_statement', true );
=======
        <?php if (!empty($profile_img)) : ?>
            <div class="profileimg">
                <img src="<?php echo esc_url($profile_img); ?>" alt="<?php echo $vendor_name; ?>" />
            </div>
        <?php endif;    ?>
    </div>
    <div class="profileright">
        <div class="hidden-impact-icon">
            <?php  
                $image_id_0 = get_post_meta($impacts[0], '_impact_icon', true);
                $image_src_0 = wp_get_attachment_url($image_id_0);
            ?>
            <img src="<?php echo $image_src_0; ?>" class="pd-impact-img impact-icon-vendor">
            <h2>About <?php echo $title; ?></h2>
        </div>
        <p><?php echo $vendor_details; ?></p>
        <?php foreach ($impacts as $key => $impact) { ?>
            <?php
            $image_id = get_post_meta($impact, '_impact_icon', true);
            $image_src = wp_get_attachment_url($image_id);
            $specific_impact_statement = get_post_meta($impact, 'specific_impact_statement', true);
>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe
            ?>
            <div class="impact-icon-txt-wrap">
                <?php if (!empty($image_src)) { ?>
                    <div class="proddetails_impact_statement_icon"><img src="<?php echo $image_src; ?>" class="pd-impact-img"></div>
                <?php } ?>
                <div class="proddetails_impact_statement_title"><?php echo $specific_impact_statement; ?></div>
            </div>
<<<<<<< HEAD
            <div style="clear:both"></div>
            <p></p>
        <?php } ?>
        <?php echo do_shortcode('[single-vendor-highfive vendor='.$id.']'); ?>
        <?php echo do_shortcode('[single-vendor-follow vendor='.$id.']'); ?>
    </div>
</div>
<?php 
$postsperpage = 15;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
        'post_type' => 'product',
        'order' => 'ASC',        
        'posts_per_page' => $postsperpage,   
        'paged' => $paged,   
        'post_status' => 'publish',
        'meta_query' => array(
           'meta_value' => array(
                'key' => 'vendor_name', 
                'value' => $id,
    )));
=======
            <!-- <div style="clear:both"></div>
            <p></p> -->
        <?php } ?>
        <?php echo do_shortcode('[single-vendor-highfive vendor=' . $id . ']'); ?>
        <?php echo do_shortcode('[single-vendor-follow vendor=' . $id . ']'); ?>
    </div>
</div>
<?php
$postsperpage = 15;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type' => 'product',
    'order' => 'ASC',
    'posts_per_page' => $postsperpage,
    'paged' => $paged,
    'post_status' => 'publish',
    'meta_query' => array(
        'meta_value' => array(
            'key' => 'vendor_name',
            'value' => $id,
        )
    )
);
>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe
$catname = '';
if (isset($_GET['vc']) && !empty($_GET['vc'])) {
    //$args['cat'] = sanitize_text_field($_GET['vc']);
    $args['tax_query'] = array(
<<<<<<< HEAD
            array(
        'taxonomy' => 'product_cat', 
        'field' => 'id', 
        'terms' => array( $_GET['vc'])
        )
    );
    $term = get_term_by( 'id', sanitize_text_field($_GET['vc']), 'product_cat' );
=======
        array(
            'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => array($_GET['vc'])
        )
    );
    $term = get_term_by('id', sanitize_text_field($_GET['vc']), 'product_cat');
>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe
    $catname = $term->name;
}
//print_r($args);
$loop = new WP_Query($args);
//print_r($loop);
$products = $loop->posts;
<<<<<<< HEAD
if($loop->have_posts())  { ?>
    <div class="vendorBanner" id="productlist">
        <div class="container">
            <h2>Products of <?php echo $title; ?><?php echo !empty($catname) ? ' for ' .$catname : ''; ?></h2>
        </div>
    </div>
    <div class="single_vendor vendor-list-products-section" style="text-align: center;">
    <?php
    $html = '';
    foreach($products as $value) {
        $html .=  productloop($value->ID);
    } 
    echo $html; 
    ?>
    </div>
    <?php 
} else { ?>
    <div class="vendorBanner" id="productlist">
        <div class="container">
            <h2>No Products were found <?php echo !empty($catname) ? 'for ' .$catname : ''; ?></h2>
=======
if ($loop->have_posts()) { ?>
    <div class="vendorBanner" id="productlist">
        <div class="container">
            <h2>Products of <?php echo $title; ?><?php echo !empty($catname) ? ' for ' . $catname : ''; ?></h2>
        </div>
    </div>
    <div class="single_vendor vendor-list-products-section" style="text-align: center;">
        <?php
        $html = '';
        foreach ($products as $value) {
            $html .=  productloop($value->ID);
        }
        echo $html;
        ?>
    </div>
<?php
} else { ?>
    <div class="vendorBanner" id="productlist">
        <div class="container">
            <h2>No Products were found <?php echo !empty($catname) ? 'for ' . $catname : ''; ?></h2>
>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe
        </div>
    </div>

<?php } ?>
<<<<<<< HEAD
<?php if ( $loop->max_num_pages > 1 ) { ?>
=======
<?php if ($loop->max_num_pages > 1) { ?>
>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe
    <nav class="navigation posts-navigation rg-posts-navigation" role="navigation">
        <div class="nav-links">
            <?php
            $big = 999999999; // need an unlikely integer
            $args = array(
<<<<<<< HEAD
                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
=======
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe
                'format' => '?paged=%#%',
                'current' => $paged,
                'total' => $loop->max_num_pages,
                'prev_next' => false
            );
            if (isset($_GET['vc']) && !empty($_GET['vc'])) {
                $args['add_args'] = array('vc' => sanitize_text_field($_GET['vc']));
            }
<<<<<<< HEAD
            echo paginate_links($args );
=======
            echo paginate_links($args);
>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe

            ?>
        </div>
    </nav>
<?php } ?>

<<<<<<< HEAD
=======
<?php echo do_shortcode('[List-Of-Products_by_Impact impact="' . $impacts[0] . '"]'); ?>

>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe
</div>
</div>
</div>

<?php foreach ($impacts as $impact) : 
    
    $impactTitle = get_the_title($impact);
    $impactStatement = get_post_meta( $impact, 'specific_impact_statement', true );
    $impactUrl = get_permalink($impact);
    $impactImg = 
    wp_get_attachment_image_url(get_post_meta( $impact, 'learn_more_bg', true)) ?
    wp_get_attachment_image_url(get_post_meta( $impact, 'learn_more_bg', true), 'full') :
<<<<<<< HEAD
    wp_get_attachment_image_url(21922, 'full');
=======
    wp_get_attachment_image_url(14630, 'full');
>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe
?>

<div class="learn-more-impact" style="background-image: url(<?php echo $impactImg ?>)">
    <div class="impact-wrap">
        <h3 class="learn-title"><?php echo $impactTitle; ?></h3>
        <span class="learn-span"><?php echo $impactStatement; ?></span>
        <a class="learn-a join-btn" href="<?php echo $impactUrl; ?>">Shop This Impact</a>
    </div>
</div>

<<<<<<< HEAD
<?php endforeach; ?>

   <?php
get_footer();
=======

<?php endforeach; ?>
<?php
get_footer();
>>>>>>> db0c04c485b21b4a3a28ccdf0f55ac476237dffe
