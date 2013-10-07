<?php
/**
 * Plugin Name: Speak
 * Description: Translate anything with Speak!
 */

include 'library/Speak.php';
spl_autoload_register('\\Speak\\autoload');

\Speak\register();
