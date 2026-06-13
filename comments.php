<?php
/**
 * Template dei commenti.
 * @package Diary
 */
if (!defined('ABSPATH')) exit;
if (post_password_required()) return;
?>
<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $diary_count = get_comments_number();
            if ('1' === $diary_count) {
                esc_html_e('Un commento', 'diary');
            } else {
                printf(
                    esc_html(_n('%s commento', '%s commenti', $diary_count, 'diary')),
                    number_format_i18n($diary_count)
                );
            }
            ?>
        </h2>
        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size'=> 48,
            ));
            ?>
        </ol>
        <?php
        the_comments_pagination(array(
            'prev_text' => __('&larr; Precedenti', 'diary'),
            'next_text' => __('Successivi &rarr;', 'diary'),
        ));
        ?>
    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments"><?php esc_html_e('I commenti sono chiusi.', 'diary'); ?></p>
    <?php endif; ?>

    <?php
    comment_form(array(
        'title_reply'        => __('Lascia un commento', 'diary'),
        'label_submit'       => __('Invia commento', 'diary'),
        'class_submit'       => 'submit diary-button',
        'comment_notes_before' => '',
    ));
    ?>
</div>
