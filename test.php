<?php
	$video = $atts['youku'];
	
	// Check for a cached result (stored in the post meta)
	$key_suffix = md5( $video );
	$cachekey = '_oembed_' . $key_suffix;
	$cachekey_time = '_oembed_time_' . $key_suffix;
	$post_ID = the_ID();
	
	/**
	 * Filter the oEmbed TTL value (time to live).
	 *
	 * Default is 2 days.
	 */
	$ttl = apply_filters( 'oembed_ttl', 2 * DAY_IN_SECONDS, $video, false, $post_ID );
	
	$cache = get_post_meta( $post_ID, $cachekey, true );
	$cache_time = get_post_meta( $post_ID, $cachekey_time, true);
	
	if (! $cache_time ) {
		$cache_time = 0;
	}
	
	$cached_recently = ( time() - $cache_time ) < $ttl; // bool
	
	if ($cached_recently) {
		if (! empty( $cache ) ) {
			return $cache;
		}
	}
	
	// If any of the above fails, use oEmbed to get the HTML
	$matches = array();
	preg_match('/v_show\/id_(.*?).html/i', $video, $matches);
	
	if ($matches) {
		// compose the HTML
		$src = 'http://player.youku.com/embed/' . $matches[1];
		$embed_html = '<iframe width=800 height=450 src="' . esc_attr($src) . '" frameorder=0 allowfullscreen></iframe>';
	
		// Cache the result
		update_post_meta( $post_ID, $cachekey, $embed_html );
		update_post_meta( $post_ID, $cachekey_time, time() );
	
		// return the result
		return $embed_html;
	}