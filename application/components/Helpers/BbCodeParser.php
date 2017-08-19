<?php
namespace app\components\Helpers;
use yii\helpers\Html;

/**
 *
 */

class BbCodeParser
{
    public static function parse($string, $prepareString = true, $cleanString = true)
    {
        $string = self::_cleanEnters($string);

        if ($cleanString) {
            $string = self::_cleanCode($string);
        }

        $find = array(
            '~\>(.*?)\s~s',
            '~\*\*(.*?)\*\*~s',
            '~\_(.*?)\_~s',
            '~\[(.*?)\]\((.*?)\)~s',
            '~\`(.*?)\`~s',
            '~![[:space:]](.*?)\n~s',
            '~![[:space:]](.*?)$~s',
        );

        // HTML tags to replace BBcode
        $replace = array(
            '<cite>$1</cite>',
            '<b>$1</b>',
            '<i>$1</i>',
            '<a href="$2">$1</a>',
            '<code>$1</code>',
            '<a class="showSpoiler">pokaż spoiler</a><code class="dnone">$1</code>' . "\n",
            '<a class="showSpoiler">pokaż spoiler</a><code class="dnone">$1</code>' . "\n",
        );

        // Replacing the BBcodes with corresponding HTML tags
        $string = preg_replace($find, $replace, $string);

        if ($prepareString) {
            $string = self::_prepareString($string);
        }

        return $string;
    }

    private function _cleanEnters($string)
    {
        return strtr(
            $string,
            ["\r\n" => "\n", "\n\r" => "\n", "\r" => "\n", ]
        );
    }

    private function _prepareString($string)
    {
        return strtr(
            $string,
            ["\n" => '<br />', "\t" => '    ']
        );
    }

    private function _cleanCode($string)
    {

        return $string;
    }
}