<?php
/**
 * Template part: anteprima post in home e archivi.
 *
 * La modalità di visualizzazione (estratto / completo / automatico)
 * è scelta dall'utente in Aspetto → Personalizza → Diary: Opzioni Blog.
 *
 * In tutti i casi si usa la stessa struttura .entry-content / .entry-summary
 * così font, dimensione e giustificazione restano IDENTICI tra home e
 * articolo singolo.
 *
 * @package Diary
 */
if (!defined('ABSPATH')) exit;

$diary_display = get_theme_mod('diary_home_display', 'excerpt');

// In modalità "auto": estratto manuale se presente, altrimenti testo completo.
$diary_show_full = false;
if ('full' === $diary_display) {
    $diary_show_full = true;
} elseif ('auto' === $diary_display && !has_excerpt()) {
    $diary_show_full = true;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('entry'); ?>>

    <header class="entry-header">
        <?php the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>
        <?php diary_post_meta(); ?>
    </header>

    <?php if (has_post_thumbnail()) : ?>
        <div class="entry-thumbnail">
            <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                <?php the_post_thumbnail('diary-featured'); ?>
            </a>
        </div>
    <?php endif; ?>

    <?php if ($diary_show_full) : ?>

        <div class="entry-content">
            <?php
            the_content(sprintf(
                wp_kses(
                    __('Continua a leggere<span class="screen-reader-text"> %s</span> &rarr;', 'diary'),
                    array('span' => array('class' => array()))
                ),
                get_the_title()
            ));
            diary_link_pages();
            ?>
        </div>

    <?php else : ?>

        <div class="entry-summary">
            <?php the_excerpt(); ?>
            <a class="read-more" href="<?php the_permalink(); ?>">
                <?php esc_html_e('Continua a leggere', 'diary'); ?> &rarr;
            </a>
        </div>

    <?php endif; ?>

</article>
