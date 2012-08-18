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
class Soyoz_Microsoft_MSN_Adapter_Helper_Login
{
    /**
     * @var array
     */
    private static $_connection = array(
        'protocol' => 'https://', 'hostname' => 'nexus.passport.com', 'port' => 443, 'redirect' => '/rdr/pprdr.asp'
    );

    /**
     * @param $passport string
     * @param $password string
     * @param $ticket string
     * @return string | boolean
     */
    public static function request($passport, $password, $ticket)
    {
        if (isset ($passport) && isset ($password) && isset ($ticket)) {
            $response = self::_execute($passport, $password, $ticket);

            if ($response) {
                return $response;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * @param $passport string
     * @param $password string
     * @param $ticket string
     * @return string | boolean
     */
    private static function _execute($passport, $password, $ticket)
    {
        if (isset ($passport) && isset ($password) && isset ($ticket)) {
            if (!extension_loaded('curl')) {
                die ('Please enable the cURL extension.');
            } else {
                $command = array(
                    "GET " . self::$_connection ['redirect'] . " HTTP/1.0\r\n"
                );

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, self::$_connection ['protocol'] . self::$_connection ['hostname'] . ":" . self::$_connection ['port'] . self::$_connection ['redirect']);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_VERBOSE, 0);
                curl_setopt($curl, CURLOPT_HEADER, 1);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $command);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($curl);
                curl_close($curl);

                $matches = null;
                preg_match("/DALogin=(.+?),/", $response, $matches);

                if (is_array($matches)) {
                    $matches = explode("/", end($matches));

                    $DALogin = reset($matches);
                    $DALoginRedirect = end($matches);

                    $command = array(
                        "GET /" . $DALoginRedirect . " HTTP/1.1\r\n", "Authorization: Passport1.4 OrgVerb=GET,OrgURL=http%3A%2F%2Fmessenger%2Emsn%2Ecom,sign-in=" . rawurlencode($passport) . ",pwd=" . rawurlencode($password) . ", " . trim($ticket) . "\r\n"
                    );

                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, self::$_connection ['protocol'] . $DALogin . ":" . self::$_connection ['port'] . '/' . $DALoginRedirect);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_VERBOSE, 0);
                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
                    curl_setopt($curl, CURLOPT_HEADER, 1);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $command);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($curl);
                    curl_close($curl);

                    preg_match("/t=(.+?)'/", $response, $matches);

                    if (is_array($matches)) {
                        $ticket = trim(end($matches));

                        if ($ticket) {
                            return $ticket;
                        } else {
                            return false;
                        }
                    }
                }
            }
        }
        return false;
    }
}

?>