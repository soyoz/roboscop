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
class Soyoz_Microsoft_MSN_Adapter_Helper_Check
{
    /**
     * @param array $parameter
     * @param array $allowed
     * @return array | boolean
     */
    public static function parameter($parameter, $allowed)
    {
        if (isset ($parameter) && isset ($allowed) && is_array($parameter) && is_array($allowed)) {
            $result = true;

            foreach ($parameter as $key => $value) {
                $value = $value;
                if (array_key_exists($key, $allowed)) {
                    foreach ($allowed as $allowedKey => $allowedValue) {
                        if ($allowedValue == 'MANDATORY') {
                            if (!array_key_exists($allowedKey, $parameter)) {
                                return false;
                            }
                        }
                    }
                } else {
                    return false;
                }
            }
            return $result;
        } else {
            return false;
        }
    }
}

?>
