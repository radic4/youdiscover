<?php get_header(); ?>
<?php
	$args =  array( 
		'post_type' => 'continents',
		'p' => get_the_ID()
	);

	$custom_query = new WP_Query( $args );
	while ($custom_query->have_posts()) : $custom_query->the_post();
		
		get_template_part( 'content-single-continents', get_post_format() );

	endwhile;
?>
<?php get_footer(); ?>