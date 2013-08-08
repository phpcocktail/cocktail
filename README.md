cocktail
========

This file is part of PhpCocktail. PhpCocktail is free software: you can redistribute it and/or modify it under the
	terms of the GNU Lesser General Public License as published by the Free Software Foundation, either version 3
	of the License, or (at your option) any later version.
 PhpCocktail is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 	warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 	more details. You should have received a copy of the GNU Lesser General Public License along with PhpCocktail.
 	If not, see <http://www.gnu.org/licenses/>.
Copyright 2013 t <tomi20v@gmail.com>


PLEASE READ:
this branch is closed. PhpCocktail is being moved to https://github.com/phpcocktail/cocktail starting with version 1.1

PhpCocktail (or just Cocktail) is a set of reusable PHP framework components. It gives you tools and schemas to build
	your application in PHP, let it be a website, a web service, or some server side daemon. It's licensed under LGPL3.
Key features:
	- based on PHP 5.3 (this may change to 5.4, see below)
	- MVC implementation, building MVC websites is easy
	- modular routing system
	- modular view system (implement the templating system of your choise by adapters)
	- Block classes to be used as blocks of your pages in a reusable way
	- ajax aware
	- HMVC in Cocktail means you can adress modules (Blocks) of a page by its URL and pass actions to it directly (eg.
	login data for a login box, updating its content by ajax)
In work:
	- tasks, one-time scripts or daemon like usage
	- include more templating systems in views
	- skeletons and scaffolding
	- form binding to models, table views to collections (via Camarera)
	- some aspects could be done by means of mixins instead of class inheritance, yet to be investigated. This would
	also imply that PHP minimum version changes to 5.4
	- comprehensive unit tests (however, at this early time of development it has little focus)

PhpCocktail relies on Camarera. Camarera is an activerecord/ORM implementation (also made by me).
