<?php
// === Custom Post Type: Property ===
function create_property_cpt() {
    $labels = array(
        'name'                  => 'Properties',
        'singular_name'         => 'Property',
        'menu_name'             => 'Properties',
        'name_admin_bar'        => 'Property',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Property',
        'edit_item'             => 'Edit Property',
        'new_item'              => 'New Property',
        'view_item'             => 'View Property',
        'all_items'             => 'All Properties',
        'search_items'          => 'Search Properties',
        'not_found'             => 'No properties found.',
        'not_found_in_trash'    => 'No properties found in Trash.',
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => false,
        'rewrite'               => array('slug' => 'property'),
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
        'menu_icon'             => 'dashicons-admin-multisite',
        'show_in_rest'          => true,
    );

    register_post_type('property', $args);
}


add_action('init', 'create_property_cpt');
	function enqueue_owl_carousel_assets() {
		wp_enqueue_style('owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css');
		wp_enqueue_style('owl-theme', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css');
		wp_enqueue_script('owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array('jquery'), null, true);
	}
add_action('wp_enqueue_scripts', 'enqueue_owl_carousel_assets');


// === Property Carousel Shortcode ===
function property_carousel_shortcode() {
    ob_start(); ?>

    <div class="property-carousel owl-carousel">
        <?php
        $args = array(
            'post_type' => 'property',
            'posts_per_page' => -1,
        );
        $loop = new WP_Query($args);
        if ($loop->have_posts()) :
            while ($loop->have_posts()) : $loop->the_post(); ?>
                <div class="item">
                    <div class="property-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="property-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <h3 class="property-title"><?php the_title(); ?></h3>

                        <div class="property-content">
                            <?php the_excerpt(); ?>
                        </div>

                        <a href="<?php the_permalink(); ?>" class="property-btn">Objekt ansehen</a>
                    </div>
                </div>
            <?php endwhile;
        endif;
        wp_reset_postdata();
        ?>
    </div>

    <script>
    jQuery(document).ready(function($){
        $('.property-carousel').owlCarousel({
            loop: true,
            margin: 20,
            nav: false,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            dots: true,
            smartSpeed: 800,
			stagePadding: 100,
            responsive:{
                 0: {
					items: 1
				},
				601: {
					items: 2
				},
				1025: {
					items: 4
				}
            }
        });
    });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('property_carousel', 'property_carousel_shortcode');
