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
class Soyoz_Microsoft_MSN_Adapter_Log
{
    /**
     * @var string
     */
    public static $path = null;

    /**
     * @var string
     */
    public static $name = 'some.log';

    /**
     * @var array
     */
    private static $_allowedOptionsParameters = array(
        'type' => 'OPTIONAL'
    );

    /**
     * @param $message string
     * @param array $options
     * @return void
     */
    public static function write($message, array $options = array('type' => 'INFO'))
    {
        Zend_Loader::loadClass('Soyoz_Microsoft_MSN_Adapter_Helper_Check');
        if (Soyoz_Microsoft_MSN_Adapter_Helper_Check::parameter($options, self::$_allowedOptionsParameters)) {
            $path = self::$path;
            $name = self::$name;

            if (isset ($path) && $path != null) {
                if (isset ($message)) {
                    if (is_dir($path) && is_writable($path)) {
                        $filePointer = fopen($path . DIRECTORY_SEPARATOR . $name, 'a+');

                        if ($filePointer) {
                            switch (strtoupper($options ['type'])) {
                                case 'SEND' :
                                    $title = "SEND";
                                    break;

                                case 'RECV' :
                                    $title = "RECV";
                                    break;

                                case 'INFO' :
                                    $title = "INFO";
                                    break;

                                default :
                                    $title = "INFO";
                                    break;
                            }
                            $date = date("d-m-Y H:i:s");
                            $message = "[" . $date . "][" . $title . "] " . $message . "\n";

                            fwrite($filePointer, $message);
                            fclose($filePointer);
                        }
                    } else {
                        die ('Log file does not match or does not file or does not writable. Please check the log file: ' . self::$path);
                    }
                } else {
                    die ('Please set the log message.');
                }
            } else {
                die ('Please set the log file path.');
            }
        }
    }
}

?>
