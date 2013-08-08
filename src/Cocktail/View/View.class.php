<?php
/**
 * This file is part of PhpCocktail. PhpCocktail is free software: you can redistribute it and/or modify it under the
 * 		terms of the GNU Lesser General Public License as published by the Free Software Foundation, either version 3
 * 		of the License, or (at your option) any later version.
 * PhpCocktail is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * 		warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * 		more details. You should have received a copy of the GNU Lesser General Public License along with PhpCocktail.
 * 		If not, see <http://www.gnu.org/licenses/>.
 * @author t
 * @since 1.01
 * @license LGPL
 * @copyright Copyright 2013 t
 */
namespace Cocktail;

/**
 * @author t
 * @package Cocktail\View
 * @version 1.01
 */
class View {

	protected $_templateName;
	protected $_viewClassname;
	protected $_data = array();
	protected $_fnameExtension;

	/**
	 * @var array[] @see _getContainerConfig()
	 */
	protected static $_containerConfigCache = array();



	/**
	 * @param null $templateName
	 * @param null $data
	 * @return static
	 */
	public static function get($templateName=null, $data=null) {
		$View = new static;
		if (!empty($templateName)) {
			$View->_templateName = $templateName;
		}
		if (!empty($data)) {
			$View->_data = $data;
		}
		return $View;
	}

	/**
	 * @param string $fnameExtension I set the primary extension which shall depend on desired output type, eg. ???
	 */
	public function setFnameExtension($fnameExtension) {
		$this->_fnameExtension = $fnameExtension;
	}

	public function assign($keyOrData, $value=null) {
		if (func_num_args() == 2) {
			$this->_data[$keyOrData] = $value;
		}
		else {
			$this->_data = array_merge($this->_data, $keyOrData);
		}
		return $this;
	}

	public function clear() {
		$this->_data = array();
		return $this;
	}


	/**
	 * I return a cached and verified slice of the config in $container.View config key
	 * @param $container
	 * @return null|array null if no View config section or if its invalid, otherwise array of checked config values.
	 * 		see code for array definition
	 */
	protected function _getContainerConfig($container) {

		if (!array_key_exists($container, static::$_containerConfigCache)) {

			$config = null;

			$viewConfig = \Camarera::conf('View', $container);

			if (empty($viewConfig)) {
				goto finish;
			}

			$templates = \Camarera::conf('View.templates', $container);
			if (!empty($templates) && !is_array($templates)) {
				\Camarera::log(
					\Camarera::LOG_NOTICE,
					'invalid "templates" setting (should be array if not empty) in config container: ' . $container
				);
			}

			$templatePath = \Camarera::conf('View.templatePath', $container);
			if ($templatePath === \Camarera::CONF_AUTO) {
				$localRoot = \Camarera::conf($container.'.localRoot', $container);
				$templatePath = $localRoot . '/templates';
			}

			if (empty($templatePath) || !is_string($templatePath)) {
				\Camarera::log(
					\Camarera::LOG_NOTICE,
					'invalid or missing "containerpath" value in config container: ' . $container
				);
				goto finish;
			};

			$viewClassname = \Camarera::conf('View.classname', $container);
			if (empty($viewClassname) || !is_string($viewClassname) || !class_exists($viewClassname)) {
				\Camarera::log(
					\Camarera::LOG_NOTICE,
					'invalid or missing "classname" value in config container: ' . $container
				);
				goto finish;
			}

			$fnameExtension = $viewClassname::getTemplateFnameExension();

			// this is the return format
			$config = array(
				'templates' => $templates,
				'templatePath' => $templatePath,
				'viewClassname' => $viewClassname,
				'fnameExtension' => $fnameExtension,
			);

			finish:

			static::$_containerConfigCache[$container] = $config;

		}

		return static::$_containerConfigCache[$container];

	}

