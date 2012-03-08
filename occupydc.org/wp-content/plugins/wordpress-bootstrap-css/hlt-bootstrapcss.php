<?php

/* 
Plugin Name: Wordpress Twitter Bootstrap CSS
Plugin URI: http://www.hostliketoast.com/wordpress-resource-centre/wordpress-plugins/
Description: Allows you to install Twitter Bootstrap CSS and Javascript files for your site, before all others. 
Version: 2.0.1c
Author: Host Like Toast
Author URI: http://www.hostliketoast.com/
*/

/**
 * Copyright (c) 2011 Host Like Toast <helpdesk@hostliketoast.com>
 * All rights reserved.
 * 
 * "Wordpress Twitter Bootstrap CSS" (formerly "Wordpress Bootstrap CSS") is
 * distributed under the GNU General Public License, Version 2,
 * June 1991. Copyright (C) 1989, 1991 Free Software Foundation, Inc., 51 Franklin
 * St, Fifth Floor, Boston, MA 02110, USA
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */
include_once( dirname(__FILE__).'/hlt-bootstrap-shortcodes.php' );

define( 'DS', DIRECTORY_SEPARATOR );

//global $wp_version;
//global $doc_db_version;
//global $wpdb;

//$exit_msg = "The Wordpress installation must be version 3 or above.";
//if (version_compare($wp_version, "3.0", "<")) {
 //   exit($exit_msg);
//}

class HLT_BootstrapCss extends HLT_Plugin {
	
	const InputPrefix = 'hlt_bootstrap_';
	const OptionPrefix = 'hlt_bootstrapcss_';
	
	// possibly configurable in the UI, we'll determine this as new releases occur.
	const TwitterVersion = '2.0.1';
	const TwitterVersionLegacy = '1.4.0';
	
	public function __construct() {
		parent::__construct();

		self::$VERSION		= '2.0.1c';
		
		self::$PLUGIN_NAME	= basename(__FILE__);
		self::$PLUGIN_PATH	= plugin_basename( dirname(__FILE__) );
		self::$PLUGIN_DIR	= WP_PLUGIN_DIR.DS.self::$PLUGIN_PATH.DS;
		self::$PLUGIN_URL	= WP_PLUGIN_URL.'/'.self::$PLUGIN_PATH.'/';
		
		if ( is_admin() ) {
			$oInstall = new HLT_BootstrapCss_Install();
			$oUninstall = new HLT_BootstrapCss_Uninstall();
		}
	}

	public function rewriteHead( $insContents ) {
		$sOption = self::getOption( 'option' );
		$fHotlink = ( self::getOption( 'hotlink' ) == 'Y' );

		$fCustomCss = ( self::getOption( 'customcss' ) == 'Y' );

		if ( !in_array( $sOption, array( 'yahoo-reset', 'normalize', 'twitter', 'twitter-legacy' ) ) ) {
			return $insContents;
		}

		$aLocalCss = array(
			'twitter'			=> self::$PLUGIN_URL.'resources/bootstrap-'.self::TwitterVersion.'/css/bootstrap.min.css',
			'twitter-legacy'	=> self::$PLUGIN_URL.'resources/bootstrap-'.self::TwitterVersionLegacy.'/css/bootstrap.min.css',
			'yahoo-reset'		=> self::$PLUGIN_URL.'resources/misc/css/yahoo-2.9.0.min.css',
			'normalize'			=> self::$PLUGIN_URL.'resources/misc/css/normalize.css'
		);

		/*
		$aHotlinkCss = array(
			'twitter'			=> 'http://twitter.github.com/bootstrap/'.self::TwitterVersion.'/bootstrap.min.css',
			'twitter-legacy'	=> 'http://twitter.github.com/bootstrap/'.self::TwitterVersionLegacy.'/bootstrap.min.css',
			'yahoo-reset'		=> 'http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css',
			'normalize'			=> 'https://raw.github.com/necolas/normalize.css/master/normalize.css'
		);
 removed
		if ( $fHotlink ) {
			$sCssLink = $aHotlinkCss[$sOption];
		}
		else {
			$sCssLink = $aLocalCss[$sOption];
		}
		*/
		
		$sCssLink = $aLocalCss[$sOption];
		
		$sRegExp = "/(<\bhead\b([^>]*)>)/i";
		$sReplace = '${1}';
		$sReplace .= "\n<!-- This site uses WordPress Twitter Bootstrap CSS plugin v".self::$VERSION." from http://worpit.com/ -->";
		$sReplace .= "\n".'<link rel="stylesheet" type="text/css" href="'.$sCssLink.'">';

		echo $fCustomCss .' '. $sCustomCssUrl;
		if ( $fCustomCss ) {
			$sCustomCssUrl = self::getOption( 'customcss_url' );
			$sReplace .= "\n".'<link rel="stylesheet" type="text/css" href="'.$sCustomCssUrl.'">';
		}
		$sReplace .= "\n<!-- / WordPress Twitter Bootstrap CSS Plugin from Host Like Toast. -->";
		
		return preg_replace( $sRegExp, $sReplace, $insContents );
	}

