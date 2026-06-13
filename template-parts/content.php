<?php
/**
 * Template part: anteprima post in home e archivi.
 *
 * Usa la stessa struttura .entry-content dell'articolo singolo,
 * così font, dimensione e giustificazione sono IDENTICI nelle
 * due viste. L'unica differenza è estratto vs contenuto completo.
 *
 * @package Diary
 */
if (!defined('ABSPATH')) exit;
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

    <div class="entry-summary">
        <?php
        // Se l'autore ha scritto un estratto manuale, mostra quello.
        // Altrimenti WordPress genera un estratto automatico.
        the_excerpt();
        ?>
        <a class="read-more" href="<?php the_permalink(); ?>">
            <?php esc_html_e('Continua a leggere', 'diary'); ?> &rarr;
        </a>
    </div>

</article>
