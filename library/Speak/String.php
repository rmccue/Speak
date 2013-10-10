<?php

namespace Speak;

class String extends Post {
	protected $content;

	public function __construct($post = null) {
		parent::__construct($post);
	}

	public function __get($key) {
		switch ($key) {
			case 'value':
				return $this->content;

			case 'plural':
			case 'context':
				return $this->meta[ $key ];
		}

		return null;
	}

	public function __set($key, $value) {
		switch ($key) {
			case 'value':
				$this->content = $value;
				return;

			case 'plural':
			case 'context':
				$this->meta[ $key ] = $value;
				return;
		}
	}

	public function __isset($key) {
		switch ($key) {
			case 'value':
				return true;

			case 'plural':
			case 'context':
				return isset( $this->meta[ $key ] );
		}

		return false;
	}

	public function get_project() {
		return $this->get_connected_post('speak-project_to_string');
	}

	public function set_project(Project $project) {
		return $this->set_connected_post('speak-project_to_string', $project);
	}

	public function get_language() {
		return $this->get_connected_post('speak-language_to_string');
	}

	public function set_language(Language $language) {
		return $this->set_connected_post('speak-language_to_string', $language);
	}

	public function get_base() {
		return $this->get_connected_post('speak-base_to_translated');
	}

	public function set_base(String $string) {
		return $this->set_connected_post('speak-base_to_translated', $string);
	}

	public function is_plural() {
		return ! empty( $this->plural );
	}

	public function __toString() {
		return $this->value;
	}

	/**
	 * Get the post type
	 *
	 * @return string
	 */
	protected function post_type() {
		return 'speak-string';
	}
}
