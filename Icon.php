<?php
/**
 * MIT licence
 * Version 1.1
 * Sjaak Priester, Amsterdam 21-04-2021.
 *
 * yii2-icon
 * FontAwesome raw SVG symbols in Yii2
 *
 * @link: https://sjaakpriester.nl/software/icon
 */

namespace sjaakp\icon;

use Yii;
use yii\helpers\Html;
use yii\base\ErrorException;
use yii\base\InvalidArgumentException;

abstract class Icon
{
    public static $register = [];

    public static $prefix = 'i-';

    /**
     * @param $fam      - icon family
     * @param $name     - icon name
     * @param $options  - icon HTML options
     * @return string   - HTML of icon
     * @throws ErrorException   - '@icons' not set or icon not found
     */
    public static function renderIcon($fam, $name, $options = [])
    {
        self::registerIcon($fam, $name);
        Html::addCssClass($options, 'svg-inline--fa');
        $prf = self::$prefix;
        $id = "{$prf}$fam-$name";
        $asp = self::$register[$id]['aspect'];
        if ($asp != 1) Html::addCssStyle($options, ['aspect-ratio' => $asp ]);
        return Html::tag('svg', "<use href=\"#$id\"></use>", $options);
    }

    /**
     * @param $fam      - icon family
     * @param $name     - icon name
     * @return void
     * @throws ErrorException   - '@icons' not set or icon not found
     */
    public static function registerIcon($fam, $name)
    {
        $prf = self::$prefix;
        $id = "{$prf}$fam-$name";
        if (! isset($id, self::$register[$id]))  {      // if not already registered
            try {
                $icons = Yii::getAlias("@icons");
                $svgFile = str_replace([ '{family}', '{name}' ], [ $fam, $name ], $icons);
                $r = file_get_contents($svgFile);
            }
            catch (\Exception $e) {
                if (is_a($e, InvalidArgumentException::class)) throw new ErrorException("Alias \"@icons\" is not set.");
                throw new ErrorException("Icon family: \"$fam\", name: \"$name\" not found.");
            }
            $r1 = preg_replace('/(width|height)="(\d+)" /', '', $r);
            $symbol = str_replace(['xmlns="http://www.w3.org/2000/svg"', 'svg'], ["id=\"$id\"", 'symbol'], $r1);
            $aspect = 1;
            if (preg_match('/viewBox="0 0 (\d+) (\d+)">/', $symbol, $m))    {
                $aspect = $m[1] / $m[2];
            }
            self::$register[$id] = ['symbol' => $symbol, 'aspect' => $aspect];
        }
    }

    /**
     * @param $view     - yii\web\View
     * @return string   - HTML of symbol table
     */
    public static function symbols($view)
    {
        if (count(self::$register) == 0) return '';      // no icons
        IconAsset::register($view);

        $view->registerCss('.svg-inline--fa {aspect-ratio:1;fill: currentColor;}
.fa-primary {fill:var(--fa-primary-color,currentColor);opacity:var(--fa-primary-opacity,1); }
.fa-secondary {fill:var(--fa-secondary-color,currentColor);opacity:var(--fa-secondary-opacity, 0.4) !important;}
.fa-swap-opacity {--fa-primary-opacity:0.4;--fa-secondary-opacity:1;}');

        $syms = [];
        if (preg_match('/<!--(.*)-->/', current(self::$register)['symbol'], $m))  {
            $syms[0] = $m[0];
        }

        foreach (self::$register as $id => $data)   {
            $c = preg_replace('/<!--(.*)-->/', '', $data['symbol']);
            $syms[] = $c;
        }
        return Html::tag('svg', implode("\n", $syms), [
            'xmlns' => 'http://www.w3.org/2000/svg',
            'style' => 'display:none;'
        ]);
    }

    /**
     * @param $fName   - function name is icon family name
     * @param $arguments    - [ <name>, <options> ]
     * @return string   - HTML of icon
     * Magic method: Icon::regular('xyz') => Icon::renderIcon('regular', 'xyz') etc.
     */
    public static function __callStatic($fName, $arguments)
    {
        if (count($arguments) == 1) $arguments[] = [];
        array_unshift($arguments, $fName);

        return forward_static_call_array('self::renderIcon', $arguments);
    }

    /**
     * @param $name
     * @param $options
     * @return string
     * @throws ErrorException
     * Fix for family 'two-tone' (Google Material Icons)
     */
    public static function two_tone($name, $options = [])
    {
        return self::renderIcon('two-tone', $name, $options);
    }
}