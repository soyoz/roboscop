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
class Soyoz_Microsoft_MSN_Adapter_Command_Tercume_API
{
    public static function translate($source, $destination, $text)
    {
        if (isset ($source) && isset ($destination) && isset ($text)) {
            $translate = file_get_contents('http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=' . rawurlencode($text) . '&langpair=' . $source . rawurlencode('|') . $destination);

            return htmlspecialchars_decode(json_decode($translate)->responseData->translatedText, ENT_QUOTES);
        }
        return false;
    }
}

?>