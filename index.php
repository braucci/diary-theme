<?php
/**
 * Template principale (home blog e fallback).
 * @package Diary
 */
if (!defined('ABSPATH')) exit;
get_header();
?>
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <?php get_template_part('template-parts/content', get_post_format()); ?>
    <?php endwhile; ?>

    <?php
    the_posts_pagination(array(
        'mid_size'  => 2,
        'prev_text' => __('&larr; Precedenti', 'diary'),
        'next_text' => __('Successivi &rarr;', 'diary'),
        'class'     => 'diary-pagination',
    ));
    ?>
<?php else : ?>
    <?php get_template_part('template-parts/content', 'none'); ?>
<?php endif; ?>
<?php
get_footer();
