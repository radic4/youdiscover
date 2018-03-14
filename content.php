<?php get_template_part( 'templates/menu' ); ?>
<div class="banner" style="background-image: url('<?php echo (get_option( 'home_image', '' )!="") ? get_option( 'home_image', '' ) : get_bloginfo('template_directory').'/images/home-background.jpg'; ?>');">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="banner-desc">
                    <?php
                    $home_title = get_option( 'home_title', '' );
                    $home_subtitle = get_option( 'home_subtitle', '' );
                    ?>
                    <h2><?php echo htmlspecialchars($home_title) ?></h2>
                    <h3><?php echo htmlspecialchars($home_subtitle) ?></h3>
                </div>
                <?php get_template_part( 'templates/form' ); ?>
            </div>
        </div>
    </div>
</div>

<?php get_template_part( 'templates/services' ); ?>

<?php get_template_part( 'templates/slider' ); ?>