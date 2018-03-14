<?php
$customtitle = get_post_meta( $post->ID, '_customtitle', true );
$customsubtitle = get_post_meta( $post->ID, '_customsubtitle', true );
get_template_part( 'templates/menu' );
?>
<?php if (has_post_thumbnail() ): ?>
	<div class="banner" style="background-image: url('<?php the_post_thumbnail_url(); ?>');">
<?php else : ?>
<div class="banner" style="background-image: url('<?php echo (get_option( 'home_image', '' )!="") ? get_option( 'home_image', '' ) : get_bloginfo('template_directory').'/images/home-background.jpg'; ?>');">
<?php endif; ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="banner-desc">
                    <h2><?php echo $customtitle ?></h2>
                    <h3><?php echo $customsubtitle ?></h3>
                </div>
                <?php get_template_part( 'templates/form' ); ?>
            </div>
        </div>
    </div>
</div>

<?php get_template_part( 'templates/services' ); ?>

<div class="country-desc">
    <div class="container">
		<?php echo apply_filters('the_content',$post->post_content); ?>
    </div>
</div>

<?php get_template_part( 'templates/slider' ); ?>