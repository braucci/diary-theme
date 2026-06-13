<?php
/**
 * Pagina statica.
 * @package Diary
 */
if (!defined('ABSPATH')) exit;
get_header();
?>
<?php while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>
        <header class="entry-header">
            <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
        </header>
        <?php if (has_post_thumbnail()) : ?>
            <div class="entry-thumbnail"><?php the_post_thumbnail('diary-featured'); ?></div>
        <?php endif; ?>
        <div class="entry-content">
            <?php the_content(); diary_link_pages(); ?>
        </div>
    </article>
    <?php
    if (comments_open() || get_comments_number()) comments_template();
    ?>
<?php endwhile; ?>
<?php
get_footer();
