yii2-icon
=========
 
###FontAwesome raw SVG symbols in Yii2

[![Latest Stable Version](https://poser.pugx.org/sjaakp/yii2-icon/v/stable)](https://packagist.org/packages/sjaakp/yii2-collapse)
[![Total Downloads](https://poser.pugx.org/sjaakp/yii2-icon/downloads)](https://packagist.org/packages/sjaakp/yii2-collapse)
[![License](https://poser.pugx.org/sjaakp/yii2-icon/license)](https://packagist.org/packages/sjaakp/yii2-collapse)

Probably the best way to embed [FontAwesome](https://fontawesome.com/) icons on your 
website is to use [bare SVGs](https://fontawesome.com/docs/web/add-icons/svg-bare)
as [SVG symbols](https://fontawesome.com/docs/web/add-icons/svg-symbols).
The SVG-code of the icons is embedded in the page, so no extra font file has to be downloaded,
no JavaScript needs to run at page set up,
and the browser will render the icons extremely fast.

At the downside, this requires a bit more coding. Until now, that is; because now there is **yii2-icon**.
This provides a helper class for [Yii 2.0](https://www.yiiframework.com/) that
takes the burden out of FontAwesome bare SVG Symbols.

A demonstration of **yii2-icon** is
[here](http://www.sjaakpriester.nl/software/icon).

## Installation ##

Install **yii2-icon** in the usual way with [Composer](https://getcomposer.org/).
Add the following to the require section of your `composer.json` file:

`"sjaakp/yii2-icon": "*"`

or run:

`composer require sjaakp/yii2-icon`

You can manually install **yii2-icon** by [downloading the source in ZIP-format](https://github.com/sjaakp/yii2-icon/archive/master.zip).

### FontAwesome ###

For **yii2-icon** to work, FontAwesome has to be installed on your site.
There are several ways to do this.

If you are using **FontAwesome Free**, probably the easiest way is to add:

`"fortawesome/font-awesome": "*"`

to the require section of your `composer.json` file, or to run:

`composer require fortawesome/font-awesome`

Users of **FontAwesome Pro** may be better of by using `npm`. More information
[here](https://fontawesome.com/docs/web/setup/packages#_1-configure-access).

## Setup ##

An [Yii2 alias](https://www.yiiframework.com/doc/guide/2.0/en/concept-aliases)
has to be defined for `'@fontawesome'`. This should point to the
location of the FontAwesome files.

For **FontAwesome Free**, installed via `composer`, this will probably be:

`'@vendor/fortawesome/font-awesome'`

For **FontAwesome Pro**, installed via `npm`, it will be:

`'@app/node_modules/@fortawesome/fontawesome-pro'`

The preferred way to set the alias is to add it to `'aliases'`-section
of the main site configuration (often `web.php` in the `config`-directory).
The `'aliases'`-section should look something like this:

    'aliases' => [
        '@bower'        => '@vendor/bower-asset',
        '@npm'          => '@vendor/npm-asset',
        '@fontawesome'  => '@vendor/fortawesome/font-awesome',
        // ... probably more aliases ...
    ],

If alias `'@fontawesome'` is not set, **yii2-icon** will throw an error.

Next, the main layout view (probably `'views/layouts/main.php'`) should
be modified. At the top of the `body`-section, **yii2-icon**'s
class `Icon` has to render the
SVG-symbols. The beginning of the main layout view should look something like this:

    <?php

    use ...
    use sjaakp\icon\Icon;

    //...
    ?>
    ...
    <html ...>
    <head> ... </head>
    <body class=" ... ">
        <?php $this->beginBody() ?>
        <?= Icon::symbols($this) ?>

        // ...



## Using yii2-icon ##

If everything is set up correctly, you can render FontAwesome icons
anywhere in any view. To render an icon of the `regular` family, just
use something like this:

    <?= Icon::regular('bicycle') ?>

Likewise for the other families:

    <?= Icon::solid('heart') ?>
    <?= Icon::light('bell') ?> (pro only)

The names of the icons can be found at [excellent FontAwesome search page ](https://fontawesome.com/icons).

Icons can be extended with HTML options, like so:

    <?= Icon::regular('bicycle', [ 'class' => 'blue' ]) ?>

Most of the [FontAwesome styling classes](https://fontawesome.com/docs/web/style/styling),
such as `fa-lg`, `fa-rotate-90`, will work out of the box, f.i.:

    <?= Icon::solid('rotate', [ 'class' => 'fa-spin' ]) ?>

## Class Icon ##

All functionality of **yii2-icon** is bundled in one abstract class, `sjaakp\icon\Icon`.
It has only abstract functions, all returning HTML:

 - **regular($name, $options = [])** - render icon with name `$name` from the
`regular` family. Shortcut for `renderIcon('regular', $name, $options)`.   
 - **solid($name, $options = [])** - render icon with name `$name` from the
`solid` family. Shortcut for `renderIcon('solid', $name, $options)`.   
 - **light($name, $options = [])** - render icon with name `$name` from the
`light` family. Shortcut for `renderIcon('light', $name, $options)`.   
 - **thin($name, $options = [])** - render icon with name `$name` from the
`thin` family. Shortcut for `renderIcon('thin', $name, $options)`.   
 - **duotone($name, $options = [])** - render icon with name `$name` from the
`duotone` family. Shortcut for `renderIcon('duotone', $name, $options)`.   
 - **brands($name, $options = [])** - render icon with name `$name` from the
`brands` family. Shortcut for `renderIcon('brands', $name, $options)`.   
 - **renderIcon($fam, $name, $options = [])** - render icon witn name `Sname`
from family `$fam`.
 - **symbols($view)** - render the symbol table in 
[View](https://www.yiiframework.com/doc/guide/2.0/en/structure-views) `$view`,
preferably a [Layout](https://www.yiiframework.com/doc/guide/2.0/en/structure-views#layouts).

## How icons are rendered ##

Suppose we have a page with `<?= Icon::solid('mug-hot', [ 'class' => 'fa-rotate-90' ]) ?>`
in it. It will be rendered like:

    <body>
        <svg xmlns="http://www.w3.org/2000/svg"> <!-- invisible symbol table -->
            <symbol ...><path d="..."/></symbol>
            <symbol id="solid-mug-hot" viewBox="0 0 512 512"><path d="M400 192H32C14.25 192 ..."/></symbol>
            <symbol ...><path d="..."/></symbol> <!-- more symbols -->
        </svg>

        ...
        <!-- lots of other HTML -->
        ...
    
        ... <svg class="fa-rotate-90 svg-inline--fa">
                <use href="#solid-mug-hot"></use>
            </svg> ...

        ...
    
    </body>

## Gotcha ##

Icons in the main layout file may not be rendered if they come *after* the 
symbol table. One way to avoid this problem is by assigning the icon to a PHP-variable,
like so:

    <?php
    /* views/layouts/main.php */

    use ...
    use sjaakp\icon\Icon;

    $iconHouse = Icon::solid('house');

    //...
    ?>
    ...
    <html ...>
    <head> ... </head>
    <body class=" ... ">
        <?php $this->beginBody() ?>
        <?= Icon::symbols($this) ?>

        // ...

        Render house icon: <?= $iconHouse ?>.

        // ...

