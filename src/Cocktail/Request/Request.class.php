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
 * @version 1.01
 *
 */
abstract class Request {

    /**
     * @var static singleton instance, get with static::instance()
     */
    protected static $_Instance;

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
	 * @return \static
	 */
	public static function instance() {
		if (is_null(static::$_Instance)) {
			static::$_Instance = static::getFromCurrentRequest();
		}
		return static::$_Instance;
	}

    /**
     * I create and return an instance
     * @param true|???
     * @return Cocktail\Request
     */
    public static function get($data) {
        if ($data === true) {
			debug_print_backtrace(); die('FUCK');
            return static::instance();
        }
        // @todo handle if data is sent
        else die('@TODO');
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
	 * @return string I return request method in nicer form
	 */
	public function getRequestMethod() {
		return ucfirst(strtolower($this->_requestMethod));
	}

	/**
	 * I am protected, use get()
	 */
	protected function __construct() {}

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

	/**
	 * I set basic things in Request, currently just $_SERVER
	 * @return static
	 */
	public static function getFromCurrentRequest() {
		$Request = new static;
		$Request->_SERVER = $_SERVER;
		return $Request;
	}

}
