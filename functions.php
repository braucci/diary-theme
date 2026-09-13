<?php
/**
 * Diary — funzioni del tema
 *
 * @package Diary
 * @author  Biagio Raucci
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('DIARY_VERSION')) {
    define('DIARY_VERSION', '1.12.0');
}

/* ============================================================
 * 1) Setup del tema
 * ============================================================ */
function diary_setup() {

    load_theme_textdomain('diary', get_template_directory() . '/languages');

    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 80,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    add_theme_support('html5', array(
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
    ));

    add_theme_support('post-formats', array('aside', 'gallery', 'quote', 'image', 'video'));

    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_editor_style('assets/css/editor-style.css');

    register_nav_menus(array(
        'primary' => __('Menu principale', 'diary'),
        'footer'  => __('Menu footer', 'diary'),
    ));

    add_image_size('diary-featured', 1200, 675, true);
}
add_action('after_setup_theme', 'diary_setup');


/* ============================================================
 * 2) Larghezza contenuto (per embed)
 * ============================================================ */
function diary_content_width() {
    $GLOBALS['content_width'] = apply_filters('diary_content_width', 820);
}
add_action('after_setup_theme', 'diary_content_width', 0);


/* ============================================================
 * 3) Enqueue stili e script
 * ============================================================ */
function diary_scripts() {

    // Preconnect Google Fonts
    // (gestito via filtro più sotto per aggiungere attributi)

    // Google Fonts: EB Garamond + Space Grotesk + JetBrains Mono
    wp_enqueue_style(
        'diary-fonts',
        'https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,600&family=Space+Grotesk:wght@400;500;700&family=JetBrains+Mono:wght@400;500&display=swap',
        array(),
        null
    );

    // Foglio di stile principale
    wp_enqueue_style(
        'diary-style',
        get_stylesheet_uri(),
        array('diary-fonts'),
        DIARY_VERSION
    );

    // Script di navigazione e back-to-top
    wp_enqueue_script(
        'diary-scripts',
        get_template_directory_uri() . '/assets/js/diary.js',
        array(),
        DIARY_VERSION,
        true
    );

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'diary_scripts');

/* Preconnect ai server dei font (performance) */
function diary_resource_hints($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = array('href' => 'https://fonts.gstatic.com', 'crossorigin');
        $urls[] = 'https://fonts.googleapis.com';
    }
    return $urls;
}
add_filter('wp_resource_hints', 'diary_resource_hints', 10, 2);


/* ============================================================
 * 4) Widget areas
 * ============================================================ */
