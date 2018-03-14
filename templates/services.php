<?php
$services_title = get_option( 'services_title', '' );

$args = array( 
    'post_type' => 'services',
    'posts_per_page' => -1,
    'orderby' => array('date' => 'ASC')
);

$custom_query = new WP_Query( $args );
if ($custom_query->have_posts()) :
?>
<div class="qualitat">
    <div class="container">
        <div class="row">
            <?php if($services_title!="") { ?>
            <div class="col-md-12">
                <h2><?php echo $services_title; ?></h2>
            </div>
            <?php } ?>
<?php $br=0; while ($custom_query->have_posts()) : $custom_query->the_post(); if($br!=0 && $br%4==0) echo '</div><div class="row">'; ?>
            <div class="col-md-3 col-sm-3">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( array('class' => 'img-responsive') ); ?>
                <?php endif; ?>
                <p><?php echo $post->post_content; ?></p>
            </div>
<?php $br++; endwhile; ?>
        </div>
    </div>
</div>
<?php endif; wp_reset_postdata(); ?>