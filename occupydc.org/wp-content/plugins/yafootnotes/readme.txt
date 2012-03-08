=== YAFootnotes ===
Tags: footnotes
Requires at least: 2.1
Tested up to: 2.8
Stable tag: trunk

Yet Another Footnotes (YAFootnotes) is a plugin that gives you the ability to add footnotes to any text you are writing.

== Description ==

Writing a post with footnotes has never been easier and cleaner before. All you need to do is anchor a footnote at any point and then write it on the end of the paragraph or wherever it fits you.

**Usage**

To anchor a footnote use the {{FOOTNOTE_NUMBER}}. Then to write the body of it you need to embrace it with [[FOOTNOTE_NUMBER]]. Here is an example:

------ document starts here -------
Lorem ipsum dolor sit amet, con sectetuer adipiscing elit, sed diam
tempor incidunt ut labore et dolore magna ali quarn erat volupat.{{1}}
venian, quis nostrud exerci tation ullamcorper{{2}} suscipit laboris
commodo consequat. Duis autem vel eum irure dolore

[[1]]This is the first footnote.[[1]]
[[2]]This is the second footnote.[[2]]

This is the next paragraph. Lorem ipsum dolor sit amet, con sectetuer
adipiscing elit, sed diam nonnumy nibh euismod tempor incidunt ut
labore et dolore magna ali quarn erat volupat. Ut wisi enim ad minim.
------ document ends here ---------

When the document is parsed, the footnotes that are between paragraphs will be removed from where they are and placed at the end of the document. The {{1}} will be replaced with an anchor link to the footnote. The footnotes will be a list having and anchor link back to the place the text has it. Here is what roughly the above text will look like after parsing:

------ document starts here -------
Lorem ipsum dolor sit amet, con sectetuer adipiscing elit, sed diam
tempor incidunt ut labore et dolore magna ali quarn erat volupat.[1]
venian, quis nostrud exerci tation ullamcorper[2] suscipit laboris
commodo consequat. Duis autem vel eum irure dolore

This is the next paragraph. Lorem ipsum dolor sit amet, con sectetuer
adipiscing elit, sed diam nonnumy nibh euismod tempor incidunt ut
labore et dolore magna ali quarn erat volupat. Ut wisi enim ad minim.

FOOTNOTES
1. This is the first footnote.
2. This is the second footnote.
------ document ends here ---------

The up arrows and the [1] and [2] are the links. There are a few options that you can tweak, for now it's in the code. Please refer to the plugin's homepage at: http://www.stratos.me/wp-plugins/yafootnotes


== Requirements ==

None what so ever...

== Installation ==

Activate like any plugin. Just copy the yafootnote.php file in the plugins directory and activate. Then start writing!

== Credits ==

Mr. Mike Nichols from http://anxietypanichealth.com/ inspired me, motivated me and helped me debug.

== Contact ==

Suggestion, fixes, rants, congratulations, gifts etc to stratosg@stratosector.net
Also visit the plugin's page at http://www.stratos.me/wp-plugins/yafootnotes/

== Changelog ==

= 1.1 =
* Added support for formated footnotes, not just plain text.
