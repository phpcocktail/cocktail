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
 * this config class encapsulates all information, gathered by the router, and sufficient to run an action
 * @author t
 * @package Cocktail\Route
 * @version 1.01
 */
class Route extends \Config {

	/**
	 * @var string class name, with namespace
	 */
	public $controllerClassname;
	/**
	 * @var string exact name of the method to be called
	 */
	public $actionMethodName;
	/**
	 * @var array[]string url parameter parts remaining after routing
	 */
	public $paramParts;
	/**
	 * @var array[string]what mapped router auto-params
	 */
	public $autoParams;

}
