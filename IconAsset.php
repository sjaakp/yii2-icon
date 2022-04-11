<?php
/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 10-04-2021.
 *
 * yii2-icon
 * FontAwesome raw SVG symbols in Yii2
 *
 * @link: https://sjaakpriester.nl/software/icon
 */

namespace sjaakp\icon;

use yii\web\AssetBundle;

class IconAsset extends AssetBundle
{
    public $sourcePath = '@fontawesome/css';

    public $css = [
        'svg-with-js.css'
    ];
}
