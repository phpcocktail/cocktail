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
 * basic class for input validator objects. Uses validation functionality of models.
 *
 * @author t
 * @package Cocktail\Model
 * @version 1.01
 */
abstract class ModelValidator extends \Model {

	/**
	 * @var string[] parameter names enlisted here will be skipped on checking extra params
	 */
	protected static $_ignoredParams = array();

	/**
	 * @var bool false=any extra param triggers validation error. A sent in $origin but neither used in the validator
	 * 		nor is enlisted in $this->_ignoredParams is considered an extra param.
	 */
	protected static $_extraParamsAllowed = false;

	/**
	 * @var bool should not be overwritten
	 */
	protected static $_isManageable = false;

	/**
	 * @var bool should not be overwritten
	 */
	protected $_isRegistered = false;

	/**
	 * @var \Request the object from which $this was hydrated
	 */
	protected $_Request;

	/**
	 * @var array all the fieldnames which have data set
	 */
	protected $_loadedFields = array();

	/**
	 * @var string[] all the fieldname=>value pairs which were present in source data but not used
	 */
	protected $_extraFields = array();

	/**
	 * I return a model instance filled from request
	 * @param \Request $Request if null, I'll use request singleton instance
	 * @param null $origin from which input scope shall I load data, eg. \RequestHttp::REQUESTMETHOD_POST, if null,
	 * 		topmost container will be used, eg. $_REQUEST for http requests.
	 * @return static
	 */
	public static function getFromRequest(\Request $Request=null, $origin=null) {
		if (is_null($Request)) {
			$Request = \Request::instance();
		}

		$ModelValidator = new static;
		$ModelValidator->_Request = $Request;
		foreach ($ModelValidator::getField() as $eachFieldname=>$EachField) {
			$value = $Request->param($eachFieldname, $origin);
			if (!is_null($value)) {
				$ModelValidator->$eachFieldname = $value;
				$ModelValidator->_loadedFields[$eachFieldname] = $value;
			}
		}

		$ModelValidator->_extraFields = array_diff_key(
				$Request->param(null, $origin),
				$ModelValidator->_loadedFields
		);

		return $ModelValidator;
	}



}
