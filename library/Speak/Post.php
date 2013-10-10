<?php

namespace Speak;

abstract class Post {
	/**
	 * Post object
	 *
	 * Speak would extend this, but because some people can't understand that
	 * inheritance is a good thing, WP_Post is declared final. *sigh*
	 *
	 * @var WP_Post
	 */
	protected $post;

	/**
	 * Post title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * Post content
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Map of properties to post fields
	 *
	 * @var array
	 */
	protected static $fieldmap = array(
		'title' => 'post_title',
		'content' => 'post_content',
	);

	public function __construct($post = null) {
		$this->post = $post;

		if ( ! empty( $this->post ) ) {
			foreach ( static::$fieldmap as $key => $field ) {
				$this->$key = $this->post->$field;
			}
		}
	}

	public function exists() {
		return ( $this->post !== null );
	}

	public function insert() {
		if ( $this->exists() )
			return false;

		// Find values to update
		$post_values = array();
		foreach ( static::$fieldmap as $key => $field ) {
			$post_values[ $field ] = $this->$key;
		}
		$post_values['post_type'] = $this->post_type();

		// Allow plugins to override the insertion
		$post = apply_filters( 'speak_pre_insert_post', null, $post_values );
		if ( empty( $post ) ) {
			// $post = wp_insert_post( $post_values );
		}

		if ( is_wp_error( $post ) ) {
			return false;
		}

		return true;
	}

	public function update() {
		if ( ! $this->exists() )
			return false;

		// Update the post itself
		$post_values = array();
		foreach ( static::$fieldmap as $key => $field ) {
			$old_value = $this->post->$field;
			if ( $this->$key !== $old_value ) {
				$post_values[ $field ] = $this->$key;
			}
		}

		// If we have changes to the post itself, update it first
		if ( ! empty( $post_values ) ) {
			$post_values['ID'] = $this->post->ID;
			$post_values['post_type'] = $this->post_type();
			// wp_update_post( $post_values );
		}

		return true;
	}

	/**
	 * Get the post type
	 *
	 * @return string
	 */
	abstract protected function post_type();
}
