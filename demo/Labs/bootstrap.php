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
 * @since 1.0
 * @license LGPL
 * @copyright Copyright 2013 t
 */
namespace Blog;

if (!defined('APP_ROOT')) {
	define('APP_ROOT', dirname(__FILE__));
}

// load Camarera
if (!class_exists('Camarera', false)) {
	require(dirname(__FILE__) . '/../../../Camarera/bootstrap.php');
}

// load Cocktail
if (!\Camarera::conf('Cocktail')) {
	require(dirname(__FILE__) . '/../../bootstrap.php');
}

// load app config
\Camarera::loadConf('App', APP_ROOT . '/conf/conf.php');

// turn on beautifier
\Beautify::setMode(\Beautify::MODE_BEAUTIFUL_NEWLINES);

class_alias('ViewBasic', 'View');