	public function onWpInit() {
		parent::onWpInit();
		
		if ( !is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) ) {
			ob_start( array( &$this, 'onOutputBufferFlush' ) );
		}

		add_action( 'wp_enqueue_scripts', array( &$this, 'onWpPrintStyles' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'onWpEnqueueScripts' ) );
		// if shortcodes are enabled!
		
		$sBootstrapOption = self::getOption( 'option' );
		if ( preg_match( "/^twitter/", $sBootstrapOption ) && self::getOption( 'useshortcodes' ) == 'Y' ) {
			$sVersion = ($sBootstrapOption == 'twitter') ? '2' : '1';
			$oShortCodes = new HLT_BootstrapShortcodes( $sVersion );
		}
	}
	
	public function onWpAdminInit() {
		parent::onWpAdminInit();
	}
	
	public function onWpPluginsLoaded() {
		parent::onWpPluginsLoaded();
		
		if ( is_admin() ) {
			$this->handlePluginUpgrade();
			$this->handleSubmit();
		}
	}
	
	public function onWpAdminMenu() {
		parent::onWpAdminMenu();
		
		add_submenu_page( self::ParentMenuId, $this->getSubmenuPageTitle( 'Bootstrap CSS' ), 'Bootstrap CSS', self::ParentPermissions, $this->getSubmenuId( 'bootstrap-css' ), array( &$this, 'onDisplayPlugin' ) );

		$this->fixSubmenu();
	}
	
	public function handlePluginUpgrade() {

		if ( self::getOption( 'upgraded1to2' ) != 'Y' ) {
			
			if ( self::getOption( 'option' ) == 'twitter' ) {
				self::updateOption( 'option', 'twitter-legacy' );
			}
			if ( self::getOption( 'alerts_js' ) == 'Y' ) {
				self::addOption( 'alert_js', 'Y' );
			}
			self::deleteOption( 'alerts_js'  );

			if ( self::getOption( 'tabs_js' ) == 'Y' ) {
				self::addOption( 'tab_js', 'Y' );
			}
			self::deleteOption( 'tabs_js' );

			if ( self::getOption( 'twipsy_js' ) == 'Y' ) {
				self::addOption( 'tooltip_js', 'Y' );
			}
			self::deleteOption( 'twipsy_js' );

			self::addOption( 'upgraded1to2', 'Y' );
			self::updateOption( 'upgraded1to2', 'Y' );
		}
	}
	
	public function onDisplayPlugin() {
	
		$aData = array(
			'plugin_url'			=> self::$PLUGIN_URL,
			'option'				=> self::getOption( 'option' ),
			'hotlink'				=> self::getOption( 'hotlink' ),

			'option_alert_js'		=> self::getOption( 'alert_js' ),
			'option_button_js'		=> self::getOption( 'button_js' ),
			'option_dropdown_js'	=> self::getOption( 'dropdown_js' ),
			'option_modal_js'		=> self::getOption( 'modal_js' ),
			'option_tooltip_js'		=> self::getOption( 'tooltip_js' ),
			'option_popover_js'		=> self::getOption( 'popover_js' ),
			'option_scrollspy_js'	=> self::getOption( 'scrollspy_js' ),
			'option_tab_js'			=> self::getOption( 'tab_js' ),
			'option_transition_js'	=> self::getOption( 'transition_js' ),	// Bootstrap v2.0+
			'option_collapse_js'	=> self::getOption( 'collapse_js' ),	// Bootstrap v2.0+
			'option_carousel_js'	=> self::getOption( 'carousel_js' ),	// Bootstrap v2.0+
			'option_typeahead_js'	=> self::getOption( 'typeahead_js' ),	// Bootstrap v2.0+
			'option_all_js'			=> self::getOption( 'all_js' ),			// Bootstrap v2.0+

			'option_js_head'		=> self::getOption( 'js_head' ),
			'option_useshortcodes'	=> self::getOption( 'useshortcodes' ),
			'option_prettify'		=> self::getOption( 'prettify' ),

			'option_customcss'		=> self::getOption( 'customcss' ),
			'text_customcss_url'	=> self::getOption( 'customcss_url' ),
		
			'form_action'			=> 'admin.php?page=hlt-directory-bootstrap-css'
		);
		$this->display( 'bootstrapcss_index', $aData );
	}
	
	public function onOutputBufferFlush( $insContent ) {
		return $this->rewriteHead( $insContent );
	}
	
	protected function handleSubmit() {
		if ( isset( $_POST['hlt_bootstrap_option'] ) ) {
			if ( self::updateOption( 'option', $_POST['hlt_bootstrap_option'] ) === false ) {
				// TODO: need to say it hasn't worked
			}
			$sCustomUrl = $_POST[self::InputPrefix.'text_customcss_url'];
			$fCustomCss = ($this->getAnswerFromPost( 'option_customcss' ) === 'Y');
			$fIncludeTooltip = ($this->getAnswerFromPost( 'option_popover_js' ) === 'Y' || $this->getAnswerFromPost( 'option_tooltip_js' ) === 'Y' );
			
			self::updateOption( 'hotlink',			$this->getAnswerFromPost( 'hotlink' ) );
		
			self::updateOption( 'alert_js',			$this->getAnswerFromPost( 'option_alert_js' ) );
			self::updateOption( 'button_js',		$this->getAnswerFromPost( 'option_button_js' ) );
			self::updateOption( 'dropdown_js',		$this->getAnswerFromPost( 'option_dropdown_js' ) );
			self::updateOption( 'modal_js',			$this->getAnswerFromPost( 'option_modal_js' ) );
			self::updateOption( 'tooltip_js',		$fIncludeTooltip? 'Y': 'N' );
			self::updateOption( 'popover_js',		$this->getAnswerFromPost( 'option_popover_js' ) );
			self::updateOption( 'scrollspy_js',		$this->getAnswerFromPost( 'option_scrollspy_js' ) );
			self::updateOption( 'tab_js',			$this->getAnswerFromPost( 'option_tab_js' ) );
			self::updateOption( 'transition_js',	$this->getAnswerFromPost( 'option_transition_js' ) );	// Bootstrap v2.0+
			self::updateOption( 'collapse_js',		$this->getAnswerFromPost( 'option_collapse_js' ) );		// Bootstrap v2.0+
			self::updateOption( 'carousel_js',		$this->getAnswerFromPost( 'option_carousel_js' ) );		// Bootstrap v2.0+
			self::updateOption( 'typeahead_js',		$this->getAnswerFromPost( 'option_typeahead_js' ) );	// Bootstrap v2.0+
			self::updateOption( 'all_js',			$this->getAnswerFromPost( 'option_all_js' ) );	// Bootstrap v2.0+
			// self::updateOption( '_js',			$this->getAnswerFromPost( 'option_' ) );

			self::updateOption( 'js_head',			$this->getAnswerFromPost( 'option_js_head' ) );
			self::updateOption( 'useshortcodes',	$this->getAnswerFromPost( 'option_useshortcodes' ) );
			self::updateOption( 'prettify',			$this->getAnswerFromPost( 'option_prettify' ) );

			self::updateOption( 'customcss',		$this->getAnswerFromPost( 'option_customcss' ) );

			if ( $fCustomCss && !empty( $sCustomUrl ) ) {
				if ( $this->checkUrlValid( $sCustomUrl ) ) {
					self::updateOption( 'customcss_url', $_POST[self::InputPrefix.'text_customcss_url'] );
				}
				else {
					self::updateOption( 'customcss_url', '' );
				}
			}
			
			// Flush W3 Total Cache (compatible up to version 0.9.2.4)
			if ( class_exists( 'W3_Plugin_TotalCacheAdmin' ) ) {
				$oW3TotalCache =& w3_instance('W3_Plugin_TotalCacheAdmin');
				$oW3TotalCache->flush_all();
			}
		}
	}

	public function onWpPrintStyles() {
		if ( self::getOption( 'prettify' ) == 'Y' ) {
			$sUrlPrefix = self::$PLUGIN_URL.'resources/misc/js/google-code-prettify/';
			wp_register_style( 'prettify_style', $sUrlPrefix.'prettify.css' );
			wp_enqueue_style( 'prettify_style' );
		}
	}

	public function onWpEnqueueScripts() {
		
		$bInFooter = (self::getOption( 'js_head' ) == 'Y'? false : true);
		$sBootstrapOption = self::getOption( 'option' );
		
		if ( self::getOption( 'prettify' ) == 'Y' ) {
			$sUrlPrefix = self::$PLUGIN_URL.'js/google-code-prettify/';
			wp_register_script( 'prettify_script', $sUrlPrefix.'prettify.js', '', self::$VERSION, $bInFooter );
			wp_enqueue_script( 'prettify_script' );
		}
		

		if ( preg_match ( "/^twitter/", $sBootstrapOption ) ) {
			
			$sTwitterVersion = self::TwitterVersionLegacy;
			
			$aBootstrapJsOptions = array (
				'alert'			=> self::getOption( 'alert_js' ),
				'button'		=> self::getOption( 'button_js' ),
				'dropdown'		=> self::getOption( 'dropdown_js' ),
				'modal'			=> self::getOption( 'modal_js' ),
				'tooltip'		=> self::getOption( 'tooltip_js' ),
				'popover'		=> self::getOption( 'popover_js' ),
				'scrollspy'		=> self::getOption( 'scrollspy_js' ),
				'tab'			=> self::getOption( 'tab_js' )
				//'name of TBS lib with .js'		=> self::getOption( 'carousel_js' )
			);

			if ( $sBootstrapOption == 'twitter' ) {
				$aBootstrapJsOptions[ 'transition' ]	= self::getOption( 'transition_js' );	// Bootstrap v2.0+
				$aBootstrapJsOptions[ 'collapse' ]		= self::getOption( 'collapse_js' );		// Bootstrap v2.0+
				$aBootstrapJsOptions[ 'carousel' ]		= self::getOption( 'carousel_js' );		// Bootstrap v2.0+
				$aBootstrapJsOptions[ 'typeahed' ]		= self::getOption( 'typeahed_js' );		// Bootstrap v2.0+
				
				$sTwitterVersion = self::TwitterVersion;
			}
		
			$sUrlPrefix = self::$PLUGIN_URL.'resources/bootstrap-'.$sTwitterVersion.'/js/bootstrap';
			/* removed - no longer supported
			if ( self::getOption( 'hotlink' ) == 'Y' && ($sTwitterVersion == self::TwitterVersionLegacy) ) {
				$sUrlPrefix = 'http://twitter.github.com/bootstrap/'.$sTwitterVersion.'/bootstrap-';
			}
			*/

			if ( self::getOption( 'all_js' ) == 'Y' && $sTwitterVersion == self::TwitterVersion ) {
				wp_register_script( 'bootstrap-all-min', $sUrlPrefix.'.min.js', '', self::$VERSION, $bInFooter );
				wp_enqueue_script( 'bootstrap-all-min' );
			} else {
				foreach ( $aBootstrapJsOptions as $sJsLib => $sDisplay ) {
					if ( $sDisplay == 'Y' ) {
						$sUrl = $sUrlPrefix.'-'.$sJsLib.'.js';
						wp_register_script( 'bootstrap'.$sJsLib, $sUrl, '', self::$VERSION, $bInFooter );
						wp_enqueue_script( 'bootstrap'.$sJsLib );
					}
				}
			}
			
			//Include the JS to "activate" all the tooltips & popovers.
			//From Version 2.0.1a this was moved "inline" using hooks to wp_footer
			/*
			if ( self::getOption( 'tooltip_js' ) == 'Y'
					|| self::getOption( 'popover_js' ) == 'Y'
					|| (self::getOption( 'all_js' ) == 'Y' && $sTwitterVersion == self::TwitterVersion) ) {

				$sScriptVersion = self::$PLUGIN_URL.'resources/misc/js/plugin/auto_tooltip_popover.js';
				if ($sBootstrapOption == 'twitter-legacy') {
					$sScriptVersion = self::$PLUGIN_URL.'resources/misc/js/plugin/auto_tooltip_popover-tbs140.js';
				}
				//wp_register_script( 'plugin_auto_tooltip_popover', $sScriptVersion, '', self::$VERSION, $bInFooter );
				//wp_enqueue_script( 'plugin_auto_tooltip_popover' );
			}
			*/
		}
	}//onWpEnqueueScripts
	
	protected function checkUrlValid( $insUrl ) {
		$oCurl = curl_init();
		curl_setopt( $oCurl, CURLOPT_URL, $insUrl );
		curl_setopt( $oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $oCurl, CURLOPT_CONNECTTIMEOUT, 10 );
		
		$sContent = curl_exec( $oCurl );
		$sHttpCode = curl_getinfo( $oCurl, CURLINFO_HTTP_CODE );
		curl_close( $oCurl );
		
		return ( intval( $sHttpCode ) === 200 );
	}
	
	protected function getAnswerFromPost( $insKey, $insPrefix = null ) {
		if ( is_null( $insPrefix ) ) {
			$insKey = self::InputPrefix.$insKey;
		}
		return ( isset( $_POST[$insKey] )? 'Y': 'N' );
	}
	
	/**
	 * Not currently used, but could be useful once we work out what way the JS should be included.
	 * @param $insHandle	For example: 'prettify/prettify.css'
	 */
	protected function isRegistered( $insHandle ) {
		return (
			wp_script_is( $insHandle, 'registered' ) ||
			wp_script_is( $insHandle, 'queue' ) ||
			wp_script_is( $insHandle, 'done' ) ||
			wp_script_is( $insHandle, 'to_do' )
		);	
	}
	
	static public function getOption( $insKey ) {
		return get_option( self::OptionPrefix.$insKey );
	}
	
	static public function addOption( $insKey, $insValue ) {
		return add_option( self::OptionPrefix.$insKey, $insValue );
	}
	
	static public function updateOption( $insKey, $insValue ) {
		return update_option( self::OptionPrefix.$insKey, $insValue );
	}
	
	static public function deleteOption( $insKey ) {
		return delete_option( self::OptionPrefix.$insKey );
	}
}

