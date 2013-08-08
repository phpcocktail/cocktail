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
 * Model tool, to instanciate and measure models
 * eg.:
 *	./shake.php mix model UserWeb -s email no@ema.il -m
 *		(tells how many UserWeb models you could create with setting just login field, and tells how many of these can be done in a sec)
 * @author t
 * @package Cocktail\Controller
 * @version 1.01
 */
class ShakeMixModel extends \ShakeMix {

	/**
	 * @var int I keep track of how many objects were created
	 */
	protected static $_modelCnt = 0;

	public $Models = array();

	/**
	 * I will print an info after every increment number of objects instanciated
	 * -i
	 * @var int
	 */
	protected $_paramInc = 1000;

	/**
	 * I will set these values into each model, eg. -s email no@ema.il
	 * -s
	 * @var array[string]string
	 */
	protected $_paramSet = array();

	/**
	 * I will instanciate these src
	 * -c
	 * @var string
	 */
	protected $_paramClassname = false;

	/**
	 * if true, I will print timing info as well (to measure the cost of setting a param, compared to not setting)
	 * -m
	 * @var bool
	 */
	protected $_paramMeasuretime = false;

	/**
	 * if specified, I will just print what I'd do but won't do it actually
	 * -t
	 * @var bool
	 */
	protected $_paramTestMode = false;


	/**
	 * I open _ationHelp() to generate automatic help
	 * @return mixed
	 */
	public function actionAllHelp() {
		return parent::_actionAllHelp();
	}

	/**
	 * I create objects and print info on how many of them can be created.
	 * @return string|void
	 * @throws \RuntimeException
	 * @throws \UnimplementedException
	 */
	public function actionIndex() {

		$args = func_get_args();
		while (count($args)) {
			$shiftParams = 1;
			switch($args[0]) {
				case '--classname':
				case '-c':
					$this->_paramClassname = @$args[1];
					$shiftParams++;
					break;
				case '--increment':
				case '-i':
					$this->_paramInc = @$args[1];
					$shiftParams++;
					break;
				case '--measuretime':
				case '-m':
					$this->_paramMeasuretime = true;
					break;
				case '--testmode':
				case '-t':
					$this->_paramTestMode = true;
					break;
				case '--set':
				case '-s':
					$key = @$args[1];
					$val = @$args[2];
					if (is_numeric($val)) {
						$val = $val == intval($val) ? intval($val) : (float)$val;
					}
					if (empty($key) || empty($val)) {
						throw new \RuntimeException('--set requires 2 parameters, key and value');
					}
					$this->_paramSet[$key] = $val;
					$shiftParams+=2;
					break;
				default:
					if (!empty($this->_paramClassname)) {
						throw new \RuntimeException('unknown option: ' . $args[0]);
					}
					$this->_paramClassname = $args[0];
			}
			while($shiftParams--) {
				array_shift($args);
			}
		}

		if (empty($this->_paramClassname)) {
			throw new \RuntimeException('no classname specified');
		}

		if (!class_exists($this->_paramClassname)) {
			throw new \RuntimeException('class ' . $this->_paramClassname . ' does not exist');
		}

		// setup vars
		$withGet = method_exists($this->_paramClassname, 'get') ? true : false;
		$classname = $this->_paramClassname;

		// testmode
		if ($this->_paramTestMode) {
			$Model = $withGet
					? $classname::get()
					: new $classname();
			foreach ($this->_paramSet as $eachKey=>$eachVal) {
				$Model->$key = $val;
			}
			if (function_exists('echop')) {
				echop($Model);
			}
			else {
				var_dump($Model);
			}
			die("\n\n");
		}

		$i=0; $j=0;

		$t0 = microtime(true);

		while (++$i) {
			$Model = $withGet
					? $classname::get()
					: new $classname();
			foreach ($this->_paramSet as $eachKey=>$eachVal) {
				$method = 'set' . ucfirst($key);
				if(0) $Model->$key = $val; else $Model->$method($val);
			}
			$this->Models[] = $Model;
			if (($x = intval($i/$this->_paramInc)) > $j) {
				$tdiff = microtime(true) - $t0;
				echo('You could create ' . count($this->Models) . ' instances' .
						(count($this->_paramSet) ? ' with setting ' . count($this->_paramSet) . ' params' : '') .
						($this->_paramMeasuretime ? ' in ' . sprintf('%.2f', ($tdiff))  . 's (' . intval(count($this->Models)/$tdiff) . '/s)' : '') .
						"\n");
				$j = $x;
			}
		}

		die('run on: ' . $this->_paramClassname);
	}

}
