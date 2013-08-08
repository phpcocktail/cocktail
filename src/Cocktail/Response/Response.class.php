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
 * Response abstract for all kinds of responses.
 * @author t
 * @package Cocktail\Response
 * @version 1.01
 */
abstract class Response {

	/**
	 * @var \ResponseConfig
	 */
	protected $_Config;

	protected $_content;

	/**
	 * @var static singleton response instance
	 */
	static protected $_instance;

	/**
	 * @return static
	 */
	public static function instance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new static();
		}
		return self::$_instance;
	}

	/**
	 * @param ResponseConfig $Config
	 * @return static
	 */
	public static function get(\ResponseConfig $Config=null) {

		if (is_null($Config)) {
			$configClassname = get_called_class() . 'Config';
			if (strpos($configClassname, '\\')) {
				$configClassname = substr($configClassname, strrpos($configClassname, '\\')+1);
			}
			$Config = $configClassname::get();
		}

		$Response = new static();
		$Response->_Config = $Config;

		return $Response;

	}

	/**
	 * I send the actual response as it's been set up
	 * @return void
	 */
	abstract public function send();

	/**
	 * I set current content
	 * @param mixed $content printable variable or object with toStrong() method (not __tostring() !!!)
	 * @return \ResponseHttp
	 */
	public function setContent($content) {
		$this->_content = $content;
		return $this;
	}
	/**
	 * I can aggregate content, but then make sure you are sending strings
	 * @param mixed $content
	 * @return \ResponseHttp
	 */
	public function addContent($content) {
		$this->_content = \Util::toString($this->_content) . \Util::toString($content);
		return $this;
	}
	/**
	 * I return the actual content as a string
	 * @return string
	 */
	public function getContent() {
		return \Util::toString($this->_content);
	}

}
