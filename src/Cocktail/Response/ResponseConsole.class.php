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
 * simple response for console apps
 * @author t
 * @package Cocktail\Response
 * @version 1.01
 *
 * @property \ResponseConsoleConfig $_Config
 * @property-read \ResponseConsoleConfig $Config
 */
class ResponseConsole extends \Response {

	/**
	 * I send agregated content. Note content will be usually empty for autoFlushsince=true (in config)
	 *  (because addContent() should be used for output and that considers autoFlush already)
	 */
	public function send() {
		echo $this->getContent();
	}

	/**
	 * I echo or buffer some response
	 * @param mixed $content anything that \Camarera\String::toString accepts
	 * @return $this
	 */
	public function addContent($content) {
		if ($this->_Config->autoFlush) {
			echo \Util::toString($content);
			return $this;
		}
		else {
			return parent::addContent($content);
		}
	}

}
