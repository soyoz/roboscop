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
class Soyoz_Microsoft_MSN_Adapter_Command_Muzik
{
    /**
     * @var array
     */
    public static $information = array(
        "example" => "", "description" => "(i) Roboscop Müzik servisi ile ilgili detaylar çok yakında."
    );

    /**
     * @param $parameter string
     * @param $messageId integer
     * @return string | boolean
     */
    public static function run($message)
    {
        if (isset ($message)) {
            $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send('(i) Roboscop Müzik servisi ile ilgili detaylar çok yakında.');

            return $header;
        } else {
            return false;
        }
    }
}

?>