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
class Soyoz_Microsoft_MSN_Adapter_Helper_Message
{
    /**
     * @var array
     */
    private static $_field = array(
        'MIME-Version' => 'MANDATORY', 'Content-Type' => 'MANDATORY', 'X-MMS-IM-Format' => 'OPTIONAL', 'Invitation-Command' => 'MANDATORY', 'Invitation-Cookie' => 'MANDATORY', 'Application-Name' => 'MANDATORY', 'Application-GUID' => 'MANDATORY', 'Application-File' => 'MANDATORY', 'Application-FileSize' => 'MANDATORY', 'Connectivity' => 'OPTIONAL', 'Launch-Application' => 'MANDATORY', 'IP-Address' => 'MANDATORY', 'IP-Address-Internal' => 'OPTIONAL', 'Port' => 'MANDATORY', 'PortX' => 'OPTIONAL', 'PortX-Internal' => 'OPTIONAL', 'AuthCookie' => 'MANDATORY', 'Sender-Connect' => 'MANDATORY'
    );

    /**
     * @param $message string
     * @return array | boolean
     */
    public static function parse($message)
    {
        if (isset ($message)) {
            $message = explode("\r\n", $message);
            $parse = null;

            if (is_array($message)) {
                foreach ($message as $value) {
                    foreach (self::$_field as $field => $status) {
                        $status;

                        if (strstr($value, $field)) {
                            list ($messageField, $messageFieldValue) = explode(':', $value);
                            $parse [trim($messageField)] = trim($messageFieldValue);
                        }
                    }
                }
                return $parse;
            }
        }
        return false;
    }

    /**
     * @param string $message
     * @return array | boolean
     */
    public static function getCommand($message)
    {
        if (isset ($message)) {
            $matches = null;
            preg_match('/^!(.+?)$/', $message, $matches);

            if (is_array($matches) && $matches) {
                $command = reset(explode(' ', end($matches)));

                return $command;
            }
        }
        return false;
    }

    /**
     * @param string $message
     * @return array | boolean
     */
    public static function getParameter($message)
    {
        if (isset ($message)) {
            $matches = null;
            preg_match('/^!(.+?)\s(.*)$/', $message, $matches);

            if (is_array($matches) && $matches) {
                $parameter = end($matches);

                return $parameter;
            }
        }
        return false;
    }

    /**
     * @param $message string
     * @return string | boolean
     */
    public static function get($message)
    {
        if (isset ($message)) {
            $message = trim(end(explode("\r\n", $message)));

            return $message;
        } else {
            return false;
        }
    }

    /**
     * @param $message string
     * @return string | boolean
     */
    public static function send($message)
    {
        if (isset ($message)) {
            $header = null;
            $header = $header . "MIME-Version: 1.0\r\n";
            $header = $header . "Content-Type: text/plain; charset=UTF-8\r\n";
            $header = $header . "X-MMS-IM-Format: FN=Arial; EF=; CO=ff0000; CS=0; PF=18\r\n\r\n";
            $header = $header . $message;

            return $header;
        } else {
            return false;
        }
    }

    /**
     * @param $cookie integer
     * @return string | boolean
     */
    public static function cancel($cookie)
    {
        if (isset ($cookie) && is_numeric($cookie)) {
            $header = null;
            $header = $header . "MIME-Version: 1.0\r\n";
            $header = $header . "Content-Type: text/x-msmsgsinvite; charset=UTF-8\r\n\r\n";
            $header = $header . "Invitation-Command: CANCEL\r\n";
            $header = $header . "Invitation-Cookie: " . $cookie . "\r\n";
            $header = $header . "Cancel-Code: REJECT\r\n\r\n";

            return $header;
        } else {
            return false;
        }
    }

    /**
     * @param $file string
     * @param $size integer
     * @param $cookie integer
     * @return string | boolean
     */
    public static function invite($file, $size, $cookie)
    {
        if (isset ($file) && is_file($file) && file_exists($file) && is_readable($file) && isset ($size) && isset ($cookie)) {
            $header = null;
            $header = $header . "MIME-Version: 1.0\r\n";
            $header = $header . "Content-Type: text/x-msmsgsinvite; charset=UTF-8\r\n\r\n";
            $header = $header . "Application-Name: File Transfer\r\n";
            $header = $header . "Application-GUID: {5D3E02AB-6190-11d3-BBBB-00C04F795683}\r\n";
            $header = $header . "Invitation-Command: INVITE\r\n";
            $header = $header . "Invitation-Cookie: " . $cookie . "\r\n";
            $header = $header . "Application-File: " . $file . "\r\n";
            $header = $header . "Application-FileSize: " . $size . "\r\n";
            $header = $header . "Connectivity: N\r\n\r\n";

            return $header;
        } else {
            return false;
        }
    }

    /**
     * @param $cookie integer
     * @return string | boolean
     */
    public static function accept($cookie)
    {
        if (isset ($cookie) && is_numeric($cookie)) {
            $header = null;
            $header = $header . "MIME-Version: 1.0\r\n";
            $header = $header . "Content-Type: text/x-msmsgsinvite; charset=UTF-8\r\n\r\n";
            $header = $header . "Invitation-Command: ACCEPT\r\n";
            $header = $header . "Invitation-Cookie: " . $cookie . "\r\n";
            $header = $header . "Launch-Application: FALSE\r\n";
            $header = $header . "Request-Data: IP-Address:\r\n\r\n";

            return $header;
        } else {
            return false;
        }
    }
}

?>