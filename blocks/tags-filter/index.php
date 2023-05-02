<?php
function pl_query_vars( $qvars ) {
	$qvars[] = 'filter';
	return $qvars;
}
add_filter( 'query_vars', 'pl_query_vars' );

function pl_handle_filter_query( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {
		// Not a query for an admin page.
		// It's the main query for a front end page of your site.
		if ( is_category() ) {
			// It's the main query for a category archive.
			// Let's change the query for category archives.
            $filter = get_query_var('filter');
            if ($filter) {
                $query->set( 'tag', $filter );
            }

			$cat_sd_prompt = get_category_by_slug('sd-prompt');
        	$cat_chatgpt = get_category_by_slug('chatgpt');

			$content_cat_ids = [
				$cat_sd_prompt->term_id,
				$cat_chatgpt->term_id,
			];
			
			foreach ($query->tax_query->queries as $tax_query) {
				if ($tax_query['taxonomy'] == 'category') {
					if (
						($tax_query['field'] == 'slug' && in_array($tax_query['terms'][0], ['sd-prompt', 'chatgpt']))
						|| in_array($tax_query['terms'][0], $content_cat_ids)
					) {
						$query->set( 'posts_per_page', 20 );
						$query->set( 'orderby', 'ID' );
						$query->set( 'order', 'ASC' );
					}

					break;
				}
			}
		}
	}
}
add_action( 'pre_get_posts', 'pl_handle_filter_query' );