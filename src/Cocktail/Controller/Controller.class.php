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
 * Basic controller class. Has functionality for:
 * 	- invoke() to call an action method with all the chimes, can be extended by overriding
 * 	- _before() and _after() which also can be extended, and called by invoke() with same params as main action
 * 	- automatic templating using a layout template
 *  - getting/setting layout data
 *  - handling responses by action methods
 *  - autoparams for URI (or other sequential type routing), they pull params from the request param stack
 * @author t
 * @package Cocktail\Controller
 * @version 1.01
 */
class Controller {

	use \Camarera\TraitServe, \Camarera\TraitSingletonGlobal;

	/**
	 * @var string if non empty, the actual content will be put in this template, and rendered with view data again
	 */
	protected $_layout;

	/**
	 * @var array data to be set to main view
	 */
	protected $_viewData = array();

	/**
	 * @var \Request
	 */
	protected $_Request;

	/**
	 * @var \Response
	 */
	protected $_Response;

	/**
	 * @var \Route
	 */
	protected $_Route;

	/**
	 * @var \View
	 */
	protected $_View;

	/**
	 * @var array of fieldname=>callback settings to set controller variables
	 * 	eg. array('User'=>function($id) { return User::serve($id); }) means $Controller->User to be set by calling the lambda
	 */
	public static $autoParams = array();

	/**
	 * @return static
	 */
	protected static function _instance() {
		return static::serve();
	}

	/**
	 * I return layout filename, no defaulting
	 * @return string
	 * @todo add template path handling and possibly layout defaulting
	 */
	protected function _getTemplateFname() {
		return empty($this->_layout) ? null : 'Controller' . '/' . $this->_layout;
	}

	/**
	 * I apply template if $this->_layout is set
	 */
	protected function _applyTemplate() {
		if (!is_null($templateFname = $this->_getTemplateFname())) {
			$this->_View = \View::build($templateFname);
			$this->_View->setFnameExtension('.html');
			$data = array_merge($this->_viewData, array(
				'content' => $this->_Response->getContent(),
			));
			$this->_View->assign($data);
			$this->response($this->_View);
		}
	}

	/**
	 * calling an action by this invoke is necessary to perform _before() and _after() and to avoid scope problems with
	 * 		protected methods (which are reachable only ba rerouting)
	 * @param Route $Route it contains everything needed
	 * @return mixed
	 */
	public function invoke(\Route $Route) {
		try {

			$this->_Route = $Route;

			// maybe this is only useful for URL routing...?
			$this->_setAutoParams($Route->autoParams);

			$methodReflection = new \ReflectionMethod($this, $Route->actionMethodName);
			$numberOfParameters = $methodReflection->getNumberOfParameters();
			if ($numberOfParameters) {
				$methodParams = array_slice($Route->paramParts, 0, $numberOfParameters);
				$Route->paramParts = array_slice($Route->paramParts, $numberOfParameters);
			}
			else {
				$methodParams = $Route->paramParts;
			}

			call_user_func_array(array($this, '_before'), $methodParams);

			$content = call_user_func_array(array($this, $Route->actionMethodName), $methodParams);
			if (!is_null($content)) {
				$this->response($content);
			}

			call_user_func_array(array($this, '_after'), $methodParams);

		}
		catch (\Exception $e) {
			// @todo implement a 500 throw here
			throw $e;
		}
		return $content;
	}

	/**
	 * I set autoparams, which were analized by routing
	 * @param array $autoParams
	 */
	protected function _setAutoParams($autoParams) {
		foreach ($autoParams as $eachClassname=>$eachValues) {
			foreach ($eachValues as $eachParamName=>$eachValue) {
				// the callback to call comes from the class's static $autoParams var
				$callback = $eachClassname::$autoParams[$eachParamName];
				if (is_null($callback)) {
					$this->$eachParamName = $eachValue;
				}
				elseif (is_string($callback) || (is_array($callback) && (count($callback)==2))) {
					$this->$eachParamName = $x = call_user_func($callback, $eachValue);
				}
				else {
					throw new UnimplementedException();
				}
			}
		}
	}

	/**
	 * invoke() executes me before the main action
	 * @param array $methodParams same params as main action receives
	 */
	protected function _before() {}

	/**
	 * invoke() executes me after the main action.
	 * @param array $methodParams same params as main action receives
	 */
	protected function _after() {
		$this->_applyTemplate();
	}

	/**
	 * I set my response's content
	 * @param mixed $content
	 * @return $this
	 */
	public function response($content) {
		$this->_Response->setContent($content);
		return $this;
	}

	/**
	 * I return current response object
	 * @throws \RuntimeException if response object is empty
	 * @return Response
	 */
	public function getResponse() {
		if (is_null($this->_Response)) {
			throw new \RuntimeException();
		}
		return $this->_Response;
	}

	/**
	 * I set response object
	 * @param Response $Response
	 * @return $this
	 */
	public function setResponse(\Response $Response) {
		$this->_Response = $Response;
		return $this;
	}

	/**
	 * I just set request object
	 * @param \Request $Request
	 * @return $this
	 */
	public function setRequest(\Request $Request) {
		$this->_Request = $Request;
		return $this;
	}

	/**
	 * I return a layout param or null. Might come handy if layout has an object
	 * @param string $key
	 * @return null|mixed null if param is not set, otherwise param value
	 */
	public function getViewData($key) {
		return (isset($this->_viewData[$key]) ? $this->_viewData[$key] : null);
	}
	/**
	 * I set a layout param. Layout is applied to generated content
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function setViewData($key, $value) {
		$this->_viewData[$key] = $value;
		return $this;
	}

}
