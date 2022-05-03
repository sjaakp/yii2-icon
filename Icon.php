<?php
/**
 * MIT licence
 * Version 1.2.0
 * Sjaak Priester, Amsterdam 03-05-2021.
 *
 * yii2-icon
 * Raw SVG symbols in Yii2
 *
 * @link: https://sjaakpriester.nl/software/icon
 */

namespace sjaakp\icon;

use Yii;
use yii\helpers\Html;
use yii\base\ErrorException;

abstract class Icon
{
    protected static $register = [];

    protected static $settings;

    protected static function iconId($fam, $name)
    {
        return str_replace([ '{family}', '{name}' ], [ $fam, $name ], self::$settings['template']);
    }

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
        Html::addCssClass($options, self::$settings['iconClass']);
        $id = self::iconId($fam, $name);
        $asp = self::$register[$id]['aspect'];
        if ($asp != 1) Html::addCssStyle($options, ['aspect-ratio' => $asp ]);
        return Html::tag('svg', "<use href=\"#$id\"></use>", $options);
    }

    /**
     * @return void
     * @throws ErrorException
     * Set $settings from application parameter 'icons'
     */
    public static function reset()
    {
        $icons = Yii::$app->params['icons'] ?? false;
        if (! $icons) throw new ErrorException("Parameter \"icons\" is not set.");
        if (is_string($icons)) $icons = [ 'location' => $icons ];
        self::$settings = array_merge([
            'faCss' => true,                // defaults
            'template' => 'i-{family}-{name}',
            'iconClass' => 'svg-inline--fa'
        ], $icons);
        if (! isset(self::$settings['location'])) throw new ErrorException("Parameter \"location\" is not set.");
    }

    /**
     * @param $fam      - icon family
     * @param $name     - icon name
     * @return void
     * @throws ErrorException   - '@icons' not set or icon not found
     */
    public static function registerIcon($fam, $name)
    {
        if (is_null(self::$settings))    {           // if self::$settings not set
            self::reset();
        }
        $id = self::iconId($fam, $name);
        if (! isset($id, self::$register[$id]))  {      // if not already registered
            try {
                $svgFile = preg_replace_callback('/{(\w+)}/', function($m) use ($fam, $name) {
                    switch($m[1])   {
                        case 'name' : return $name;
                        case 'family' : return $fam;
                        case 'phosphor' :
                            $t = "$fam/$name";
                            if ($fam != 'regular') $t .= "-$fam";
                            return $t;
                    }
                    return $m[0];
                }, self::$settings['location']);
                $r = file_get_contents(Yii::getAlias($svgFile));
            }
            catch (\Exception $e) {
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

        if (self::$settings['faCss']) {
            IconAsset::register($view);

            $view->registerCss('.svg-inline--fa {aspect-ratio:1;fill: currentColor;}
.fa-primary {fill:var(--fa-primary-color,currentColor);opacity:var(--fa-primary-opacity,1); }
.fa-secondary {fill:var(--fa-secondary-color,currentColor);opacity:var(--fa-secondary-opacity, 0.4) !important;}
.fa-swap-opacity {--fa-primary-opacity:0.4;--fa-secondary-opacity:1;}');
        }

        $syms = [];
        if (preg_match('/<!--(.*)-->/', current(self::$register)['symbol'], $m))  {
            $syms[0] = $m[0];   // include comment (credentials) once
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
