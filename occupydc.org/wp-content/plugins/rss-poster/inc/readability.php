<?php
// This is my attempt at porting Arc90's Readability to PHP
// Based on readability.js version 0.4
// Original URL: http://lab.arc90.com/experiments/readability/js/readability.js
// Modified by Jesse: http://www.wprssposter.com
// Arc90's project URL: http://lab.arc90.com/experiments/readability/
// Author: Keyvan Minoukadeh
// Author URL: http://www.keyvan.net
// License: Apache License, Version 2.0
// Requires: PHP5

// Usage: include readability.php in your script and pass your HTML content to grabArticleHtml() for a string, grabArticle() for a DOMElement object

// Alternative usage (uncomment the lines below)
// Usage: call readability.php in your browser passing it the URL of the page you'd like content from:
// readability.php?url=http://medialens.org/alerts/09/090615_the_guardian_climate.php
/*
if (!isset($_GET['url']) || $_GET['url'] == '') {
	die('Please pass a URL to the script. E.g. readability.php?url=bla.com/story.html');
}
$url = $_GET['url'];
$html = file_get_contents($url);
echo grabArticleHtml($html);
*/

// returns XHTML
function grabArticleHtml($html) {
	$contentNode = grabArticle($html);
	
	return $contentNode->ownerDocument->saveXML($contentNode);
}

// returns DOMElement object
function grabArticle($html) {
	// Replace all doubled-up <BR> tags with <P> tags, and remove fonts.
	$html = preg_replace('!<br ?/?>[ \r\n\s]*<br ?/?>!', '</p><p>', $html);
	$html = preg_replace('!</?font[^>]*>!', '', $html);
	$document = new DOMDocument();
	$html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8"); 
	@$document->loadHTML($html);
	
	
	
	$allParagraphs = $document->getElementsByTagName('p');

	
	$topDivCount = 0;
	$topDiv = null;
	$topDivParas;
	
	$articleContent = $document->createElement('div');
	
	
	// Study all the paragraphs and find the chunk that has the best score.
	// A score is determined by things like: Number of <p>'s, commas, special classes, etc.
	for ($j=0; $j < $allParagraphs->length; $j++) {
		$parentNode = $allParagraphs->item($j)->parentNode;

		// Initialize readability data
		if (!$parentNode->hasAttribute('readability'))
		{
			$readability = $document->createAttribute('readability');
			$readability->value = 0;
			$parentNode->appendChild($readability);

			// Look for a special classname
			if($parentNode->hasAttribute('class') && $parentNode->getAttribute('class') != '')
			{

				if (preg_match('/combx|comment|disqus|extra|foot|header|menu|remark|rss|shoutbox|sidebar|sponsor|ad-break|agegate|pagination|pager|popup/',$parentNode->getAttribute('class')) )
				{
	
						$readability->value -= 50;


				} else if(preg_match('/article|body|content|entry|hentry|main|page|pagination|post|text|blog|story/',$parentNode->getAttribute('class'))) 					{

						$readability->value += 25;
				}

			}
			
			
			// Look for a special ID
			if($parentNode->hasAttribute('id') && $parentNode->getAttribute('id') != '')
			{
			
				if (preg_match('/(combx|comment|disqus|extra|foot|header|menu|remark|rss|shoutbox|sidebar|sponsor|ad-break|agegate|pagination|pager|popup)/', $parentNode->getAttribute('id'))) {
					$readability->value -= 50;
				} else if (preg_match('/article|body|content|entry|hentry|main|page|pagination|post|text|blog|story/', $parentNode->getAttribute('id'))) {
					$readability->value += 25;
				}
			}

		} else {
			$readability = $parentNode->getAttributeNode('readability');
		}

		// Add a point for the paragraph found
		if(strlen($allParagraphs->item($j)->textContent) > 10) {
			$readability->value++;
		}

		// Add points for any commas within this paragraph
		$readability->value += substr_count($allParagraphs->item($j)->textContent, ',');
	}

	// Assignment from index for performance. See http://www.peachpit.com/articles/article.aspx?p=31567&seqNum=5 
	$allElements = $document->getElementsByTagName('*');
	$topDiv = null;
	foreach ($allElements as $node) {
		if($node->hasAttribute('readability') && ($topDiv == null || (int)$node->getAttribute('readability') > (int)$topDiv->getAttribute('readability'))) {
			$topDiv = $node;
		}
	}

	if($topDiv == null) {
		$topDiv = $document->createElement('div', 'Sorry, readability was unable to parse this page for content.');
	} else {
		cleanStyles($topDiv);					// Removes all style attributes
		
		$topDiv = killDivs($topDiv);				// Goes in and removes DIV's that have more non <p> stuff than <p> stuff
		$topDiv = killBreaks($topDiv);            // Removes any consecutive <br />'s into just one <br /> 

		// Cleans out junk from the topDiv just in case:
		$topDiv = clean($topDiv, 'form');
		$topDiv = clean($topDiv, 'object');
		$topDiv = clean($topDiv, 'table', 250);

		$topDiv = clean($topDiv, 'h1');
		//$topDiv = clean($topDiv, 'h2');
		$topDiv = clean($topDiv, 'iframe');
		$topDiv = clean($topDiv, 'script');
	}
	
	$articleContent->appendChild($topDiv);
	
	return $articleContent;
}

function cleanStyles($node) {
	$elems = $node->getElementsByTagName('*');
	foreach ($elems as $elem) {
		$elem->removeAttribute('style');
	}
}

function killDivs ($node) {
	$divsList = $node->getElementsByTagName('div');
	$curDivLength = $divsList->length;
	
	// Gather counts for other typical elements embedded within.
	// Traverse backwards so we can remove nodes at the same time without effecting the traversal.
	for ($i=$curDivLength-1; $i >= 0; $i--) {
		$p = $divsList->item($i)->getElementsByTagName('p')->length;
		$img = $divsList->item($i)->getElementsByTagName('img')->length;
		$li = $divsList->item($i)->getElementsByTagName('li')->length;
		$a = $divsList->item($i)->getElementsByTagName('a')->length;
		$embed = $divsList->item($i)->getElementsByTagName('embed')->length;

		// If the number of commas is less than 10 (bad sign) ...
		if (substr_count($divsList->item($i)->textContent, ',') < 10) {
			// And the number of non-paragraph elements is more than paragraphs 
			// or other ominous signs :
			if (  $li > $p || $a > $p || $p == 0 || $embed > 0) {
				if($img >0){}
				else
					$divsList->item($i)->parentNode->removeChild($divsList->item($i));
			}
		}
	}
	return $node;
}

function killBreaks ($node) {
	$pattern = '!(<br\s*/?>(\s|&nbsp;)*){1,}!';
	$xml = $node->ownerDocument->saveXML($node);
	$xml = preg_replace($pattern, '<br />', $xml);
	$f = $node->ownerDocument->createDocumentFragment();
	@$f->appendXML($xml); // @ to prevent PHP warnings
	$node->parentNode->replaceChild($f,$node); 
	return $node;
}

function clean($node, $tag, $minWords=1000000) {
	$targetList = $node->getElementsByTagName($tag);
	$_len = $targetList->length;
	
	for ($y=$_len-1; $y >=0; $y--) {
		$img = $targetList->item($y)->getElementsByTagName('img')->length;
		// If the text content isn't laden with words, remove the child:
		if (substr_count($targetList->item($y)->textContent, ' ') < $minWords) {
			if($img >0){}
			else
				$targetList->item($y)->parentNode->removeChild($targetList->item($y));
		}
	}
	return $node;
}
?>
