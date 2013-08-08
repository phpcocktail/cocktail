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
<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">

			<?php if ($brandHomeLink): ?>

			<a class="brand" href="#">Home</a>
			<?php endif; ?>

			<?php
				/**
				 * I could include parent template here but setting it up is more hassle than use.
				 *	All of the static calls below are functional.
				 */
				#echo \b::b(static::_include($__data, 'Block/BlockContainer.html', $__templatePath), 2)
				#echo \b::b(static::_include($__data, 'Block/BlockContainer.html'), 2)
				#echo \b::b(static::_include(null, 'Block/BlockContainer.html', $__templatePath), 2)
				#echo \b::b(static::_includeParent(), 2)
				#echo \b::b(static::_includeTemplate('Block/BlockContainer.html'), 2)
			?>

			<?php foreach ($blocks as $eachBlock): ?>
			<?= \b::b($eachBlock, 3) ?>
			<?php endforeach; ?>

			<ul class="nav">
				<li><a href="about/">About</a>
				<li class="dropdown"><a id="nav-docs" href="#" class="dropdown-toggle" data-toggle="dropdown" >Docs</a>
				<ul class="dropdown-menu" role="menu" >
					<li><a href="#">Blog demo application docs</a></li>
					<li><a href="#">Cocktail (PhpCocktail) docs</a></li>
					<li><a href="#">Camarera docs</a></li>
				</ul>
				<li><a href="contact/">Contact</a>
			</ul>
		</div>
	</div>
</nav>
