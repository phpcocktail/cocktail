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
 *
 * @property \ResponseConfig $_Config
 * @property-read \ResponseConfig $Config
 * @property-read mixed $content
 */
abstract class Response {

	use \Camarera\TraitSingletonGlobal, \Camarera\TraitServeWithConfig;

	/**
	 * @var mixed string, or anything that's string-castable
	 */
	protected $_content;

	/**
	 * I just return an empty object of my class
	 * @return static
	 */
	protected static function _instance() {
		return static::serve();
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
