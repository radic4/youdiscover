<form action="<?php echo get_bloginfo( 'wpurl' ); ?>" method="post" id="thirdform">
    <input name="step" value="third" type="hidden" />
    <p><b><?php echo get_option( 'results_found_who', '' ); ?></b></p>
    <div class="row">
        <div class="col-sm-6 form-input">
            <div class="error-message error-message1" style="display: none;">
                <span data-toggle="tooltip" data-placement="top" title="Bitte füllen Sie dieses Feld aus."></span>
            </div>
            <input name="fname" type="text" placeholder="Vorname">
        </div>
        <div class="col-sm-6 form-input">
            <div class="error-message error-message2" style="display: none;">
                <span data-toggle="tooltip" data-placement="top" title="Bitte füllen Sie dieses Feld aus."></span>
            </div>
            <input name="lname" type="text" placeholder="Nachname">
        </div>
        <div class="col-sm-6 form-input">
            <div class="error-message error-message3" style="display: none;">
                <span data-toggle="tooltip" data-placement="top" title="Bitte füllen Sie dieses Feld aus."></span>
            </div>
            <input name="useremail" type="email" placeholder="Email">
        </div>
        <div class="col-sm-6 form-input">
            <div class="error-message error-message4" style="display: none;">
                <span data-toggle="tooltip" data-placement="top" title="Bitte füllen Sie dieses Feld aus."></span>
            </div>
            <input name="usertelefon" type="text" placeholder="Telefonnummer">
        </div>
        <div class="col-sm-12">
            <button type="submit"><b><?php echo get_option( 'button_title', '' ); ?></b><br><?php echo get_option( 'button_subtitle', '' ); ?></button>
        </div>
    </div>
    <input type="hidden" name="where" value="<?php echo htmlspecialchars($where); ?>" />
    <input type="hidden" name="duration" value="<?php echo htmlspecialchars($duration); ?>" />
    <input type="hidden" name="count" value="<?php echo htmlspecialchars($count); ?>" />
    <?php foreach($answers as $key => $value) { ?>
    <input type="hidden" name="question<?php echo ($key) ?>" value="<?php echo htmlspecialchars($value); ?>" />
    <?php } ?>
    <?php foreach($asks as $key => $value) { ?>
    <input type="hidden" name="ask<?php echo ($key) ?>" value="<?php echo htmlspecialchars($value); ?>" />
    <?php } ?>
</form>
<script>
    jQuery(function () {
      jQuery('#thirdform').submit(function(event) {
        event.preventDefault();
        
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        var error = false;

        jQuery('.error-message').hide();

        if(jQuery('input[name="fname"]').val() == '') { error = true; jQuery('.error-message1').show(); jQuery('.error-message1 span').tooltip({trigger: 'manual'}).tooltip('show'); }
        if(jQuery('input[name="lname"]').val() == '') { error = true; jQuery('.error-message2').show(); jQuery('.error-message2 span').tooltip({trigger: 'manual'}).tooltip('show'); }
        if(jQuery('input[name="useremail"]').val() == '' || !emailPattern.test(jQuery('input[name="useremail"]').val())) { error = true; jQuery('.error-message3').show(); jQuery('.error-message3 span').tooltip({trigger: 'manual'}).tooltip('show'); }
        if(jQuery('input[name="usertelefon"]').val() == '') { error = true; jQuery('.error-message4').show(); jQuery('.error-message4 span').tooltip({trigger: 'manual'}).tooltip('show'); }
        if(!error) this.submit();
      });
    });
    
    setInterval(function(){ jQuery(".loading").hide(200); }, 3000);
</script>