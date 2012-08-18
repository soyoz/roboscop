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
abstract class Soyoz_Microsoft_MSN_Abstract_Container_DS extends Soyoz_Microsoft_MSN_Abstract_Container_NS
{
    /**
     * @param $id string
     * @param $resource resource
     * @param $response string
     * @return void
     */
    protected function _parseDS($id, $server, $response)
    {
        if (isset ($id) && isset ($server) && isset ($response)) {
            $command = strtoupper(trim(reset(explode(' ', $response))));

            if (is_numeric($command)) {
                Soyoz_Microsoft_MSN_Adapter_Log::write('[' . $id . '][' . $server . '] ' . Soyoz_Microsoft_MSN_Adapter_Helper_Error::$information [$command], array(
                    'type' => 'RECV'
                ));

                $this->_putOUT($id, $server);
            } else {
                switch ($command) {
                    case 'VER' :
                        $this->_putCVR($id, $server);
                        break;

                    case 'CVR' :
                        $this->_putUSRTweener($id, $server);
                        break;

                    case 'XFR' :
                        $this->_jumpNS($response, $this->_client);
                        break;
                }
            }
        }
    }
}

?>