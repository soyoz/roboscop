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
class Soyoz_Microsoft_MSN_Container_NS extends Soyoz_Microsoft_MSN_Abstract_Factory
{
    /**
     * @var array
     */
    private $_allowedConfigParameters = array(
        'hostname' => 'MANDATORY', 'port' => 'MANDATORY', 'client' => 'MANDATORY'
    );

    /**
     * @param $config array
     * @return void
     */
    public function factory($config)
    {
        if (isset ($config) && is_array($config)) {
            if (Soyoz_Microsoft_MSN_Adapter_Helper_Check::parameter($config, $this->_allowedConfigParameters)) {
                $hostname = $config ['hostname'];
                $port = $config ['port'];
                $client = $config ['client'];

                $this->_client = array_merge($this->_client, $client);

                $this->_connection ['hostname'] = $hostname;
                $this->_connection ['port'] = $port;

                $id = uniqid();
                $server = 'ns';

                $stream = array(
                    'id' => $id, 'server' => $server
                );
                $resource = $this->_connect($stream);

                if ($resource && is_resource($resource)) {
                    $this->_putVER($id, $server);

                    $robotId = $_SESSION ['system'] ['robot'] ['id'];

                    $System_Notification = new System_Notification ();
                    while (!feof($resource)) {
                        $response = $this->_get($id, $server);

                        if (is_array($response)) {
                            $this->_parse($response);
                        } else {
                            if (Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$isConnected) {
                                if (Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::checkSentTime()) {
                                    if (time() > strtotime('+1 minutes', Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::getSentTime())) {
                                        Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::clearSentTime();
                                    }
                                }

                                if (Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::checkSentTime() == false) {
                                    if (Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::isLocked() == false) {
                                        $getNotification = $System_Notification->check($robotId);

                                        if ($getNotification && is_array($getNotification)) {
                                            $notificationId = $getNotification ['id'];
                                            $buddyId = $getNotification ['buddyId'];
                                            $passport = $getNotification ['passport'];
                                            $header = $getNotification ['header'];

                                            if (Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::isOffline($passport) == false) {
                                                Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::lock($notificationId, $buddyId, $passport, $header);
                                                $this->_sendMSG();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    unset ($System_Notification);

                    $this->_close($id, $server);
                }
            }
        }
    }
}

?>