class HLT_BootstrapCss_Install {
	
	public function __construct() {
		register_activation_hook( __FILE__, array( &$this, 'onWpActivatePlugin' ) );
	}
	
	public function onWpActivatePlugin() {
		HLT_BootstrapCss::addOption( 'option',			'none' );
		HLT_BootstrapCss::addOption( 'hotlink',			'N' );
		
		HLT_BootstrapCss::addOption( 'alert_js',		'N' );
		HLT_BootstrapCss::addOption( 'button_js',		'N' );
		HLT_BootstrapCss::addOption( 'dropdown_js',		'N' );
		HLT_BootstrapCss::addOption( 'modal_js',		'N' );
		HLT_BootstrapCss::addOption( 'tooltip_js',		'N' );
		HLT_BootstrapCss::addOption( 'popover_js',		'N' );
		HLT_BootstrapCss::addOption( 'scrollspy_js',	'N' );
		HLT_BootstrapCss::addOption( 'tab_js',			'N' );
		HLT_BootstrapCss::addOption( 'transition_js',	'N' );	// Bootstrap v2.0+
		HLT_BootstrapCss::addOption( 'collapse_js',		'N' );	// Bootstrap v2.0+
		HLT_BootstrapCss::addOption( 'carousel_js',		'N' );	// Bootstrap v2.0+
		HLT_BootstrapCss::addOption( 'typeahead_js',	'N' );	// Bootstrap v2.0+
		HLT_BootstrapCss::addOption( 'all_js',			'N' );	// Bootstrap v2.0+
		
		HLT_BootstrapCss::addOption( 'js_head',			'N' );
		HLT_BootstrapCss::addOption( 'useshortcodes',	'N' );
		HLT_BootstrapCss::addOption( 'prettify',		'N' );
		
		HLT_BootstrapCss::addOption( 'customcss',		'N' );
		HLT_BootstrapCss::addOption( 'customcss_url',	'http://' );
	}
}

