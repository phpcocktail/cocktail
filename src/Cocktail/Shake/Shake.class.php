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
 * Base controller class for built-in console tool. Extend this to create shell apps.
 * It has built in functionality to handle command lines params and mapping them to object properties. Eg. define the
 * 		property $_paramFoo and access it via {shake} --foo
 * 		Also you can define a shorthand param name in phpdoc just add eg. '-q' on a line in the php docblock
 * 		Different property types are handled differently. You should define a var type in phpdoc, one of:
 * 		* @var bool - assign the param a default value true or false, and command line param sets it to its counterpart
 * 		* @var string - assign the next param to the property, eg. --foo bar
 * 		* @var number - alias for string
 * 		* @var int - like string, but value will be intval'ed
 * 		* @var float - like string, but value will be floatval'ed
 * 		note that if a setter method named eg. _setParamFoo() is found in the class, it will be used. Also, these
 * 		setters are called with the default property values upon startup.
 * 		There are some predefined param setters, define their param as the commented ones to take advantage of them. You
 * 		can also override them, to use a different default value, but don't forget to copy the docblock as well.
 *
 * @package Cocktail\Shake
 * @author t
 * @version 1.01
 */
class Shake extends \Controller {

	/**
	 * path to start at
	 * -i
	 * @var string
	 */
	protected $_paramStartPath = '';

	/**
	 * target folder path to filename (single file mode)
	 * -o
	 * @var string
	 */
	protected $_paramOutPath = '';

	/**
	 * if specified, existing files will be overwritten
	 * -f
	 * @var bool
	 */
	protected $_paramOverWrite = false;

	/**
	 * if specified, I will just print what I'd do but won't do it actually
	 * -t
	 * @var bool
	 */
	protected $_paramTestMode = false;

	/**
	 * if true, verbose mode is on. Verbose mode just adds a logger that listens to verbose level messages.
	 * -v
	 * @var bool
	 */
	protected $_paramVerbose = false;

	/**
	 * I validate  startPath and set it
	 * @param string $startPath I take absolute and relative paths as well but will convert relatives, thus store only
	 * 		absolute paths
	 * @throws \RuntimeException
	 * @return $this
	 */
	protected function _setParamStartPath($startPath) {
		if (is_null($startPath)) {
			throw new \RuntimeException('startPath is empty');
		}
		$realPath = substr($startPath, 0, 1) == '/'
			? $startPath
			: realpath('./' . trim($startPath, '/'));
		$this->_paramStartPath = $realPath;
		return $this;
	}

	/**
	 * I validate outPath and set it
	 * @param string $outPath
	 * @return $this
	 * @throws \RuntimeException
	 */
	protected function _setParamOutPath($outPath) {
		if (is_null($outPath)) {
			throw new \RuntimeException('outPath is empty');
		}
		$realPath = substr($outPath, 0, 1) == '/'
			? trim($outPath, '/')
			: './' . trim($outPath, '/');
		$this->_paramOutPath = $realPath;
		return $this;
	}

	/**
	 * I set verbose mode if not yet set
	 * @param $verbose
	 * @return $this
	 */
	protected function _setParamVerbose($verbose) {
		if ($verbose) {
			\Camarera::registerLogger(
					'Verbose logger',
					\Camarera::LOG_INFORMATIONAL,
					function($log) {
						echo $log . "\n";
					}
			);
			$this->_verboseRegistered = true;
		}
		return $this;
	}

