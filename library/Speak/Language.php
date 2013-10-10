<?php

namespace Speak;

class Language extends Post {
	public static function default() {
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
