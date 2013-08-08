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
 */ ?>
Usage:
	shake.php mod {list|install|disable|remove|conf|export|info} [moduleName] param1 param2 param3 -o paramx

Description:
	mod is the tool to manage modules.

Built-in tools:
	list - lists all modules installed (active or disabled)
	install - install a module from an origin
	disable - disable module, not implemented yet
	remove - remove (delte) a module
	export - export a module to target
	...

Get more help:
	each tool has its own help, access it by eg.

	shake.php mode list help

