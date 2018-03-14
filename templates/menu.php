<?php if (is_home() || is_singular('post') || is_singular('page')) : ?>
<header<?php echo (!has_post_thumbnail() && (is_singular('post') || is_singular('page'))) ? ' class="nobackground"' : '' ?>>
    <div class="container">
        <div class="row">
            <div class="navigation-menu">
                <div class="col-md-4 col-sm-3 logo">
                    <a class="nav-logo" href="<?php echo get_bloginfo( 'wpurl' ); ?>"><h1><?php echo get_bloginfo('name') ?></h1></a>
                </div>
                <?php if(is_home()) { ?>
                <div class="col-md-5 col-sm-6 col-xs-12 main-menu">
                    <nav class="navbar navbar-default">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu" aria-expanded="false">
                                <span class="icon-bar top-bar"></span>
                                <span class="icon-bar middle-bar"></span>
                                <span class="icon-bar bottom-bar"></span>
                            </button>
                        </div>
                            <?php 
                                $args = array('theme_location' => 'primary', 'menu_class' => 'nav navbar-nav', 'container_class' => 'collapse navbar-collapse', 'container_id' => 'menu', 'depth' => 1);
                                wp_nav_menu($args);
                            ?>
                    </nav>
                </div>
                <?php
                $phone = get_option( 'phone_number', '' );
                if($phone!="") {
                ?>
                <div class="col-md-3 col-sm-3 hidden-xs"><span>Call us: <a href="tel:<?php echo htmlspecialchars($phone) ?>"><?php echo htmlspecialchars($phone) ?></a></span></div>
                <?php }} ?>
            </div>
        </div>
    </div>
</header>

<?php else : ?>

<header>
    <div class="container">
        <div class="row">
            <div class="navigation-menu">
                <div class="col-md-7 col-sm-7 logo">
                    <a class="nav-logo" href="<?php echo get_bloginfo( 'wpurl' ); ?>"><h1><?php echo get_bloginfo('name') ?></h1></a>
                    <ul class="list-inline hidden-xs">
                        
                        <?php if(is_singular('countries')) : ?>
                        	<li><a href="<?php the_permalink(get_post_meta(get_the_ID(), '_continent', true)); ?>"><?php echo get_the_title(get_post_meta(get_the_ID(), '_continent', true)); ?></a> <span> <i class="fas fa-chevron-right"></i> </span></li>
                        <?php endif; ?>

                        <?php if(is_singular('places')) : ?>
                        	<li><a href="<?php the_permalink(get_post_meta(get_post_meta(get_the_ID(), '_country', true), '_continent', true)); ?>"><?php echo get_the_title(get_post_meta(get_post_meta(get_the_ID(), '_country', true), '_continent', true)); ?></a> <span> <i class="fas fa-chevron-right"></i> </span></li>
                        	<li><a href="<?php the_permalink(get_post_meta(get_the_ID(), '_country', true)); ?>"><?php echo get_the_title(get_post_meta(get_the_ID(), '_country', true)); ?></a> <span> <i class="fas fa-chevron-right"></i> </span></li>
                        <?php endif; ?>

                        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> </li>
                    </ul>   
                </div>
                <div class="col-md-5 col-sm-5 main-menu menu-detail">
                    <nav class="navbar navbar-default">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu" aria-expanded="false">
                                <span class="icon-bar top-bar"></span>
                                <span class="icon-bar middle-bar"></span>
                                <span class="icon-bar bottom-bar"></span>
                            </button>
                        </div>

<?php if(is_singular('countries')) : ?>

                        <div class="collapse navbar-collapse" id="menu">
                            <ul class="list-inline visible-xs">
                                
                                <?php if(is_singular('countries')) : ?>
                                    <li><a href="<?php the_permalink(get_post_meta(get_the_ID(), '_continent', true)); ?>"><?php echo get_the_title(get_post_meta(get_the_ID(), '_continent', true)); ?></a> <span> <i class="fas fa-chevron-right"></i> </span></li>
                                <?php endif; ?>

                                <?php if(is_singular('places')) : ?>
                                    <li><a href="<?php the_permalink(get_post_meta(get_post_meta(get_the_ID(), '_country', true), '_continent', true)); ?>"><?php echo get_the_title(get_post_meta(get_post_meta(get_the_ID(), '_country', true), '_continent', true)); ?></a> <span> <i class="fas fa-chevron-right"></i> </span></li>
                                    <li><a href="<?php the_permalink(get_post_meta(get_the_ID(), '_country', true)); ?>"><?php echo get_the_title(get_post_meta(get_the_ID(), '_country', true)); ?></a> <span> <i class="fas fa-chevron-right"></i> </span></li>
                                <?php endif; ?>

                                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> </li>
                            </ul>  
                            <ul class="nav navbar-nav">
<?php
$terms = get_terms( array(
    'post_type' => 'places',
    'taxonomy' => 'category',
    'parent' => 0,
    'hide_empty' => true
));

