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
 * @version 1.01
 * @license LGPL
 * @copyright Copyright 2013 t
 */
namespace Cocktail;

/**
 * Shake pack commands
 *
 * @author t
 * @package Cocktail\Shake
 * @version 1.01
 */
class ShakeMod extends \Shake {

	/**
	 * scan path for modules and return list of each modules found
	 * @param $path
	 */
	function _findModules($path, $options=array()) {
		\Camarera::log(
			\Camarera::LOG_INFORMATIONAL,
			'Processing vendor path: ' . $path
		);
		$Directory = dir($path);
		if (!$Directory) {
			throw new \InvalidArgumentException();
		}
		$modules = array();
		while ($eachNamespace = $Directory->read()) {
			\Camarera::log(
				\Camarera::LOG_INFORMATIONAL,
				'Processing namespace: ' . $eachNamespace
			);
			$eachPath = $path . '/' . $eachNamespace;
			if (in_array($eachNamespace, array('.', '..')));
			elseif (!is_dir($eachPath));
			elseif (!($SubDirectory = dir ($eachPath))) {
				\Camarera::log('WARNING: cannot read ' . $eachPath, \Camarera::LOG_WARNING);
			}
			else {
				$modulesInNamespace = array();
				while ($eachModule = $SubDirectory->read()) {
					\Camarera::log(\Camarera::LOG_INFORMATIONAL, 'Processing module dir: ' . $eachModule);
					$eachSubPath = $eachPath . '/' . $eachModule;
					if (in_array($eachModule, array('.', '..')));
					elseif (!is_dir($eachSubPath));
					else {
						$moduleInfo = $this->_getModuleInfo($eachSubPath, $options);
						if (!empty($moduleInfo)) {
							$modulesInNamespace[$eachModule] = $moduleInfo;
						}
					}
				}
				if (!empty($modulesInNamespace)) {
					\Camarera::log(
						\Camarera::LOG_INFORMATIONAL,
						'Modules found in ' . $eachNamespace . ': ' . implode(',', $modulesInNamespace)
					);
					$modules[$eachNamespace] = $modulesInNamespace;
				}
				else {
					\Camarera::log(
						\Camarera::LOG_INFORMATIONAL,
						'Empty namespace found: ' . $eachNamespace
					);
				}
			}
		}
		return $modules;
	}

	/**
	 * I get info about a module, based on its root path.
	 * @param $eachSubPath
	 * @param array $options keys which have value of true or false, valid keys are 'readme', 'config', 'files'
	 * @return array
	 */
	protected function _getModuleInfo($eachSubPath, $options=array()) {
		if (empty($options)) {
			$options = array(
				'readme' => true,
				'config' => true,
				'files' => true,
			);
		}
		else {
			$options['readme'] = isset($options['readme']) && $options['readme'] ? true : false;
			$options['config'] = isset($options['config']) && $options['config'] ? true : false;
			$options['files'] = isset($options['files']) && $options['files'] ? true : false;
		};

		$eachModule = substr($eachSubPath, strrpos($eachSubPath, '/')+1);

		$moduleInfo = array();
		if (!file_exists($headerFilename = $eachSubPath . '/' . $eachModule . '.php') ||
			!is_readable($headerFilename)) {
			\Camarera::log(
				\Camarera::LOG_INFORMATIONAL,
				'WARNING: missing or unreadable module header ' . $headerFilename
			);
		}
		elseif (!file_exists($configFilename = $eachSubPath . '/conf/conf.php') ||
			!is_readable($configFilename)) {
			\Camarera::log(
				\Camarera::LOG_INFORMATIONAL,
				'WARNING: missing or unreadable config file ' . $configFilename
			);
		}
		elseif (!file_exists($infoFilename = $eachSubPath . '/README.md') ||
			!is_readable($infoFilename)) {
			\Camarera::log(
				\Camarera::LOG_INFORMATIONAL,
				'WARNING: missing or unreadable info file ' . $infoFilename
			);
		}
		else {
			$moduleInfo['name'] = $eachModule;
			if ($options['readme']) {
				$moduleInfo['readme'] = file_get_contents($infoFilename);
			};
			if ($options['config']) {
				$moduleInfo['config'] = file_get_contents($configFilename);
			};
			if ($options['files']) {
				$moduleInfo['files'] = \Util::scanDir($eachSubPath, 1);
			}
		}
		return $moduleInfo;
	}

	/**
	 * @return \View I return mod help template
	 */
	public function actionAllHelp() {
		return \View::build('Shake/Mod/help');
	}

	/**
	 * @return \View I return actionAllHelp()
	 */
	public function actionAllIndex() {
		return $this->actionAllHelp();
	}

}
