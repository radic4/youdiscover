<?php get_template_part( 'templates/menu' ); ?>
<div class="banner" style="background-image: url('<?php echo (get_option( 'home_image', '' )!="") ? get_option( 'home_image', '' ) : get_bloginfo('template_directory').'/images/home-background.jpg'; ?>');">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row wizard" id="rootwizard">
                    <h2><?php echo get_option( 'question_title', '' ); ?></h2>
                    <div class="step-bar-wrapper">
                        <div id="step-bar" style="width: 1%;"></div>
                    </div>
                    <?php include( locate_template( 'templates/form-questions.php', false, false ) ); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_template_part( 'templates/services' ); ?>

<?php get_template_part( 'templates/slider' ); ?>