class HLT_BootstrapCss_Uninstall {
	
	// TODO: when uninstalling, maybe have a WPversion save settings offsite-like setting
	
	public function __construct() {
		register_deactivation_hook( __FILE__, array( &$this, 'onWpDeactivatePlugin' ) );
	}
	
	public function onWpDeactivatePlugin() {
		HLT_BootstrapCss::deleteOption( 'option' );
		HLT_BootstrapCss::deleteOption( 'hotlink' );
		
		HLT_BootstrapCss::deleteOption( 'alert_js' );
		HLT_BootstrapCss::deleteOption( 'button_js' );
		HLT_BootstrapCss::deleteOption( 'dropdown_js' );
		HLT_BootstrapCss::deleteOption( 'modal_js' );
		HLT_BootstrapCss::deleteOption( 'tooltip_js' );
		HLT_BootstrapCss::deleteOption( 'popover_js' );
		HLT_BootstrapCss::deleteOption( 'scrollspy_js' );
		HLT_BootstrapCss::deleteOption( 'tab_js' );
		HLT_BootstrapCss::deleteOption( 'transition_js' );	// Bootstrap v2.0+
		HLT_BootstrapCss::deleteOption( 'collapse_js' );	// Bootstrap v2.0+
		HLT_BootstrapCss::deleteOption( 'carousel_js' );	// Bootstrap v2.0+
		HLT_BootstrapCss::deleteOption( 'typeahead_js' );	// Bootstrap v2.0+
		HLT_BootstrapCss::deleteOption( 'all_js' );			// Bootstrap v2.0+
		
		HLT_BootstrapCss::deleteOption( 'js_head' );
		HLT_BootstrapCss::deleteOption( 'useshortcodes' );
		HLT_BootstrapCss::deleteOption( 'prettify' );

		HLT_BootstrapCss::deleteOption( 'customcss'  );
		HLT_BootstrapCss::deleteOption( 'customcss_url' );
		
		HLT_BootstrapCss::deleteOption( 'upgraded1to2' );
		
		/* Clean-up from previous versions */
		HLT_BootstrapCss::deleteOption( 'alerts_js'  );
		HLT_BootstrapCss::deleteOption( 'tabs_js'  );
		HLT_BootstrapCss::deleteOption( 'twipsy_js'  );
		
	}
}

