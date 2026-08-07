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
    define('DIARY_VERSION', '1.6.3');
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
 * 14) Note pubbliche del Planner ("diario delle lezioni")
 *     - Tipo di contenuto "Nota" con pagina pubblica dedicata.
 *     - Inserimento ed eliminazione rapidi dal calendario,
 *       consentiti SOLO all'autore autorizzato.
 * ============================================================ */

/**
 * Chi può gestire le note.
 * Default: chi può modificare gli articoli (autore/editore/amministratore).
 * Restringibile via filtro, es. add_filter('diary_nota_capability', fn() => 'manage_options');
 */
function diary_nota_puo_gestire() {
    return is_user_logged_in()
        && current_user_can(apply_filters('diary_nota_capability', 'edit_posts'));
}

/**
 * URL della pagina che usa il template del Planner.
 * Serve, ad esempio, al link "Torna al Planner" dalla pagina di una nota.
 * Il risultato è messo in cache per la durata della richiesta.
 */
function diary_get_planner_url() {
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $pagine = get_posts(array(
        'post_type'      => 'page',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'template-planner.php',
        'no_found_rows'  => true,
    ));
    $cache = !empty($pagine) ? get_permalink($pagine[0]) : home_url('/');
    return $cache;
}

/* Registrazione del tipo di contenuto "Nota" */
function diary_registra_cpt_nota() {

    $labels = array(
        'name'               => __('Note', 'diary'),
        'singular_name'      => __('Nota', 'diary'),
        'menu_name'          => __('Note Planner', 'diary'),
        'add_new'            => __('Aggiungi nota', 'diary'),
        'add_new_item'       => __('Aggiungi nuova nota', 'diary'),
        'edit_item'          => __('Modifica nota', 'diary'),
        'new_item'           => __('Nuova nota', 'diary'),
        'view_item'          => __('Vedi nota', 'diary'),
        'search_items'       => __('Cerca note', 'diary'),
        'not_found'          => __('Nessuna nota trovata', 'diary'),
        'not_found_in_trash' => __('Nessuna nota nel cestino', 'diary'),
        'all_items'          => __('Tutte le note', 'diary'),
    );

    register_post_type('diary_nota', array(
        'labels'             => $labels,
        'public'             => true,           // pagina pubblica dedicata
        'has_archive'        => false,          // l'archivio è il Planner stesso
        'publicly_queryable' => true,
        'show_in_rest'       => true,           // editor a blocchi per estendere il testo
        'menu_icon'          => 'dashicons-calendar-alt',
        'menu_position'      => 5,
        'supports'           => array('title', 'editor', 'thumbnail'),
        'rewrite'            => array('slug' => 'nota', 'with_front' => false),
    ));
}
add_action('init', 'diary_registra_cpt_nota');

/**
 * Rigenerazione una-tantum delle regole di rewrite dopo il deploy,
 * così le pagine /nota/... non danno 404 senza dover risalvare i permalink.
 */
function diary_nota_flush_una_tantum() {
    if (get_option('diary_nota_rewrite_flushed') !== '1') {
        flush_rewrite_rules();
        update_option('diary_nota_rewrite_flushed', '1');
    }
}
add_action('init', 'diary_nota_flush_una_tantum', 20);

/**
 * Inserimento rapido di una nota dal calendario.
 * Schema Post/Redirect/Get: dopo il POST si reindirizza, per evitare
 * il reinvio del modulo con il refresh della pagina.
 */
function diary_gestisci_invio_nota() {

    if (empty($_POST['diary_nota_submit'])) {
        return;
    }

    // 1) Solo l'autore autorizzato
    if (!diary_nota_puo_gestire()) {
        return;
    }

    // 2) Verifica del token anti-CSRF
    if (!isset($_POST['diary_nota_nonce'])
        || !wp_verify_nonce($_POST['diary_nota_nonce'], 'diary_nota_add')) {
        return;
    }

    $testo  = isset($_POST['diary_nota_testo'])  ? sanitize_text_field(wp_unslash($_POST['diary_nota_testo'])) : '';
    $anno   = isset($_POST['diary_nota_anno'])   ? absint($_POST['diary_nota_anno'])   : 0;
    $mese   = isset($_POST['diary_nota_mese'])   ? absint($_POST['diary_nota_mese'])   : 0;
    $giorno = isset($_POST['diary_nota_giorno']) ? absint($_POST['diary_nota_giorno']) : 0;

    // 3) Testo non vuoto e data valida (checkdate: mese, giorno, anno)
    if ($testo !== '' && checkdate($mese, $giorno, $anno)) {

        // IMPORTANTE: la nota viene pubblicata SUBITO (data di creazione = adesso).
        // Il giorno del calendario a cui si riferisce è salvato come metadato
        // '_diary_nota_data' (YYYY-MM-DD). In questo modo lo stato resta sempre
        // 'publish': se usassimo post_date sul giorno scelto, una data futura
        // farebbe passare WordPress allo stato 'future' (programmato) e la nota
        // scomparirebbe dalla vista — impedendo la pianificazione in avanti.
        $giorno_iso = sprintf('%04d-%02d-%02d', $anno, $mese, $giorno);

        $nuovo_id = wp_insert_post(array(
            'post_type'   => 'diary_nota',
            'post_status' => 'publish',
            'post_title'  => $testo,
        ));

        if ($nuovo_id && !is_wp_error($nuovo_id)) {
            update_post_meta($nuovo_id, '_diary_nota_data', $giorno_iso);
        }
    }

    // 4) Ritorno alla stessa vista mese/anno
    $base = isset($_POST['diary_nota_planner_url'])
        ? esc_url_raw(wp_unslash($_POST['diary_nota_planner_url']))
        : home_url('/');
    $redirect = add_query_arg(array('pl_anno' => $anno, 'pl_mese' => $mese), $base);
    wp_safe_redirect($redirect);
    exit;
}
add_action('template_redirect', 'diary_gestisci_invio_nota');

