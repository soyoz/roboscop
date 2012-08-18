<?php
/**
 * @author Erol Soyöz <erol@soyoz.com>
 * @copyright 2009 The Authors
 * @license http://www.gnu.org/licenses/gpl.txt GNU General Public License
 * @link https://github.com/soyoz/roboscop
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class Soyoz_Microsoft_MSN_Adapter_Helper_Function
{
    /**
     * @param string $text
     *
     * @return string | boolean
     */
    public static function strtoupper($text)
    {
        if (isset ($text)) {
            $text = str_replace(array(
                'ğ', 'ş', 'ı', 'ö', 'ü', 'ç', 'i'
            ), array(
                'g', 's', 'i', 'o', 'u', 'c', 'i'
            ), $text);

            return strtoupper($text);
        } else {
            return false;
        }
    }

    /**
     * @param string $text
     *
     * @return string | boolean
     */
    public static function strtolower($text)
    {
        if (isset ($text)) {
            $text = str_replace(array(
                'ğ', 'ş', 'ı', 'ö', 'ü', 'ç', 'Ğ', 'Ş', 'I', 'Ö', 'Ü', 'Ç', 'İ'
            ), array(
                'g', 's', 'i', 'o', 'u', 'c', 'g', 's', 'i', 'o', 'u', 'c', 'i'
            ), $text);

            return strtolower($text);
        } else {
            return false;
        }
    }

    /**
     * @param string $string
     * @param array $array
     *
     * @return boolean
     */
    public static function arrayValueExists($string, $array)
    {
        foreach ($array as $value) {
            if ($value == $string) {
                return $value;
            }
        }
        return false;
    }
}

?>