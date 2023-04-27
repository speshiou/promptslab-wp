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

			$query->set( 'posts_per_page', 20 );
		}
	}
}
add_action( 'pre_get_posts', 'pl_handle_filter_query' );