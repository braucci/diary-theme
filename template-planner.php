<?php
/**
 * Template Name: Planner Mensile
 *
 * Vista a calendario: una matrice con i giorni del mese; in ogni
 * cella i titoli dei post pubblicati quel giorno. In alto: selettore
 * mese/anno e frecce per scorrere un mese alla volta. Si apre sul
 * mese/anno corrente.
 *
 * Assegnare questo template a una pagina (es. "Planner").
 * Parametri: ?pl_anno=YYYY&pl_mese=MM
 *
 * @package Diary
 */
if (!defined('ABSPATH')) exit;

get_header();

// --- Mese/anno correnti come default, sovrascrivibili da querystring ---
$oggi_ts   = current_time('timestamp');
$anno_ora  = (int) date('Y', $oggi_ts);
$mese_ora  = (int) date('n', $oggi_ts);
$giorno_ora = (int) date('j', $oggi_ts);

$pl_anno = isset($_GET['pl_anno']) ? absint($_GET['pl_anno']) : $anno_ora;
$pl_mese = isset($_GET['pl_mese']) ? absint($_GET['pl_mese']) : $mese_ora;
if ($pl_mese < 1 || $pl_mese > 12) $pl_mese = $mese_ora;
if ($pl_anno < 1970 || $pl_anno > 2100) $pl_anno = $anno_ora;

$mesi_it = array(
    1 => 'Gennaio', 2 => 'Febbraio', 3 => 'Marzo', 4 => 'Aprile',
    5 => 'Maggio', 6 => 'Giugno', 7 => 'Luglio', 8 => 'Agosto',
    9 => 'Settembre', 10 => 'Ottobre', 11 => 'Novembre', 12 => 'Dicembre',
);

$pagina_url = get_permalink();

// Chi può modificare i contenuti vede i controlli di editing delle note
$puo_editare = current_user_can('edit_posts');

// --- Calcolo mese precedente e successivo ---
$prev_mese = $pl_mese - 1; $prev_anno = $pl_anno;
if ($prev_mese < 1) { $prev_mese = 12; $prev_anno--; }
$next_mese = $pl_mese + 1; $next_anno = $pl_anno;
if ($next_mese > 12) { $next_mese = 1; $next_anno++; }

$url_prev = add_query_arg(array('pl_anno' => $prev_anno, 'pl_mese' => $prev_mese), $pagina_url);
$url_next = add_query_arg(array('pl_anno' => $next_anno, 'pl_mese' => $next_mese), $pagina_url);

// --- Struttura del mese ---
$primo_ts     = mktime(0, 0, 0, $pl_mese, 1, $pl_anno);
$giorni_mese  = (int) date('t', $primo_ts);      // 28..31
$dow_primo    = (int) date('N', $primo_ts);      // 1 (lun) .. 7 (dom)
$offset       = $dow_primo - 1;                   // celle vuote iniziali

// --- Recupera i post del mese, raggruppati per giorno ---
$query_mese = new WP_Query(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'ASC',
    'date_query'     => array(
        array('year' => $pl_anno, 'month' => $pl_mese),
    ),
));

$post_per_giorno = array();
if ($query_mese->have_posts()) {
    while ($query_mese->have_posts()) {
        $query_mese->the_post();
        $g = (int) get_the_date('j');
        $post_per_giorno[$g][] = array(
            'title' => get_the_title(),
            'url'   => get_permalink(),
        );
    }
}
wp_reset_postdata();

// --- Anni disponibili (dal primo post a oggi) per il selettore ---
$primo_post = get_posts(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 1,
    'orderby'        => 'date',
    'order'          => 'ASC',
    'fields'         => 'ids',
));
$anno_min = $anno_ora;
if (!empty($primo_post)) {
    $anno_min = (int) get_the_date('Y', $primo_post[0]);
}
if ($anno_min > $pl_anno) $anno_min = $pl_anno;

$giorni_settimana = array('Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom');
?>