class HLT_Plugin {
	
	static public $VERSION;
	
	static public $PLUGIN_NAME;
	static public $PLUGIN_PATH;
	static public $PLUGIN_DIR;
	static public $PLUGIN_URL;
	
	const ParentTitle		= 'Host Like Toast Plugins';
	const ParentName		= 'Host Like Toast';
	const ParentPermissions	= 'manage_options';
	const ParentMenuId		= 'hlt-directory';
	
	const ViewExt			= '.php';
	const ViewDir			= 'views';
	
	public function __construct() {
		add_action( 'init', array( &$this, 'onWpInit' ), 1 );
		add_action( 'admin_init', array( &$this, 'onWpAdminInit' ) );
		add_action( 'plugins_loaded', array( &$this, 'onWpPluginsLoaded' ) );
	}
	
	protected function fixSubmenu() {
		global $submenu;
		if ( isset( $submenu[self::ParentMenuId] ) ) {
			$submenu[self::ParentMenuId][0][0] = 'Dashboard';
		}
	}
	
	/**
	 * 
	 * @param $insUrl
	 * @param $innTimeout
	 */
	protected function redirect( $insUrl, $innTimeout = 1 ) {
		echo '
			<script type="text/javascript">
				function redirect() {
					window.location = "'.$insUrl.'";
				}
				//var oTimer = setTimeout( "redirect()", "'.($innTimeout * 1000).'" );
			</script>'; 
	}
	
