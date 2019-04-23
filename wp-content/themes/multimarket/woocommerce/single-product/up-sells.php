<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $upsells ) : ?>

	<div class="mm-related-products upsells">
		<div class="container">
			<div class="section-header --has-icon">
				<i class="ti-thumb-up section-icon"></i>
				<h2 class="section-title"><?php echo apply_filters( 'woocommerce_upsells_products_title', esc_html__( 'You may also like', 'multimarket' ) ); ?></h2>
				<small class="section-subtitle">
					<?php echo get_theme_mod( 'multimarket_upsells_product_subtitle', 'Below is all related products to complement this product' ); ?>
				</small>
			</div>

		<?php 
			$get_columns 			= get_theme_mod( 'multimarket_upsells_product_columns', 4 );
			$get_default_layout 	= get_theme_mod( 'multimarket_product_layout', 'modern' );
			$default_layout  		= ( $get_default_layout == 'list' ) ? 'classic' : $get_default_layout;
			$product_layout 		= get_theme_mod( 'multimarket_upsells_product_layout', $default_layout );

			if ( 'modern' == $product_layout ) {
				echo '<div class="product-modern grid-layout columns-'.$get_columns.'">';
			} else {
				echo '<div class="product-grid grid-layout columns-'.$get_columns.'">';
			}
		 ?>

			<?php foreach ( $upsells as $upsell ) : ?>

				<?php
				 	$post_object = get_post( $upsell->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object );

					multimarket_woocommerce_content_product_layout( $product_layout ); ?>

			<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

		</div>
	</div>

<?php endif;

wp_reset_postdata();
