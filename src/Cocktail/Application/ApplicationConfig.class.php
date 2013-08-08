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
 * Base config file for applications. In the app config App.App.* refers to fields in this object
 * @author t
 * @package Cocktail\Application
 * @version 1.01
 */
class ApplicationConfig extends \Config {

	/**
	 *
	 * @var string name of current environment
	 */
	public $env = 'default';

	public $theme = '';

	/**
	 * @var string I store in the config the application classname to be initialized (use eg. different environment
	 *		configs to overwrite)
	 */
	public $applicationClassname = '\Application';

	/**
	 * @var string classname of request object
	 */
	public $requestClassname = '\Request';

	/**
	 * @var string classname of response object
	 */
	public $responseClassname = '\Response';

	/**
	 * @var string application router class name. Default is an abstract class so you must overwrite...
	 */
	public $routerClassname = '\Router';

	/**
	 * @var string, application name. May be displayed at some UIs
	 */
	public $name;

	/**
	 * @var string dynamically generated classnames will be prefixed by this namespace. It's desirable to have a
	 * 		separate namepsace for all applications
	 */
	public $namespace;

	/**
	 * @var string (absolute) path to
	 */
	public $localRoot = '';

	/**
	 * @var string controller classname prefix. Used by routing. For console apps, this should be 'Shake'
	 */
	public $controllerPrefix = 'Controller';

}
