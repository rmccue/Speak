<?php

namespace Speak;

class Post {
	protected $post;

	public function __construct($post = null) {
		$this->post = $post;
	}
}