function diary_widgets_init() {
    register_sidebar(array(
        'name'          => __('Footer 1', 'diary'),
        'id'            => 'footer-1',
        'description'   => __('Area widget nel footer.', 'diary'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    register_sidebar(array(
        'name'          => __('Footer 2', 'diary'),
        'id'            => 'footer-2',
        'description'   => __('Area widget nel footer.', 'diary'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'diary_widgets_init');


/* ============================================================
 * 5) Lunghezza e "more" dell'estratto
 * ============================================================ */
function diary_excerpt_length($length) {
    return 55;
}
add_filter('excerpt_length', 'diary_excerpt_length', 999);

function diary_excerpt_more($more) {
    return '&hellip;';
}
add_filter('excerpt_more', 'diary_excerpt_more');


/* ============================================================
 * 6) Meta del post (data + autore) — funzione riusabile
 * ============================================================ */
if (!function_exists('diary_post_meta')) {
    function diary_post_meta() {
        $date   = get_the_date();
        $author = get_the_author();
        printf(
            '<div class="entry-meta"><time class="published" datetime="%1$s">%2$s</time><span class="sep">&middot;</span><span class="author">%3$s</span></div>',
            esc_attr(get_the_date('c')),
            esc_html($date),
            esc_html($author)
        );
    }
}

/* ------------------------------------------------------------
 * 6-bis) Prima immagine trovata nel corpo del post.
 *        Serve come "immagine di apertura" in home quando il
 *        post non ha un'immagine in evidenza impostata.
 *        Restituisce l'URL, oppure stringa vuota.
 * ------------------------------------------------------------ */
if (!function_exists('diary_first_content_image')) {
    function diary_first_content_image($post = null) {
        $post = get_post($post);
        if (!$post) {
            return '';
        }
        // Cerca il primo <img src="..."> nel contenuto renderizzato
        $content = $post->post_content;
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches)) {
            return esc_url_raw($matches[1]);
        }
        return '';
    }
}


/* ============================================================
 * 7) Paginazione articolo singolo (multipagina)
 * ============================================================ */
if (!function_exists('diary_link_pages')) {
    function diary_link_pages() {
        wp_link_pages(array(
            'before' => '<div class="page-links">' . __('Pagine:', 'diary'),
            'after'  => '</div>',
        ));
    }
}


/* ============================================================
 * 8) Open Graph + Twitter Cards
 * ============================================================ */
function diary_social_meta() {
    if (is_admin() || is_404()) return;

    if (is_singular()) {
        global $post;
        $title = get_the_title($post);
        $url   = get_permalink($post);
        $type  = is_page() ? 'website' : 'article';

        if (has_excerpt($post)) {
            $desc = get_the_excerpt($post);
        } else {
            $raw  = wp_strip_all_tags(strip_shortcodes($post->post_content), true);
            $desc = mb_substr($raw, 0, 160) . (mb_strlen($raw) > 160 ? '…' : '');
        }

        $image = '';
        if (has_post_thumbnail($post)) {
            $img = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'large');
            if ($img) $image = $img[0];
        }
    } else {
        $title = get_bloginfo('name');
        $desc  = get_bloginfo('description');
        $url   = home_url('/');
        $type  = 'website';
        $image = has_site_icon() ? get_site_icon_url(512) : '';
    }

    echo "\n<!-- Diary: Open Graph -->\n";
    printf('<meta property="og:site_name" content="%s">' . "\n", esc_attr(get_bloginfo('name')));
    printf('<meta property="og:locale" content="%s">' . "\n", esc_attr(get_locale()));
    printf('<meta property="og:type" content="%s">' . "\n", esc_attr($type));
    printf('<meta property="og:title" content="%s">' . "\n", esc_attr($title));
    if ($desc) printf('<meta property="og:description" content="%s">' . "\n", esc_attr($desc));
    printf('<meta property="og:url" content="%s">' . "\n", esc_url($url));
    if ($image) printf('<meta property="og:image" content="%s">' . "\n", esc_url($image));
    printf('<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary');
    printf('<meta name="twitter:title" content="%s">' . "\n", esc_attr($title));
    if ($desc)  printf('<meta name="twitter:description" content="%s">' . "\n", esc_attr($desc));
    if ($image) printf('<meta name="twitter:image" content="%s">' . "\n", esc_url($image));
    echo "<!-- /Diary: Open Graph -->\n\n";
}
add_action('wp_head', 'diary_social_meta', 5);


/* ============================================================
 * 9) Citazione personalizzata nel footer (filtrabile)
 * ============================================================ */
function diary_footer_quote() {
    $default = sprintf(
        '<div class="squallor-quote"><p><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></p></div>',
        esc_url('https://braucci.github.io/squallor/'),
        esc_html__('Si stava meglio quando c\'erano gli Squallor', 'diary')
    );
    echo apply_filters('diary_footer_quote', $default);
}


/* ============================================================
 * 10) Fallback menu se nessun menu assegnato
 * ============================================================ */
function diary_primary_menu_fallback() {
    echo '<ul id="primary-menu" class="primary-menu-list">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'diary') . '</a></li>';
    wp_list_pages(array('title_li' => '', 'depth' => 1));
    echo '</ul>';
}


/* ============================================================
 * 11) Pingback header
 * ============================================================ */
function diary_pingback_header() {
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">' . "\n", esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'diary_pingback_header');


/* ============================================================
 * 12) Opzioni del tema nel Customizer
 *     "Diary: Opzioni Blog" → scelta visualizzazione home
 * ============================================================ */
function diary_customize_register($wp_customize) {

    // Sezione dedicata
    $wp_customize->add_section('diary_blog_options', array(
        'title'    => __('Diary: Opzioni Blog', 'diary'),
        'priority' => 30,
    ));

    // Impostazione: modalità di visualizzazione in home/archivi
    $wp_customize->add_setting('diary_home_display', array(
        'default'           => 'excerpt',
        'sanitize_callback' => 'diary_sanitize_home_display',
        'transport'         => 'refresh',
    ));

    $wp_customize->add_control('diary_home_display', array(
        'label'       => __('In home e archivi mostra', 'diary'),
        'description' => __('Scegli come appaiono i post negli elenchi. Puoi cambiare e vedere subito l\'anteprima.', 'diary'),
        'section'     => 'diary_blog_options',
        'type'        => 'radio',
        'choices'     => array(
            'excerpt' => __('Solo estratto (poche righe + "Continua a leggere")', 'diary'),
            'full'    => __('Testo completo di ogni articolo', 'diary'),
            'auto'    => __('Estratto se scritto a mano, altrimenti testo completo', 'diary'),
        ),
    ));
}
add_action('customize_register', 'diary_customize_register');

/* Sanitizzazione del valore */
function diary_sanitize_home_display($value) {
    $valid = array('excerpt', 'full', 'auto');
    return in_array($value, $valid, true) ? $value : 'excerpt';
}


/* ============================================================
 * 13) PLANNER — note personali (post-it)
 *     Salvate in un'opzione del database: sopravvivono a
 *     qualsiasi aggiornamento del tema. Pubbliche in lettura,
 *     modificabili solo da chi può editare i contenuti.
 * ============================================================ */

/* Legge tutte le note (array 'YYYY-MM-DD' => testo) */
function diary_get_planner_notes() {
    $notes = get_option('diary_planner_notes', array());
    return is_array($notes) ? $notes : array();
}

/* Legge la nota di un singolo giorno */
function diary_get_planner_note($date) {
    $notes = diary_get_planner_notes();
    return isset($notes[$date]) ? $notes[$date] : '';
}

/* Carica JS + dati solo sulla pagina che usa il template Planner */
function diary_planner_assets() {
    if (is_page_template('template-planner.php')) {
        wp_enqueue_script(
            'diary-planner',
            get_template_directory_uri() . '/assets/js/planner.js',
            array(),
            DIARY_VERSION,
            true
        );
        wp_localize_script('diary-planner', 'DiaryPlanner', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('diary_planner_notes'),
            'canEdit' => current_user_can('edit_posts') ? 1 : 0,
        ));
    }
}
add_action('wp_enqueue_scripts', 'diary_planner_assets');

/* AJAX: salva o aggiorna una nota (solo utenti autorizzati) */
function diary_ajax_save_note() {
    check_ajax_referer('diary_planner_notes', 'nonce');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('msg' => 'non autorizzato'), 403);
    }
    $date = isset($_POST['date']) ? sanitize_text_field(wp_unslash($_POST['date'])) : '';
    $note = isset($_POST['note']) ? sanitize_textarea_field(wp_unslash($_POST['note'])) : '';

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        wp_send_json_error(array('msg' => 'data non valida'));
    }

    $notes = diary_get_planner_notes();
    if ('' === trim($note)) {
        unset($notes[$date]);            // testo vuoto = cancella
        $saved = '';
    } else {
        $notes[$date] = $note;
        $saved = $note;
    }
    update_option('diary_planner_notes', $notes, false);

    wp_send_json_success(array(
        'date'    => $date,
        'note'    => $saved,
        'preview' => wp_trim_words($saved, 6, '…'),
    ));
}
add_action('wp_ajax_diary_save_note', 'diary_ajax_save_note');

