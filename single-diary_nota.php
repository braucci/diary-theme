<?php
/**
 * Pagina singola di una Nota del Planner.
 * Aspetto "post-it" giallo, coerente con il diario delle lezioni.
 *
 * @package Diary
 */
if (!defined('ABSPATH')) exit;

get_header();
?>

<div class="nota-postit-wrap">

<?php while (have_posts()) : the_post();

    // Giorno di riferimento della nota: dal metadato '_diary_nota_data'
    // (con fallback alla data di pubblicazione per eventuali note vecchie).
    $iso_nota = get_post_meta(get_the_ID(), '_diary_nota_data', true);
    $ts_nota  = $iso_nota ? strtotime($iso_nota . ' 12:00:00') : (int) get_the_time('U');

    $n_anno     = (int) date('Y', $ts_nota);
    $n_mese     = (int) date('n', $ts_nota);
    $data_estesa = date_i18n('l j F Y', $ts_nota);

    $planner_url = function_exists('diary_get_planner_url') ? diary_get_planner_url() : home_url('/');
    $torna_url   = add_query_arg(
        array('pl_anno' => $n_anno, 'pl_mese' => $n_mese),
        $planner_url
    );

    // La nota ha un corpo oltre al titolo?
    $ha_corpo = trim(get_the_content()) !== '';
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class('nota-postit'); ?>>
        <span class="nota-postit-pin" aria-hidden="true"></span>

        <p class="nota-postit-data">
            <?php echo esc_html($data_estesa); ?>
        </p>

        <h1 class="nota-postit-title"><?php the_title(); ?></h1>

        <?php if ($ha_corpo) : ?>
            <div class="nota-postit-content">
                <?php the_content(); ?>
            </div>
        <?php endif; ?>
    </article>

    <p class="nota-postit-back">
        <a href="<?php echo esc_url($torna_url); ?>">
            &larr; <?php esc_html_e('Torna al Planner', 'diary'); ?>
        </a>
    </p>

<?php endwhile; ?>

</div><!-- .nota-postit-wrap -->

<?php
get_footer();
