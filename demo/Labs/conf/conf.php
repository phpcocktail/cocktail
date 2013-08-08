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
 * @since 1.01
 * @license LGPL
 * @copyright Copyright 2013 t
 */

/**
 * demo Blog application config
 * @version 1.01
 * @package Demo\Blog
 */
return array(
	'App' => array(
		'name' => 'Labs testing',
		'namespace' => 'Labs',
		'localRoot' => APP_ROOT,
	),
	'_autoloader' => array(
		// register autoloader for files of Blog, see file structure of src folder. It's in a function since needs self back reference to app.cl_root config item
		function() {
			return \AutoloaderClassPathGrouped::get(array(
				'path' => \Camarera::conf('.localRoot') . '/src',
				'namespace' => '',
			));
		},
	),
	'_store' => array(
	),
	'_logger' => array(

	),
);
