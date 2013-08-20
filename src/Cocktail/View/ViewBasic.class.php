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
class ViewBasic extends \ViewAbstract {

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

	/**
	 * I render a template with the given data
	 * @param array[string]mixed $__data data to use. will be extracted to current scope
	 * @param string $__template full template filename
	 * @param string $__templatePath path to local template folder
	 * @return string the output
	 */
	protected static function _render($__data, $__template, $__templatePath) {

		// I push template data so _include() _includeTemplate() _includeParent() can be aware
		array_push(static::$_templateDatas, array(
			'__template' => $__template,
			'__templatePath' => $__templatePath,
		));

		$templateFname = $__templatePath . '/' . $__template;

		if (!is_null($__data)) {
			static::$_currentData = $__data;
		}
		else {
			$__data = static::$_currentData;
		}

		extract($__data);
		ob_start();
		require($templateFname);
		$output = ob_get_contents();
		ob_end_clean();
		// why this array_pop??? may it be leftover useless code?
//		array_pop(static::$_currentData);
		array_pop(static::$_templateDatas);
		return $output;
	}

	/**
	 * I include an arbitary template with arbitary data. To be called from the templates themselves.
	 * @param array[string]mixed $__data for the template
	 * @param string $__template template name or full filepath
	 * @param string $__templatePath local template base path, not necessary if $__template is a full tilename
	 * @return string the output
	 */
	protected static function _include($__data, $__template, $__templatePath=null) {
#debug_print_backtrace();
#die('FUP');
		if (empty($__templatePath)) {
			$templateData = end(static::$_templateDatas);
			$__templatePath = $templateData['__templatePath'];
		}
		$ret = static::_render($__data, $__template . static::$_templateFnameExtension, $__templatePath);
		return $ret;
	}

	/**
	 * I include another template, rendered with current data. Ideal for including arbitrary parent templates or to use
	 * 	one out of the box. The included template will have the same data availble as the caller template. To be called
	 *	from within the templates themsleves.
	 * @param string $template template name or full filepath
	 * @return string the output
	 */
	protected static function _includeTemplate($template) {
		$templateData = end(static::$_templateDatas);
		return static::_render(null, $template, $templateData['__templatePath']);
	}

	/**
	 * auto-guess the parent template and inlcude it. The included template will have the same data availble as the
	 *	caller template. Shall be called from within the templates themselves
	 * @return string the output
	 */
	protected static function _includeParent() {

		// I've pushed data in the $_currentData property to use in recursive rendering
		$templateData = end(static::$_templateDatas);

		$parentTemplate = null;
		if (preg_match('/(.+)(\/[A-Z].+)\/([A-Z].+)?([A-Z][a-z]+)(.*)$/', $templateData['__template'], $matches)) {
			$parentTemplate = $matches[1] . '/' . $matches[3] . $matches[5];
		}

		$ret = $parentTemplate
			? static::_render(null, $parentTemplate, $templateData['__templatePath'])
			: null;
		return $ret;
	}

	/**
	 * I am the standard content generator method. actually I just proxy to _render which has reverse order params
	 * @param string $templatePath the absolute base path of templates in which current resides. Needed by inline
	 * 		template inclusion. @see _include()
	 * @param string $templateName path to template file, relative to template path
	 * @param array $data of key-value pairs
	 * @return string the rendered template
	 */
	public function render($templatePath, $templateName, $data) {
		return static::_render($data, $templateName, $templatePath);
	}

}
