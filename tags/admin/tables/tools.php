<?php
/**
 * @package	Tags
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class TagsTableTools extends DSCTable {

	function TagsTableTools(&$db) {
		$tbl_key = 'extension_id';
		$tbl_suffix = 'extensions';

		$this -> set('_suffix', $tbl_suffix);
		$name = "press";

		parent::__construct("#__{$tbl_suffix}", $tbl_key, $db);
	}

	function check() {
		return true;
	}

}
