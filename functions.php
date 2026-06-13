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
    define('DIARY_VERSION', '1.0.3');
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
    echo '<div class="menu-wrap"><ul>';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'diary') . '</a></li>';
    wp_list_pages(array('title_li' => '', 'depth' => 1));
    echo '</ul></div>';
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
