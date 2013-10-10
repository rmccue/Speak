<?php

namespace Speak;

class Language {
	protected $term;

	public function __construct( $term ) {
		$this->term = $term;
	}

	/**
	 * Get the default language
	 *
	 * @internal `default` would be a better name here, but it's a parser token, so we can't work around that.
	 * @return Language
	 */
	public static function get_default() {
		$id = get_option( 'speak_language_default', 'english' );
		$post = get_post( $id );
		return new Language( $post );
	}

	/**
	* Define default terms for custom taxonomies in WordPress 3.0.1
	*
	* @author    Michael Fields     http://wordpress.mfields.org/
	* @props     John P. Bloch      http://www.johnpbloch.com/
	*
	* @since     2010-09-13
	* @alter     2010-09-14
	*
	* @license   GPLv2
	*/
	public static function set_default_language( $post_id, $post ) {
		if ( 'publish' !== $post->post_status ) {
			return;
		}

		$terms = wp_get_post_terms( $post_id, static::type() );
		if ( empty( $terms ) ) {
			$default = static::get_default();
			wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
		}
	}

	/**
	 * Get the post type
	 *
	 * @return string
	 */
	public static function type() {
		return 'speak-language';
	}
}
