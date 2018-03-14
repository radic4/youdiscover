<?php get_template_part( 'templates/menu' ); ?>
<div class="banner" style="background-image: url('<?php echo (get_option( 'home_image', '' )!="") ? get_option( 'home_image', '' ) : get_bloginfo('template_directory').'/images/home-background.jpg'; ?>');">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row wizard" id="formwizard">
                    <div class="loading">
                        <div class="sk-folding-cube">
                          <div class="sk-cube1 sk-cube"></div>
                          <div class="sk-cube2 sk-cube"></div>
                          <div class="sk-cube4 sk-cube"></div>
                          <div class="sk-cube3 sk-cube"></div>
                        </div>
                        <h4><?php echo get_option( 'wait_title', '' ); ?></h4>
                        <p><?php echo get_option( 'wait_subtitle', '' ); ?></p>
                    </div>
                    <h2><?php echo get_option( 'results_found', '' ); ?></h2>
                    <h3><?php echo get_option( 'results_found_subtitle', '' ); ?></h3>
                    <?php include( locate_template( 'templates/form-final.php', false, false ) ); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_template_part( 'templates/services' ); ?>

<?php get_template_part( 'templates/slider' ); ?>