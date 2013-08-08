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
 * simple http server application
 * @author t
 * @package Cocktail\Application
 * @version 1.01
 */
class ApplicationHttp extends \Application {

	/**
	 * @var \ApplicationHttpConfig
	 */
	protected $_Config;

	/**
	 * @var \RequestHttp
	 */
	protected $_Request;

	/**
	 * @var \Controller
	 */
	protected $_Controller;

	/**
	 * @var \Router
	 */
	protected $_Router;

	/**
	 * @var \ResponseHttp
	 */
	protected $_Response;

	/**
	 * @param \ApplicationHttpConfig $Config
	 * @return \ApplicationHttp
	 */
	public static function get(\ApplicationHttpConfig $Config) {
		return parent::get($Config);
	}

	protected function _getRequest() {
		return \RequestHttp::instance();
	}
	protected function _getResponse() {
		return \ResponseHttp::get();
	}

	public function getUser() {
		return \UserWeb::instance();
	}

}
