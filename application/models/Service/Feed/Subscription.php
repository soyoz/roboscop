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
require_once 'Zend/Db/Table/Abstract.php';

class Service_Feed_Subscription extends Zend_Db_Table_Abstract
{
    protected $_name = 'SERVICE_FEED_SUBSCRIPTION';
    protected $_primary = 'FEED_SUBSCRIPTION_ID';

    public function getList($buddyId)
    {
        if (isset ($buddyId)) {
            $sql = '
			SELECT
			SFS.*,
			SF.FEED_ID AS SF_FEED_ID,
			SF.URL AS SF_URL,
			SF.INSERT_DATE AS SF_INSERT_DATE,
			SF.LAST_UPDATE_DATE AS SF_LAST_UPDATE_DATE,
			SF.STATUS AS SF_STATUS
			FROM
			SERVICE_FEED_SUBSCRIPTION AS SFS
			INNER JOIN SERVICE_FEED AS SF ON SFS.FEED_ID = SF.FEED_ID
			WHERE
			SFS.BUDDY_ID = ' . $buddyId;

            $getList = $this->_db->query($sql)->fetchAll();

            if ($getList) {
                return $getList;
            }
        }
        return false;
    }

    public function getListByStatus($status)
    {
        if (isset ($status)) {
            $sql = '
			SELECT
			SFS.*,
			SF.FEED_ID AS SF_FEED_ID,
			SF.URL AS SF_URL,
			SF.INSERT_DATE AS SF_INSERT_DATE,
			SF.LAST_UPDATE_DATE AS SF_LAST_UPDATE_DATE,
			SF.STATUS AS SF_STATUS
			FROM
			SERVICE_FEED_SUBSCRIPTION AS SFS
			INNER JOIN SERVICE_FEED AS SF ON SFS.FEED_ID = SF.FEED_ID
			WHERE
			SFS.STATUS = ' . $status;

            $getListByStatus = $this->_db->query($sql)->fetchAll();

            if ($getListByStatus) {
                return $getListByStatus;
            }
        }
        return false;
    }
}

?>