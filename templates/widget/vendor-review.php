<?php 
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/widget/vendor-review.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version     0.0.1
 */

global $WCMp;
$review_count = count($comments) ;
if($review_count > 3)	{ ?>
<div id="wcmp_widget_vendor_review" style="height: 308px; overflow-y: scroll; width: 226px;" >
<?php } else { ?>
<div id="wcmp_widget_vendor_review" style=" height: auto; width: 226px;" >
<?php
 }
if($comments){
	foreach($comments as $comment) {
		$rating   = intval( get_comment_meta( $comment->comment_ID, 'vendor_rating', true ) );
		$verified = wcmp_review_is_from_verified_owner( $comment, $vendor->term_id );
		if ( $rating && get_option( 'woocommerce_enable_review_rating' ) === 'yes'){?>
			<div class="comment-text">
				<a href="<?php echo $vendor->permalink.'#li-comment-'.$comment->comment_ID?>">
					<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', 'dc-woocommerce-multi-vendor' ), $rating ) ?>">
						<span style="width:<?php echo ( $rating / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'dc-woocommerce-multi-vendor' ); ?></span>
					</div>
				</a>
				<?php if ( $comment->comment_approved != '0' ) {?>
					<p class="meta">
						<strong itemprop="author"><?php comment_author($comment->comment_ID); ?></strong> <?php
						if ( get_option( 'woocommerce_review_rating_verification_label' ) === 'yes' )
							if ( $verified )
								echo '<em class="verified">(' . apply_filters('wcmp_widget_varified_buyer_text_filter',__( 'verified buyer', 'dc-woocommerce-multi-vendor' )) . ')</em> ';

						?>&ndash; <time itemprop="datePublished" datetime="<?php echo get_comment_date( 'c',$comment->comment_ID ); ?>"><?php echo get_comment_date( wc_date_format(), $comment->comment_ID ); ?></time>
					</p>
				<?php } ?>
				<div itemprop="description" class="description"><?php 
					echo wp_trim_words($comment->comment_content, 10, "..<a href=".$vendor->permalink.'#li-comment-'.$comment->comment_ID.">Read more</a>" );?>
					<div style="height:10px; width:100%">&nbsp;</div>
				</div>
			</div>
		<?php
		}
	}
}else{
 		echo apply_filters('wcmp_widget_empty_reviews_text_filter',__( 'No Reviews..', 'dc-woocommerce-multi-vendor' ));
 	}
 ?>
</div>