<?php
/**
 * Form di ricerca.
 * @package Diary
 */
if (!defined('ABSPATH')) exit;
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="screen-reader-text" for="diary-search"><?php esc_html_e('Cerca:', 'diary'); ?></label>
    <input type="search" id="diary-search" class="search-field" placeholder="<?php esc_attr_e('Cerca&hellip;', 'diary'); ?>" value="<?php echo get_search_query(); ?>" name="s">
    <button type="submit" class="diary-button"><?php esc_html_e('Cerca', 'diary'); ?></button>
</form>
