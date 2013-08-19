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

/**
 * I am a skeleton class for console applications
 *
 * @author t
 * @package Cocktail\Application
 * @version 1.1
 *
 * @property \ApplicationConsoleConfig $_Config
 * @property-read \ApplicationConsoleConfig $Config
 * @property-read \RequestConsole $Request
 * @property-read \ResponseConsole $Response
 * @property-read \Router $Router
 * @property-read \Controller $Controller
 */
class ApplicationConsole extends \Application {

	public function getUser() {
		return null;
		throw new \UnimplementedException();
	}

}
