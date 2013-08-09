<?php
/**
 * This file is part of PhpCocktail. PhpCocktail is free software: you can redistribute it and/or modify it under the
 * 		terms of the GNU Lesser General Public License as published by the Free Software Foundation, either version 3
 * 		of the License, or (at your option) any later version.
 * PhpCocktail is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * 		warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * 		more details. You should have received a copy of the GNU Lesser General Public License along with PhpCocktail.
 * 		If not, see <http://www.gnu.org/licenses/>.
 * @copyright Copyright 2013 t
 */
namespace Cocktail;

// @var string root path to Cocktail lib
if (!defined('CL_ROOT')) {
	define('CL_ROOT', realpath(dirname(__FILE__)));
}

// load config
\Camarera::loadConf('Cocktail', CL_ROOT . '/conf/conf.php');

// I alias this class as \b for easy usage in templates. I know it is nasty...
class_alias('Cocktail\Beautify', 'b');