	/**
	 * I try to find a template file and tell config info about it
	 */
	protected function _findTemplate($templateName) {

		$templateFname = $templateName . $this->_fnameExtension;
		// I'll build an array of possible template filenames and their view class
		$templateFnames = array();
		// a secondary array, if fnameExtension specific template was not found
		$templateFnamesFallback = array();


		$containers = \Camarera::confContainers();

		foreach ($containers as $eachContainer) {

			$containerConfig = $this->_getContainerConfig($eachContainer);

			if (empty($containerConfig)) {
				continue;
			}

			if (!empty($containerConfig['templates'])) {
				if (!in_array($templateFname, $containerConfig['templates'])) {
					continue;
				}
			}

			if (!empty($this->_fnameExtension)) {
				$templateFnames[] = array(
					'templateName' => $templateFname . $containerConfig['fnameExtension'],
					'templatePath' => $containerConfig['templatePath'],
					'fname' => $containerConfig['templatePath'] . '/' . $templateFname . $containerConfig['fnameExtension'],
					'viewClassname' => $containerConfig['viewClassname'],
				);
				$templateFnamesFallback[] = array(
					'templateName' => $templateName . $containerConfig['fnameExtension'],
					'templatePath' => $containerConfig['templatePath'],
					'fname' => $containerConfig['templatePath'] . '/' . $templateName . $containerConfig['fnameExtension'],
					'viewClassname' => $containerConfig['viewClassname'],
				);
			}
			else {
				$templateFnames[] = array(
					'templateName' => $templateName . $containerConfig['fnameExtension'],
					'templatePath' => $containerConfig['templatePath'],
					'fname' => $containerConfig['templatePath'] . '/' . $templateName . $containerConfig['fnameExtension'],
					'viewClassname' => $containerConfig['viewClassname'],
				);
			}
		}

		foreach ($templateFnames as $eachTemplateFname) {
			if (file_exists($eachTemplateFname['fname'])) {
				return $eachTemplateFname;
			}
		}

		foreach ($templateFnamesFallback as $eachTemplateFname) {
			if (file_exists($eachTemplateFname['fname'])) {
				return $eachTemplateFname;
			}
		}

		$msg = 'TEMPLATE NOT FOUND, LOOKED IN: ' . print_r($templateFnames, 1) . ' AND FALLBACK: ' . print_r($templateFnamesFallback, 1);
		\Camarera::log(\Camarera::LOG_NOTICE, $msg);
		echo ($msg);
		return null;

	}

	/**
	 * I render a template with data. both of them can be pre-set or sent in param.
	 * @param string|array|null $templateNameOrData is string, $templateName, if array, $data
	 * @param null|array $data
	 * @param bool $mergeData if nor true, only actual sent data will be used in the render, otherwise merged with
	 * 		data assigned already
	 * @return string
	 * @throws \Exception
	 */
	public function render($templateNameOrData=null, $data=null, $mergeData=true) {

		if (!is_null($templateNameOrData) && !is_null($data)) {
			$templateName = $templateNameOrData;
			$data = $mergeData ? array_merge($data, $this->_data) : $data;
		}
		elseif (!is_null($templateNameOrData)) {
			if (is_string($templateNameOrData)) {
				$templateName = $templateNameOrData;
				$data = $this->_data;
			}
			elseif (is_array($templateNameOrData)) {
				$templateName = $this->_templateName;
				$data = $mergeData ? array_merge($this->_data, $templateNameOrData) : $templateNameOrData;
			}
			else {
				throw new \Exception('invalid $templateNameOrData param');
			}
		}
		else {
			$templateName = $this->_templateName;
			$data = $this->_data;
		}

		$templateConfig = $this->_findTemplate($templateName);
		if (empty($templateConfig)) {
			return '';
		}

		$viewClassname = $templateConfig['viewClassname'];
		$View = $viewClassname::get();
		$ret = $View->render($templateConfig['templatePath'], $templateConfig['templateName'], $data);

		return $ret;

	}

	/**
	 * this shall be triggered at final render time only eg. $Response->send()
	 * @return string
	 */
	public function toString() {
		return $this->render();
	}

}
