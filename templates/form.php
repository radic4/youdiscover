<?php if(isset($_GET['formerror']) && $_GET['formerror'] == '1') { ?>
<div class="row text-center">
    <div class="global-error">
      <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
      <strong>Fehler!</strong> Etwas ist schief gelaufen.
    </div>
</div>
<?php } ?>

<form action="<?php echo get_bloginfo( 'wpurl' ); ?>" method="post" class="search-form" id="firstform">
    <input type="hidden" name="step" value="first" />
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12 search-form-block wohin">
            <label>WOHIN</label>
            <ul class="select-list-group" id="slg">
                <li>
                    <div class="error-message" style="display: none;">
                        <span data-toggle="tooltip" data-placement="top" title="Bitte füllen Sie dieses Feld aus."></span>
                    </div>
                    <input name="where" type="text" class="select-list-group__search" autocomplete="off" placeholder="Wohin möchten Sie reisen?" value="<?php echo (is_singular('countries')) ? get_the_title(get_the_ID()) : (is_singular('places') ? get_the_title(get_post_meta(get_the_ID(), '_country', true)).' | '.get_the_title() : '') ?>"/>
                    <span class="select-list-group__toggle"> </span>
                    <ul class="false">
<?php
    $args =  array( 
        'post_type' => 'countries',
        'orderby' => array('title' => 'ASC')
    );

    $custom_query = new WP_Query( $args );
    while ($custom_query->have_posts()) : $custom_query->the_post();
?>
                        <li><?php the_title() ?></li>
<?php
    endwhile; wp_reset_postdata();
?>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12 search-form-block dauer">
            <label>DAUER</label>
            <div class="form-select">
                <select name="duration" class="turnintodropdown">
                    <option value="1-2 Wochen">1-2 Wochen</option>
                    <option value="3-4 Wochen">3-4 Wochen</option>
                    <option value="5+ Wochen">5+ Wochen</option>
                    <option value="Weiß nicht">Weiß nicht</option>
                </select>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12 search-form-block anzahl">
            <label>ANZAHL</label>
            <div class="form-select">
                <select name="count" class="turnintodropdown">
                    <option value="1-2 Personen">1-2 Personen</option>
                    <option value="3-4 Personen">3-4 Personen</option>
                    <option value="5-6 Personen">5-6 Personen</option>
                    <option value="7+ Personen">7+ Personen</option>
                </select>
            </div>
        </div>
        <div class="col-md-2 col-sm-6 col-xs-12">
            <button type="submit">Suchen</button>
        </div>
    </div>
</form>
<script>
    jQuery(function () {
      jQuery('#firstform').submit(function(event) {
        event.preventDefault();
        if(jQuery('input[name="where"]').val() == '') { jQuery('.error-message').show(); jQuery('.error-message span').tooltip({trigger: 'manual'}).tooltip('show'); }
        else this.submit();
      });
    });
</script>