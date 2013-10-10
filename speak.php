<?php
/**
 * Plugin Name: Speak
 * Description: Translate anything with Speak!
 */

include 'library/Speak.php';
spl_autoload_register('\\Speak\\autoload');

// Manual file loading
require( ABSPATH . WPINC . '/pomo/po.php' );
require( __DIR__ . '/library/Speak/POMO.php' );

\Speak\register();
