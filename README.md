yii2-icon
=========
 
### Raw SVG symbols in Yii2 ###

[![Latest Stable Version](https://poser.pugx.org/sjaakp/yii2-icon/v/stable)](https://packagist.org/packages/sjaakp/yii2-collapse)
[![Total Downloads](https://poser.pugx.org/sjaakp/yii2-icon/downloads)](https://packagist.org/packages/sjaakp/yii2-collapse)
[![License](https://poser.pugx.org/sjaakp/yii2-icon/license)](https://packagist.org/packages/sjaakp/yii2-collapse)

Probably the best way to embed icons like those from [FontAwesome](https://fontawesome.com/)
on your 
website is to use [bare SVGs](https://fontawesome.com/docs/web/add-icons/svg-bare)
as [SVG symbols](https://fontawesome.com/docs/web/add-icons/svg-symbols).
The SVG-code of the icons is embedded in the page. Therefore no extra font file has to be downloaded,
no JavaScript needs to run at page set up,
and the browser will render the icons as fast as possible.

At the downside, this requires a bit more coding. Until now, that is; because now there is **yii2-icon**.
This provides a helper class for [Yii 2.0](https://www.yiiframework.com/) that
takes the burden out of icons based on bare SVG Symbols.

A demonstration of **yii2-icon** is
[here](http://www.sjaakpriester.nl/software/icon).
**yii2-icon**'s GitHub-page is [here](https://github.com/sjaakp/yii2-icon).

## Installation ##

Install **yii2-icon** in the usual way with [Composer](https://getcomposer.org/).
Add the following to the require section of your `composer.json` file:

`"sjaakp/yii2-icon": "*"`

or run:

`composer require sjaakp/yii2-icon`

You can manually install **yii2-icon** by [downloading the source in ZIP-format](https://github.com/sjaakp/yii2-icon/archive/master.zip).

## Setup ##

For **yii2-icon** to work, some icon collection has to be installed on your site as well.
In many cases this will be installed via [`npm`](https://www.npmjs.com/), other
collections (notably [FontAwesome Free](https://fontawesome.com/)) via
[Composer](https://getcomposer.org/). Refer to the icon provider's web site for details.

In any case, an [Yii2 alias](https://www.yiiframework.com/doc/guide/2.0/en/concept-aliases)
has to be defined for `'@icons'`. This should describe the
location of the icon files.

As an example, for **FontAwesome Free**, installed via `composer`, this will be:

`'@vendor/fortawesome/font-awesome/svgs/{family}/{name}.svg'`

Here, **yii2-icon** will replace `{family}` with the icon family (like `'regular'`,
`'solid'`, or `'thin'`). 

`'{name}'` will be replaced by the icon name (like
`'bicycle'`, `'chess-queen'`, or `'arrow-down'`). Icon names can be found at the 
icon provider's site.

As another example, for **FontAwesome Pro**, installed via `npm`, `'@icons'` 
should be:

`'@app/node_modules/@fortawesome/fontawesome-pro/svgs/{family}/{name}.svg'`

The preferred way to set the alias is to add it to `'aliases'`-section
of the main site configuration (often `web.php`, or `main.php` in the `config`-directory).
The `'aliases'`-section should look something like this:

    'aliases' => [
        '@bower'  => '@vendor/bower-asset',
        '@npm'    => '@vendor/npm-asset',
        '@icons'  => '@vendor/fortawesome/font-awesome/svgs/{family}/{name}.svg',
        // ... probably more aliases ...
    ],

If alias `'@icons'` is not set, **yii2-icon** will throw an error.

Next, the main layout view of the site (probably `'views/layouts/main.php'`) should
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

If everything is set up correctly, you can render icons
anywhere in any view. For example, to render an icon of the `regular` family, just
use something like this:

    <?= Icon::regular('bicycle') ?>

Likewise for the other families:

    <?= Icon::solid('heart') ?>
    <?= Icon::light('bell') ?>

If an icon collection has no families, like the [Bootstrap Icons](https://icons.getbootstrap.com/),
an icon can be rendered with:

    <?= Icon::icon('camera') ?>

## Styling ##

Icons can be extended with HTML options, like so:

    <?= Icon::regular('bicycle', [ 'class' => 'blue' ]) ?>

**yii2-icon** always includes the [FontAwesome styling classes](https://fontawesome.com/docs/web/style/styling),
even for other font collections. This means that classes
such as `fa-lg`, `fa-rotate-90`, will work out of the box. For example:

    <?= Icon::solid('rotate', [ 'class' => 'fa-spin' ]) ?>

## Class Icon ##

All functionality of **yii2-icon** is bundled in one abstract class, `sjaakp\icon\Icon`.
It has only abstract functions, all returning HTML:

 - **renderIcon($fam, $name, $options = [])** - render icon witn name `Sname`
from family `$fam`.
 - **symbols($view)** - render the symbol table in 
[View](https://www.yiiframework.com/doc/guide/2.0/en/structure-views) `$view`,
preferably a [Layout](https://www.yiiframework.com/doc/guide/2.0/en/structure-views#layouts).

**All other function calls** will be translated in a call to **renderIcon**,
where the function name will be the value of `$fam`. Therefore, a function
call like:

    Icon::regular('bicycle', [ <options> ]);

will be interpreted as:

    Icon::renderIcon('regular', 'bicycle', [ <options> ]);

## How icons are rendered ##

Suppose we have a page with `<?= Icon::solid('mug-hot', [ 'class' => 'fa-rotate-90' ]) ?>`
in it. It will be rendered like:

    <body>
        <svg xmlns="http://www.w3.org/2000/svg"> <!-- invisible symbol table -->
            <symbol ...><path d="..."/></symbol>
            <symbol id="i-solid-mug-hot" viewBox="0 0 512 512"><path d="M400 192H32C14.25 192 ..."/></symbol>
            <symbol ...><path d="..."/></symbol> <!-- more symbols -->
        </svg>

        ...
        <!-- lots of other HTML -->
        ...
    
        ... <svg class="fa-rotate-90 svg-inline--fa">
                <use href="#i-solid-mug-hot"></use>
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

## Some icon resources ##

| Name                                         | @icons                                                                                            |
|----------------------------------------------|---------------------------------------------------------------------------------------------------|
| [FontAwesome Free](https://fontawesome.com/) | `'@vendor/fortawesome/font-awesome/svgs/{family}/{name}.svg'`                                     |
| [FontAwesome Pro](https://fontawesome.com/)  | `'@node/@fortawesome/fontawesome-pro/svgs{family}/{name}.svg'`                                    |
|[Google Material Icons](https://fonts.google.com/icons)| `'@node/@material-design-icons/svg{family}/{name}.svg'`                                           |
|[Feather](https://feathericons.com/)| `'@node/feather-icons/dist/icons/{name}.svg'`                                                     |
|[Bootstrap Icons](https://icons.getbootstrap.com/)| `'@vendor/twbs/bootstrap-icons/icons/{name}.svg'`<br/> `'@node/bootstrap-icons/icons/{name}.svg'` |
|[Teeny Icons](https://teenyicons.com/)| `'@node/teenyicons/{family}/{name}.svg'`                                                          |
|[Microsoft Fluent Icons](https://fluenticons.co/)| `'@node/@fluentui/svg-icons/icons/{name}_24_{family}.svg'`                                        |  

(Notice: `'@node'` is an alias for `'@app/node_modules'`.)