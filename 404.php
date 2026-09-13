<?php
/**
 * Pagina 404 — "Pagina smarrita"
 *
 * Invece di un messaggio d'errore, una piccola pagina di scrittura:
 * un 404 tipografico, una frase che cambia a ogni visita, la ricerca
 * e tre articoli pescati a caso dall'archivio.
 *
 * @package Diary
 */
if (!defined('ABSPATH')) exit;

get_header();

/* Frasi originali, scritte per il registro del blog.
   Ne viene mostrata una a caso ad ogni caricamento. */
$diary_404_frasi = array(
    'Anche le pagine, come le persone, a volte se ne vanno senza lasciare detto dove.',
    'Hai bussato a una porta che non c&rsquo;è più. Capita, nelle case vecchie.',
    'Questa pagina non esiste. Esiste però la strada che ti ha portato fin qui.',
    'Qualcosa è stato scritto, forse. Poi cancellato. Resta l&rsquo;indirizzo, come un nome sul citofono.',
    'Non tutto ciò che si cerca è perduto: qualcosa, semplicemente, non è mai stato scritto.',
    'C&rsquo;è un posto vuoto al tavolo. Siediti lo stesso: da qualche parte si continua a raccontare.',
    'Le parole che cercavi hanno cambiato casa. Succede anche alle parole.',
);
$diary_404_frase = $diary_404_frasi[ array_rand($diary_404_frasi) ];
?>

<section class="error-404 not-found">

    <div class="error-404-hero">
        <span class="error-404-number" aria-hidden="true">404</span>
        <h1 class="error-404-title"><?php esc_html_e('Pagina smarrita', 'diary'); ?></h1>
    </div>

    <p class="error-404-frase"><?php echo wp_kses_post($diary_404_frase); ?></p>

    <div class="error-404-search">
        <?php get_search_form(); ?>
    </div>

    <?php
    $diary_random = new WP_Query(array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => 3,
        'orderby'             => 'rand',
        'ignore_sticky_posts' => true,
    ));

    if ($diary_random->have_posts()) : ?>
        <div class="error-404-suggeriti">
            <h2 class="error-404-sub"><?php esc_html_e('Tre pagine a caso, dal diario', 'diary'); ?></h2>
            <ul class="error-404-lista">
                <?php while ($diary_random->have_posts()) : $diary_random->the_post(); ?>
                    <li>
                        <a href="<?php the_permalink(); ?>">
                            <span class="sugg-title"><?php the_title(); ?></span>
                            <span class="sugg-date"><?php echo esc_html(get_the_date('j F Y')); ?></span>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    <?php endif;
    wp_reset_postdata(); ?>

    <p class="error-404-torna">
        <a class="error-404-home" href="<?php echo esc_url(home_url('/')); ?>">
            &larr; <?php esc_html_e('Torna alla prima pagina', 'diary'); ?>
        </a>
    </p>

</section>

<?php
get_footer();
