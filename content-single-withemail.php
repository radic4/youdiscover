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
                <?php
                if(isset($_GET['show']) && $_GET['show'] == "message"){
                    echo "<h3>Vielen Dank!</h3></div>";
                } else {
                ?>
                </div>
                <form action="?send=email" method="post" class="search-form emailform" id="emailform">
                    <input type="hidden" name="step" value="emailform" />
                    <div class="row">
                        <div class="col-md-9 col-sm-8 col-xs-12 search-form-block wohin">
                            <label>E-MAIL</label>
                            <ul class="select-list-group">
                                <li>
                                    <div class="error-message" style="display: none;">
                                        <span data-toggle="tooltip" data-placement="top" title="Bitte füllen Sie dieses Feld aus."></span>
                                    </div>
                                    <input name="email" type="text" class="select-list-group__search" placeholder="Ihre E-Mail Adresse" />
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-3 col-sm-4 col-xs-12">
                            <button type="submit">SENDEN</button>
                        </div>
                    </div>
                </form>
                <?php } ?>
                <script>
                    jQuery(function () {
                      jQuery('#emailform').submit(function(event) {
                        event.preventDefault();
                        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

                        if(jQuery('input[name="email"]').val() == '' || !emailPattern.test(jQuery('input[name="email"]').val())) { jQuery('.error-message').show(); jQuery('.error-message span').tooltip({trigger: 'manual'}).tooltip('show'); }
                        else this.submit();
                      });
                    });
                </script>
            </div>
        </div>
    </div>
</div>

<div class="country-desc">
    <div class="container">
		<?php echo apply_filters('the_content',$post->post_content); ?>
    </div>
</div>