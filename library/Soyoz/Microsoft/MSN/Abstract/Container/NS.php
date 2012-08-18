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
abstract class Soyoz_Microsoft_MSN_Abstract_Container_NS extends Soyoz_Microsoft_MSN_Abstract_Container_SB
{
    /**
     * @var string
     */
    private $_clientIdString = 'PROD00974#MT*RC2';

    /**
     * @var string
     */
    private $_clientIdCode = 'LMCVO*18PQJ3H!K3';

    /**
     * @param $id string
     * @param $server string
     * @param $response string
     * @return void
     */
    protected function _parseNS($id, $server, $response)
    {
        if (isset ($id) && isset ($server) && isset ($response)) {
            $command = strtoupper(trim(reset(explode(' ', $response))));

            $robotId = $_SESSION ['system'] ['robot'] ['id'];

            if (is_numeric($command)) {
                Soyoz_Microsoft_MSN_Adapter_Log::write('[' . $id . '][' . $server . '] ' . Soyoz_Microsoft_MSN_Adapter_Helper_Error::$information [$command], array(
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
                        $this->_putOUT($id, $server);
                        break;

                    case 800 :
                        Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::setSentTime(time());
                        Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::unLock();
                        break;
                }
            } else {
                switch ($command) {
                    case 'VER' :
                        $this->_putCVR($id, $server);
                        break;

                    case 'CVR' :
                        $this->_putUSRTweener($id, $server);
                        break;

                    case 'USR' :
                        /**
                         * command, transactionId, answer
                         */
                        list ($command, $null, $answer) = explode(' ', $response);
                        $null;

                        switch ($answer) {
                            case 'TWN' :
                                Zend_Loader::loadClass('Soyoz_Microsoft_MSN_Adapter_Helper_Login');
                                /**
                                 * command, transactionId, tweener, subsequent, ticket
                                 */
                                list ($command, $null, $null, $null, $ticket) = explode(' ', $response);
                                $null;

                                $ticket = trim($ticket);
                                $ticket = Soyoz_Microsoft_MSN_Adapter_Helper_Login::request($this->_client ['passport'], $this->_client ['password'], $ticket);
                                $ticket = trim($ticket);

                                /**
                                 * Eger ticket kodu geldiyse, isleme devam et.
                                 */
                                if ($ticket) {
                                    $this->_putUSRTweenerSubsequent($id, $server, $ticket);
                                } else {
                                    $this->_putOUT($id, $server);
                                }
                                break;

                            case 'OK' :
                                $this->_putSYN($id, $server);
                                break;
                        }
                        break;

                    case 'SYN' :
                        /**
                         * command, transactionId, version, buddy, group
                         */
                        list ($command, $null, $null, $buddy, $group) = explode(' ', $response);
                        $null;

                        Soyoz_Microsoft_MSN_Adapter_Helper_Buddy::$total = $buddy;
                        Soyoz_Microsoft_MSN_Adapter_Helper_Robot::$totalGroup = $group;

                        $MSN_Buddy = new MSN_Buddy ();
                        $MSN_Buddy->update(array(
                            'STATUS' => 9
                        ), array(
                            'ROBOT_ID = ' . $robotId
                        ));
                        unset ($MSN_Buddy);
                        $this->_putCHG($id, $server, 'NLN');
                        break;

                    case 'CHG' :
                        $this->_putREA($id, $server);
                        break;

                    case 'REA' :
                        $this->_putPNG($id, $server);
                        break;

                    case 'LSG' :
                        @list ($command, $groupId, $name) = explode(' ', $response);

                        $buddy = array(
                            'groupId' => trim(intval($groupId)), 'name' => trim($name)
                        );
                        Soyoz_Microsoft_MSN_Adapter_Helper_Robot::deployGroupList($buddy);
                        break;

                    case 'LST' :
                        @list ($command, $passport, $screenName, $list, $groupId) = explode(' ', $response);

                        $buddy = array(
                            'passport' => trim($passport), 'screenName' => trim($screenName), 'list' => trim(intval($list)), 'groupId' => trim(intval($groupId))
                        );
                        Soyoz_Microsoft_MSN_Adapter_Helper_Buddy::deployList($buddy);
                        break;

                    case 'ILN' :
                        list ($command, $unused, $status, $passport, $screenName, $unused) = explode(' ', $response);
                        $unused;

                        $MSN_Buddy = new MSN_Buddy ();
                        $MSN_Buddy->updateStatus($robotId, trim($passport), trim($status));
                        unset ($MSN_Buddy);
                        break;

                    case 'NLN' :
                        list ($command, $status, $passport, $screenName, $unused) = explode(' ', $response);
                        $unused;

                        $MSN_Buddy = new MSN_Buddy ();
                        $MSN_Buddy->updateStatus($robotId, trim($passport), trim($status));
                        unset ($MSN_Buddy);
                        Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::deleteOffline(trim($passport));
                        break;

                    case 'FLN' :
                        list ($status, $passport) = explode(' ', $response);

                        $MSN_Buddy = new MSN_Buddy ();
                        $MSN_Buddy->updateStatus($robotId, trim($passport), trim($status));
                        unset ($MSN_Buddy);
                        break;

                    case 'CHL' :
                        /**
                         * command, transactionId, challenge
                         */
                        list ($command, $null, $challenge) = explode(' ', $response);
                        $null;

                        $challenge = trim($challenge);
                        $md5digest = md5($challenge . $this->_clientIdCode);

                        $this->_putQRY($id, $server, $md5digest);
                        break;

                    case 'QRY' :
                        /**
                         * Tum service ve system islemleri isConnected pointer'ini true duruma
                         * geldigi zaman aktif olarak calismaya baslayacaktir.
                         */
                        Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$isConnected = true;
                        break;

                    case 'QNG' :
                        $groupList = Soyoz_Microsoft_MSN_Adapter_Helper_Robot::getGroupList();

                        $Lookup_RobotGroup = new Lookup_RobotGroup ();
                        $Lookup_RobotGroup->add($robotId, $groupList);
                        unset ($Lookup_RobotGroup);

                        $buddyList = Soyoz_Microsoft_MSN_Adapter_Helper_Buddy::getList();
                        /**
                         * @todo
                         *
                         * Robot offline iken gelen ekleme istekleri kabul edilecek. Yani AL = 0
                         * FL = 0 BL = 0 RL = 1 olanlar kontrol edilecek ve bu kisilere ekleme
                         * istegi gonderilecek.
                         */
                        $MSN_Buddy = new MSN_Buddy ();
                        $MSN_Buddy->add($robotId, $buddyList);
                        unset ($MSN_Buddy);
                        break;

                    case 'RNG' :
                        $this->_jumpSB($command, $response, $this->_client);
                        break;

                    case 'XFR' :
                        $this->_jumpSB($command, $response, $this->_client);
                        break;

                    case 'ADD' :
                        /**
                         * command, transactionId, list, status, passport, screenName
                         */
                        list ($command, $null, $list, $status, $passport, $screenName) = explode(' ', $response);
                        $null;

                        $passport = trim($passport);
                        $screenName = rawurlencode(trim($screenName));

                        if (!$status) {
                            $limit = $_SESSION ['system'] ['robot'] ['limit'];

                            /**
                             * Bu robotun listesinde su anda kac kisi var kontrol et
                             */
                            $MSN_Buddy = new MSN_Buddy ();
                            $getBuddyList = $MSN_Buddy->fetchAll(array(
                                'ROBOT_ID = ' . $robotId
                            ));

                            if (sizeof($getBuddyList) < $limit) {
                                $getBuddyList = $MSN_Buddy->fetchAll(array(
                                    'PASSPORT = "' . $passport . '"'
                                ));

                                if (sizeof($getBuddyList) >= 1) {
                                    /**
                                     * Bu buddy, daha once baska bir robotu eklemis demektir. Buddy'nin istegini kabul
                                     * etmiyoruz fakat database'e yazacagiz. List numarasi olarak 8 gonderecegiz, bu
                                     * sadece RL'e ekledigimizi gosterir.
                                     */
                                    $list = 8;
                                } else {
                                    /**
                                     * AL, FL, RL
                                     */
                                    $list = 11;
                                }

                                $buddy = array(
                                    'passport' => trim($passport), 'screenName' => trim($screenName), 'list' => $list, 'groupId' => 0
                                );
                                Soyoz_Microsoft_MSN_Adapter_Helper_Buddy::deployList($buddy);
                                $buddyList = Soyoz_Microsoft_MSN_Adapter_Helper_Buddy::getList();

                                $addBuddy = $MSN_Buddy->add($robotId, $buddyList);

                                if ($addBuddy) {
                                    if ($list == 11) {
                                        $list = 'FL';
                                        $this->_putADD($id, $server, $status, $list, $passport, $screenName, 0);

                                        $list = 'AL';
                                        $this->_putADD($id, $server, $status, $list, $passport, $screenName);
                                    } else {
                                        /**
                                         * Bu buddy, daha once baska bir robotu eklemis, ekleme istegini kabul etme
                                         * ve PNG gonder.
                                         */
                                        $this->_putPNG($id, $server);
                                    }
                                }
                                unset ($MSN_Buddy);
                            } else {
                                $this->_putPNG($id, $server);
                                /**
                                 * @todo
                                 *
                                 * Robotun hafizasinin dolu olduguna dair e-posta gonderilecek.
                                 */
                            }
                        }
                        break;
                }
            }
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @param $ticket string
     * @return void
     */
    private function _putUSRTweenerSubsequent($id, $server, $ticket)
    {
        if (isset ($id) && isset ($server) && isset ($ticket)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "USR " . $transactionId . " TWN S t=" . $ticket . "\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @return void
     */
    private function _putSYN($id, $server)
    {
        if (isset ($id) && isset ($server)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "SYN " . $transactionId . " 0 0\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @param $status string
     * @return void
     */
    private function _putCHG($id, $server, $status)
    {
        if (isset ($id) && isset ($server) && isset ($status)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "CHG " . $transactionId . " " . $status . "\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @return void
     */
    private function _putREA($id, $server)
    {
        if (isset ($id) && isset ($server)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "REA " . $transactionId . " " . $this->_client ['passport'] . " " . rawurlencode($this->_client ['screenName']) . "\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @param $md5digest string
     * @return void
     */
    private function _putQRY($id, $server, $md5digest)
    {
        if (isset ($id) && isset ($server) && isset ($md5digest)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "QRY " . $transactionId . " " . $this->_clientIdString . " 32\r\n" . $md5digest;
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @param $status integer
     * @param $passport string
     * @param $screenName string
     * @return void
     */
    private function _putADD($id, $server, $status, $list, $passport, $screenName, $groupId = null)
    {
        if (isset ($id) && isset ($server) && isset ($status) && isset ($list) && isset ($passport) && isset ($screenName)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "ADD " . $transactionId . " " . $list . " " . $passport . " " . $screenName . " " . $groupId . "\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @param $passport string
     * @param $list string
     * @return void
     */
    private function _putREM($id, $server, $passport, $list)
    {
        if (isset ($id) && isset ($server) && isset ($passport) && isset ($list)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "REM " . $transactionId . " " . $list . " " . $passport . "\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @param $groupId integer
     * @return void
     */
    private function _putRMG($id, $server, $groupId)
    {
        if (isset ($id) && isset ($server) && isset ($groupId) && is_numeric($groupId)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "RMG " . $transactionId . " " . $groupId . "\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @return void
     */
    private function _putPNG($id, $server)
    {
        if (isset ($id) && isset ($server)) {
            $command = "PNG\r\n";
            $this->_command($command, $id, $server);
        }
    }
}

?>