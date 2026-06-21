<?php
/**
 * Template Name: Archivio Mensile
 *
 * Pagina di archivio che mostra:
 *  - un indice di tutti i mesi/anni con post (vista predefinita);
 *  - per il mese selezionato, i post in una matrice di schede
 *    con titolo e poche righe di anteprima.
 *
 * Assegnare questo template a una pagina chiamata "Archivio".
 * Il mese si seleziona via parametri ?am_anno=YYYY&am_mese=MM.
 *
 * @package Diary
 */
if (!defined('ABSPATH')) exit;

get_header();

// Leggi e valida i parametri del mese selezionato
$am_anno = isset($_GET['am_anno']) ? absint($_GET['am_anno']) : 0;
$am_mese = isset($_GET['am_mese']) ? absint($_GET['am_mese']) : 0;
if ($am_mese < 1 || $am_mese > 12) {
    $am_mese = 0;
}

$mesi_it = array(
    1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
    5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
    9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre',
);

$pagina_archivio_url = get_permalink();
?>

<div class="diary-archivio">

<?php if ($am_anno && $am_mese) :

    // ---------- VISTA: MESE SELEZIONATO (matrice) ----------
    $query_mese = new WP_Query(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'date_query'     => array(
            array('year' => $am_anno, 'month' => $am_mese),
        ),
    ));
    ?>

    <header class="archivio-header">
        <p class="archivio-breadcrumb">
            <a href="<?php echo esc_url($pagina_archivio_url); ?>">&larr; <?php esc_html_e('Tutti i mesi', 'diary'); ?></a>
        </p>
        <h1 class="archivio-title">
            <?php echo esc_html($mesi_it[$am_mese] . ' ' . $am_anno); ?>
        </h1>
        <p class="archivio-count">
            <?php
            $n = $query_mese->found_posts;
            printf(
                esc_html(_n('%s articolo', '%s articoli', $n, 'diary')),
                number_format_i18n($n)
            );
            ?>
        </p>
    </header>

    <?php if ($query_mese->have_posts()) : ?>
        <div class="archivio-matrice">
            <?php while ($query_mese->have_posts()) : $query_mese->the_post(); ?>
                <article class="archivio-card">
                    <h2 class="archivio-card-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    <p class="archivio-card-date">
                        <?php echo esc_html(get_the_date('j F Y')); ?>
                    </p>
                    <div class="archivio-card-excerpt">
                        <?php
                        $estratto = has_excerpt()
                            ? get_the_excerpt()
                            : wp_trim_words(wp_strip_all_tags(get_the_content()), 28, '&hellip;');
                        echo esc_html($estratto);
                        ?>
                    </div>
                    <a class="archivio-card-link" href="<?php the_permalink(); ?>">
                        <?php esc_html_e('Leggi', 'diary'); ?> &rarr;
                    </a>
                </article>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p class="archivio-vuoto"><?php esc_html_e('Nessun articolo in questo mese.', 'diary'); ?></p>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>

<?php else :

    // ---------- VISTA: INDICE DI TUTTI I MESI ----------

    // Eventuale contenuto introduttivo della pagina
    while (have_posts()) : the_post();
        if (trim(get_the_content()) !== '') : ?>
            <div class="archivio-intro entry-content">
                <?php the_content(); ?>
            </div>
        <?php endif;
    endwhile;
    ?>

    <header class="archivio-header">
        <h1 class="archivio-title"><?php esc_html_e('Archivio', 'diary'); ?></h1>
        <p class="archivio-sottotitolo"><?php esc_html_e('Seleziona un mese per vedere tutti gli articoli pubblicati.', 'diary'); ?></p>
    </header>

    <?php
    // Recupera tutti i mesi con post, raggruppati per anno
    global $wpdb;
    $risultati = $wpdb->get_results("
        SELECT YEAR(post_date) AS anno, MONTH(post_date) AS mese, COUNT(ID) AS totale
        FROM {$wpdb->posts}
        WHERE post_type = 'post' AND post_status = 'publish'
        GROUP BY anno, mese
        ORDER BY anno DESC, mese DESC
    ");

    if ($risultati) :
        $per_anno = array();
        foreach ($risultati as $r) {
            $per_anno[(int)$r->anno][] = $r;
        }
        foreach ($per_anno as $anno => $mesi_anno) : ?>
            <section class="archivio-anno">
                <h2 class="archivio-anno-title"><?php echo esc_html($anno); ?></h2>
                <div class="archivio-mesi-grid">
                    <?php foreach ($mesi_anno as $m) :
                        $url = add_query_arg(
                            array('am_anno' => (int)$m->anno, 'am_mese' => (int)$m->mese),
                            $pagina_archivio_url
                        );
                        ?>
                        <a class="archivio-mese-cell" href="<?php echo esc_url($url); ?>">
                            <span class="archivio-mese-nome"><?php echo esc_html($mesi_it[(int)$m->mese]); ?></span>
                            <span class="archivio-mese-conteggio"><?php echo esc_html($m->totale); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach;
    else : ?>
        <p class="archivio-vuoto"><?php esc_html_e('Nessun articolo pubblicato.', 'diary'); ?></p>
    <?php endif; ?>

<?php endif; ?>

</div><!-- .diary-archivio -->

<?php
get_footer();
