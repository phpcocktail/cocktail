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
 * Base class for all kind of requests. Wraps all input that is available at startup
 * @author t
 * @package Cocktail\Request
 * @version 1.1
 */
abstract class Request {

	use \Camarera\TraitSingletonGlobal, \Camarera\TraitServe;

	/**
	 * @var string default request method, required for routing
	 */
	protected $_requestMethod = '';

    /**
     *
     * @var string[] I will put uri parts or command line commands into this
     */
    protected $_routeParts = array();

	protected $_SERVER;
	const ORIGIN_SERVER = 'SERVER';

	/**
	 * I set params based on current env request
	 * @return static
	 */
	protected static function _instance() {
		$Request = new static;
		$Request->_SERVER = $_SERVER;
		return $Request;
	}

    /**
     * I provide read access to protected props
     * @param string property name
     * @return mixed
     */
	public function __get($key) {
		$property = '_' . $key;
		if (property_exists($this, $property)) {
			return $this->$property;
		}
		throw new \MagicGetException($key, get_called_class());
	}

	/**
	 * I return a named param, $origin should be restricted to input sources and defaulted to the most widely used one
	 * @param null|string $paramName param to get, if null, all params in origin will be returned
	 * @param null $origin should be a name of a protected variable eg. 'get' to access $this->_GET
	 * @return mixed|null
	 */
	public function param($paramName=null, $origin=null) {
		$origin = '_' . $origin;

		if (is_null($paramName)) {
			return $this->$origin;
		}
		else {
			return isset($this->{$origin}[$paramName]) ? $this->{$origin}[$paramName] : null;
		}
	}

}
