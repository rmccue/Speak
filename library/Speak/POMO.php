<?php

namespace Speak\POMO;

use Speak\String;
use PO;
use Translations;
use Translation_Entry;

/**
 * Import entries from POMO
 *
 * @param Translations $pomo POMO translations object
 * @return array List of String objects, not inserted into database
 */
function import(Translations $pomo) {
	$strings = array();
	foreach ($pomo->entries as $entry) {
		$strings[] = string_from_entry($entry);
	}

	return $strings;
}

/**
 * Import entries from POMO
 *
 * @param string $filename PO file path
 * @return array List of String objects, not inserted into database
 */
function import_from_file($filename) {
	$po = new PO();
	$po->import_from_file($filename);
	return import($po);
}

function export(Translations $pomo) {
	return $pomo->export();
}

function string_from_entry(Translation_Entry $entry) {
	$string = new String();
	$string->value = $entry->singular;
	$string->plural = (string) $entry->plural;

	return $string;
}
