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
 * @package Cocktail\Controller
 * @version 1.01
 */
class ControllerHmvc extends \Controller {

	/**
	 * @var boolean you can turn off hmvc routing before main action. See docs on usage
	 */
	protected $_hmvcRouting = null;
	/**
	 * @var boolean if true, then eg. /my-id will match the block 'my-id' and 'SideBar'=>'my-id' as well (in this order)
	 */
	protected $_hmvcPartialMatches = null;

	/**
	 * I fetch config params if unless set in class
	 */
	protected function __construct() {
		if (is_null($this->_hmvcRouting)) {
			$this->_hmvcRouting = \Camarera::conf('Controller.Hmvc.hmvcRouting');
		}
		if (is_null($this->_hmvcPartialMatches)) {
			$this->_hmvcPartialMatches = \Camarera::conf('Controller.Hmvc.hmvcPartialMatches');
		}
	}

	/**
	 * I recursively search for matching routes. There is no ranking, if you mess up overlapping partial routes then who knows what will happen :)
	 * @param array $paramParts remaining param parts (from uri)
	 * @param array $datas array of child items
	 * @param array $usedParamParts these were used already
	 * @param array $usedDataKeys these were used already
	 * @return array of array($usedParamParts, $usedDataKeys, $routedViewItem)
	 */
	protected function _hmvcRoute($paramParts, $datas, $usedParamParts=array(), $usedDataKeys=array()) {
		$matches = array();
		// nothing to examine further
		if (empty($paramParts) || empty($datas)) {
			return array();
		}

		// if no exact match, loop data parts
		foreach ($datas as $eachDataKey=>$eachDataPart) {
			// set up common variables, to be used in recursive calls
			if ($eachDataPart instanceof Block) {
				// ???????
				$newDataParts = $eachDataPart->getViewData(true);
			}
			elseif (is_array($eachDataPart)) {
				$newDataParts = $eachDataPart;
			}
			else {
				$newDataParts = array();
			}
			$newUsedDataKeys = array_merge($usedDataKeys, array($eachDataKey));

			// this is a match
			if ($eachDataKey === reset($paramParts)) {
				// check if there's a better match
				$newUsedParamParts = array_merge($usedParamParts, array_slice($paramParts, 0, 1));
				$newMatches = $this->_hmvcRoute(
						array_slice($paramParts, 1),
						$newDataParts,
						$newUsedParamParts,
						$newUsedDataKeys
				);
				if (empty($newMatches)) {
					// @todo check here if block is a block instance ! ?
					if ($eachDataPart instanceof Block) {
						$matches = array_merge($matches, array(array(
	 							'paramParts' => $newUsedParamParts,
	 							'dataKeys' => $newUsedDataKeys,
	 							'block' => $eachDataPart
	 					)));
					}
				}
				else {
					$matches = array_merge($matches, $newMatches);
				}
			}
			elseif ($this->_hmvcPartialMatches) {
				$newMatches = $this->_hmvcRoute(
						$paramParts,
						$newDataParts,
						$usedParamParts,
						$newUsedDataKeys
				);
				$matches = array_merge($matches, $newMatches);
			}
		}

	return $matches;

	}

	/**
	 * calling an action by this invoke is necessary to perform _before() and _after() and to avoid scope problems with
	 * 		protected methods (which are reachable only ba rerouting)
	 * @param Route $Route it contains everything needed
	 * @return mixed
	 */
	public function invoke(\Route $Route) {
		try {

			$this->_setAutoParams($Route->autoParams);

			$this->_Route = $Route;

			$this->_before();

			$invoked = false;

			if ($this->_hmvcRouting) {
				$match = null;
				$matches = $this->_hmvcRoute($this->_Route->paramParts, $this->_viewData);
				if (count($matches) > 1) {
					// @todo implement match ranking here
					throw new \UnImplementedException();
				}
				elseif (count($matches) == 1) {
					$match = reset($matches);
				}

				if ($match) {
					$remainingParts = array_slice($this->_Route->paramParts, count($match['paramParts']));
					$Block = $match['block'];
					if (count($remainingParts)) {
						$actionMethodRaw = ucfirst(strtolower(reset($remainingParts)));

						$actionMethodMethod = $this->_Request->requestMethod;
						if (method_exists(
								$Block,
								$actionMethodName = 'action' . $actionMethodMethod . $actionMethodRaw
						));
						elseif (method_exists(
								$Block,
								$actionMethodName = 'actionAll' . $actionMethodRaw
						));
						else {
							$actionMethodName = null;
						}
					}
					else {
						$actionMethodName = 'generate';
					}
					if (!empty($actionMethodName)) {
						if (count($remainingParts)) {
							array_shift($remainingParts);
						}
						$Block->Request = $this->_Request;
						$methodReflection = new \ReflectionMethod($Block, $actionMethodName);
						$methodParams = array_slice($remainingParts, 0, $methodReflection->getNumberOfParameters());
						$remainingParts = array_slice($remainingParts, $methodReflection->getNumberOfParameters());
						$content = $Block->invoke($actionMethodName, $methodParams);
						if (!is_null($content)) {
							$invoked = true;
							$this->response($content);
						}
					}
				}
			}

			if (!$invoked) {
				$methodReflection = new \ReflectionMethod($this, $Route->actionMethodName);
				$methodParams = array_slice($Route->paramParts, 0, $methodReflection->getNumberOfParameters());
				$Route->paramParts = array_slice($Route->paramParts, $methodReflection->getNumberOfParameters());

				$content = call_user_func_array(array($this, $Route->actionMethodName), $methodParams);
				if (!is_null($content)) {
					$this->response($content);
				}

				$this->_after();

			};

			// @todo after hmvc routing ???

		}
		catch (\Exception $e) {
			// @todo implement a 500 throw here
			print_r($e);
			die ('@todo - uncaught exception in ControllerHmvc::invoke');
		}

		return $content;
	}

// 	protected function _before() {
// 		parent::_before();
// 	}

// 	protected function _after() {
// 		parent::_after();
// 	}

}
