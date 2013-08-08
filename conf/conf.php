<?php
/**
 * This file is part of PhpCocktail. PhpCocktail is free software: you can redistribute it and/or modify it under the
 * 		terms of the GNU Lesser General Public License as published by the Free Software Foundation, either version 3
 * 		of the License, or (at your option) any later version.
 * PhpCocktail is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * 		warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * 		more details. You should have received a copy of the GNU Lesser General Public License along with PhpCocktail.
 * 		If not, see <http://www.gnu.org/licenses/>.
 * Copyright © 2013 t
 */

return array(
	'Cocktail' => array(
		'version' => '1.01',
		'localRoot' => defined('CL_ROOT') ? CL_ROOT : realpath(dirname(__FILE__) . '/..'),
		'namespace' => 'Cocktail',
		'controllerPrefix' => 'Controller',
	),
	'Field' => array(
		'password' => array(
			'method' => function() { return \FieldPassword::METHOD_CUSTOM; },
		),
	),
	'Beautify' => array(
		'mode' => function() {
			return \Beautify::MODE_BEAUTIFUL_NEWLINES;
		},
	),
	'Auth' => array(
		array(
			'classname' => 'AuthDriverBasic',
			'userClassname' => 'User',
			'cookieName' => 'c_sid',
			'stayLoggedin' => false,
		),
	),
	'Controller' => array(
		'Hmvc' => array(
			// turns on or off
			'hmvcRouting' => true,
			// if true, url route will match any block route of which it is part of. False requires exact match
			'hmvcPartialMatches' => true,
		),
	),
	// minimal view config. One module may have one view class, and one template path.
	'View' => array(
		'classname' => '\ViewBasic',
		'templatePath' => \Camarera::CONF_AUTO,
	),
);
