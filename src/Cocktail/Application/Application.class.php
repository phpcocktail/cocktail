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
 * @author t
 * @package Cocktail\Application
 * @version 1.01
 */
abstract class Application {

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
	 * @var self main application instance
	 */
	protected static $_Instance;

	/**
	 * I return singleton instance
	 * @return self
	 */
	public static function instance() {
		if (empty(static::$_Instance)) {
			static::$_Instance = static::boot();
		}
		return static::$_Instance;
	}

	/**
	 * I autoguess config class name based on ApplicationXxxYyy classname. I recursively look for ApplicationXxxYyyConfig,
	 * 		ApplicationXxxConfig, ApplicationConfig and usefirst found class. Then just create an instance of myself and
	 * 		return it
	 * @param array $config if null, will fetch 'App' from Camarera config. If you send config array, make it similar.
	 * @return \Application
	 */
	public static function boot($config=null) {

		if (is_null($config)) {
			$config = \Camarera::conf('App');
		}
		if (!is_array($config) || empty($config)) {
			throw new \ConfigException('config key APP not found');
		}

		// I get a full and a namespaceless aopplication classname
		$applicationClassname = get_called_class();
		$applicationClassname2 = '';
		if ($pos = strrpos($applicationClassname, '\\')) {
			$applicationClassname2 = substr($applicationClassname, $pos+1);
		}
		// if application classname doesn't match config value, emit a dev message
		if (isset($config['applicationClassname']) &&
				($config['applicationClassname'] != $applicationClassname) &&
				($config['applicationClassname'] != $applicationClassname2)) {
			\Camarera::log(
				\Camarera::LOG_NOTICE,
				'Application::boot called with wrong $config["applicationClassname"] setting, ' .
					'called class: "' . $applicationClassname . '" $config value: "' . $config['applicationClassname'] . '"'
			);
		}

		// I use the namespaceless classname for proper class shifting
		$configClassname = $applicationClassname2 . 'Config';
		if (class_exists($configClassname)) {
			\Camarera::log(\Camarera::LOG_NOTICE, 'Config class FOUND: ' . $configClassname);
		}
		else {
			\Camarera::log(\Camarera::LOG_NOTICE, 'Config class ' . $configClassname . ' not found');
			do {
				$applicationClassname2 = \Util::stripCamelPart($applicationClassname);
				if ($applicationClassname == $applicationClassname2) {
					$configClassname = 'ApplicationConfig';
					break;
				}
				$applicationClassname = $applicationClassname2;
				$configClassname = $applicationClassname . 'Config';
			} while (!class_exists($configClassname));
			\Camarera::log(\Camarera::LOG_NOTICE, 'Using config class: ' . $configClassname);
		}

		$ApplicationConfig = $configClassname::get($config);

		$Application = static::get($ApplicationConfig);
		return $Application;
	}

	/**
	 * I get an application instance and store it in static::$_Instance if this is the first call (main app instance)
	 * @param \ApplicationConfig $Config
	 * @return static
	 */
	public static function get(\ApplicationConfig $Config=null) {
		if (is_null($Config)) {
			$Application = static::boot();
		}
		else {
			$Application = new static($Config);
		}
		if (empty(self::$_Instance)) {
			self::$_Instance = $Application;
		}
		return $Application;
	}

	/**
	 * I am protected, use get()
	 * @param \ApplicationConfig $Config
	 * @throws \InvalidArgumentException
	 */
	protected function __construct(\ApplicationConfig $Config) {
		if (empty($Config->namespace)) {
			throw new \InvalidArgumentException();
		}
		$this->_Config = $Config;
	}

	/**
	 * @return \ApplicationConfig I return current config
	 */
	public function getConfig() {
		return $this->_Config;
	}

	/**
	 * Implement this to return a correct Request object
	 */
	abstract protected function _getRequest();
	/**
	 * Implement this to return a proper Response object
	 */
	abstract protected function _getResponse();

	/**
	 * I do, in order:
	 *		get a Request object
	 *		get a Response object
	 *		get a Router object
	 *		get a Route by having the Router route the Request
	 *		instanciate the Controller
	 *		invoke the route in the Controller
	 *		send response
	 */
	public function run() {

		$requestClassname = $this->_Config->requestClassname;
		// @todomake this a ::get() also. there might be multiple applications, though not chancy
		$this->_Request = $requestClassname::instance();

		$responseClassname = $this->_Config->responseClassname;
		$this->_Response = $responseClassname::get();

		$routerClassname = $this->_Config->routerClassname;

		if (substr($routerClassname, 0, 1) != '\\') {
			$routerClassname =
				(empty($this->_Config->namespace) ? '' : $this->_Config->namespace . '\\') .
					$routerClassname;
		}

		$this->_Router = $routerClassname::get();

		$Route = $this->_Router->route($this->_Request);

		$controllerClassname = $Route->controllerClassname;

		$this->_Controller = $controllerClassname::get();
		$this->_Controller->setRequest($this->_Request);
		$this->_Controller->setResponse($this->_Response);
		$this->_Controller->invoke($Route);

		$this->_Response = $this->_Controller->getResponse();

		$this->_Response->send();

	}

	/**
	 * maybe I don't have to define here, only in ApplicationHttp
	 * @return \User
	 */
	abstract function getUser();

}
