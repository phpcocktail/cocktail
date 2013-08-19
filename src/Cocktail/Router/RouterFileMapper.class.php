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
 * RouterFilemapper is the most useful automatic route mapper. It extends Cocktail\Router to avoid resolving loop
 *
 * @author t
 * @package Cocktail\Router
 * @version 1.1
 */
class RouterFileMapper extends \Cocktail\Router {

	/**
	 * I route a request - choose controller and action method, map params, and create response object based on request.
	 * @return \Route
	 */
	public function route(\Request $Request) {

		$controllerParts = $Request->routeParts;

		foreach ($controllerParts as &$eachControllerPart) {
			// I make the ucfirst so autoloading will work properly
 			$eachControllerPart = ucfirst(strtolower($eachControllerPart));
 		}

		$controllerPrefix =  \Application::instance()->Config->controllerPrefix;
		$controllerClassname = '';
		$controllerNamespace = \Application::instance()->Config->namespace;
		$actionParts = array();
		$actionMethodName = '';
		$paramParts = array();
		$autoParams = array();

		// I select as many url parts as I can, keeping that these parts when concatenated form a classname
		while(count($controllerParts)) {
			$classname = $controllerNamespace . '\\' . $controllerPrefix . implode($controllerParts);

			if (class_exists($classname)) {
				$controllerClassname = $classname;
				break;
			}
			if ($part = array_pop($controllerParts)) {
				array_unshift($actionParts, $part);
			}
		}
		if (empty($controllerClassname)) {
			$controllerClassname = $controllerNamespace . '\\' . $controllerPrefix . 'Index';
		}
		if (!class_exists($controllerClassname)) {
			throw new \RuntimeException('Controller class not found: ' . $controllerClassname);
		}

		if (property_exists($controllerClassname, 'autoParams') && count($controllerClassname::$autoParams)) {
			// @todo check here if uri still has enough parts
			$autoParamDefs = $controllerClassname::$autoParams;
			$_autoParams = array_slice($Request->routeParts, 0 - count($actionParts), count($autoParamDefs));
			$_autoParams = array_combine(
					array_keys($autoParamDefs),
					$_autoParams
			);
			$autoParams[$controllerClassname] = $_autoParams;
			$actionParts = array_slice($actionParts, count($autoParamDefs));
		}

		// @todo recursively (or at least once more) re-route without the stripped params
		while (count($actionParts)) {
			if (method_exists($controllerClassname, ($method = 'action' . ucfirst(strtolower($Request->requestMethod)) . ($actionMethodRaw = implode($actionParts))))) {
				$actionMethodMethod = strtolower($Request->requestMethod);
			}
			elseif (method_exists($controllerClassname, ($method = 'actionAll' . ($actionMethodRaw = implode($actionParts))))) {
				$actionMethodMethod = 'all';
			}
			else {
				if ($part = array_pop($actionParts)) {
					array_unshift($paramParts, $part);
				}
				continue;
			}
			$actionMethodName = $method;
			break;
		}

		// I default the action to be called to actionAllIndex() without checking if it exists. You can just omit it, especially for hmvc routers.
		if (empty($actionMethodName)) {
			if (method_exists($controllerClassname, 'actionAllIndex')) {
				$actionMethodRaw = 'Index';
				$actionMethodMethod = 'all';
				$actionMethodName = 'actionAllIndex';
			}
			elseif (method_exists($controllerClassname, 'actionIndex')) {
				$actionMethodRaw = 'Index';
				$actionMethodMethod = 'all';
				$actionMethodName = 'actionIndex';
			}
			else {
				throw new \RuntimeException;
			}
		}

		// check if reroute method is present
		$rerouteMethodName = 'reroute' . $actionMethodRaw;
		if (method_exists($controllerClassname, $rerouteMethodName)) {
			list($controllerClassname, $actionMethodName) = $controllerClassname::$rerouteMethodName();
			$actionMethodRaw = $actionMethodName;
			do {
				$actionMethodRaw = substr($actionMethodRaw, 1);
			}
			while (strlen($actionMethodRaw) && ($actionMethodRaw[0] != strtoupper($actionMethodRaw)));
		}

		// @todo if no actionIndex defined, or routing fails for other reason, what to do?

		// I re-get the params from original values because they were ucfirst'ed
		$paramParts = count($paramParts)
			? array_slice($Request->routeParts, 0 - count($paramParts))
			: array();

		return \Route::serve(array(
				'controllerClassname' => $controllerClassname,
				'actionMethodName' => $actionMethodName,
				'paramParts' => $paramParts,
				'autoParams' => $autoParams,
		));
	}

}
