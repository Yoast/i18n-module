<?php

/**
 * The Yoast i18n module with a connection to WordPress.org.
 */
class Yoast_I18n_WordPressOrg {

	protected $i18n;

	/**
	 * Constructs the 1i8n module for wordpress.org. Required fields are the 'textdomain', 'plugin_name' and 'hook'
	 *
	 * @param array $args The settings for the i18n module
	 */
	public function __construct( $args ) {
		$args = $this->set_defaults( $args );

		$this->i18n = new yoast_i18n( $args );
		$this->set_api_url( $args['textdomain'] );
	}

	private function set_defaults( $args ) {

		if ( ! isset( $args['glotpress_logo'] ) ) {
			$args['glotpress_logo'] = 'https://plugins.svn.wordpress.org/' . $args['textdomain'] . '/assets/icon-128x128.png';
		}

		if ( ! isset( $args['register_url'] ) ) {
			$args['register_url'] = 'https://translate.wordpress.org/projects/wp-plugins/' . $args['textdomain'] . '/';
		}

		if ( ! isset( $args['glotpress_name'] ) ) {
			$args['glotpress_name'] = 'Translating WordPress';
		}

		if ( ! isset( $args['project_slug'] ) ) {
			$args['project_slug'] = $args['textdomain'];
		}

		return $args;
	}

	private function set_api_url( $textdomain ) {
		$this->i18n->set_api_url( 'https://translate.wordpress.org/api/projects/wp-plugins/' . $textdomain . '/stable/' );
	}
}
