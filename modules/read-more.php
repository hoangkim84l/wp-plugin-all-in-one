<?php
if (!defined('ABSPATH')) exit;

/**
 * Filter the default "Read More" link for the_content() and the_excerpt()
 */

// Filter the_content() read more link
add_filter('the_content_more_link', function ($more_link, $more_link_text) {
    if (!is_admin()) {
        return str_replace($more_link_text, __('Read More', 'my-woo-aio'), $more_link);
    }
    return $more_link;
}, 10, 2);

// Filter the_excerpt() read more text
add_filter('excerpt_more', function ($more) {
    if (!is_admin()) {
        $link = sprintf('<a href="%1$s" class="more-link">%2$s</a>',
            esc_url(get_permalink(get_the_ID())),
            __('Read More', 'my-woo-aio')
        );
        return ' &hellip; ' . $link;
    }
    return $more;
});
