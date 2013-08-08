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
/** @var $modules array */
?>

<?php if(empty($modules)): ?>
NO modules found
<?php else: ?>

Modules found:

<?php foreach ($modules as $eachNamespace => $eachModules): ?>
NAMESPACE: <?= $eachNamespace ?>

MODULES:
	<?php foreach ($eachModules as $eachModule=>$moduleInfo): ?>
	<?= $eachModule ?>

		<?php if (!empty($moduleInfo['readme'])): ?>

		READ.ME:<?= \b::b($moduleInfo['readme'], 2) ?>
		<?php endif; ?>
		<?php if (!empty($moduleInfo['config'])): ?>

		CONFIG:<?= \b::b(\b::stripHeadComment($moduleInfo['config']), 2) ?>
		<?php endif; ?>
		<?php if (!empty($moduleInfo['files'])): ?>

		FILES:<?= \b::b(
			static::_include(array('data' => $moduleInfo['files']), 'Util/Files/FolderStructure/console'), 2
		);

		endif; ?>
	<?php endforeach; ?>
<?php endforeach; ?>
<?php endif; ?>
