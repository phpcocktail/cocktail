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
 * @package Cocktail\Application
 * @version 1.01
 */
class ApplicationHttpConfig extends \ApplicationConfig {

	/**
	 * @var string set this to the path inside the domain where the app is. Leave blank if app is in htdocs root
	 */
	public $basePath;

	/**
	 * @var string normally http applications yield html, but you may want to override this(eg. rest application)
	 */
	public $theme = 'html';

	public $applicationClassname = '\ApplicationHttp';

	public $requestClassname = '\RequestHttp';

	public $responseClassname = '\ResponseHttp';

	public $routerClassname = '\RouterFileMapper';

}