	/**
	 * I return an array of available command line params, based on class property definitions
	 * @return array
	 */
	protected function _getParams() {
		$rClass = new \ReflectionClass($this);
		$properties = $rClass->getProperties();
		foreach ($properties as $eachPropertyKey=>$eachProperty) {
			if (strpos($eachProperty->getName(), '_param') !== 0) {
				unset($properties[$eachPropertyKey]);
			}
		}

		$params = array();
		foreach ($properties as $eachProperty) {
			$el = array(
				'name' => lcfirst(substr($eachProperty->getName(), 6)),
				'shortName' => '',
				'propertyName' => $eachProperty->getName(),
				'dataType' => '',
				'defaultValue' => $this->{$eachProperty->getName()},
				'doc' => '',
			);
			$comment = explode("\n", $eachProperty->getDocComment());
			foreach ($comment as $eachLine) {
				// * @var {type}
				if (in_array(trim($eachLine), array('/**', '*/')));
				elseif (preg_match('/^\s*\*\s+@var\s+(\w+)/', $eachLine, $matches)) {
					$el['dataType'] = $matches[1];
				}
				elseif (preg_match('/^\s*\*\s+\-([a-z0-9]+)\s*$/', $eachLine, $matches)) {
					$el['shortName'] = $matches[1];
				}
				else {
					$el['doc'].= ltrim(trim($eachLine), '*') . "\n";
				}
			}
			$params[] = $el;
		}
		return $params;
	}

	/**
	 * I apply values from command line to available params
	 */
	protected function _setup() {

		$params = $this->_getParams();

		// search for setter method and call it if exists (to init)
		foreach ($params as $eachParam) {
			$setterName = '_setParam' . ucfirst($eachParam['name']);
			if (method_exists($this, $setterName)) {
				$this->$setterName($this->{$eachParam['propertyName']});
			}
		}

		$args = func_get_args();

		// apply command line args
		while (count($args)) {
			$shiftParams = 1;
			foreach ($params as $eachParam) {
				if (($args[0] == '-' . $eachParam['shortName']) ||
					($args[0] == '--' . $eachParam['name'])) {

					switch($eachParam['dataType']) {
						case 'bool':
						case 'boolean':
							$val = !$this->{$eachParam['propertyName']};
							break;
						case 'int':
							$val = intval(@$args[1]);
							$shiftParams++;
							break;
						case 'float':
							$val = floatval(@$args[1]);
							$shiftParams++;
							break;
						case 'string':
						case 'number':
						default:
							$val = @$args[1];
							$shiftParams++;
							break;
					}

					$setterName = '_setParam' . ucfirst($eachParam['name']);
					if (method_exists($this, $setterName)) {
						$this->$setterName($val);

					}
					else {
						$this->{$eachParam['propertyName']} = $val;
					}
					break;
				}
				else {
					\Camarera::log('invalid param: ' . $args[0], \Camarera::LOG_WARNING);
				}
			}
			while($shiftParams--) {
				array_shift($args);
			}
		}

		return $this;

	}

	/**
	 * calls parent::_before() then _setup() with the same params as this _before() was called with
	 */
	protected function _before() {
		$args = func_get_args();
		call_user_func_array('parent::_before', $args);
		call_user_func_array(array($this, '_setup'), $args);
		$this->_sendHeadline();
	}

	/**
	 * I catch any exception and output its message
	 * @param \Route $route
	 * @return mixed|string
	 */
	public function invoke(\Route $Route) {
		try {
			return parent::invoke($Route);
		}
		catch (\Exception $e) {
			\Camarera::log(\Camarera::LOG_NOTICE, print_r($e,1));
			$this->_Response->addContent('FATAL: ' . $e->getMessage() . "\n\n");
			return '';
		}
	}

