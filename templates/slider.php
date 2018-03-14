<?php
$args = array( 
	'post_type' => 'countries',
	'posts_per_page' => -1,
	'orderby' => 'rand',
	'post__not_in' => array(get_the_ID()),
    'meta_key' => '_featured',
    'meta_value' => '1'
);

$custom_query = new WP_Query( $args );
if ($custom_query->have_posts()) :
?>
<div class="place-slider">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2>Reiseziele</h2>
            </div>
            <div class="col-md-12">
                <div class="slider multiple-items">
<?php while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
                    <div>
                        <a href="<?php the_permalink(); ?>"> 
                            <span><?php the_title(); ?></span>
                            <?php if ( has_post_thumbnail() ) : ?>
                            	<?php the_post_thumbnail( 'slider', array('class' => 'img-responsive') ); ?>
                            <?php endif; ?>
                        </a>
                    </div>
<?php endwhile; ?>
                </div>  
            </div>          
        </div>
    </div>
</div>
<?php endif; wp_reset_postdata(); ?>