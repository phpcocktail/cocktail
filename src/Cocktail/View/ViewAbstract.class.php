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
 * @since 1.0
 * @license LGPL
 * @copyright Copyright 2013 t
 */
namespace Cocktail;

/**
 * @author t
 * @package Cocktail\View
 * @version 1.01
 */
abstract class ViewAbstract {

	/**
	 * @var string template fname, should include .html but not further extensions
	 */
	protected $_templateName;
	/**
	 * @var string loaded template, if template can be cached then this should hold the loaded template
	 */
	protected $_template;
	/**
	 * @var string overwrite this to apply a default template extension (this way all view types can have their own
	 * 			extension)
	 */
	protected static $_templateFnameExtension = '';
	/**
	 * @var string if set, I will use this instead of the application's default template path (when loading files)
	 */
	protected $_templatePath;
	/**
	 * @var array[string]mixed array of template variables
	 */
	protected $_data = array();

	/**
	 * I return a view instance
	 * @param string $templateName
	 * @param array[string]mixed $data
	 * @throws \InvalidArgumentException
	 * @return \View
	 */
	public static function build($templateName=null, $data=null) {
		/** @var \ViewAbstract $View */
		$View = new static();
		$View->_templateName = $templateName;
		if (!is_null($data)){
			if (!is_array($data)) {
				throw new \InvalidArgumentException();
			}
			$View->_data = $data;
		}
		return $View;
	}

	/**
	 * I render a template with the data given Note this method is static, it's static to separate code in the templates
	 *	as much as possible, since they may be PHP code running. This, the scope inside such templates shall not include
	 *	anything but assigned params
	 * @param array[string]mixed $data data for the template
	 * @param string template filename, should be a fill absolute path
	 * @param array[strin]mixed some system data
	 */
	abstract protected static function _render($__data, $__template, $__templatePath);

	/**
	 * I am protected, use get()
	 */
	protected function __construct() {}

	/**
	 * I set a variable, or array of variables, depending on parameter types. Wrapper for _set indeed.
	 * @param array|string $field set array of params, or a single field by this name
	 * @param string|null $value field value
	 * @throws \InvalidArgumentException
	 * @return \View
	 */
	public function assign($field, $value=null) {
		if (is_array($field) && is_null($value)) {
			$this->_data = array_merge($this->_data, $field);
		}
		elseif (is_string($field)) {
			$this->_data[$field] = $value;
		}
		else {
			throw new \InvalidArgumentException();
		}
		return $this;
	}

	/**
	 * I set a template filename to use
	 * @param string $templateName
	 * @return \View
	 */
	public function setTemplate($templateName) {
		$this->_templateName = $templateName;
		return $this;
	}

	/**
	 * I just return current filename extension
	 * @return string
	 */
	public static function getTemplateFnameExtension() {
		return static::$_templateFnameExtension;
	}

	/**
	 * I return the absolute path to the template
	 * @param string $templateName
	 * @return string
	 */
	protected static function _getTemplateFname($templateName, $templatePath=null) {
		throw new \Exception('OBS');
		// normal template path can be pre-set from the calling scope, or the application default is to be used
		$fnames = array();
		if (!empty($templatePath)) {
			$fnames[] = $templatePath . '/' . $templateName . static::$_templateFnameExtension;
		};
		$fnames[] = \Camarera::conf('.localRoot') . '/templates/' . $templateName . static::$_templateFnameExtension;
		$fnames[] = \Camarera::conf('Cocktail.localRoot') . '/templates/' . $templateName . static::$_templateFnameExtension;

		$templateNameParts = explode('/', $templateName);
		if (count($templateNameParts)) {
			$fnames[] = \Camarera::conf('Cocktail.localRoot') .
				'/vendor/phpcocktail' .
				'/' . $templateNameParts[0] .
				'/templates/' .
				implode('/', array_slice($templateNameParts, 1)) .
				static::$_templateFnameExtension
			;
		}

		$fname = null;
		$fnames = array_unique($fnames);
		foreach ($fnames as $eachFname) {
			if (file_exists($eachFname)) {
				$fname = $eachFname;
				break;
			}
		};

		if (is_null($fname)) {
			\Camarera::log(
				\Camarera::LOG_NOTICE,
				'View::_getTemplateFname NOT FOUND: ' . $templateName . ' IN (' . "\n" . implode("\n", $fnames) . "\n" . ')'
			);
			throw new \RuntimeException('template NOT FOUND: ' . $templateName . ' IN (' . "\n" . implode("\n", $fnames) . "\n" . ')');
		}
		return $fname;
	}

	/**
	 * I generate content. When implementing, take care of parameter which can be data or template name
	 * 		The idea behind this parameter is to provide ways to compile different templates same data, and same
	 * 		template, different data
	 * Note that there is no option to render just a template by preset data, since View (which shall set the data)
	 * 		doesn't know whic template engine to ue until a template is requested and found.
	 * @param string $templatePath the absolute base path of templates in which current resides. Needed by inline
	 * 		template inclusion. @see _include()
	 * @param string $templateName path to template file, relative to template path
	 * @return string the compiled template
	 */
	abstract public function render($templatePath, $templateFname, $data);

	/**
	 * I call generate() and return compiled template.
	 * @obsolete this method is now obsolete as ->toString() should be called only on the View object which is the adapter.
	 * @return string
	 */
	public function toString() {
		throw new \Exception ('this is now obsolete!');
		return $this->generate();
	}

}
