<?php
/**
 * This file is part of PhpCocktail. PhpCocktail is free software: you can redistribute it and/or modify it under the
 * 		terms of the GNU Lesser General Public License as published by the Free Software Foundation, either version 3
 * 		of the License, or (at your option) any later version.
 * PhpCocktail is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * 		warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * 		more details. You should have received a copy of the GNU Lesser General Public License along with PhpCocktail.
 * 		If not, see <http://www.gnu.org/licenses/>.
 * @author t
 * @version 1.01
 * @license LGPL
 * @copyright Copyright 2013 t
 */
global $a;
$a = function($data, $level=0) {
	global $a;
	$ret = array();
	foreach ($data as $eachDataKey=>$eachData) {
		if (is_array($eachData) && empty($eachData)) {
			$line = '';
		}
		elseif (is_array($eachData)) {
			$line = $eachDataKey . "\n" . \b::b($a($eachData, $level+1), 1);
		}
		else {
			$line = \b::b($eachData, 0);
		}
		$ret[] = $line;
	}
	return implode("\n", $ret);
};

echo $a($data);
