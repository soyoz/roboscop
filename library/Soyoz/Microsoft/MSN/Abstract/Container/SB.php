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
abstract class Soyoz_Microsoft_MSN_Abstract_Container_SB extends Soyoz_Microsoft_MSN_Abstract_Kernel
{
    /**
     * @var array
     */
    protected $_config = array(
        'password' => 'test123456'
    );

    /**
     * @param $sessionId string
     * @param $resource resource
     * @param $response string
     * @return void
     */
    protected function _parseSB($sessionId, $server, $response)
    {
        if (isset ($sessionId) && isset ($server) && isset ($response)) {
            $command = strtoupper(trim(reset(explode(' ', $response))));

            $robotId = $_SESSION ['system'] ['robot'] ['id'];
            if (is_numeric($command)) {
                Soyoz_Microsoft_MSN_Adapter_Log::write('[' . $sessionId . '][' . $server . '] ' . Soyoz_Microsoft_MSN_Adapter_Helper_Error::$information [$command], array(
                    'type' => 'RECV'
                ));

                switch ($command) {
                    case 217 :
                        if (Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::isLocked()) {
                            $getMessageQueue = Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::get();
                            $passport = $getMessageQueue ['passport'];

                            Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::addOffline($passport);
                            Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::unLock();
                        }
                        $this->_putOUT($sessionId, $server);
                        break;

                    case 800 :
                        Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::setSentTime(time());
                        Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::unLock();
                        break;
                }
            } else {
                switch ($command) {
                    case 'ANS' :

                        break;

                    case 'USR' :
                        $passport = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$call ['passport'];

                        if ($passport) {
                            $this->_putCAL($sessionId, $server, $passport);
                        }
                        break;

                    case 'CAL' :

                        break;

                    case 'JOI' :
                        $header = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$call ['header'];

                        if ($header) {
                            $this->_putMSG($sessionId, $server, $header);

                            if (Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::isLocked()) {
                                $getMessageQueue = Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::get();
                                $notificationId = $getMessageQueue ['id'];

                                $System_Notification = new System_Notification ();
                                if ($System_Notification->markAsRead($notificationId)) {
                                    Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::unLock();
                                }
                                unset ($System_Notification);
                            }
                        }
                        break;

                    case 'IRO' :

                        break;

                    case 'MSG' :
                        list ($command, $passport, $screenName, $length) = explode(' ', $response);
                        $length = intval($length);

                        $passport = trim($passport);
                        $screenName = rawurldecode($screenName);
                        $message = substr($response, -$length);

                        $messageParse = Soyoz_Microsoft_MSN_Adapter_Helper_Message::parse($message);

                        if (isset ($messageParse) && is_array($messageParse)) {
                            /**
                             * Eger MSNFTP islemi ise
                             */
                            if (isset ($messageParse ['Invitation-Command'])) {
                                $command = $messageParse ['Invitation-Command'];

                                switch ($command) {
                                    /**
                                     * Eger karsi taraf dosya gondermeye calisiyorsa
                                     */
                                    case 'INVITE' :
                                        $MSN_Buddy_Message = new MSN_Buddy_Message ();
                                        $MSN_Buddy_Message->add($robotId, $passport, $screenName, $message);
                                        unset ($MSN_Buddy_Message);

                                        $cookie = $messageParse ['Invitation-Cookie'];

                                        $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::cancel($cookie);
                                        $this->_putMSG($sessionId, $server, $header);

                                        $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send('Roboscop Kabul servisi durdurulmuştur. Bilginize.');
                                        $this->_putMSG($sessionId, $server, $header);
                                        break;

                                    /**
                                     * Eger robot dosya gondermeye calisiyorsa
                                     */
                                    case 'ACCEPT' :

                                        break;
                                }
                            }

                            /**
                             * Eger normal bir text mesaj ise
                             */
                            if (isset ($messageParse ['X-MMS-IM-Format'])) {
                                $message = Soyoz_Microsoft_MSN_Adapter_Helper_Message::get($message);
                                $command = Soyoz_Microsoft_MSN_Adapter_Helper_Message::getCommand($message);
                                $command = Soyoz_Microsoft_MSN_Adapter_Helper_Function::strtolower($command);
                                /**
                                 * Gelen tum mesajlari database'e kaydet.
                                 */
                                $MSN_Buddy_Message = new MSN_Buddy_Message ();
                                $add = $MSN_Buddy_Message->add($robotId, $passport, $screenName, $message);
                                unset ($MSN_Buddy_Message);

                                if ($add) {
                                    /**
                                     * Eger gelen mesaj bir komut ise
                                     */
                                    if ($command) {
                                        switch ($command) {
                                            case 'feed' :
                                                $checkServiceIsAvailable = Soyoz_Microsoft_MSN_Adapter_Helper_System::checkServiceIsAvailable('feed');

                                                if ($checkServiceIsAvailable) {
                                                    $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send('(o) Seni biraz bekleteceğim, şu anda işlemini gerçekleştiriyorum...');
                                                    $this->_putMSG($sessionId, $server, $header);

                                                    $header = Soyoz_Microsoft_MSN_Adapter_Command_Feed::run($robotId, $passport, $screenName, $message);
                                                    $this->_putMSG($sessionId, $server, $header);
                                                } else {
                                                    $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send(':( Üzgünüm, maalesef feed servisi hizmet listemde bulunmuyor.');
                                                    $this->_putMSG($sessionId, $server, $header);
                                                }
                                                break;

                                            case 'tercume' :
                                                $checkServiceIsAvailable = Soyoz_Microsoft_MSN_Adapter_Helper_System::checkServiceIsAvailable('tercume');

                                                if ($checkServiceIsAvailable) {
                                                    $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send('(o) Seni biraz bekleteceğim, şu anda işlemini gerçekleştiriyorum...');
                                                    $this->_putMSG($sessionId, $server, $header);

                                                    $header = Soyoz_Microsoft_MSN_Adapter_Command_Tercume::run($message);
                                                    $this->_putMSG($sessionId, $server, $header);
                                                } else {
                                                    $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send(':( Üzgünüm, maalesef tercume servisi hizmet listemde bulunmuyor.');
                                                    $this->_putMSG($sessionId, $server, $header);
                                                }
                                                break;

                                            case 'muzik' :
                                                $checkServiceIsAvailable = Soyoz_Microsoft_MSN_Adapter_Helper_System::checkServiceIsAvailable('muzik');

                                                if ($checkServiceIsAvailable) {
                                                    $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send('(o) Seni biraz bekleteceğim, şu anda işlemini gerçekleştiriyorum...');
                                                    $this->_putMSG($sessionId, $server, $header);

                                                    $header = Soyoz_Microsoft_MSN_Adapter_Command_Muzik::run($message);
                                                    $this->_putMSG($sessionId, $server, $header);
                                                } else {
                                                    $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send(':( Üzgünüm, maalesef muzik servisi hizmet listemde bulunmuyor.');
                                                    $this->_putMSG($sessionId, $server, $header);
                                                }
                                                break;

                                            case 'finans' :
                                                $checkServiceIsAvailable = Soyoz_Microsoft_MSN_Adapter_Helper_System::checkServiceIsAvailable('finans');

                                                if ($checkServiceIsAvailable) {
                                                    $header = Soyoz_Microsoft_MSN_Adapter_Command_Finans::run($message);
                                                    $this->_putMSG($sessionId, $server, $header);
                                                } else {
                                                    $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send(':( Üzgünüm, maalesef finans servisi hizmet listemde bulunmuyor.');
                                                    $this->_putMSG($sessionId, $server, $header);
                                                }
                                                break;

                                            case 'indir' :
                                                $checkServiceIsAvailable = Soyoz_Microsoft_MSN_Adapter_Helper_System::checkServiceIsAvailable('indir');

                                                if ($checkServiceIsAvailable) {
                                                    $header = Soyoz_Microsoft_MSN_Adapter_Command_Indir::run($message);
                                                    $this->_putMSG($sessionId, $server, $header);
                                                } else {
                                                    $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send(':( Üzgünüm, maalesef indir servisi hizmet listemde bulunmuyor.');
                                                    $this->_putMSG($sessionId, $server, $header);
                                                }
                                                break;

                                            case 'yardim' :
                                                $header = Soyoz_Microsoft_MSN_Adapter_Command_Yardim::run($message);
                                                $this->_putMSG($sessionId, $server, $header);
                                                break;

                                            case 'defol' :
                                                $parameter = Soyoz_Microsoft_MSN_Adapter_Helper_Message::getParameter($message);

                                                if ($parameter == $this->_config ['password']) {
                                                    $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send('Tabii ki efendim!');
                                                    $this->_putMSG($sessionId, $server, $header);

                                                    $this->_logOUT();
                                                }
                                                break;

                                            /**
                                             * Tanimlanamayan komut ya da parametre
                                             */
                                            default :
                                                $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send('<:o) Tanımlanmamış ya da geçersiz bir komut isteğinde bulundunuz.');
                                                $this->_putMSG($sessionId, $server, $header);
                                                break;
                                        }
                                    } else {
                                        $header = Soyoz_Microsoft_MSN_Adapter_Command_Yardim::run($message);
                                        $this->_putMSG($sessionId, $server, $header);
                                    }
                                }
                            }
                        }
                        break;

                    case 'BYE' :
                        $this->_putOUT($sessionId, $server);
                        break;

                    case 'OUT' :
                        $this->_putOUT($sessionId, $server);
                        break;
                }
            }
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @param $ticket string
     * @param $sessionId string
     * @return void
     */
    protected function _putANS($id, $server, $ticket, $sessionId)
    {
        if (isset ($id) && isset ($server) && isset ($ticket) && isset ($sessionId)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "ANS " . $transactionId . " " . $this->_client ['passport'] . " " . $ticket . " " . $sessionId . "\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @param $ticket string
     * @return void
     */
    protected function _putUSR($id, $server, $ticket)
    {
        if (isset ($id) && isset ($server) && isset ($ticket)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "USR " . $transactionId . " " . $this->_client ['passport'] . " " . $ticket . "\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @param $passport string
     * @return void
     */
    private function _putCAL($id, $server, $passport)
    {
        if (isset ($id) && isset ($server) && isset ($passport)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "CAL " . $transactionId . " " . $passport . "\r\n";
            $this->_command($command, $id, $server);
        }
    }
}

?>