	protected function display( $insView, $inaData = array() ) {
		$sFile = dirname(__FILE__).DS.self::ViewDir.DS.$insView.self::ViewExt;
		
		if ( !is_file( $sFile ) ) {
			echo "View not found: ".$sFile;
			return false;
		}
		
		if ( count( $inaData ) > 0 ) {
			extract( $inaData, EXTR_PREFIX_ALL, 'hlt' );
		}
		
		ob_start();
			include( $sFile );
			$sContents = ob_get_contents();
		ob_end_clean();
		
		echo $sContents;
		return true;
	}
	
	protected function getImageUrl( $insImage ) {
		return self::$PLUGIN_URL.'images/'.$insImage;
	}
	
	protected function getSubmenuPageTitle( $insTitle ) {
		return self::ParentTitle.' - '.$insTitle;
	}
	
	protected function getSubmenuId( $insId ) {
		return self::ParentMenuId.'-'.$insId;
	}
	
	public function onWpInit() {
		add_action( 'admin_menu', array( &$this, 'onWpAdminMenu' ) );
		add_action( 'plugin_action_links', array( &$this, 'onWpPluginActionLinks' ), 10, 4 );
	}
	
	public function onWpAdminInit() {

	}
	
	public function onWpPluginsLoaded() {
		
	}
	
	public function onWpAdminMenu() {
		add_menu_page( self::ParentTitle, self::ParentName, self::ParentPermissions, self::ParentMenuId, array( $this, 'onDisplayMainMenu' ), $this->getImageUrl( 'toaster_16x16.png' ) );
	}
	
	public function onDisplayMainMenu() {
		$this->redirect( 'admin.php?page=hlt-directory-bootstrap-css' );

		$aData = array(
			'plugin_url'	=> self::$PLUGIN_URL
		);
		$this->display( 'hostliketoast_index', $aData );
	}
	
	public function onWpPluginActionLinks( $inaLinks, $insFile ) {
		if ( $insFile == plugin_basename( __FILE__ ) ) {
			$sSettingsLink = '<a href="'.admin_url( "admin.php" ).'?page=hlt-directory-bootstrap-css">' . __( 'Settings', 'hostliketoast' ) . '</a>';
			array_unshift( $inaLinks, $sSettingsLink );
		}
		return $inaLinks;
	}
	
}

$oHLT_BootstrapCss = new HLT_BootstrapCss();