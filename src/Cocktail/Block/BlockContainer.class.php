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
class BlockContainer extends \Block {

	public $containerElement = 'ul';
	public $containerClass = '';

	public $blockWrapperElement = 'li';
	public $blockWrapperClass = '';

	protected $_blocks = array();

	/**
	 * I return a $_viewData field, whole $_viewData, or $_viewData merged with $_blocks (for eg mvc routing)
	 * @param true|string|null if true, I return merged all, otherwise see parent's doc
	 * @see \Block::getViewData()
	 */
	public function getViewData($key=null) {
		if ($key === true) {
			return array_merge($this->_viewData, $this->_blocks);
		}
		return parent::getViewData($key);
	}

	public function append(Block $block, $id=null) {
		if ((func_num_args() == 2) && !empty($id)) {
			$this->_blocks[$id] = $block;
		}
		else {
			$this->_blocks[] = $block;
		}
		return $this;
	}
	public function prepend(Block $block, $id=null) {
		if (is_null($id)) {
			array_unshift($this->_blocks, $block);
		}
		else {
			$blocks = array_reverse($this->_blocks);
			$blocks[$id] = $block;
			$this->_blocks = array_reverse($blocks);
		}
		return $this;
	}

	/**
	 * I add some params to basic block view
	 * @see \Block::_generate()
	 * @return \View
	 */
	protected function _generate() {
		$View = parent::_generate()
			->assign('containerElement', $this->containerElement)
			->assign('containerClass', $this->containerClass)
			->assign('blocks', $this->_blocks)
			->assign('blockWrapperElement', $this->blockWrapperElement)
			->assign('blockWrapperClass', $this->blockWrapperClass)
		;
		return $View;
	}

}
