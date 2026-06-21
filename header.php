<?php
/**
 * Header del tema
 * @package Diary
 */
if (!defined('ABSPATH')) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Vai al contenuto', 'diary'); ?></a>

<div id="page" class="site">

    <header id="masthead" class="site-header">
        <?php if (has_custom_logo()) : ?>
            <div class="site-logo"><?php the_custom_logo(); ?></div>
        <?php endif; ?>

        <?php if (is_front_page() && is_home()) : ?>
            <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
        <?php else : ?>
            <p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
        <?php endif; ?>

        <?php
        $diary_description = get_bloginfo('description', 'display');
        if ($diary_description || is_customize_preview()) : ?>
            <p class="site-description"><?php echo $diary_description; ?></p>
        <?php endif; ?>
    </header>

    <nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e('Menu principale', 'diary'); ?>">
        <input type="checkbox" id="diary-menu-checkbox" class="menu-toggle-checkbox" aria-hidden="true">
        <label for="diary-menu-checkbox" class="menu-toggle">
            <span class="menu-toggle-icon" aria-hidden="true">&#9776;</span>
            <span class="menu-toggle-text"><?php esc_html_e('Menu', 'diary'); ?></span>
        </label>
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'menu_id'        => 'primary-menu',
            'menu_class'     => 'primary-menu-list',
            'container'      => false,
            'fallback_cb'    => 'diary_primary_menu_fallback',
            'depth'          => 2,
        ));
        ?>
    </nav>

    <main id="main" class="site-main">
