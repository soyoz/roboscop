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
abstract class Soyoz_Microsoft_MSN_Abstract_Factory extends Soyoz_Microsoft_MSN_Abstract_Container_DS
{
    protected function _parse($response)
    {
        if (isset ($response) && is_array($response)) {
            if (isset ($response ['id']) && isset ($response ['server']) && isset ($response ['response'])) {
                $id = $response ['id'];
                $server = $response ['server'];
                $response = $response ['response'];

                switch ($server) {
                    case 'ds' :
                        $this->_parseDS($id, $server, $response);
                        break;

                    case 'ns' :
                        $this->_parseNS($id, $server, $response);
                        break;

                    case 'ss' :
                        $this->_parseSB($id, $server, $response);
                        break;
                }
            }
        }
    }
}

?>