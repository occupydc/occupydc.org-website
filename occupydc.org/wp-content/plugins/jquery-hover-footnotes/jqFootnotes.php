<?php

/*
Plugin Name: JQuery Hover Footnotes
Plugin URI: http://restoredisrael.org/blog/961/footnote-plugin-test-page/
Description: Lets you add footnotes with qualifiers of you're choosing, then dynamically displays them when you hover over.
Author: Lance Weaver
Version: 1.4
License: GPLv2
*/

 
/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.
see readme.txt for more licence info
based on GNU code from YaFootnotes wordpress plugin. (c) stratosg, http://www.stratos.me, http://www.stratos.me/wp-plugins/yafootnotes/
see http://codex.wordpress.org/Administration_Menus for edit guidance

TODO
-add an option to turn on and off the dynamic hover capabilities. (keep js & css from loading!)
-add option to change the post qualifiers <ref>1<ref> instead of {{1}}?
-add links at top of pages with hide/show footnotes, & footnote popups on/off
*/


#if (is_admin()){  #only run this in the admin area
		
# Add some default options to db if they don't already exist.  NO Ucase! [add_option() only add if nothing already in db]
 add_option("jqfoot_anchor_open", '<sup>[' ); 		#html/symbols to wrap around footnote anchor link.
 add_option("jqfoot_anchor_close", ']</sup>' ); 		#html/symbols to wrap around footnote anchor link.
 add_option("jqfoot_title", 'Footnotes' );	#Title which will appear as a heading above the footnote list in the footer.
 add_option("jqfoot_backimg", '&crarr;' );
 add_option("jqfoot_hidefnlist", FALSE );
 add_option("jqfoot_nohover", FALSE );


# BEGIN ADMIN CONSOLE
if (! function_exists('jqFootnotes_add_options')) {
    function jqFootnotes_add_options() {
        if (function_exists('add_options_page')) {
            add_options_page('jQuery Hover Footnotes', 'jQ Footnotes', 9, basename(__FILE__), 'jQFootnotes_options_subpanel');
        }
    }
}



# Footnote Settings.
# before anchor, after anchor, (two side by side boxes) footnote title, back image.
# later add post backend qualifiers <sup>1</sup> instead of {{1}}, and <ref>text</ref> instead of [[1]]text[[1]]


