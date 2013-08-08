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
 */ ?>
<?php foreach ($cssFiles as $eachCssFile): ?>
	<link rel="stylesheet" href="<?= $eachCssFile['href'] . $eachCssFile['versionString'] ?>" <?php if (isset($eachCssFile['media'])): ?>media="<?= $eachCssFile['media'] ?>"<?php endif; ?> />
<?php endforeach; ?>

<?php foreach ($jsFiles as $eachJsFile): ?>
	<script <?php if (!is_null($eachJsFile['async']) && $eachJsFile['async']): ?>async<?php endif; ?> src="<?= $eachJsFile['src']?>" ></script>
<?php endforeach; ?>

<?php if (!empty($cssHead)): ?>
	<style>
		<?= $cssHead ?>
	</style>
<?php endif; ?>

<?php if(0): ?>
<?php if (!empty($jsCodes['head']) || !empty($jsCodes['onLoad'])): ?>
	<script>
		<?php if (!empty($jsCodes['onLoad'])): ?>
		$(function() {
			<?= $jsCodes['onLoad'] ?>
		});
		<?php endif; ?>
		<?= empty($jsCodes['head']) ? '' : $jsCodes['head'] ?>
	</script>
<?php endif; ?>
<?php endif; ?>
