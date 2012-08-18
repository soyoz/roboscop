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
class Soyoz_Microsoft_MSN_Adapter_Command_Yardim
{
    /**
     * @param $messageId integer
     * @return boolean
     */
    public static function run($message)
    {
        if (isset ($message)) {
            $parameter = Soyoz_Microsoft_MSN_Adapter_Helper_Message::getParameter($message);
            $parameter = explode(' ', $parameter);

            if ($parameter && is_array($parameter)) {
                if (sizeof($parameter) >= 1) {
                    $command = strtolower($parameter [0]);

                    switch ($command) {
                        case 'feed' :
                            $checkServiceIsAvailable = Soyoz_Microsoft_MSN_Adapter_Helper_System::checkServiceIsAvailable('feed');

                            if ($checkServiceIsAvailable) {
                                $description = Soyoz_Microsoft_MSN_Adapter_Command_Feed::$information ['description'];
                            }
                            break;

                        case 'tercume' :
                            $checkServiceIsAvailable = Soyoz_Microsoft_MSN_Adapter_Helper_System::checkServiceIsAvailable('tercume');

                            if ($checkServiceIsAvailable) {
                                $description = Soyoz_Microsoft_MSN_Adapter_Command_Tercume::$information ['description'];
                            }
                            break;

                        case 'muzik' :
                            $checkServiceIsAvailable = Soyoz_Microsoft_MSN_Adapter_Helper_System::checkServiceIsAvailable('muzik');

                            if ($checkServiceIsAvailable) {
                                $description = Soyoz_Microsoft_MSN_Adapter_Command_Muzik::$information ['description'];
                            }
                            break;

                        case 'finans' :
                            $checkServiceIsAvailable = Soyoz_Microsoft_MSN_Adapter_Helper_System::checkServiceIsAvailable('finans');

                            if ($checkServiceIsAvailable) {
                                $description = Soyoz_Microsoft_MSN_Adapter_Command_Finans::$information ['description'];
                            }
                            break;

                        case 'indir' :
                            $checkServiceIsAvailable = Soyoz_Microsoft_MSN_Adapter_Helper_System::checkServiceIsAvailable('indir');

                            if ($checkServiceIsAvailable) {
                                $description = Soyoz_Microsoft_MSN_Adapter_Command_Indir::$information ['description'];
                            }
                            break;

                        default :
                            $description = false;
                            break;
                    }
                }
            }

            if (isset ($description) && $description) {
                $header = $description;
            } else {
                /**
                 * Robot icin tanimlanan servis listesini kontrol et ve ona gore bir yardim
                 * menusu hazirla.
                 */
                if (isset ($_SESSION ['system'] ['robot'] ['service'])) {
                    $serviceList = $_SESSION ['system'] ['robot'] ['service'];
                    $header = null;
                    foreach ($serviceList as $value) {
                        $header = $header . '!' . $value . ' ';
                    }
                    $header = "(i) Size hizmet verebileceğim servisler: " . $header . "\r\n\r\n(*) Servislerin kullanımı ile ilgili detaylı bilgi almak için; http://labs.soyoz.com/roboscop/services";
                } else {
                    $header = ":( Maalesef şu anda size verebileceğim bir hizmet yok.";
                }
            }

            $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send($header);

            return $header;
        }
        return false;
    }
}

?>