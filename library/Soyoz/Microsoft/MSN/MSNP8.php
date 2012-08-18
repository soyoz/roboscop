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
Zend_Loader::loadClass('Soyoz_Microsoft_MSN_Abstract_Kernel');
Zend_Loader::loadClass('Soyoz_Microsoft_MSN_Adapter_Log');
Zend_Loader::loadClass('Soyoz_Microsoft_MSN_Adapter_Helper_Check');
Zend_Loader::loadClass('Soyoz_Microsoft_MSN_Abstract_Container_DS');
Zend_Loader::loadClass('Soyoz_Microsoft_MSN_Abstract_Container_NS');
Zend_Loader::loadClass('Soyoz_Microsoft_MSN_Abstract_Container_SB');

class Soyoz_Microsoft_MSN_MSNP8
{
    public function __construct()
    {
        Soyoz_Microsoft_MSN_Adapter_Log::$path = realpath(APPLICATION_PATH . '/../var/log');
        Soyoz_Microsoft_MSN_Adapter_Log::$name = date('Ymd.His', time()) . '.log';
    }

    public function factory($client)
    {
        if (isset ($client) && is_array($client)) {
            $ds = new Soyoz_Microsoft_MSN_Container_DS ();
            $ds->factory($client);
            unset ($ds);
        }
    }
}

?>