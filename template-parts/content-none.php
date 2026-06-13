<?php
/**
 * Template part: nessun contenuto trovato.
 * @package Diary
 */
if (!defined('ABSPATH')) exit;
?>
<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Nessun risultato', 'diary'); ?></h1>
    </header>
    <div class="page-content">
        <?php if (is_search()) : ?>
            <p><?php esc_html_e('Nessun risultato per la ricerca. Prova con altre parole.', 'diary'); ?></p>
            <?php get_search_form(); ?>
        <?php else : ?>
            <p><?php esc_html_e('Non è stato trovato nulla in questa posizione.', 'diary'); ?></p>
            <?php get_search_form(); ?>
        <?php endif; ?>
    </div>
</section>
