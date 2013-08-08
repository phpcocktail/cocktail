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
 * @package Cocktail\View
 * @version 1.01
 */
class ViewSmarty extends \ViewAbstract {

	/**
	 * @var string default extension to apply to template names when generated
	 */
	protected static $_templateFnameExtension = '.php';
	/**
	 * @var array[string]mixed I store datas sent in _render in this so included templates can access as well
	 *	note the data is not stacked. It gets extracted before
	 */
	protected static $_currentData = array();
	/**
	 * @var array[]array I push some template info into this array for template inclusions
	 */
	protected static $_templateDatas = array();


}
