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
 * BlockHmvc is a special block type with hmvc routing capabilities (for ajax eg.). Hmvc blocks are to be created in
 *	the main controller's _before() and when invoking the actual action, routing takes place to see if the requested url
 *	is adressed to a hmvc block. If it is, the request is routed to the corresponding block's action method. Otherwise,
 *	when final rendering takes place, generate() is called, which invokes action{RequestMethod}{ActionMethod}() or
 *	actionAll{ActionMethod}() where ActionMethod is fetched from $this->_actionNameToCall and thus can be set at
 *	runtime.
 * @author t
 * @package Cocktail\Block
 * @version 1.01
 */
// @todo implement modules/packages and move this class to Cocktail\Hmvc namespace ! ?
class BlockHmvc extends \Block {

	/**
	 * @var \Request
	 */
	public $Request;

	/**
	 * @var string by default, I invoke a suitable Index action method
	 */
	protected $_actionNameToCall = 'Index';

	/**
	 * I am used to invoke an action and store the returned content in $this->_content()
	 * @param $actionMethodName
	 * @param $methodParams
	 * @return mixed
	 */
	public function invoke($actionMethodName, $methodParams) {
		$this->_content = call_user_func_array(array($this, $actionMethodName), $methodParams);
		return $this->_content;
	}

	/**
	 * @return \View|string something to be printed
	 */
	function _generate() {
		$Request = isset($this->Request) ? $this->Request : \RequestHttp::instance();
		$requestMethod = $Request->requestMethod;
		if (method_exists($this, $actionMethod = 'action' . $requestMethod . $this->_actionNameToCall)) {
			$ret = $this->$actionMethod();
		}
		elseif (method_exists($this, $actionMethod = 'actionAll' . $this->_actionNameToCall)) {
			$ret = $this->$actionMethod();
		}
		else {
			// @todo I think I should do a 404 here, or just call index action???
			die('@todo in BlockHmvc.class.php');
		}
		if (is_null($ret)) {
			$ret = $this->_View;
		}
		// if a \View object is returned, apply the block's data (to keep consitency with Bock:::generate)
		if ($ret instanceof \View) {
			$ret->assign($this->_viewData);
		}
		return $ret;
	}

}
