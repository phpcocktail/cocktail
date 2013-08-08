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
<?php if ($containerElement): ?>
<<?= $containerElement ?> class="unstyled cl-block-container<?= empty($containerClass) ? '' : ' ' . $containerClass ?>">
<?php endif; ?>

	<?php foreach ($blocks as $eachBlock): ?>
	<?php if ($blockWrapperElement): ?>
	<<?= $blockWrapperElement ?> <?= empty($blockWrapperClass) ? '' : 'class="'.$blockWrapperClass . '" ' ?>>
		<?= \b::b($eachBlock, 2) ?>
	</<?= $blockWrapperElement ?>>
	<?php else: ?>
	<?= \b::b($eachBlock, 1) ?>
	<?php endif; ?>
	<?php endforeach; ?>

<?php if($containerElement): ?>
</<?= $containerElement ?>>
<?php endif; ?>