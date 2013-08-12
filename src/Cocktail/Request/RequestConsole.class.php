<?php
/**
 * This file is part of PhpCocktail. PhpCocktail is free software: you can redistribute it and/or modify it under the
 * 		terms of the GNU Lesser General Public License as published by the Free Software Foundation, either version 3
 * 		of the License, or (at your option) any later version.
 * PhpCocktail is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * 		warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * 		more detailechop($_REQUEST);s. You should have received a copy of the GNU Lesser General Public License along with PhpCocktail.
 * 		If not, see <http://www.gnu.org/licenses/>.
 * @copyright Copyright 2013 t
 */
namespace Cocktail;

/**
 * request class for console apps
 * @author t
 * @package Cocktail\Application
 * @version 1.1
 */
class RequestConsole extends \Request {

	/**
	 * @var string default request method, required for routing. In console apps, should always be 'action'
	 */
	protected $_requestMethod = 'CONSOLE';

	/**
	 * I create an instance of me and fill with command line params
	 * @return \static
	 */
	protected static function _instance() {

		$Request = parent::_instance();

		$routeParts = $_SERVER['argv'];

		// shift first element since it should be shake.php (with or without .php and full path
		if (preg_match('|shake(\.php)?$|', $routeParts[0], $matches)) {
			array_shift($routeParts);
		}

		$Request->_routeParts = $routeParts;

		return $Request;
	}

	/**
	 * I return a named param, the only available source is $_SERVER
	 * @param string $paramName param to get
	 * @param null $origin not used, provided for compatibility only
	 * @return mixed|null
	 */
	public function param($paramName=null, $origin=null) {
		if (is_null($paramName)) {
			return $this->_SERVER;
		}
		else {
			return isset($this->_SERVER[$paramName]) ? $this->_SERVER[$paramName] : null;
		}
	}

}
