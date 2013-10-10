<?php

namespace Speak;

class Project extends Post {
	public function get_strings() {
		return $this->get_connected_post( 'speak-project_to_string' );
	}

	/**
	 * Get the post type
	 *
	 * @return string
	 */
	protected function post_type() {
		return static::type();
	}

	public static function type() {
		return apply_filters('speak_project_post-type', 'speak-project');
	}
}
