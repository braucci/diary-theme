<?php
/**
 * Articolo singolo.
 * @package Diary
 */
if (!defined('ABSPATH')) exit;
get_header();
?>
<?php while (have_posts()) : the_post(); ?>

    <?php get_template_part('template-parts/content', 'single'); ?>

    <?php
    the_post_navigation(array(
        'prev_text' => '<span class="nav-label">' . __('Articolo precedente', 'diary') . '</span> %title',
        'next_text' => '<span class="nav-label">' . __('Articolo successivo', 'diary') . '</span> %title',
        'class'     => 'diary-pagination',
    ));
    ?>

    <?php
    if (comments_open() || get_comments_number()) :
        comments_template();
    endif;
    ?>

<?php endwhile; ?>
<?php
get_footer();
