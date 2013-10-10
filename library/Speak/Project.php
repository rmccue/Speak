<?php

namespace Speak;

class Project extends Post {
	public function get_strings() {
		$query = new WP_Query( array(
			'connected_type' => 'speak-project_to_string',
			'connected_items' => $this->post,
			'nopaging' => true,
		) );
	}

	public static function type() {
		return apply_filters('speak_project_post-type', 'speak-project');
	}
}