function jqFootnotes_options_subpanel() {
    if ( isset($_POST['info_update']) ) {
        update_option('jqfoot_anchor_open', $_POST['jqfootnotes_anchor_open']);
        update_option('jqfoot_anchor_close', $_POST['jqfootnotes_anchor_close']);
        update_option('jqfoot_title', $_POST['jqfootnotes_title']);
        update_option('jqfoot_backimg', $_POST['jqfootnotes_backimg']);
        update_option('jqfoot_hidefnlist', $_POST['jqfootnotes_hidefnlist']);
        update_option('jqfoot_nohover', $_POST['jqfootnotes_nohover']);
        ?>
	<div class="updated"><p><strong><?php  _e('Options saved.', 'jQ Footnotes')?></strong></p></div><?php
    } ?>
    <div class="wrap">
    	<div id="icon-options-general" class="icon32"><br /></div>
        <h2>jQuery Hover Footnotes Settings</h2>

		<!-- Donate Blurb -->
		<div style="border: 1px solid #6d6; border-radius: 5px; background-color: #efe; padding: 10px;">
		<table cellpadding="0" cellspacing="0">
    			<tr>
    			    <td valign="middle" align="left" width="110">
        		    <a href="1055/donations/" target="_blank"><img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif"/></a>
    			    </td>
    			    <td valign="top" align="left">
        		    <strong>Any donation is appreciated.</strong> Donations can be done securely through paypal.</a>.<br />
        		    <small>If you find my plugins useful, please consider dontating to the cause, or leaving a nice comment on my donation page. :)</small>
			    <br /><small>Even $1 would make me feel warm, fuzzy and more motivated to further develope free plugins.</small>
    			    </td>
    			</tr>
		</table>
		</div>

            <table class="form-table"> 				
            <form method="post">

						
						<!-- Begin form contents -->

						<h3>Important notes </h3>
								<ul>
									<li>Consult the <a href="http://restoredisrael.org/blog/961/footnote-plugin-test-page/" target="_blank">website</a> for specific instructions on how to create footnotes</li>
									<li>You can leave these all as the default values. If you change an option here and find that your footnotes stop
									functioning, just change it back to the default value. (and cross your fingers)</li>
								</ul>

						<h3>General Settings</h3>

	
								  <tr valign="top">
										<th scope="row">Footnote Ref Marks</th>
										<td>
										 <input type="text" name="jqfootnotes_anchor_open" size="20" value="<?php echo get_option('jqfoot_anchor_open'); ?>" />    &nbsp;&nbsp;
										 <input type="text" name="jqfootnotes_anchor_close" size="20" value="<?php echo get_option('jqfoot_anchor_close'); ?>" />
											<br />The default format for the footnote reference mark is to have them appear in the page as a supercript like this <sup>[1]</sup> or you can have them
											without the brackets <sup>1</sup> or brackets without superscript [1] .  Chose what you will, but beware that some complex qualifiers might break the plugin, so check that things
											work after you update this. (if it breaks, just change it back)
										</td>
								 </tr>
	

								
								 <tr valign="top">
										<th scope="row">Footnote List Heading</th>
										<td>
										 <input type="text" name="jqfootnotes_title" size="50" value="<?php echo get_option('jqfoot_title'); ?>" /> 
											 <br />This is the title of the section at the bottom of the page where the footnotes will be stored.
											 The default is 'Footnotes', but you could change it to 'References' or whatever your fancy is.
										</td>
								</tr>
								

								 <tr valign="top">
										<th scope="row">Back Symbol</th>
										<td>
										 <input type="text" name="jqfootnotes_backimg" size="50" value="<?php echo get_option('jqfoot_backimg'); ?>" /> 
											 <br />Symbol/Link which appears next to footnote which will take the user back up to the text.
											the default is the ANSI character &crarr; (&#38crarr&#059;) but you could also do (&#38uarr&#059;) for &uarr;. Or an image such as
											&lt;img alt="back" src="/footnoteback.png"&gt; or just the word "back".  [Type ansi characters, dont copy and paste from this note!]
											
										</td>
								</tr>	

								 <tr valign="top">
										<th scope="row">Hide Footnotes</th>
										<td>
										 <input type="checkbox" name="jqfootnotes_hidefnlist" <?php if (get_option('jqfoot_hidefnlist')==on) echo 'checked="checked"'; ?> /> 
											 Hide Footnotes in bottom of page. (They will still display in the hover boxes.)
											
										</td>
								</tr>	

								 <tr valign="top">
										<th scope="row">Disable Hovering</th>
										<td>
										 <input type="checkbox" name="jqfootnotes_nohover" <?php if (get_option('jqfoot_nohover')==TRUE) echo 'checked="checked"'; ?> /> 
											 Disables all js files from loading so the jQuery popups do not display. To see footnotes users will have to
											click on a reference mark, which will take them to the footnote list at the bottom of the page.
											(Thus the above option MUST BE UNCHECKED!)	
										</td>
								</tr>	

							</table>
							
		<!-- Begin Wordpress required fields -->
  
	      <?php wp_nonce_field('update-options'); ?>

             <div class="submit">
              <input type="submit" name="info_update" class="button-primary" value="<?php _e('Save Changes') ?>" />
	      </div>
						
	     <!-- End Wordpress required form elements -->
						
        </form>
    </div>

<?php
}

#}   #end is_admin() area


