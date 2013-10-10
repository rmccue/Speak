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
	 * Meta values
	 *
	 * @var array
	 */
	protected $meta = array();

	/**
	 * Cached meta values
	 *
	 * This stores the original copy of the meta values, so that we only need
	 * to update the changed values.
	 *
	 * @var array
	 */
	protected $cached_meta = array();

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

	/**
	 * Post connection map (relationship => Post object)
	 *
	 * @var array
	 */
	protected $connections = array();

	/**
	 * Post connection cache
	 *
	 * This stores the original copy of the connections, so that we only need to
	 * update changed connections.
	 *
	 * @var array
	 */
	protected $connection_cache = array();

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

		if ( ! $this->update_meta() ) {
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

		if ( ! $this->update_meta() ) {
			return false;
		}

		return true;
	}

	protected function update_meta() {
		// Update the meta values
		$changed = array_diff_assoc($this->meta, $this->cached_meta);
		foreach ($changed as $key => $value) {
			update_post_meta( $this->post->ID, '_speak_' . $key, $value );
		}

		// Update the internal cache
		$this->cached_meta = $this->meta;

		return true;
	}

	protected function get_connected_post($relationship) {
		if ( isset( $this->connections[ $relationship ] ) ) {
			return $this->connections[ $relationship ];
		}

		$query = new WP_Query( array(
			'connected_type' => $relationship,
			'connected_items' => $this->post,
			'numberposts' => 1,
		) );

		if ( ! $query->have_posts() ) {
			return null;
		}

		$connected = post_to_object( $query->get_posts()[0] );

		$this->connections[ $relationship ] = $connected;
		$this->connection_cache[ $relationship ] = $connected;

		return $connected;
	}

	protected function set_connected_post($relationship, Post $connected) {
		$this->connections[$relationship] = $connected;
	}

	/**
	 * Update post connections
	 */
	protected function update_connections() {
		$changed = array_diff_assoc($this->connections, $this->connection_cache);

		foreach ($changed as $relationship => $connected) {
			$type = p2p_type( $relationship );
			$type->connect( $this->post->ID, $connected->post->ID );
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
