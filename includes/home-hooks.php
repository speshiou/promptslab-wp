<?php

function pl_home_query( $query ) {
	if ( is_home() && $query->is_category ) {

        $cat_sd_prompt = get_category_by_slug('sd-prompt');
        $cat_chatgpt = get_category_by_slug('chatgpt');

        $content_cat_ids = [
            $cat_sd_prompt->term_id,
            $cat_chatgpt->term_id,
        ];

        foreach ($query->tax_query->queries as $tax_query) {
            if ($tax_query['taxonomy'] == 'category' && in_array($tax_query['terms'][0], $content_cat_ids)) {
                $query->set( 'posts_per_page', 6 );
            } else if ($tax_query['taxonomy'] == 'post_tag') {
                // print("<pre>".print_r($query, true)."</pre>");
            }
        }
	}
}

add_action( 'pre_get_posts', 'pl_home_query', 1000 );