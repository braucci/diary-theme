<?php
/**
 * Template part: articolo singolo completo.
 * Stessa struttura .entry-content della home: coerenza tipografica totale.
 *
 * @package Diary
 */
if (!defined('ABSPATH')) exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>

    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
        <?php diary_post_meta(); ?>
    </header>

    <?php if (has_post_thumbnail()) : ?>
        <div class="entry-thumbnail">
            <?php the_post_thumbnail('diary-featured'); ?>
        </div>
    <?php endif; ?>

    <div class="entry-content">
        <?php
        the_content();
        diary_link_pages();
        ?>
    </div>

    <?php if (has_tag()) : ?>
        <footer class="entry-footer">
            <div class="entry-tags"><?php the_tags('', ' '); ?></div>
        </footer>
    <?php endif; ?>

</article>