	/**
	 * I display dinamic help based on class definition
	 * define a public actionAllHelp() in your task to make it available, and see ShakeMixClasslist for example and doc
	 */
	protected function _actionAllHelp() {

		if (count($this->_Route->paramParts)) {
			$this->_Response->addContent('Cannot get help like this.' . "\n\n");
			if (count($this->_Route->paramParts) == 1) {
				$this->_Response
					->addContent('Maybe you want to try: "' . $this->_Route->paramParts[0] . ' help" instead'."\n\n");
			}
			return null;
		}

		$rClass = new \ReflectionClass($this);

		$classComment = explode("\n", $rClass->getDocComment());
		foreach($classComment as $eachKey=>$eachLine) {
			if (preg_match('|^\s*\*\s+\@\w+|', $eachLine) ||
				preg_match('|^\s*/\*\*|', $eachLine) ||
				preg_match('|\s*\*/|', $eachLine)) {
				unset($classComment[$eachKey]);
			}
			else {
				$classComment[$eachKey] = trim(ltrim(trim($eachLine),'*'));
			}
		}

		$subCommands = array();
		$maxSubCommandLength = 0;
		$methods = $rClass->getMethods();
		foreach ($methods as $EachMethod) {
			if ($EachMethod->class != $rClass->name) {
				continue;
			}
			elseif(strtolower($EachMethod->name) == 'actionindex') {
				$indexDocComment = explode("\n", $EachMethod->getDocComment());
				$indexDocComment = trim($indexDocComment[1]);
				$subCommands = array_reverse($subCommands);
				$subCommands[''] = $indexDocComment;
				$subCommands = array_reverse($subCommands);
				continue;
			}
			elseif (!preg_match('/^action(all|console)(\w+)$/', strtolower($EachMethod->name), $matches)) {
				continue;
			}
			elseif($matches[2] == 'help') {
				continue;
			}
			$docComment = explode("\n", $EachMethod->getDocComment());
			$subCommands[$matches[2]] = trim($docComment[1]);
			$maxSubCommandLength = max($maxSubCommandLength, strlen($matches[2]));
		}

		$paramUsage = array();
		$maxKeyLength = 0;
		$properties = $rClass->getProperties();
		foreach ($properties as $EachProperty) {
			$paramType = '';
			if ($EachProperty->class != get_class($this)) {
				continue;
			}
			elseif (!preg_match('|^_param|', $EachProperty->name)) {
				continue;
			}
			$docComment = explode("\n", $EachProperty->getDocComment());
			$paramNames = array('--' . strtolower(substr($EachProperty->getName(), 6)));
			foreach ($docComment as $eachLine) {
				if (preg_match('|\*\s+(-\w+)\s?$|', $eachLine, $matches)) {
					$paramNames[] = strtolower($matches[1]);
				}
				elseif (preg_match('|\s*\*\s+@var\s+array\[|i', $eachLine, $matches)) {
					$paramType = 'set';
				}
				elseif (preg_match('|\s*\*\s+@var\s+(\w+)|', $eachLine, $matches)) {
					$paramType = strtolower($matches[1]);
				}
			}
			usort($paramNames, function($a, $b) {
				return strlen($a) - strlen($b);
			});
			$key = $paramNames[0] .
				(count($paramNames)<=1 ? '' : ' [' . implode('|', array_slice($paramNames, 1)) . ']');
			switch($paramType) {
				case 'string':
					$key.= ' <value>';
					break;
				case 'array':
					$key.= ' <arrayitem>';
					break;
				case 'set':
					$key.= ' <key> <val>';
					break;
				case 'int':
					$key.= ' <number>';
					break;
				case 'bool':
				default:
					break;
			}
			$val =
				(empty($docComment[1]) ? '???' : trim(trim(trim($docComment[1]), '*')));

			$paramUsage[$key] = $val;
			$maxKeyLength = max($maxKeyLength, strlen($key));
		}

		$command = array_pop(explode('\\', get_class($this)));
		if (substr($command, 0, 8) == 'ShakeMix') {
			$command = substr($command, 8);
		}
		$command = explode('/', trim(strtolower(\Util::camelcaseToSlashes($command)),'/'));

		$content = \View::get('Shake/autohelp', array(
			'classComment' => implode("\n", $classComment),
			'command' => $command,
			'subCommands' => $subCommands,
			'maxSubCommandLength' => $maxSubCommandLength,
			'paramUsage' => $paramUsage,
			'maxKeyLength' => $maxKeyLength,
		));

		return $content;
	}

	/**
	 * I send the default headline for the Shake tool
	 */
	protected function _sendHeadline() {
		$headline = \View::get(
			'Shake/headline',
			array(
				'version' => \Camarera::conf('Cocktail.version'),
			)
		);
		$this->_Response->addContent($headline);
		return $this;
	}

}
