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
 */ ?>
<?= $classComment ?>


Usage:

	shake.php mix <?= implode(" ", $command) ?> <?
	if(count($subCommands)): ?>{<?= implode('|', array_keys($subCommands)) ?>}<?endif;
	?> <options>

<?php if (count($subCommands)): ?>
Commands:

<?php foreach($subCommands as $eachSubCommand=>$eachSubCommandDescription): ?>
	<?= str_pad($eachSubCommand, $maxSubCommandLength+1) ?>	<?= $eachSubCommandDescription ?>

<?php endforeach; ?>

<?php endif; ?>
Options:
<?php foreach($paramUsage as $eachParamKey=>$eachParamval): ?>
	<?= str_pad($eachParamKey, $maxKeyLength+1) ?>   <?= $eachParamval ?>

<?php endforeach; ?>

