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
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\check_dependencies', -100 );
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\default_actions', -50 );
}

function check_dependencies() {
	$missing = array();
	if ( ! defined( 'P2P_PLUGIN_VERSION' ) ) {
		$missing[] = 'posts-to-posts';
	}

	if ( ! empty( $missing ) ) {
		remove_action( 'plugins_loaded', __NAMESPACE__ . '\\default_actions', -50 );
		add_action( 'admin_notices', function () use ($missing) {
			missing_dependencies_notice($missing);
		});
	}
}

function missing_dependencies_notice($missing) {
	$links = array();
	foreach ($missing as $plugin) {
		switch ($plugin) {
			case 'posts-to-posts':
				$links[] = '<a href="http://wordpress.org/plugins/posts-to-posts/">Posts 2 Posts</a>';
				break;
		}
	}

	echo '<div id="message" class="error">';
	echo '<p>' . __( 'Speak requires the following plugins, please activate them before using Speak:', 'speak' ) . '</p><ul>';
	foreach ($links as $link) {
		echo '<li>' . $link . '</li>';
	}
	echo '</ul></div>';
}

function default_actions() {
	add_action( 'init', __NAMESPACE__ . '\\register_types' );
	add_action( 'init', __NAMESPACE__ . '\\register_taxonomies' );
	add_action( 'p2p_init', __NAMESPACE__ . '\\register_relations' );

	// UI
	add_action( 'admin_menu', __NAMESPACE__ . '\\register_menu' );
	add_action( 'parent_file', __NAMESPACE__ . '\\tax_menu_correction' );

	// Internals
	add_action( 'save_post', __NAMESPACE__ . '\\Language::set_default_language', 100, 2 );
}

function register_types() {
	$project_type = Project::type();

	// Do we need to register this ourself?
	if ( ! post_type_exists( $project_type ) ) {
		$project_type = register_post_type( 'speak-project', array(
			'label' => __( 'Project', 'speak' ),
			'public' => true,
			'hierarchical' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions', 'page-attributes'),
			'has_archive' => true,
			'show_in_menu' => 'speak-menu',
		) );

		add_action( 'speak_register_menu', function ($parent) use ($project_type) {
			add_submenu_page(
				$parent,
				$project_type->labels->add_new,
				$project_type->labels->add_new,
				$project_type->cap->create_posts,
				sprintf( 'post-new.php?post_type=%s', $project_type->name )
			);
		}, 10, 1);
	}

	register_post_type( 'speak-string', array(
		'label' => __( 'String', 'speak' ),
		'public' => true,
		'show_in_nav_menus' => false,
		'show_in_menu' => false,
		'supports' => array( 'title', 'editor', 'custom-fields', 'comments', 'revisions' ),
	) );
}

function register_taxonomies() {
	$language_tax = register_taxonomy( 'speak-language', 'speak-string', array(
		'label' => __( 'Language', 'speak' ),
		'hierarchical' => true,
	) );

	add_action( 'speak_register_menu', function ($parent) {
		$language_tax = get_taxonomy( 'speak-language' );
		add_submenu_page(
			$parent,
			$language_tax->labels->menu_name,
			$language_tax->labels->menu_name,
			$language_tax->cap->manage_terms,
			sprintf( "edit-tags.php?taxonomy=%s&amp;post_type=speak-string", $language_tax->name )
		);
	}, 10, 1 );
}

function register_relations() {
	p2p_register_connection_type( array(
		'name' => 'speak-project_to_string',
		'from' => Project::type(),
		'to' => 'speak-string',
		'cardinality' => 'one-to-many',
		'admin_box' => array(
			'show' => false,
		),
	) );

	p2p_register_connection_type( array(
		'name' => 'speak-base_to_translated',
		'from' => 'speak-string',
		'to' => 'speak-string',
		'cardinality' => 'one-to-many',
	) );
}

function register_menu() {
	add_menu_page(
		__( 'Speak', 'speak' ),
		__( 'Speak', 'speak' ),
		'edit_posts',
		'speak-menu',
		__NAMESPACE__ . '\\admin_error'
	);

	do_action( 'speak_register_menu', 'speak-menu' );
}

function tax_menu_correction($parent_file) {
	global $current_screen;

	$taxonomy = $current_screen->taxonomy;
	if ( $taxonomy == 'speak-language' ) {
		$parent_file = 'speak-menu';
	}

	return $parent_file;
}

function post_to_object($post) {
	switch ($post->post_type) {
		case Project::type():
			return new Project($post);

		case 'speak-string':
			return new String($post);
	}

	return null;
}
