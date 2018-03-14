<?php
get_template_part( 'templates/menu' );
?>
<?php if (has_post_thumbnail() ): ?>
	<div class="banner banner-short" style="background-image: url('<?php the_post_thumbnail_url(); ?>');">
<?php else : ?>
<div class="banner banner-short nobackground">
<?php endif; ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="banner-desc">
                    <h2><?php the_title() ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="country-desc">
    <div class="container">
		<?php echo apply_filters('the_content',$post->post_content); ?>
    </div>
</div>