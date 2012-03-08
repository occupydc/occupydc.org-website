<?php
/*
Plugin Name: DirtySuds - Embed PDF
Plugin URI: http://dirtysuds.com
Description: Embed a PDF using Google Docs Viewer
Author: Dirty Suds
Version: 1.04
Author URI: http://blog.dirtysuds.com
License: GPL2

Updates:
1.04 20111125 - Automatically enable auto-embeds on activation
1.03 20110321 - Automatically enable auto-embeds on activation
1.02 20110315 - Added support for `gdoc` shortcode
1.01 20110303 - Added support for class and ID attributes
1.00 20110224 - First Version

  Copyright 2011 Pat Hawks  (email : pat@pathawks.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


register_activation_hook( __FILE__, 'dirtysuds_embed_pdf_enable_embeds' );
wp_embed_register_handler( 'pdf', '#(^(http|wpurl)\:\/\/.+\.pdf$)#i', 'dirtysuds_embed_pdf' );
add_shortcode( 'gdoc', 'dirtysuds_embed_pdf' );

function dirtysuds_embed_pdf_enable_embeds() {
	update_option('embed_autourls',1);
}

function dirtysuds_embed_pdf( $matches, $atts, $url, $rawattr=null ) {
	extract( shortcode_atts( array(
		'height' => get_option('embed_size_h'),
		'width' => get_option('embed_size_w'),
		'border' => '0',
		'style' => '',
		'title' => '',
		'class' => 'pdf',
		'id' => '',
	), $atts ) );
	
	if (!strstr($url,'http://') && strstr($atts,'http://')) {
		$url = $atts;
		extract( shortcode_atts( array(
			'height' => get_option('embed_size_h'),
			'width' => get_option('embed_size_w'),
			'border' => '0',
			'style' => '',
			'title' => '',
			'class' => 'pdf',
			'id' => '',
		), $matches ) );
	}

	$embed = '<iframe src="http://docs.google.com/viewer?url='.urlencode($url).'&amp;embedded=true" class="'.$class.'"';
	if ($id) {
		$embed .= ' id="'.$id.'"';
	}
	
	$embed .= ' frameborder="'.$border.'"';
	if ($border != '0') {
		$border .= 'px';
	}
	
	if ($style) {
		$embed .= ' style="height:'.$height.'px;width:'.$width.'px;border:'.$border.';'.$style.'"';
	} else {
		$embed .= ' style="height:'.$height.'px;width:'.$width.'px;border:'.$border.'"';
	}
	if ($title) {
		$embed .= ' title="'.$title.'"';
	}
	$embed .= ' width="'.$width.'" height="'.$height.'"></iframe>';
	
	$embed  = '<![if !IE]>'.$embed.'<![endif]>';

	$embed .= '<!--[if IE]>'.
		'<object width="'.$width.'" height="'.$height.'" type="application/pdf" data="'.$url.'" class="'.$class.' ie"';

	if ($id) {
		$embed .= ' id="'.$id.'"';
	}
	
	$embed .= '>'.
		'<div style="width:'.$width.';height:'.$height.';text-align:center;background:#fff;color:#000;margin:0;border:0;padding:0">Unable to display PDF<br /><a href="'.$url.'">Click here to download</a></div>'.
		'</object>'.
		'<![endif]-->';	

	return apply_filters( 'embed_pdf', $embed, $matches, $attr, $url, $rawattr  );
}

function dirtysuds_embed_pdf_rate($links,$file) {
		if (plugin_basename(__FILE__) == $file) {
			$links[] = '<a href="http://wordpress.org/extend/plugins/dirtysuds-embed-pdf/">Rate this plugin</a>';
		}
	return $links;
}
add_filter('plugin_row_meta', 'dirtysuds_embed_pdf_rate',10,2);