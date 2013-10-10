<?php

namespace Speak;

function autoload($class) {
	$file = str_replace('\\', DIRECTORY_SEPARATOR, $class);
	$file = __DIR__ . DIRECTORY_SEPARATOR . $file . '.php';

	if (file_exists($file)) {
		include $file;
	}
}

function register() {
	add_action( 'init', __NAMESPACE__ . '\\register_types' );
	add_action( 'p2p_init', __NAMESPACE__ . '\\register_relations' );
}

function register_types() {
	$project_type = Project::type();

	// Do we need to register this ourself?
	if ( ! post_type_exists( $project_type ) ) {
		register_post_type( 'speak-project', array(
			'label' => __( 'Project', 'speak' ),
			'public' => true,
			'hierarchical' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions', 'page-attributes'),
			'has_archive' => true,
		) );
	}

	register_post_type( 'speak-language', array(
		'label' => __( 'Language', 'speak' ),
		'public' => true,
		'hierarchical' => true,
		'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions', 'page-attributes'),
		'has_archive' => true,
	) );
	register_post_type( 'speak-string', array(
		'label' => __( 'String', 'speak' ),
		'public' => true,
		'show_in_nav_menus' => false,
		'show_in_menu' => false,
		'supports' => array( 'title', 'editor', 'custom-fields', 'comments', 'revisions' ),
	) );
}

function register_relations() {
	p2p_register_connection_type( array(
		'name' => 'speak-project_to_string',
		'from' => Project::type(),
		'to' => 'speak-string',
		'cardinality' => 'one-to-many',
	) );

	p2p_register_connection_type( array(
		'name' => 'speak-base_to_translated',
		'from' => 'speak-string',
		'to' => 'speak-string',
		'cardinality' => 'one-to-many',
	) );

	p2p_register_connection_type( array(
		'name' => 'speak-language_to_string',
		'from' => 'speak-language',
		'to' => 'speak-string',
		'cardinality' => 'one-to-many'
	) );
}
