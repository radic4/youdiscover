<?php
$br = 0;
$args = array( 
    'post_type' => 'questions',
    'posts_per_page' => -1,
    'orderby' => array('date' => 'ASC')
);

$custom_query = new WP_Query( $args );
if ($custom_query->have_posts()) :
?>
<form action="<?php echo get_bloginfo( 'wpurl' ); ?>" method="post" id="secondform">
    <input type="hidden" name="step" value="second" />
    <div class="tab-content">
<?php while ($custom_query->have_posts()) : $custom_query->the_post();
$answer1 = get_post_meta( $post->ID, '_answer1', true );
$answer2 = get_post_meta( $post->ID, '_answer2', true );
$answer3 = get_post_meta( $post->ID, '_answer3', true );
$answer4 = get_post_meta( $post->ID, '_answer4', true );

$picture1 = wp_get_attachment_image_src(get_post_meta( $post->ID, 'first_answer', true ))[0];
$picture2 = wp_get_attachment_image_src(get_post_meta( $post->ID, 'second_answer', true ))[0];
$picture3 = wp_get_attachment_image_src(get_post_meta( $post->ID, 'third_answer', true ))[0];
$picture4 = wp_get_attachment_image_src(get_post_meta( $post->ID, 'fourth_answer', true ))[0];
?>
        <?php if (($custom_query->current_post +1) != ($custom_query->post_count)) { ?>
        <div class="tab-pane question fade in<?php echo ($br == 0) ? ' active' : ''; ?>" id="tab<?php echo ($br+1) ?>">
            <h3><?php the_title() ?></h3>
            <input name="ask<?php echo ($br+1) ?>" type="hidden" value="<?php echo get_the_ID() ?>" />
            <?php if($answer1!="") { ?>
            <div>
                <label class="answer" href="#tab<?php echo ($br+2) ?>">
                    <input name="question<?php echo ($br+1) ?>" value="1" type="checkbox"><span><?php if($picture1!="") { ?><img src="<?php echo htmlspecialchars($picture1) ?>" alt="<?php echo htmlspecialchars($answer1) ?>"><?php } ?><b><?php echo htmlspecialchars($answer1) ?></b></span></label>
            </div>
            <?php } ?>
            <?php if($answer2!="") { ?>
            <div>
                <label class="answer" href="#tab<?php echo ($br+2) ?>">
                    <input name="question<?php echo ($br+1) ?>" value="2" type="checkbox"><span><?php if($picture2!="") { ?><img src="<?php echo htmlspecialchars($picture2) ?>" alt="<?php echo htmlspecialchars($answer2) ?>"><?php } ?><b><?php echo htmlspecialchars($answer2) ?></b></span></label>
            </div>
            <?php } ?>
            <?php if($answer3!="") { ?>
            <div>
                <label class="answer" href="#tab<?php echo ($br+2) ?>">
                    <input name="question<?php echo ($br+1) ?>" value="3" type="checkbox"><span><b><?php if($picture3!="") { ?><img src="<?php echo htmlspecialchars($picture3) ?>" alt="<?php echo htmlspecialchars($answer3) ?>"><?php } ?><?php echo htmlspecialchars($answer3) ?></b></span></label>
            </div>
            <?php } ?>
            <?php if($answer4!="") { ?>
            <div>
                <label class="answer" href="#tab<?php echo ($br+2) ?>">
                    <input name="question<?php echo ($br+1) ?>" value="4" type="checkbox"><span><b><?php if($picture4!="") { ?><img src="<?php echo htmlspecialchars($picture4) ?>" alt="<?php echo htmlspecialchars($answer4) ?>"><?php } ?><?php echo htmlspecialchars($answer4) ?></b></span></label>
            </div>
            <?php } ?>
        </div>
        <?php } else { ?>
        <div class="tab-pane question fade in<?php echo ($br == 0) ? ' active' : ''; ?>" id="tab<?php echo ($br+1) ?>">
            <h3><?php the_title() ?></h3>
            <input name="ask<?php echo ($br+1) ?>" type="hidden" value="<?php echo get_the_ID() ?>" />
            <?php if($answer1!="") { ?>
            <div>
                <label class="answer">
                    <input name="question<?php echo ($br+1) ?>" value="1" type="checkbox" data-submit="true"><span><?php if($picture1!="") { ?><img src="<?php echo htmlspecialchars($picture1) ?>" alt="<?php echo htmlspecialchars($answer1) ?>"><?php } ?><b><?php echo htmlspecialchars($answer1) ?></b></span></label>
            </div>
            <?php } ?>
            <?php if($answer2!="") { ?>
            <div>
                <label class="answer">
                    <input name="question<?php echo ($br+1) ?>" value="2" type="checkbox" data-submit="true"><span><?php if($picture2!="") { ?><img src="<?php echo htmlspecialchars($picture2) ?>" alt="<?php echo htmlspecialchars($answer2) ?>"><?php } ?><b><?php echo htmlspecialchars($answer2) ?></b></span></label>
            </div>
            <?php } ?>
            <?php if($answer3!="") { ?>
            <div>
                <label class="answer">
                    <input name="question<?php echo ($br+1) ?>" value="3" type="checkbox" data-submit="true"><span><?php if($picture3!="") { ?><img src="<?php echo htmlspecialchars($picture3) ?>" alt="<?php echo htmlspecialchars($answer3) ?>"><?php } ?><b><?php echo htmlspecialchars($answer3) ?></b></span></label>
            </div>
            <?php } ?>
            <?php if($answer4!="") { ?>
            <div>
                <label class="answer">
                    <input name="question<?php echo ($br+1) ?>" value="4" type="checkbox" data-submit="true"><span><?php if($picture4!="") { ?><img src="<?php echo htmlspecialchars($picture4) ?>" alt="<?php echo htmlspecialchars($answer4) ?>"><?php } ?><b><?php echo htmlspecialchars($answer4) ?></b></span></label>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
<?php $br++; endwhile; ?>
    </div>
    <input type="hidden" name="where" value="<?php echo htmlspecialchars($where); ?>" />
    <input type="hidden" name="duration" value="<?php echo htmlspecialchars($duration); ?>" />
    <input type="hidden" name="count" value="<?php echo htmlspecialchars($count); ?>" />
</form>
<script>
    jQuery(document).ready(function() {
        jQuery('input:checkbox').attr('checked', false);
        var stepBar = document.getElementById("step-bar");
        var step = 1;
        var questions = jQuery(".question").length;
        var width = 100 / questions;
        stepBar.style.width = width + '%';

        jQuery(".answer").on("click", function(event) {
            event.preventDefault();
            jQuery(this).find('input').attr('checked', true).triggerHandler('change');
            if(jQuery(this).find('input').attr('data-submit') == 'true') jQuery('#secondform').submit();
            else {
                jQuery(this).tab('show');
                step++;
                stepBar.style.width = width * step + '%';
            }
        });
    });
</script>
<?php endif; wp_reset_postdata(); ?>