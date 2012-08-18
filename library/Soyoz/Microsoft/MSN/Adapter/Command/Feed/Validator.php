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
class Soyoz_Microsoft_MSN_Adapter_Command_Feed_Validator
{
    /**
     * @var string
     */
    private static $_feedValidatorURL = 'http://validator.w3.org/feed/check.cgi?url=';

    /**
     * @param $feed string
     * @return boolean
     */
    public static function check($feed)
    {
        if (isset ($feed)) {
            $check = file_get_contents(self::$_feedValidatorURL . $feed);

            if (!strstr($check, 'Name or service not known') && !strstr($check, 'It looks like this is a web page, not a feed') && !strstr($check, 'list index out of range')) {
                preg_match('/<title>Feed Validator Results: (.*)<\/title>/', $check, $feed);

                $feed = trim(end($feed));

                return $feed;
            } else {
                return false;
            }
        }
        return false;
    }
}