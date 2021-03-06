<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>

<div <?php post_class( 'grid-item' ); ?>>
	<div class="product__inner">
		<?php
			/**
			 * woocommerce_before_shop_loop_item hook.
			 *
			 * @hooked woocommerce_template_loop_product_link_open - 10 : removed
			 */
			do_action( 'woocommerce_before_shop_loop_item' );
		?>

		<div class="product__detail">
			<?php 
				/**
				 * woocommerce_shop_loop_item_title hook.
				 *
				 * @hooked woocommerce_template_loop_product_title - 10 : removed
				 */
				do_action( 'woocommerce_shop_loop_item_title' );
				
				multimarket_product_category();

				echo get_avatar( get_the_author_meta( 'ID' ), 32 );
			
			?>
			
		</div>
		
		<figure class="product__image">
			<?php multimarket_product_sale_flash(); ?>
			<a href="<?php the_permalink(); ?>">
				<?php 
					/**
					 * woocommerce_before_shop_loop_item_title hook.
					 *
					 * @hooked woocommerce_show_product_loop_sale_flash - 10 : removed
					 * @hooked woocommerce_template_loop_product_thumbnail - 10
					 */
					do_action( 'woocommerce_before_shop_loop_item_title' );
				?>
			</a>
			<?php if ( class_exists( 'YITH_WCQV' ) ) : 
					$label = esc_html( get_option( 'yith-wcqv-button-label' ) );
					echo '<button class="button yith-wcqv-button" data-product_id="' . get_the_ID() . '"><span>'.$label.'</span></button>';
			endif; ?>
		</figure>

		<div class="product__action">
			<div class="price_wrap">
				<?php multimarket_product_price(); ?>
				<?php wc_get_template_part( 'loop/modern-rating' ); ?>
			</div>
			<div><?php multimarket_product_add_to_cart(); ?></div>
		</div>
		
		
	</div>
</div>
