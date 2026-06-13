<?php
/**
 * Pagina 404.
 * @package Diary
 */
if (!defined('ABSPATH')) exit;
get_header();
?>
<section class="error-404 not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e('Pagina non trovata', 'diary'); ?></h1>
    </header>
    <div class="page-content entry-content">
        <p><?php esc_html_e('La pagina che cerchi non esiste o è stata spostata. Forse una ricerca può aiutare.', 'diary'); ?></p>
        <?php get_search_form(); ?>
    </div>
</section>
<?php
get_footer();