# END ADMIN CONSOLE ################################################################






 // register the js and css scripts.. but not on admin pages

  if (!is_admin() ){
    if (get_option('jqfoot_nohover')!=TRUE){		//if its not checked load the JS/CSS
	//wp_enqueue_script('jquery');	//no need here, loads as dependancy in next line down
	wp_enqueue_script('footenote_js', plugins_url('/footnote-voodoo.js', __FILE__), array('jquery'));
	// it would save load to put these css rules into your template css and comment this next line out
	wp_enqueue_style('footenote_css', plugins_url('/footnote-voodoo.css', __FILE__) );			
    }
  } //end !is_admin()


  //define $data as the page text passed to this function remember it is basically 'the_content' throughout this script

  function jqFootnotes($data){	


	// User Defined Options.   See readme for notes on changing these variables

	$before_anchor = get_option('jqfoot_anchor_open');		//opening the anchor
	$after_anchor = get_option('jqfoot_anchor_close');		//closing the anchor
	$footnotes_title = get_option('jqfoot_title');			//the title to display before the footnotes on the bottom
	$back_image = get_option('jqfoot_backimg');
	$hide_fnlist = (get_option('jqfoot_hidefnlist')) ? 'style="display:none;"' : 'style="display:inherit"' ;	


	$foots = array();

	//finding anchors[#] in the text.  search reg exp matches in $data and stick them into new array of numbers called $foots[]
//	preg_match_all("/\{\{[0-9]*\}\}/", $data, $foots);    
	preg_match_all("/\{\{([^}]+)\}\}/", $data, $foots);   


	if(count($foots[0]) != 0){		//there are footnotes to process!
		$foots_text = array();


		foreach($foots[0] as $foot){	//finding the footnotes  foreach($array as $value-of-array)

			$foot_num = trim($foot, '{}');						//trim braces{} off number and store it.
			$foot_delim = '[['.$foot_num.']]';						//put on brackets for later (so both foots and links will have them)
			$foot_start = strpos($data, $foot_delim) + strlen($foot_delim);	//now we're looking through 'the_content' for the actual footnote text (not just inline link)
			$foot_end = strpos($data, $foot_delim, $foot_start);			//find out how long footnote is
			// removing from the text since v1.1 ????
			$foots_text[] =  array(substr($data, $foot_start, $foot_end - $foot_start), $foot_num);

			// now we'll find and replace actual footnotes in 'the_content'   with substr_replace(original string, replacement string, starting point, [length])
			$data = substr_replace($data, '', $foot_start - strlen($foot_delim), $foot_end - $foot_start + (2*strlen($foot_delim)));

			// INLINE FOOTNOTE REFERENCE MARK LINK: html for superscript footnote link/number
			// replace foot_num with formatted html in $data/'the_content' using str_replace(search_for, replacement string, original string, [# of replacements])
			// orig: $data = str_replace('{{'.$foot_num.'}}', '<a href="#foot_'.$foot_num.'" name="foot_src_'.$foot_num.'">'.$before_anchor.$foot_num.$after_anchor.'</a>', $data);
			$data = str_replace('{{'.$foot_num.'}}', '<a class="fn-ref-mark" href="#footnote-'.$foot_num.'" id="refmark-'.$foot_num.'">'.$before_anchor.$foot_num.$after_anchor.'</a>', $data);

		}

		// FOOTNOTE HEADING: html for the title of footer footnote section
		// by using .= we just append it to the end of 'the_content'
		//old: '.$footnotes_title.'</span><br />';
		$data .= '<br /><br /><div id="footnote-list" '.$hide_fnlist.'><span id=fn-heading>'.$footnotes_title.'</span> &nbsp;&nbsp;&nbsp;('.$back_image.' returns to text)<br /><ol>';  	

		// ACTUAL FOOTNOTE TEXT: html for footnotes in footer area
		foreach($foots_text as $foot_text){
			$data .= '<li id="footnote-'.$foot_text[1].'" class="fn-text">'.trim($foot_text[0]).'<a href="#refmark-'.$foot_text[1].'">'.$back_image.'</a></li>';  
		}
		
		//append the final </ol> and close the </div>
		$data .= '</ol></div>';

	}


	return $data;

  }



  // use wp add_filter function to modify the text with the above function between pulling it from db and writing it to screen
  // add_filter( $tag, $function_to_add, $priority, $accepted_args )

  add_filter('the_content', 'jqFootnotes', 1, 1);




  # Load the Options page into the Admin Console
  add_action('admin_menu', 'jqFootnotes_add_options');



?>