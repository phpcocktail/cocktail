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

ini_set('display_errors', 1); error_reporting(E_ALL);

require_once('../bootstrap.php');

/**
 * unit tests currently are not compatible with strict error reporting due to method parameter compatibility problems
 */
error_reporting(E_ALL ^ E_STRICT);
ini_set('display_errors', 1);

/*
copy('assets/phpunit.ori.s3db', 'assets/phpunit.s3db');
$StoreConfig = \StoreDriverSqlite3Config::get(array(
		'database' => 'phpunit.s3db',
		'path' => 'assets',
		'tablePrefix' => 'asd_',
));
\Camarera::registerStore($StoreConfig);
*/
