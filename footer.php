<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-4 col-xs-12">
                <h4><a href="<?php echo get_bloginfo( 'wpurl' ); ?>"><?php echo get_bloginfo('name') ?></a></h4>
                <p><?php echo get_bloginfo('description') ?></p>
            </div>
<?php
if(is_active_sidebar('footer-1')){
dynamic_sidebar('footer-1');
}
if(is_active_sidebar('footer-2')){
dynamic_sidebar('footer-2');
}
if(is_active_sidebar('footer-3')){
dynamic_sidebar('footer-3');
}
?>
        </div>
    </div>
</footer>

    <!-- SCRIPTS -->
    <?php wp_footer(); ?>
</body>

</html>