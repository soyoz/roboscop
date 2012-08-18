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

class System_Notification extends Zend_Db_Table_Abstract
{
    protected $_name = 'SYSTEM_NOTIFICATION';
    protected $_primary = 'NOTIFICATION_ID';

    /**
     * @param integer $notificationId
     */
    public function check($robotId)
    {
        if (isset ($robotId)) {
            $sql = '
			SELECT 
			SN.*,
			MB.*
			FROM
			SYSTEM_NOTIFICATION AS SN
			INNER JOIN MSN_BUDDY AS MB ON SN.BUDDY_ID = MB.BUDDY_ID
			WHERE
			MB.ROBOT_ID = ' . $robotId . '
			AND
			SN.IS_READ = 0
			AND
			MB.STATUS <> 9
			ORDER BY RAND()
			';
            $getNotificationList = $this->_db->query($sql)->fetch();

            if ($getNotificationList) {
                $message = array(
                    'id' => $getNotificationList ['NOTIFICATION_ID'], 'buddyId' => $getNotificationList ['BUDDY_ID'], 'passport' => $getNotificationList ['PASSPORT'], 'header' => $getNotificationList ['MESSAGE']
                );
                return $message;
            }
        }
        return false;
    }

    /**
     * @param integer $notificationId
     */
    public function markAsRead($notificationId)
    {
        if (isset ($notificationId)) {
            $update = $this->update(array(
                'IS_READ' => 1, 'LAST_UPDATE_DATE' => time()
            ), array(
                'NOTIFICATION_ID = ' . $notificationId
            ));

            if (sizeof($update) >= 1) {
                return true;
            }
        }
        return false;
    }
}

?>
