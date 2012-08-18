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
abstract class Soyoz_Microsoft_MSN_Abstract_Kernel
{
    /**
     * @var array
     */
    protected $_connection = array(
        'hostname' => 'messenger.hotmail.com', 'port' => 1863, 'timeout' => 40
    );

    /**
     * @var array
     */
    private $_allowedConnectionParameters = array(
        'hostname' => 'MANDATORY', 'port' => 'MANDATORY', 'timeout' => 'OPTIONAL'
    );

    /**
     * @var array
     */
    protected $_client = array(
        'os' => 'winnt', 'osVersion' => '5.1', 'osArchitecture' => 'i386', 'name' => 'MSNMSGR', 'localeId' => '0x041f', 'version' => '14.0.8050.1202'
    );

    /**
     * @var array
     */
    private $_allowedClientParameters = array(
        'os' => 'OPTIONAL', 'osVersion' => 'OPTIONAL', 'osArchitecture' => 'OPTIONAL', 'name' => 'OPTIONAL', 'localeId' => 'OPTIONAL', 'version' => 'OPTIONAL', 'passport' => 'MANDATORY', 'password' => 'MANDATORY', 'screenName' => 'MANDATORY'
    );

    /**
     * @var array
     */
    private $_allowedStreamParameters = array(
        'id' => 'MANDATORY', 'server' => 'MANDATORY', 'passport' => 'OPTIONAL'
    );

    /**
     * @return integer
     */
    protected function _transactionId()
    {
        $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId++;

        return $transactionId;
    }