/* AJAX: elimina una nota (solo utenti autorizzati) */
function diary_ajax_delete_note() {
    check_ajax_referer('diary_planner_notes', 'nonce');
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('msg' => 'non autorizzato'), 403);
    }
    $date = isset($_POST['date']) ? sanitize_text_field(wp_unslash($_POST['date'])) : '';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        wp_send_json_error(array('msg' => 'data non valida'));
    }
    $notes = diary_get_planner_notes();
    unset($notes[$date]);
    update_option('diary_planner_notes', $notes, false);
    wp_send_json_success(array('date' => $date));
}
add_action('wp_ajax_diary_delete_note', 'diary_ajax_delete_note');


/* ============================================================
 * 14) CONTATORE VISITE
 *     Conteggio e lettura via AJAX (admin-ajax.php non è
 *     mai in cache), così i numeri restano corretti anche
 *     con Aruba HiSpeed Cache attivo.
 *     Dati salvati nel database: sopravvivono agli aggiornamenti.
 * ============================================================ */

/* Restituisce array( 'total' => int, 'today' => int ) */
function diary_get_visit_counts() {
    $total = (int) get_option('diary_visits_total', 0);
    $day   = get_option('diary_visits_day', array());
    $oggi  = current_time('Y-m-d');

    $today = 0;
    if (is_array($day) && isset($day['date'], $day['count']) && $day['date'] === $oggi) {
        $today = (int) $day['count'];
    }
    return array('total' => $total, 'today' => $today);
}

/* Incrementa i contatori (una volta per richiesta) */
function diary_register_visit() {
    $total = (int) get_option('diary_visits_total', 0);
    $total++;
    update_option('diary_visits_total', $total, false);

    $oggi = current_time('Y-m-d');
    $day  = get_option('diary_visits_day', array());

    if (is_array($day) && isset($day['date']) && $day['date'] === $oggi) {
        $count = (int) $day['count'] + 1;
    } else {
        $count = 1;   // nuovo giorno: si riparte da 1
    }
    update_option('diary_visits_day', array('date' => $oggi, 'count' => $count), false);

    return array('total' => $total, 'today' => $count);
}

