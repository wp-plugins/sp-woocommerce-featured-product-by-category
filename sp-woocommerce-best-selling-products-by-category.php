<?php
/*
Plugin Name: WP woocommerce featured product by category
Plugin URL: http://wponlinesupport.com
Description: Display woocommerce featured product by category
Version: 1.0
Author: WP Online Support
Author URI: http://wponlinesupport.com
Contributors: WP Online Support
*/
// get featured_product_categories

add_shortcode( 'featured_product_categories', 'sp_featured_products' );
function sp_featured_products($atts){
 
	global $woocommerce_loop;
 
	extract(shortcode_atts(array(
		'cats' => '',	
		'tax' => 'product_cat',	
		'per_cat' => '3',	
		'columns' => '3',	
	), $atts));
 
	
	if(empty($cats)){
		$terms = get_terms( 'product_cat', array('hide_empty' => true, 'fields' => 'ids'));
		$cats = implode(',', $terms);
	}
 
	
	$cats = explode(',', $cats);
 
	
	if(empty($cats)){
		return '';
	}
 
	ob_start();
 
	foreach($cats as $cat){
 
		// get the product category
		$term = get_term( $cat, $tax);
 
		// setup query
		$args = array(
			'post_type'	=> 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' => $per_cat,
			'tax_query' => array(
				
				array(
					'taxonomy' => $tax,
					'field' => 'id',
					'terms' => $cat,
				)
			),
			'meta_query' => array(
				
				array(
					'key' => '_visibility',
					'value' => array('catalog', 'visible'),
					'compare' => 'IN'
				),
				// get only products marked as featured
				array(
					'key' => '_featured',
					'value' => 'yes'
				)
			)
		);
		
		// set woocommerce columns
		$woocommerce_loop['columns'] = $columns;
 
		// query database
		$products = new WP_Query( $args );
 
		$woocommerce_loop['columns'] = $columns;
 
		if ( $products->have_posts() ) : ?>
 
			
			<?php woocommerce_product_loop_start(); ?>
 
				<?php while ( $products->have_posts() ) : $products->the_post(); ?>
 
					<?php woocommerce_get_template_part( 'content', 'product' ); ?>
 
				<?php endwhile; // end of the loop. ?>
 
			<?php woocommerce_product_loop_end(); ?>
 
		<?php endif;
 
		wp_reset_postdata();
	}
 
	return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
}

?>
