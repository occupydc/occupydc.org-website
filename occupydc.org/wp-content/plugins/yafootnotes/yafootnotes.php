<?php

/*
Plugin Name: YAFootnotes Plugin
Plugin URI: http://www.stratos.me/wp-plugins/yafootnotes/
Description: Yet Another Footnotes (YAFootnotes) is a plugin that gives you the ability to add footnotes to any text you are writing.
Author: stratosg
Version: 1.1
Author URI: http://www.stratos.me
*/

function yafootnotes($data){
	
	$before_anchor = '[';//opening the anchor
	$after_anchor = ']';//closing the anchor
	$footnotes_title = 'FOOTNOTES';//the title to display before the footnotes on the bottom
	
	$foots = array();
	preg_match_all("/\{\{[0-9]*\}\}/", $data, $foots);//finding anchors in the text
	
	if(count($foots[0]) != 0){//there are footnotes to process!
		$foots_text = array();
		
		foreach($foots[0] as $foot){//finding the footnotes in the text
			$foot_num = trim($foot, '{}');
			$foot_delim = '[['.$foot_num.']]';
			$foot_start = strpos($data, $foot_delim) + strlen($foot_delim);
			$foot_end = strpos($data, $foot_delim, $foot_start);
			$foots_text[] =  array(substr($data, $foot_start, $foot_end - $foot_start), $foot_num);
			$data = substr_replace($data, '', $foot_start - strlen($foot_delim), $foot_end - $foot_start + (2*strlen($foot_delim)));//removing from the text since v1.1
			$data = str_replace('{{'.$foot_num.'}}', '<a href="#foot_'.$foot_num.'" name="foot_src_'.$foot_num.'">'.$before_anchor.$foot_num.$after_anchor.'</a>', $data);
		}
		
		$data .= '<br /><br /><span class="yafootnote_head">'.$footnotes_title.'</span><br />';
		
		foreach($foots_text as $foot_text){
			$data .= '<span class="yafootnote_body"><a name="foot_'.$foot_text[1].'">'.$foot_text[1].'.</a>&nbsp;'.$foot_text[0].'<a href="#foot_src_'.$foot_text[1].'">&uarr;</a></span><br />';
		}
	}
	
	return $data;
}

add_filter('the_content', 'yafootnotes', 1, 1);


?>