/* AJAX: registra la visita e restituisce i totali.
   Accessibile anche ai non loggati (wp_ajax_nopriv). */
function diary_ajax_hit() {
    // Non contiamo le visite di chi amministra il sito
    if (current_user_can('edit_posts')) {
        wp_send_json_success(diary_get_visit_counts());
    }
    wp_send_json_success(diary_register_visit());
}
add_action('wp_ajax_diary_hit', 'diary_ajax_hit');
add_action('wp_ajax_nopriv_diary_hit', 'diary_ajax_hit');

/* AJAX: sola lettura (senza incrementare) */
function diary_ajax_counts() {
    wp_send_json_success(diary_get_visit_counts());
}
add_action('wp_ajax_diary_counts', 'diary_ajax_counts');
add_action('wp_ajax_nopriv_diary_counts', 'diary_ajax_counts');

/* Carica lo script del contatore su tutto il sito */
function diary_counter_assets() {
    wp_enqueue_script(
        'diary-counter',
        get_template_directory_uri() . '/assets/js/counter.js',
        array(),
        DIARY_VERSION,
        true
    );
    wp_localize_script('diary-counter', 'DiaryCounter', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
    ));
}
add_action('wp_enqueue_scripts', 'diary_counter_assets');


/* ============================================================
 * 15) FASI LUNARI per il Planner
 *     Calcolo astronomico basato sul mese sinodico medio
 *     (29,530588853 giorni) a partire da un novilunio noto:
 *     6 gennaio 2000, 18:14 UTC.
 *     Precisione più che sufficiente per un planner (±1 giorno
 *     nei casi peggiori, dovuta all'eccentricità dell'orbita).
 * ============================================================ */

/* Frazione della lunazione: 0 = novilunio, 0.5 = plenilunio */
function diary_moon_phase_fraction($timestamp) {
    $epoca_novilunio = 947182440;      // 2000-01-06 18:14 UTC
    $mese_sinodico   = 29.530588853;   // giorni

    $giorni = ($timestamp - $epoca_novilunio) / 86400;
    $p = fmod($giorni / $mese_sinodico, 1);
    if ($p < 0) {
        $p += 1;
    }
    return $p;
}

/* Nome italiano della fase */
function diary_moon_phase_name($p) {
    if ($p < 0.0334 || $p >= 0.9666) return __('Luna nuova', 'diary');
    if ($p < 0.2166) return __('Luna crescente', 'diary');
    if ($p < 0.2834) return __('Primo quarto', 'diary');
    if ($p < 0.4666) return __('Gibbosa crescente', 'diary');
    if ($p < 0.5334) return __('Luna piena', 'diary');
    if ($p < 0.7166) return __('Gibbosa calante', 'diary');
    if ($p < 0.7834) return __('Ultimo quarto', 'diary');
    return __('Luna calante', 'diary');
}

/* Percentuale illuminata (0-100) */
function diary_moon_illumination($p) {
    return (int) round((1 - cos(2 * M_PI * $p)) / 2 * 100);
}

/**
 * SVG dell'icona lunare.
 * Il disco illuminato è delimitato da due archi: il bordo esterno
 * (semicirconferenza) e il terminatore, un'ellisse il cui semiasse
 * orizzontale vale r·cos(2πp) — nullo ai quarti (terminatore
 * rettilineo), massimo ai sizigi.
 */
function diary_moon_svg($p, $size = 15) {
    $r  = 14;
    $rx = $r * cos(2 * M_PI * $p);
    $arx = abs($rx);
    $waxing = ($p < 0.5);

    if ($waxing) {
        $outer = 1;
        $inner = ($rx > 0) ? 0 : 1;
    } else {
        $outer = 0;
        $inner = ($rx > 0) ? 1 : 0;
    }

    $path = sprintf(
        'M0,%1$d A%2$d,%2$d 0 0,%3$d 0,%4$d A%5$.2f,%2$d 0 0,%6$d 0,%1$d Z',
        -$r, $r, $outer, $r, $arx, $inner
    );

    return sprintf(
        '<svg class="moon-icon" viewBox="-16 -16 32 32" width="%1$d" height="%1$d" aria-hidden="true" focusable="false">'
        . '<circle cx="0" cy="0" r="%2$d" class="moon-dark"/>'
        . '<path d="%3$s" class="moon-lit"/>'
        . '</svg>',
        (int) $size,
        $r,
        esc_attr($path)
    );
}
