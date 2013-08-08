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
	shake.php mix {[namespace\]classname::method | shakemethod} param1 param2 param3 -o paramx

Description:
	mix, in short, is a tool to run a class method. It can be an internal Shake method, in the ShakeMix class, or an
	arbitrary method in a class. In latter case classname will be mapped to ShakeMixClassname and methodname to
	Action{Alll|Console}Method() eg. foo\bar::test will be routed to foo\ShakeMixBar::ActionAllTest() or
	ActionConsoleTest()

Built-in tools:
	classList - will generate compatibility class definitions for IDEs
	...

Get more help:
	each tool has its own help, access it by eg.

	shake.php mix classList help

