<?php

namespace Speak;

class Project extends Post {
	public function get_strings() {
		return $this->get_connected_post( 'speak-project_to_string' );
	}

	/**
	 * Import strings into a project
	 *
	 * @param array $strings List of String objects. Strings will be inserted if not already in the database
	 * @param Language|null $language Language object, null to use the default language
	 */
	public function import(array $strings, Language $language = null) {
		if ( empty( $language ) ) {
			$language = Language::get_default();
		}

		foreach ($strings as $string) {
			if ( ! is_a( $string, __NAMESPACE__ . '\\String' ) ) {
				return false;
			}

			$string->set_project($this);
			if ( $string->exists() ) {
				$string->update();
			}
			else {
				$string->insert();
			}
		}
	}

	/**
	 * Import strings from a file
	 *
	 * @param string $filename POT file to import from
	 * @param Language $language Language object, null to use the default language
	 */
	public function import_from_file($filename, Language $language = null) {
		$strings = POMO\import($filename);
		return $this->import($strings, $language);
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
