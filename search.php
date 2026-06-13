<?php
/**
 * Risultati di ricerca.
 * @package Diary
 */
if (!defined('ABSPATH')) exit;
get_header();
?>
<header class="page-header">
    <h1 class="page-title">
        <?php printf(esc_html__('Risultati per: %s', 'diary'), '<span>' . get_search_query() . '</span>'); ?>
    </h1>
</header>
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <?php get_template_part('template-parts/content', get_post_format()); ?>
    <?php endwhile; ?>
    <?php the_posts_pagination(array('mid_size' => 2)); ?>
<?php else : ?>
    <?php get_template_part('template-parts/content', 'none'); ?>
<?php endif; ?>
<?php
get_footer();