    /**
     * @param $server string
     * @return resource | boolean
     */
    protected function _connect($stream)
    {
        if (isset ($stream) && is_array($stream)) {
            if (Soyoz_Microsoft_MSN_Adapter_Helper_Check::parameter($stream, $this->_allowedStreamParameters)) {
                if (is_array($this->_client) && is_array($this->_connection)) {
                    if (Soyoz_Microsoft_MSN_Adapter_Helper_Check::parameter($this->_client, $this->_allowedClientParameters) && Soyoz_Microsoft_MSN_Adapter_Helper_Check::parameter($this->_connection, $this->_allowedConnectionParameters)) {
                        $this->_transactionId();
                        $errorNumber = null;
                        $errorString = null;

                        $hostname = $this->_connection ['hostname'];
                        $port = $this->_connection ['port'];
                        $timeout = $this->_connection ['timeout'];

                        $server = $stream ['server'];
                        $id = $stream ['id'];

                        Soyoz_Microsoft_MSN_Adapter_Log::write('[' . $id . '][' . $server . '] Connecting to ' . $hostname . ':' . $port, array(
                            'type' => 'SEND'
                        ));

                        Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$stream [$id] [$server] = fsockopen($hostname, $port, $errorNumber, $errorString, $timeout);

                        if (isset ($stream ['passport']) && $stream ['passport']) {
                            $passport = $stream ['passport'];
                            Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$stream [$id] ['passport'] = $passport;
                        }

                        $resource = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$stream [$id] [$server];
                        if ($resource && is_resource($resource)) {
                            stream_set_blocking($resource, false);
                            Soyoz_Microsoft_MSN_Adapter_Log::write('[' . $id . '][' . $server . '] Connected to ' . $hostname . ':' . $port, array(
                                'type' => 'SEND'
                            ));

                            return $resource;
                        } else {
                            return false;
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param $server string
     * @param $response string
     * @param $client array
     * @return void
     */
    protected function _jumpNS($response, $client)
    {
        if (isset ($response) && isset ($client)) {
            Zend_Loader::loadClass('Soyoz_Microsoft_MSN_Container_NS');
            /**
             * command, transactionId, referralType, address, unknown, hostname
             */
            list ($null, $null, $null, $address, $null, $hostname) = explode(' ', $response);
            list ($hostname, $port) = explode(':', $address);
            $null;

            $ns = new Soyoz_Microsoft_MSN_Container_NS ();
            $ns->factory(array(
                'hostname' => $hostname, 'port' => $port, 'client' => $client
            ));
            unset ($ns);
        }
    }

    /**
     * @param $command string
     * @param $response string
     * @param $client string
     * @return void
     */
    protected function _jumpSB($command, $response, $client)
    {
        if (isset ($command) && isset ($response) && isset ($client)) {
            Zend_Loader::loadClass('Soyoz_Microsoft_MSN_Container_SB');

            switch ($command) {
                case 'RNG' :
                    /**
                     * command, sessionId, address, authentication, ticket, passport, screenName
                     */
                    list ($command, $sessionId, $address, $null, $ticket, $passport, $null) = explode(' ', $response);
                    list ($hostname, $port) = explode(':', $address);
                    $null;

                    $config = array(
                        'client' => $client, 'hostname' => $hostname, 'port' => $port, 'ticket' => $ticket, 'passport' => $passport, 'sessionId' => $sessionId
                    );
                    break;

                case 'XFR' :
                    /**
                     * command, transactionId, referralType, address, authentication, ticket
                     */
                    list ($command, $null, $null, $address, $null, $ticket) = explode(' ', $response);
                    list ($hostname, $port) = explode(':', $address);
                    $null;

                    if (Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$call ['passport'] && Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$call ['header']) {
                        $passport = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$call ['passport'];
                    } else {
                        $passport = false;
                    }
                    $config = array(
                        'client' => $client, 'hostname' => $hostname, 'port' => $port, 'ticket' => trim($ticket), 'passport' => $passport
                    );
                    break;
            }

            $sb = new Soyoz_Microsoft_MSN_Container_SB ();
            $sb->factory($config);
            unset ($sb);
        }
    }

    /**
     * @return void
     */
    protected function _sendMSG()
    {
        if (Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::isLocked()) {
            $getMessageQueue = Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::get();

            $passport = $getMessageQueue ['passport'];
            $header = Soyoz_Microsoft_MSN_Adapter_Helper_Message::send($getMessageQueue ['header']);

            $passportIsExists = false;
            /**
             * Mesaj gonderilmek istenilen buddy icin daha once soket baglanti tanimlanmis mi
             * kontrol et.
             *
             * Eger buddy icin bir soket baglanti varsa, mesaji o sokete gonder.
             */
            foreach (Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$stream as $id => $value) {
                foreach ($value as $streamingServer => $streamingResource) {
                    if ($passport && $streamingServer == 'passport' && $streamingResource == $passport) {
                        $passportIsExists = true;
                        $streamId = $id;
                        $server = 'ss';
                    } else {
                        if ($passportIsExists == false && $streamingServer == 'ns') {
                            $streamId = $id;
                            $server = $streamingServer;
                        }
                    }
                }
            }

            if ($passportIsExists) {
                $this->_putMSG($streamId, $server, $header);

                $notificationId = $getMessageQueue ['id'];

                $System_Notification = new System_Notification ();
                if ($System_Notification->markAsRead($notificationId)) {
                    Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue::unLock();
                }
                unset ($System_Notification);
            } else {
                Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$call ['passport'] = $passport;
                Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$call ['header'] = $header;

                $this->_putXFR($streamId, $server);
            }
        }
    }

    /**
     * @return void
     */
    protected function _logOUT()
    {
        foreach (Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$stream as $id => $value) {
            foreach ($value as $streamingServer => $streamingResource) {
                $streamingResource;

                $this->_putOUT($id, $streamingServer);
            }
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @return void
     */
    protected function _putVER($id, $server)
    {
        if (isset ($id) && isset ($server)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "VER " . $transactionId . " MSNP8 MSNP9 CVR0\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @return void
     */
    protected function _putCVR($id, $server)
    {
        if (isset ($id) && isset ($server)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "CVR " . $transactionId . " " . $this->_client ['localeId'] . " " . $this->_client ['os'] . " " . $this->_client ['osVersion'] . " " . $this->_client ['osArchitecture'] . " " . $this->_client ['name'] . " " . $this->_client ['version'] . " msmsgs " . $this->_client ['passport'] . "\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @return void
     */
    protected function _putUSRTweener($id, $server)
    {
        if (isset ($id) && isset ($server)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "USR " . $transactionId . " TWN I " . $this->_client ['passport'] . "\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @return void
     */
    protected function _putOUT($id, $server)
    {
        if (isset ($id) && isset ($server)) {
            $command = "OUT\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @return void
     */
    protected function _putXFR($id, $server)
    {
        if (isset ($id) && isset ($server)) {
            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "XFR " . $transactionId . " SB\r\n";
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $id integer
     * @param $server string
     * @param $message string
     * @return void
     */
    protected function _putMSG($id, $server, $header)
    {
        if (isset ($id) && isset ($server) && isset ($header) && $header) {
            $payload = intval(strlen($header));

            $transactionId = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$transactionId;
            $command = "MSG " . $transactionId . " N " . $payload . "\r\n" . $header;
            $this->_command($command, $id, $server);
        }
    }

    /**
     * @param $command string
     * @param $server string
     * @return boolean
     */
    protected function _command($command, $id, $server)
    {
        if (isset ($command) && isset ($id) && isset ($server)) {
            $this->_put($command, $id, $server);
            return true;
        }
        return false;
    }

    /**
     * @param $command string
     * @param $id integer
     * @param $server string
     * @return boolean
     */
    protected function _put($command, $id, $server)
    {
        if (isset ($command) && isset ($id) && isset ($server)) {
            $this->_transactionId();
            $resource = $this->_getResource($id, $server);

            if ($resource) {
                if (is_resource($resource) && !feof($resource)) {
                    Soyoz_Microsoft_MSN_Adapter_Log::write('[' . $id . '][' . $server . '] ' . $command, array(
                        'type' => 'SEND'
                    ));

                    fwrite($resource, $command);

                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * @param $id integer
     * @param $server string
     * @return resource | boolean
     */
    protected function _get($id, $server)
    {
        if (isset ($id) && isset ($server)) {
            foreach (Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$socket as $key => $value) {
                unset (Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$socket [$key]);
            }

            foreach (Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$stream as $value) {
                foreach ($value as $resource) {
                    array_push(Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$socket, $resource);
                }
            }

            $socket = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$socket;

            usleep(100000);
            if (@stream_select($reading = $socket, $w = null, $e = null, 0) > 0) {
                $w;
                $e;
                foreach ($reading as $resource) {
                    if (is_resource($resource)) {
                        if (!feof($resource)) {
                            if ($server == 'ns') {
                                $read = fgets($resource, 4096);
                            } else {
                                $read = fread($resource, 4096);
                            }

                            if ($read) {
                                foreach (Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$stream as $sessionId => $value) {
                                    foreach ($value as $streamingServer => $streamingResource) {
                                        if ($resource == $streamingResource) {
                                            $id = $sessionId;
                                            $server = $streamingServer;
                                        }
                                    }
                                }
                                Soyoz_Microsoft_MSN_Adapter_Log::write('[' . $id . '][' . $server . '] ' . $read, array(
                                    'type' => 'RECV'
                                ));

                                $return = array(
                                    'id' => $id, 'server' => $server, 'response' => $read
                                );
                                return $return;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param $id integer
     * @param $server string
     * @return boolean
     */
    protected function _close($id, $server)
    {
        if (isset ($id) && isset ($server)) {
            $resource = $this->_getResource($id, $server);

            if ($resource) {
                if (is_resource($resource)) {
                    Soyoz_Microsoft_MSN_Adapter_Log::write('[' . $id . '][' . $server . '] Connection closed.', array(
                        'type' => 'RECV'
                    ));
                    fclose($resource);
                    unset (Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$stream [$id]);

                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * @param $id integer
     * @param $server string
     * @return resource
     */
    protected function _getResource($id, $server)
    {
        if (isset ($id) && isset ($server)) {
            $resource = Soyoz_Microsoft_MSN_Adapter_Helper_Pointer::$stream [$id] [$server];

            if (is_resource($resource)) {
                return $resource;
            } else {
                return false;
            }
        }
        return false;
    }
}

?>