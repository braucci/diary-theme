<?php
/**
 * Footer del tema
 * @package Diary
 */
if (!defined('ABSPATH')) exit;

$diary_has_footer_widgets = is_active_sidebar('footer-1') || is_active_sidebar('footer-2');
?>
    </main><!-- #main -->

    <footer id="colophon" class="site-footer">

        <?php
        if ($diary_has_footer_widgets) :
            $diary_active_footers = 0;
            if (is_active_sidebar('footer-1')) $diary_active_footers++;
            if (is_active_sidebar('footer-2')) $diary_active_footers++;
            $diary_cols_class = 'cols-' . $diary_active_footers;
        ?>
            <div class="footer-widgets <?php echo esc_attr($diary_cols_class); ?>">
                <?php if (is_active_sidebar('footer-1')) : ?>
                    <div class="footer-col"><?php dynamic_sidebar('footer-1'); ?></div>
                <?php endif; ?>
                <?php if (is_active_sidebar('footer-2')) : ?>
                    <div class="footer-col"><?php dynamic_sidebar('footer-2'); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (has_nav_menu('footer')) :
            wp_nav_menu(array(
                'theme_location' => 'footer',
                'menu_class'     => 'footer-menu',
                'container'      => 'nav',
                'depth'          => 1,
                'fallback_cb'    => false,
            ));
        endif; ?>

        <div class="copyright">
            &copy; <?php echo esc_html(date_i18n('Y')); ?>
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>.
            <?php esc_html_e('Tutti i diritti riservati.', 'diary'); ?>
        </div>

        <div class="customized-by">
            <?php
            printf(
                esc_html__('Tema %1$s di %2$s', 'diary'),
                '<strong>Diary</strong>',
                '<a href="https://www.raucci.net/" rel="author">B. Raucci</a>'
            );
            ?>
        </div>

        <?php diary_footer_quote(); ?>

    </footer><!-- #colophon -->

</div><!-- #page -->

<button id="backToTop" aria-label="<?php esc_attr_e('Torna su', 'diary'); ?>">&uarr;</button>

<?php wp_footer(); ?>
</body>
</html>
