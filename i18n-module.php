<?php

/**
 * This class defines a promo box and checks your translation site's API for stats about it, then shows them to the user.
 *
 * @abstract
 */
abstract class yoast_i18n {

	/**
	 * Hook where you want to show the promo box
	 *
	 * @var string
	 */
	public $hook;

	/**
	 * Name of your plugin
	 *
	 * @var string
	 */
	public $plugin_name;

	/**
	 * Project slug for the project on your translation site
	 *
	 * @var string
	 */
	public $project_slug;

	/**
	 * Your plugins textdomain
	 *
	 * @var string
	 */
	public $textdomain;

	/**
	 * Your translation site's logo
	 *
	 * @var string
	 */
	public $translate_project_logo;

	/**
	 * Your translation site's name
	 *
	 * @var string
	 */
	public $translate_project_name;

	/**
	 * Your translation site's URL
	 *
	 * @var string
	 */
	public $translate_project_url;

	/**
	 * Will contain the site's locale
	 *
	 * @access private
	 * @var string
	 */
	private $locale;
	
	/**
	 * Will contain the locale's name, obtained from yoru translation site
	 *
	 * @access private
	 * @var string
	 */
	private $locale_name;
	
	/**
	 * Will contain the percentage translated for the plugin translation project in the locale
	 *
	 * @access private
	 * @var int
	 */
	private $percent_translated;

	/**
	 * Will contain the percentage translated for the plugin translation project in the locale
	 *
	 * @access private
	 * @var bool
	 */
	private $translation_available;

	/**
	 * Will contain the percentage translated for the plugin translation project in the locale
	 *
	 * @access private
	 * @var bool
	 */
	private $translation_loaded;
	
	/**
	 * Class constructor
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}
		
		$this->locale = get_locale();
		if ( 'en_US' === $this->locale ) {
			return;
		}

		$this->init();

		$hide_promo = get_transient( 'yoast_i18n_' . $this->project_slug . '_promo_hide' );
		if ( ! $hide_promo ) {
			if ( isset( $_GET['remove_i18n_promo'] ) ) {
				// No expiration time, so this would normally not expire, but it wouldn't be copied to other sites etc. 
				set_transient( 'yoast_i18n_' . $this->project_slug . '_promo_hide', true );
				return;
			}
			add_action( $this->hook, array( $this, 'promo' ) );
		}
	}
	
	/**
	 * This is where you decide where to display the messages and where you set the plugin specific variables.
	 */
	abstract public function init();
	/** 
	 *	Example of what this could do:
	 *	$this->textdomain 	= 'wordpress-seo';				// The textdomain for the plugin you're extending this class in
	 *	$this->project_slug	= 'wordpress-seo';				// Project slug for the project on the translate domain
	 *	$this->plugin_name	= 'WordPress SEO by Yoast';		// Name of the plugin
	 *	$this->hook 		= 'wpseo_admin_footer';			// Hook where you want to show the promo box
	 */
	
	/**
	 * Outputs a promo box
	 */
	public function promo() {
		$this->translation_details();

		$message = false;
		
		$translate_project_link = '<a href="' . $this->translate_project_url . '">' . $this->translate_project_name . '</a>';
		
		if ( $this->translation_loaded && $this->percent_translated < 90 ) {
		 	$message = sprintf( __( 'As you can see, there is a translation of this plugin in %s. This translation is currently %s complete. We need your help to make it complete and to fix any errors. Please register at %s to help complete the %1$s translation!' ), $this->locale_name	, $this->percent_translated . '%', $translate_project_link );
		} 
		else if ( ! $this->translation_loaded && $this->translation_available ) {
			$message = sprintf( __( 'You\'re using WordPress in %1$s. While %2$s has been translated to %1$s for %3$s, it\'s not been shipped with the plugin yet. You can help! Register at %4$s to help complete the translation to %1$s!' ), $this->locale_name, $this->plugin_name, $this->percent_translated . '%', $translate_project_link );
		}
		else if ( ! $this->translation_loaded && ! $this->translation_available ) {
			$message = sprintf( __( 'You\'re using WordPress in %s. We\'d love for %s to be translated in %1$s too, but unfortunately, it isn\'t right now. You can change that! Register at %s to help translate this plugin to %1$s!' ), $this->locale_name, $this->plugin_name, $translate_project_link );
		}

		if ( $message ) {
			echo '<div id="i18n_promo_box" style="border: 1px solid #ccc; background-color: #fff; padding: 10px; max-width: 650px;">';
			echo '<a href="' . add_query_arg( array( 'remove_i18n_promo' => '1' ) ) . '" style="color:#333;text-decoration:none;font-weight:bold;font-size:16px;border:1px solid #ccc;padding:1px 4px;" class="alignright">X</a>';
			echo '<h2>' . sprintf( __( 'Translation of %s' ), $this->plugin_name ) . '</h2>';
			if ( isset( $this->translate_project_logo ) && '' != $this->translate_project_logo ) {
				echo '<a href="' . $this->translate_project_url . '"><img class="alignright" style="margin:15px 5px 5px 5px;width:200px;" src="' . $this->translate_project_logo . '" alt="'. $this->translate_project_name .'"/></a>';
			}
			echo '<p>' . $message . '</p>';
			echo '<p><a href="' . $this->translate_project_url . '">' . __( 'Register now &raquo;' ) . '</a></p>';
			echo '</div>';
		}
	}
	
	/**
	 * Try to get translation details from cache, otherwise retrieve them, then parse them.
	 *
	 * @access private
	 */
	private function translation_details() {
		$set = get_transient( 'yoast_i18n_' . $this->project_slug );
		
		if ( ! $set ) {
			$set = $this->retrieve_translation_details();
			set_transient( 'yoast_i18n_' . $this->project_slug, $set, DAY_IN_SECONDS );
		}

		if ( is_null( $set ) ) {
			$this->translation_available = false;
		} else {
			$this->translation_available = true;
		}
		
		$this->translation_loaded = is_textdomain_loaded( $this->textdomain );
		
		$this->parse_translation_set( $set );
	}
	
	/**
	 * Retrieve the translation details from Yoast Translate
	 *
	 * @access private
	 */
	private function retrieve_translation_details() {
		$project_api_url = $this->translate_project_url . 'api/projects/' . $this->project_slug;
		
		$resp = wp_remote_get( $project_api_url );
		$body = wp_remote_retrieve_body( $resp );
		
		if ( $body ) {
			unset( $resp );
			$body = json_decode( $body );
			foreach( $body->translation_sets as $set ) {
				if ( $this->locale == $set->wp_locale ) {
					return $set;
				}
			}
			
			return null;
		}
	}
	
	/** 
	 * Set the needed private variables based on the results from Yoast Translate
	 *
	 * @access private
	 */
	private function parse_translation_set( $set ) {
		$this->locale_name 		= $set->name;
		$this->percent_translated 	= $set->percent_translated;
	}
}
