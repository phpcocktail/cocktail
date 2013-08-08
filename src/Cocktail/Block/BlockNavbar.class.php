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
 * @package Cocktail\Block
 * @version 1.01
 */
class BlockNavbar extends \BlockContainer {

	/**
	 * @var string no need for list wrapper in navbar
	 */
	public $listWrapperElement;
	/**
	 * @var string no need for block wrapper in navbar
	 */
	public $blockWrapperElement;

	/**
	 * @var boolean if true a home link with 'brand' class will be shown
	 */
	public $brandHomeLink=true;

	/**
	 * I call parent to get view, then add more params
	 * @return \View
	 */
	protected function _generate() {
		$View = parent::_generate();
		$View
			->assign('brandHomeLink', $this->brandHomeLink);
		return $View;
	}

}
