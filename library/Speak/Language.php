<?php

namespace Speak;

class Language extends Post {
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
	 * Get the post type
	 *
	 * @return string
	 */
	protected function post_type() {
		return 'speak-language';
	}
}
