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
 * facade for auth drivers. These drivers have to be registered with Auth::registerDriver() by an id and later the
 * 	drivers can be referenced by that id. If you use only one auth driver, you can always leave id empty to use 'default'
 * @author t
 * @package Cocktail\Auth
 * @version 1.01
 */
class Auth  {

	/**
	 * @var AuthDriver[] drivers in an array, keyed by id
	 */
	protected $_authDrivers = array();

	protected static $_Instance;

	public static function instance() {
		if (is_null(static::$_Instance)) {
			$config = \Camarera::rConf('Auth');
			$Auth = new static;

			if (!is_array($config)) {
				throw new \ConfigException;
			};

			foreach ($config as $eachConfig) {
				if ($eachConfig instanceof \AuthDriver) {
					$Auth->registerDriver($eachConfig);
				}
				elseif (is_array($eachConfig)) {
					$driverClassname = $eachConfig['classname'];
					$AuthDriver = $driverClassname::get($eachConfig);
					$Auth->registerDriver($AuthDriver);
				}
				else {
					throw new \ConfigException;
				}
			};

			static::$_Instance = $Auth;

		}

		return static::$_Instance;
	}

	private final function __construct(){}

	/**
	 * I register an auth driver
	 * @param \AuthDriver $AuthDriver
	 * @param string id or empty for default, useful for single auth drivers
	 * @return $this
	 * @throws \RuntimeException id id is already registered
	 */
	public function registerDriver(\AuthDriver $AuthDriver, $driverId='default') {
		if (array_key_exists($driverId, $this->_authDrivers)) {
			throw new \RuntimeException('Auth::registerDriver() id ' . $driverId . ' already exists');
		}
		$this->_authDrivers[$driverId] = $AuthDriver;
		return $this;
	}

	/**
	 * I unregister a driver if exists
	 * @param string $driverId
	 * @return bool true if driver existed
	 */
	public function unregisterDriver($driverId='default') {
		if (!array_key_exists($driverId, $this->_authDrivers)) {
			return false;
		}
		unset($this->_authDrivers[$driverId]);
		return true;
	}

	/**
	 * I get the current User object from a driver
	 * @param string $driverId
	 * @return \User|bool
	 */
	public function driverGetUser($driverId='default') {
		if (!array_key_exists($driverId, $this->_authDrivers)) {
			return false;
		}
		return $this->_authDrivers[$driverId]->User;
	}

	/**
	 * use a driver to log in a user by login (email or nickname) and password. Also has keep signed in option, which
	 * 	can be interactive to user by a checkbox or defaulted to conf
	 * @param $login
	 * @param $password
	 * @param $stayLoggedIn
	 * @param string $driverId
	 * @return null
	 */
	public function driverLoginByLoginPassword($login, $password, $stayLoggedIn=null, $driverId='default') {
		if (!array_key_exists($driverId, $this->_authDrivers)){
			return null;
		};
		return $this->_authDrivers[$driverId]->loginByLoginPassword($login, $password, $stayLoggedIn);
	}

}

