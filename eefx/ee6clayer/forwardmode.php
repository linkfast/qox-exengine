<?php
# ExEngine 7 / ExEngine 6 Compatibility Layer / ExEngine 6 ForwardMode Controller

/*
	This file is part of ExEngine7.

    ExEngine7 is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Foobar is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/

// jQuery Lib

if ($ee6cl["ee7obj"]->libIsLoaded("jquery")) {

	class ex6_jquery extends jquery{	
		function __construct() {
			global $ee6cl;			
			$nee7 = &$ee6cl["ee7obj"];
			parent::__construct($nee7);
		}	
	}
	
	function jquery_load($mode="min") {
		$ee6jq = new ex6_jquery();
		if ($mode == "min")
			$ee6jq->load_compressed();
		else
			$ee6jq->load_dev();
	}
	
	function jquery_pg_load($plugin) {
		$ee6jq = new ex6_jquery();
		$ee6jq->load_plugin($plugin);
	}
	
	function jquery_ui_load($theme="ui-lightness") {
				$ee6jq = new ex6_jquery();
				$ee6jq->load_ui($theme);
	}
	
}
?>