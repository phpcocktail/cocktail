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
class ShakeModList extends \ShakeMod {

	/**
	 * path to start searching for modules in
	 * -i
	 * @var string
	 */
	protected $_paramStartPath = 'vendor';

	/**
	 * list only modules of a specific vendor (namespace)
	 * @var string
	 * -f
	 */
	protected $_paramFilter = '';

	/**
	 * if specified, I will print info on each package
	 * -r
	 * @var bool
	 */
	protected $_paramReadme = false;

	/**
	 * list all files in module
	 * @var bool
	 * -l
	 */
	protected $_paramListfiles = false;

	/**
	 * show the current conf for the given module
	 * @var bool
	 * -c
	 */
	protected $_paramConfig = false;

	/**
	 * I open _ationHelp() to generate automatic help
	 * @return mixed
	 */
	public function actionAllHelp() {
		return parent::_actionAllHelp();
	}

	/**
	 * I generate the class list file(s)
	 * @return string|void
	 * @throws \RuntimeException
	 * @throws \UnimplementedException
	 */
	public function actionAllIndex() {

		if (!is_dir($this->_paramStartPath) || !is_readable($this->_paramStartPath)) {
			throw new \RuntimeException('startPath does not exist or is not writable');
		}

		$modules = $this->_findModules(
			$this->_paramStartPath,
			array(
				'readme' => $this->_paramReadme,
				'config' => $this->_paramConfig,
				'files' => $this->_paramListfiles,
			)
		);

		$content = \View::get('Shake/Mod/List', array('modules' => $modules));
		$this->_Response->addContent($content);

		$footer = \View::get('Shake/footer');
		$this->_Response->addContent($footer);

		die;
	}

}
