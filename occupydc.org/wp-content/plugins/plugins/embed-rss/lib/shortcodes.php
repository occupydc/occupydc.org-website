<?php

/**
 * @author Deanna Schneider
 * @copyright 2008
 * @description Use WordPress Shortcode API for more features
 * @Docs http://codex.wordpress.org/Shortcode_API
 */

class cets_EmbedRSS_shortcodes {
	
	var $count = 1;
	
	// register the new shortcodes
	function cets_EmbedRSS_shortcodes() {
	
		add_shortcode( 'cetsEmbedRSS', array(&$this, 'show_RSS') );
			
	}

	
	function show_RSS( $atts ) {
	
		global $cets_EmbedRSS;
	
		extract(shortcode_atts(array(
			'id' 		=> false,
			'itemcount' => 0,
			'itemcontent' => false,
			'itemauthor' => false,
			'itemdate' => false
		), $atts ));
		
		// if there's no feed passed, just get out of here.
		if (strlen($id) == 0) {
			return;
		}
			
		// do the rss feed
		require_once( ABSPATH . WPINC . '/rss.php' );
		init(); // initialize rss constants	
		$rss = @fetch_rss( $id );
		if ( !isset($rss->items) || 0 == count($rss->items) )
		return false;
		
		// set the item count - 0 means include all
		if ($itemcount == 0) $itemcount = count($rss->items);
		
		$out =  "<ul class='cets_embedRSS' style='list-style:none;'>\n";

		$rss->items = array_slice($rss->items, 0, $itemcount);
		foreach ($rss->items as $item ) {
			
			$title = wp_specialchars($item['title']);
			list($author,$post) = explode( ':', $title, 2 );
			$link = clean_url($item['link']);
			
			$summary = '';
			if ( isset( $item['description'] ) && is_string( $item['description'] ) )
				$desc = $summary = str_replace(array("\n", "\r"), ' ', attribute_escape(strip_tags(html_entity_decode($item['description'], ENT_QUOTES))));
			elseif ( isset( $item['atom_content'] ) && is_string( $item['atom_content'] ) ) {
				$desc = $summary = str_replace(array("\n", "\r"), ' ', attribute_escape(strip_tags(html_entity_decode($item['atom_content'], ENT_QUOTES))));
				}
			if ( $itemcontent ) {
				$desc = '';
				$summary = wp_specialchars( $summary );
				$summary = "<div class='rssSummary' style='margin:5px 0 30px 15px;'>$summary</div>";
			} else {
				$summary = '';
			}

			$date = '';
			if ( $itemdate ) {
				if ( isset($item['pubdate']) )
					$date = $item['pubdate'];
				elseif ( isset($item['published']) )
					$date = $item['published'];

				if ( $date ) {
					if ( $date_stamp = strtotime( $date ) )
						$date = ' <span class="rss-date">' . date_i18n( get_option( 'date_format' ), $date_stamp ) . '</span>';
					else
						$date = '';
				}
			}

			$author = '';
			if ( $itemauthor ) {
				if ( isset($item['dc']['creator']) )
					$author = ' <cite>' . wp_specialchars( strip_tags( $item['dc']['creator'] ) ) . '</cite>';
				elseif ( isset($item['author_name']) )
					$author = ' <cite>' . wp_specialchars( strip_tags( $item['author_name'] ) ) . '</cite>';
			}

			$out .= "<li><a class='post' href='$link'  style='font-size:14px' title='$desc'>$title</a>{$date}{$summary}{$author}</li>";
			
			

	
		
		
		
		$out .= "\n";
	}

	$out .= "</ul>\n<br class='clear' />\n";
		
		
		return $out;
	}

	
}

// let's use it
$cets_EmbedRSSShortcodes = new cets_EmbedRSS_Shortcodes;	

?>