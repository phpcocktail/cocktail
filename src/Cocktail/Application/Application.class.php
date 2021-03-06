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
 * Application class is the unified frontend controller. Should be instantiated (booted) by a front controller in assets
 * 	I need a request to get data from, a response to send back, a controller to fill the response based on the request,
 * 	and a router which finds the appropriate controller. All these classes have an application-level singleton and I
 * 	use these (unless I get them injected before run() is called). I get the proper classname to create from the $Config
 * 	object.
 *
 * @author t
 * @package Cocktail\Application
 * @version 1.1
 *
 * @property \ApplicationConfig $_Config
 * @property-read \ApplicationConfig $Config
 * @property-read \Request $Request
 * @property-read \Response $Response
 * @property-read \Router $Router
 * @property-read \Controller $Controller
 */
abstract class Application {

	use \Camarera\TraitServeWithConfig,
		\Camarera\TraitSingletonGlobal,
		\Camarera\TraitMagicGetProtected,
		\Camarera\TraitPropertyExistsCached;

	/**
 	 * @var \ApplicationConfig
	 */
	protected $_Config;

	/**
	 * @var \Request
	 */
	protected $_Request;

	/**
	 * @var \Response
	 */
	protected $_Response;

	/**
	 * @var \Router
	 */
	protected $_Router;

	/**
	 * @var \Controller
	 */
	protected $_Controller;

	/**
	 * @var \Application self main application instance
	 */
	protected static $_Instance;

	/**
	 * I return singleton instance. Note I use self:: so there is one Application singleton instance, not one for all subclasses
	 * @return static
	 */
	protected final static function _instance() {
		return static::boot();
	}

	/**
	 * I instanciate an Application instance with given array config, or by fetchin field "App" fron config. I also set
	 *		the instance as the global singleton if it is not yet set.
	 * @param array $config
	 * @return static
	 * @throws \ConfigException
	 */
	public static function boot(array $config=null) {

		if (is_null($config)) {
			$config = \Camarera::conf('App');
		}
		if (!is_array($config) || empty($config)) {
			throw new \ConfigException('config key "App" not found');
		}

		$Application = static::serve($config);

		if (is_null(self::$_Instance)) {
			self::$_Instance = $Application;
		}

		return $Application;
	}

	/**
	 * I inject request object if needed
	 * @param \Request $Request
	 * @return $this
	 */
	public function setRequest(\Request $Request) {
		$this->_Request = $Request;
		return $this;
	}

	/**
	 * I inject response object if needed
	 * @param \Response $Response
	 * @return $this
	 */
	public function setResponse(\Response $Response) {
		$this->_Response = $Response;
		return $this;
	}

	/**
	 * I do, in order:
	 *		get a Request object (if empty)
	 *		get a Response object (if empty)
	 *		get a Router object
	 *		get a Route by having the Router route the Request
	 *		instanciate the Controller
	 *		invoke the route in the Controller
	 *		send response
	 */
	public function run() {

		if (empty($this->_Request)) {
			$requestClassname = $this->_Config->requestClassname;
			$this->_Request = $requestClassname::instance();
		}

		if (empty($this->_Response)) {
			$responseClassname = $this->_Config->responseClassname;
			$this->_Response = $responseClassname::instance();
		}

		if (empty($this->_Router)) {
			$routerClassname = $this->_Config->routerClassname;
			$this->_Router = $routerClassname::instance();
		}

		$Route = $this->_Router->route($this->_Request);

		if (empty($this->_Controller)) {
			$controllerClassname = $Route->controllerClassname;
			$this->_Controller = $controllerClassname::instance();
		}

		$this->_Controller
			->setRequest($this->_Request)
			->setResponse($this->_Response)
			->invoke($Route)
		;

		$this->_Response->send();

	}

	/**
	 * maybe I don't have to define here, only in ApplicationHttp
	 * @return \User
	 */
	abstract function getUser();

}
