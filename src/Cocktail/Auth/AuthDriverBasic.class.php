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
 * @obsolete this class is obsoleted and abandoned
 * basic class for input validator objects. Uses validation functionality of models.
 *
 * @author t
 * @package Cocktail\Auth
 * @version 1.01
 */
class AuthDriverBasic extends \AuthDriver {

	/**
	 * @var string provided only so configs can define this valueand get mapped
	 */
	protected $_classname;

	/**
	 * @var string classname of User instance to create. Should be a subclass of Cocktail\User or compatible with it.
	 */
	protected $_userClassname = '\User';

	/**
	 * @var string name of the cookie to store the sessionid in
	 */
	protected $_cookieName = 'c_sid';

	/**
	 * @var bool if true, user will be kept logged in
	 */
	protected $_stayLoggedin = false;

	/**
	 * @var int if user opts for staying logged in, his session will be kept for this long. Default 10 days
	 */
	protected $_stayLoggedinTimeout = 864000;

	/**
	 * @var bool if true, user will be checked against his IP when authenticating
	 */
	protected $_ipCheck = true;

	/**
	 * @var int when changing password, user cannot use this many old passwords (this many including current)
	 */
	protected $_oldPasswordCount = 3;

	/**
	 * @var string salt prefix for crypt() function, allowing you to choose crypt method
	 */
	protected $_saltPrefix = '$6$';

	/**
	 * @var int salt length (not autoguessed by saltPrefix, and used by other measures, see code)
	 */
	protected $_saltLength = 16;

	/**
	 * @var \User
	 */
	protected $_User;

	/**
	 * I simply return the cookie value from request. You can override this method to get the sessionid otherwise.
	 * @param \Request $Request
	 * @return mixed|null
	 */
	protected function _getSessionIdFromRequest(\Request $Request) {
		return $Request->param($this->_cookieName, 'COOKIE');
	}

	/**
	 * I compare a password to a salted and encrypted password. I get the salt from the encrypted pw.
	 * @param $password raw password to check
	 * @param $storedCryptedPassword encrypted password with salt
	 * @return bool true if $password with salt from $storedCryptedPassword equals to $storeCryptedPassword
	 */
	protected function _checkPassword($password, $storedCryptedPassword) {
		$salt = substr($storedCryptedPassword, 0, $this->_saltLength+strlen($this->_saltPrefix));
		$cryptedPassword = crypt($password, $salt);
		return $storedCryptedPassword == $cryptedPassword ? true : false;
	}

	/**
	 * @param \Request $Request performs user log in cookie data in request
	 */
	public function fromRequest(\Request $Request) {
		$sessionId = $this->_getSessionIdFromRequest($Request);
		$userCLassname = $this->_userClassname;
		$User = $userCLassname::get();
		$User->sid = $_COOKIE[$this->_cookieName];
		$User->load();
		echop($User);
		echop('SESSIONID: ' . $sessionId); die;
	}

	/**
	 * I load a User* model from store, then test password (it's salted, so I cannot load by it). On success, set
	 * 		$this->User and user cookie.
	 * @param string $login
	 * @param string $password (plain)
	 * @return bool true on success, otherwise false
	 */
	public function loginByLoginPassword($login, $password, $stayLoggedin=null) {
		try {

			if (is_null($stayLoggedin)) {
				$stayLoggedin = $this->_stayLoggedin;
			}

			$userClassname = $this->_userClassname;
			$User = $userClassname::get();
			$User->login = $login;
			$User->load();

			$userPassword = $User->password;
			if ($pos = strpos($userPassword, '|')) {
				$userPassword = substr($userPassword, 0, $pos);
			}

			if ($this->_checkPassword($password, $userPassword)) {
				return $this->_setUser($User, $stayLoggedin);
			}
		}
		// I don't do error handling here...
		catch (\Exception $e) {
			echop($e); die;
		}

		return false;

	}

	protected function _setUser($User, $stayLoggedin) {
		$this->_User = $User;
		$sessionId = sha1(openssl_random_pseudo_bytes($this->_saltLength));
		$this->_User->sid = $sessionId;
		$this->_User->save();
		if ($stayLoggedin) {
			setcookie($this->_cookieName, $sessionId, 0);
			setcookie('_' . $this->_cookieName, $sessionId, $this->_stayLoggedinTimeout);
		}
		else {
			setcookie($this->_cookieName, $sessionId, 0);
		}
		return true;
	}

	/**
	 * @todo
	 * @param $token
	 */
	public function loginByToken($token) {

	}

	/**
	 * I update current user's password. (core should be abstracted so updating other users is possible ! ?)
	 * @param $newPassword
	 * @return bool
	 * @throws \AuthException
	 */
	public function updatePassword($newPassword) {
		// check if user is logged in at all ! ?
		if (empty($this->_User) || !$this->_User->ID) {
			throw new \AuthException();
		}
		// check if password matches any of last x passwords
		$currentPassword = explode('|', $this->_User->password);
		foreach ($currentPassword as $eachCurrentPassword) {
			if ($this->_checkPassword($newPassword, $eachCurrentPassword)) {
				die('FU');
			}
		}
		// create new salted password, unshift into passwords, limit max old passwordcount
		$salt = $this->_saltPrefix . openssl_random_pseudo_bytes($this->_saltLength);
		$saltedPassword = crypt($newPassword, $salt);
		array_unshift($currentPassword, $saltedPassword);
		$currentPassword = array_slice($currentPassword, 0, $this->_oldPasswordCount);
		$currentPassword = implode('|', $currentPassword);
		$this->_User->password = $currentPassword;
		$this->_User->save();
		return true;
	}

}
