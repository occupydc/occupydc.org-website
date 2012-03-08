// Docu : http://wiki.moxiecode.com/index.php/TinyMCE:Create_plugin/3.x#Creating_your_own_plugins

(function() {
	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('cets_EmbedRSS');
	
	tinymce.create('tinymce.plugins.cets_EmbedRSS', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register  button
			ed.addButton('cets_EmbedRSS', {
				title : 'cets_EmbedRSS.desc',
				image : url + '/cets_EmbedRSS.gif',
				onclick : function() {
					cets_RSSButtonClick('cetsEmbedRSS');
				}
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('cets_EmbedRSS', n.nodeName == 'IMG');
			});
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
					longname  : 'cets_EmbedRSS',
					author 	  : 'Deanna Schneider',
					authorurl : 'http://deannaschneider.wordpress.com',
					infourl   : 'http://deannaschneider.wordpress.com',
					version   : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('cets_EmbedRSS', tinymce.plugins.cets_EmbedRSS);
})();