<div class="diary-planner">

    <header class="planner-header">
        <h1 class="planner-title"><?php esc_html_e('Planner', 'diary'); ?></h1>

        <div class="planner-controls">
            <a class="planner-nav planner-prev" href="<?php echo esc_url($url_prev); ?>" aria-label="<?php esc_attr_e('Mese precedente', 'diary'); ?>">&larr;</a>

            <form class="planner-selector" method="get" action="<?php echo esc_url($pagina_url); ?>">
                <select name="pl_mese" aria-label="<?php esc_attr_e('Mese', 'diary'); ?>" onchange="this.form.submit()">
                    <?php foreach ($mesi_it as $num => $nome) : ?>
                        <option value="<?php echo esc_attr($num); ?>" <?php selected($num, $pl_mese); ?>>
                            <?php echo esc_html($nome); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="pl_anno" aria-label="<?php esc_attr_e('Anno', 'diary'); ?>" onchange="this.form.submit()">
                    <?php for ($y = $anno_ora + 1; $y >= $anno_min; $y--) : ?>
                        <option value="<?php echo esc_attr($y); ?>" <?php selected($y, $pl_anno); ?>>
                            <?php echo esc_html($y); ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <noscript><button type="submit" class="diary-button"><?php esc_html_e('Vai', 'diary'); ?></button></noscript>
            </form>

            <a class="planner-nav planner-next" href="<?php echo esc_url($url_next); ?>" aria-label="<?php esc_attr_e('Mese successivo', 'diary'); ?>">&rarr;</a>
        </div>

        <p class="planner-mese-corrente"><?php echo esc_html($mesi_it[$pl_mese] . ' ' . $pl_anno); ?></p>
    </header>

    <div class="planner-grid" role="grid">

        <?php foreach ($giorni_settimana as $gs) : ?>
            <div class="planner-dow" role="columnheader"><?php echo esc_html($gs); ?></div>
        <?php endforeach; ?>

        <?php
        // Celle vuote iniziali
        for ($i = 0; $i < $offset; $i++) {
            echo '<div class="planner-cell planner-cell-empty" aria-hidden="true"></div>';
        }

        // Giorni del mese
        for ($g = 1; $g <= $giorni_mese; $g++) :
            $is_oggi = ($pl_anno === $anno_ora && $pl_mese === $mese_ora && $g === $giorno_ora);
            $ha_post = !empty($post_per_giorno[$g]);
            $data_iso = sprintf('%04d-%02d-%02d', $pl_anno, $pl_mese, $g);
            $nota     = diary_get_planner_note($data_iso);
            $ha_nota  = ('' !== $nota);
            $classi  = 'planner-cell';
            if ($is_oggi) $classi .= ' planner-oggi';
            if ($ha_post) $classi .= ' planner-ha-post';
            if ($ha_nota) $classi .= ' planner-ha-nota';
            ?>
            <div class="<?php echo esc_attr($classi); ?>" role="gridcell"
                 data-date="<?php echo esc_attr($data_iso); ?>"
                 data-note="<?php echo esc_attr($nota); ?>"
                 data-label="<?php echo esc_attr(sprintf('%d %s %d', $g, $mesi_it[$pl_mese], $pl_anno)); ?>">
                <div class="planner-giorno-num">
                    <span class="planner-giorno-cifra"><?php echo esc_html($g); ?></span>
                    <span class="planner-giorno-icone">
                        <?php
                        // Fase lunare calcolata a mezzogiorno locale del giorno
                        $ts_giorno  = mktime(12, 0, 0, $pl_mese, $g, $pl_anno);
                        $luna_p     = diary_moon_phase_fraction($ts_giorno);
                        $luna_nome  = diary_moon_phase_name($luna_p);
                        $luna_ill   = diary_moon_illumination($luna_p);
                        ?>
                        <span class="planner-luna" title="<?php echo esc_attr(sprintf('%s — %d%% illuminata', $luna_nome, $luna_ill)); ?>">
                            <?php echo diary_moon_svg($luna_p, 15); ?>
                        </span>
                        <?php if ($puo_editare) : ?>
                            <button type="button" class="planner-add-note" title="<?php esc_attr_e('Aggiungi/Modifica nota', 'diary'); ?>" aria-label="<?php esc_attr_e('Aggiungi o modifica nota', 'diary'); ?>">+</button>
                        <?php endif; ?>
                    </span>
                </div>

                <ul class="planner-post-list">
                    <?php if ($ha_post) : ?>
                        <?php foreach ($post_per_giorno[$g] as $p) : ?>
                            <li>
                                <a href="<?php echo esc_url($p['url']); ?>" title="<?php echo esc_attr($p['title']); ?>">
                                    <?php echo esc_html($p['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <li class="planner-note-item" <?php echo $ha_nota ? '' : 'hidden'; ?>>
                        <button type="button" class="planner-note-pin">
                            <span class="planner-pin" aria-hidden="true"></span>
                            <span class="planner-note-preview"><?php echo esc_html(wp_trim_words($nota, 6, '…')); ?></span>
                        </button>
                    </li>
                </ul>
            </div>
        <?php endfor;

        // Celle vuote finali per completare l'ultima riga
        $celle_totali = $offset + $giorni_mese;
        $resto = $celle_totali % 7;
        if ($resto !== 0) {
            for ($i = $resto; $i < 7; $i++) {
                echo '<div class="planner-cell planner-cell-empty" aria-hidden="true"></div>';
            }
        }
        ?>
    </div>

    <?php
    $tot_mese = $query_mese->found_posts;
    if ($tot_mese > 0) : ?>
        <p class="planner-riepilogo">
            <?php
            printf(
                esc_html(_n('%1$s articolo pubblicato in %2$s.', '%1$s articoli pubblicati in %2$s.', $tot_mese, 'diary')),
                number_format_i18n($tot_mese),
                esc_html($mesi_it[$pl_mese] . ' ' . $pl_anno)
            );
            ?>
        </p>
    <?php else : ?>
        <p class="planner-riepilogo planner-vuoto">
            <?php esc_html_e('Nessun articolo pubblicato in questo mese.', 'diary'); ?>
        </p>
    <?php endif; ?>

</div><!-- .diary-planner -->

<!-- =========================================================
     SCHERMATA POST-IT — fuori dal calendario, a pagina intera.
     Unica per tutto il planner: viene riempita via JavaScript
     con la nota del giorno selezionato.
     ========================================================= -->
<div id="diary-postit-screen" class="postit-screen" hidden>
    <div class="postit-backdrop"></div>

    <div class="postit-sheet" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Nota del giorno', 'diary'); ?>">
        <button type="button" class="postit-close" aria-label="<?php esc_attr_e('Chiudi', 'diary'); ?>">&times;</button>

        <div class="postit-date"></div>

        <!-- Vista lettura -->
        <div class="postit-view">
            <div class="postit-text"></div>
            <?php if ($puo_editare) : ?>
                <div class="postit-actions">
                    <button type="button" class="postit-btn postit-edit"><?php esc_html_e('Modifica', 'diary'); ?></button>
                    <button type="button" class="postit-btn postit-delete"><?php esc_html_e('Elimina', 'diary'); ?></button>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($puo_editare) : ?>
            <!-- Vista scrittura -->
            <div class="postit-editor" hidden>
                <textarea class="postit-textarea" rows="8" placeholder="<?php esc_attr_e('Scrivi qui la tua nota…', 'diary'); ?>"></textarea>
                <div class="postit-actions">
                    <button type="button" class="postit-btn postit-save"><?php esc_html_e('Salva', 'diary'); ?></button>
                    <button type="button" class="postit-btn postit-cancel"><?php esc_html_e('Annulla', 'diary'); ?></button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