foreach ($terms as $term) :
    $term_id = $term->term_id;
    $term_name = $term->name;
    $args = array( 
        'post_type' => 'places',
        'posts_per_page' => -1,
        'orderby' => 'name',
        'cat' => $term_id,
        'meta_key' => '_country',
        'meta_value' => get_the_ID()
    );

    $custom_query = new WP_Query( $args );
    if ($custom_query->have_posts()) :
?>
                                <li class="dropdown "><a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $term_name ?> <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
<?php while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
                                        <li>
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </li>
<?php endwhile; wp_reset_postdata(); ?>
                                    </ul>
                                </li>
<?php endif; endforeach; ?>
                            </ul>
                        </div>

<?php elseif(is_singular('continents')) : ?>

                        <div class="collapse navbar-collapse" id="menu">
                            <ul class="list-inline visible-xs">
                                
                                <?php if(is_singular('countries')) : ?>
                                    <li><a href="<?php the_permalink(get_post_meta(get_the_ID(), '_continent', true)); ?>"><?php echo get_the_title(get_post_meta(get_the_ID(), '_continent', true)); ?></a> <span> <i class="fas fa-chevron-right"></i> </span></li>
                                <?php endif; ?>

                                <?php if(is_singular('places')) : ?>
                                    <li><a href="<?php the_permalink(get_post_meta(get_post_meta(get_the_ID(), '_country', true), '_continent', true)); ?>"><?php echo get_the_title(get_post_meta(get_post_meta(get_the_ID(), '_country', true), '_continent', true)); ?></a> <span> <i class="fas fa-chevron-right"></i> </span></li>
                                    <li><a href="<?php the_permalink(get_post_meta(get_the_ID(), '_country', true)); ?>"><?php echo get_the_title(get_post_meta(get_the_ID(), '_country', true)); ?></a> <span> <i class="fas fa-chevron-right"></i> </span></li>
                                <?php endif; ?>

                                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> </li>
                            </ul> 
                            <ul class="nav navbar-nav">
<?php
    $args = array( 
        'post_type' => 'countries',
        'posts_per_page' => -1,
        'orderby' => 'name',
        'meta_key' => '_continent',
        'meta_value' => get_the_ID()
    );

    $custom_query = new WP_Query( $args );
    if ($custom_query->have_posts()) :
?>
                                <li class="dropdown "><a href="#" data-toggle="dropdown" class="dropdown-toggle">LÄNDER <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
<?php while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
                                        <li>
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </li>
<?php endwhile; wp_reset_postdata(); ?>
                                    </ul>
                                </li>
<?php endif; ?>
                            </ul>
                        </div>

<?php elseif(is_singular('places')) : ?>

                        <div class="collapse navbar-collapse" id="menu">
                            <ul class="list-inline visible-xs">
                                
                                <?php if(is_singular('countries')) : ?>
                                    <li><a href="<?php the_permalink(get_post_meta(get_the_ID(), '_continent', true)); ?>"><?php echo get_the_title(get_post_meta(get_the_ID(), '_continent', true)); ?></a> <span> <i class="fas fa-chevron-right"></i> </span></li>
                                <?php endif; ?>

                                <?php if(is_singular('places')) : ?>
                                    <li><a href="<?php the_permalink(get_post_meta(get_post_meta(get_the_ID(), '_country', true), '_continent', true)); ?>"><?php echo get_the_title(get_post_meta(get_post_meta(get_the_ID(), '_country', true), '_continent', true)); ?></a> <span> <i class="fas fa-chevron-right"></i> </span></li>
                                    <li><a href="<?php the_permalink(get_post_meta(get_the_ID(), '_country', true)); ?>"><?php echo get_the_title(get_post_meta(get_the_ID(), '_country', true)); ?></a> <span> <i class="fas fa-chevron-right"></i> </span></li>
                                <?php endif; ?>

                                <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> </li>
                            </ul> 
                            <ul class="nav navbar-nav">
<?php
$terms = get_terms( array(
    'post_type' => 'places',
    'taxonomy' => 'category',
    'parent' => 0,
    'hide_empty' => true
));

foreach ($terms as $term) :
    $term_id = $term->term_id;
    $term_name = $term->name;
    $args = array( 
        'post_type' => 'places',
        'posts_per_page' => -1,
        'orderby' => 'name',
        'cat' => $term_id,
        'post__not_in' => array(get_the_ID()),
        'meta_key' => '_country',
        'meta_value' => get_post_meta(get_the_ID(), '_country')
    );

    $custom_query = new WP_Query( $args );
    if ($custom_query->have_posts()) :
?>
                                <li class="dropdown "><a href="#" data-toggle="dropdown" class="dropdown-toggle"><?php echo $term_name ?> <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
<?php while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
                                        <li>
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </li>
<?php endwhile; wp_reset_postdata(); ?>
                                    </ul>
                                </li>
<?php endif; endforeach; ?>
                            </ul>
                        </div>

<?php endif; ?>

                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>

<?php endif; ?>