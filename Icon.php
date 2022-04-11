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

use Yii;
use yii\helpers\Html;
use yii\base\ErrorException;
use yii\base\InvalidArgumentException;

abstract class Icon
{
    public static $register = [];

    public static function renderIcon($fam, $name, $options = [])
    {
        self::registerIcon($fam, $name);
        Html::addCssClass($options, 'svg-inline--fa');
        $id = "$fam-$name";
        $asp = self::$register[$id]['aspect'];
        if ($asp != 1) Html::addCssStyle($options, ['aspect-ratio' => $asp ]);
        return Html::tag('svg', "<use href=\"#$id\"></use>", $options);
    }

    public static function registerIcon($fam, $name)
    {
        $id = "$fam-$name";
        if (! isset($id, self::$register[$id]))  {
            try {
                $svgFile = Yii::getAlias("@fontawesome/svgs/$fam/$name.svg");
                $r = file_get_contents($svgFile);
            }
            catch (\Exception $e) {
                if (is_a($e, InvalidArgumentException::class)) throw new ErrorException("Alias \"@fontawesome\" is not set.");
                throw new ErrorException("FontAwesome Icon family: \"$fam\", name: \"$name\" not found.");
            }
            $symbol = str_replace(['xmlns="http://www.w3.org/2000/svg"', 'svg'], ["id=\"$id\"", 'symbol'], $r);
            $aspect = 1;
            if (preg_match('/viewBox="0 0 (\d+) (\d+)">/', $symbol, $m))    {
                $aspect = $m[1] / $m[2];
            }
            self::$register[$id] = ['symbol' => $symbol, 'aspect' => $aspect];
        }
    }

    public static function symbols($view)
    {
        if (count(self::$register) == 0) return '';
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

    public static function brands($name, $options = [])
    {
        return self::renderIcon('brands', $name, $options);
    }

    public static function light($name, $options = [])
    {
        return self::renderIcon('light', $name, $options);
    }

    public static function duotone($name, $options = [])
    {
        return self::renderIcon('duotone', $name, $options);
    }

    public static function regular($name, $options = [])
    {
        return self::renderIcon('regular', $name, $options);
    }

    public static function solid($name, $options = [])
    {
        return self::renderIcon('solid', $name, $options);
    }

    public static function thin($name, $options = [])
    {
        return self::renderIcon('thin', $name, $options);
    }
}