/**
 * Eliminazione di una nota dal calendario.
 * La nota viene spostata nel cestino (recuperabile), non cancellata
 * definitivamente: una svista si annulla in un clic dalla Bacheca.
 */
function diary_gestisci_elimina_nota() {

    if (empty($_GET['diary_del_nota'])) {
        return;
    }

    $id = absint($_GET['diary_del_nota']);
    if (!$id) {
        return;
    }

    if (!diary_nota_puo_gestire()) {
        return;
    }

    if (!isset($_GET['_wpnonce'])
        || !wp_verify_nonce($_GET['_wpnonce'], 'diary_del_nota_' . $id)) {
        return;
    }

    $post = get_post($id);
    if ($post && $post->post_type === 'diary_nota') {
        wp_trash_post($id);
    }

    $ref = wp_get_referer();
    wp_safe_redirect($ref ? $ref : home_url('/'));
    exit;
}
add_action('template_redirect', 'diary_gestisci_elimina_nota');

/**
 * Modifica rapida del testo di una nota dal calendario (PRG).
 * Cambia solo il titolo (la riga breve); il corpo esteso si modifica
 * dalla Bacheca. Il giorno di riferimento (metadato) resta invariato.
 */
function diary_gestisci_modifica_nota() {

    if (empty($_POST['diary_nota_edit_submit'])) {
        return;
    }

    if (!diary_nota_puo_gestire()) {
        return;
    }

    if (!isset($_POST['diary_nota_nonce'])
        || !wp_verify_nonce($_POST['diary_nota_nonce'], 'diary_nota_edit')) {
        return;
    }

    $id    = isset($_POST['diary_nota_id'])    ? absint($_POST['diary_nota_id']) : 0;
    $testo = isset($_POST['diary_nota_testo']) ? sanitize_text_field(wp_unslash($_POST['diary_nota_testo'])) : '';

    if ($id && $testo !== '') {
        $post = get_post($id);
        if ($post && $post->post_type === 'diary_nota') {
            wp_update_post(array(
                'ID'         => $id,
                'post_title' => $testo,
            ));
        }
    }

    $anno = isset($_POST['diary_nota_anno']) ? absint($_POST['diary_nota_anno']) : 0;
    $mese = isset($_POST['diary_nota_mese']) ? absint($_POST['diary_nota_mese']) : 0;
    $base = isset($_POST['diary_nota_planner_url'])
        ? esc_url_raw(wp_unslash($_POST['diary_nota_planner_url']))
        : home_url('/');
    wp_safe_redirect(add_query_arg(array('pl_anno' => $anno, 'pl_mese' => $mese), $base));
    exit;
}
add_action('template_redirect', 'diary_gestisci_modifica_nota');


/* ============================================================
 * 13) Reindirizzamento voce di menu
 *     "Scienze e tecnologie delle costruzioni aeronautiche"
 *     verso la web-app esterna su GitHub Pages.
 * ============================================================ */
function diary_reindirizza_voce_menu($items, $args) {

    // URL della pagina interna da sostituire (identificata per slug)
    $slug_da_sostituire = 'scienze-e-tecnologie-delle-costruzioni-aeronautiche';

    // Nuova destinazione esterna
    $url_esterno = 'https://braucci.github.io/SCSI/';

    foreach ($items as $item) {

        // Corrispondenza per URL (slug della pagina interna)
        $per_url = (false !== strpos($item->url, $slug_da_sostituire));

        // Corrispondenza di riserva per titolo, nel caso lo slug cambi
        $per_titolo = (false !== stripos($item->title, 'Scienze e tecnologie delle costruzioni aeronautiche'));

        if ($per_url || $per_titolo) {
            $item->url = $url_esterno;
            // La web-app si apre in una nuova scheda in sicurezza
            $item->target = '_blank';
            $item->xfn    = 'noopener noreferrer';
        }
    }

    return $items;
}
add_filter('wp_nav_menu_objects', 'diary_reindirizza_voce_menu', 10, 2);
