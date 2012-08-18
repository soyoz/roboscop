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

class MSN_Buddy_Message extends Zend_Db_Table_Abstract
{
    protected $_name = 'MSN_BUDDY_MESSAGE';
    protected $_primary = 'BUDDY_MESSAGE_ID';

    /**
     * @param $passport string
     * @param $screenName string
     * @param $message string
     * @return integer
     */
    public function add($robotId, $passport, $screenName, $message)
    {
        if (isset ($robotId) && isset ($passport) && isset ($screenName) && isset ($message)) {
            $screenName = addslashes($screenName);
            $message = addslashes($message);

            /**
             * Buddy information'ini getir
             */
            $MSN_Buddy = new MSN_Buddy ();
            $getBuddyInformation = $MSN_Buddy->fetchRow(array(
                'ROBOT_ID = ' . $robotId, 'PASSPORT = "' . $passport . '"'
            ));
            /**
             * Eger bu buddy var ise,
             */
            if (sizeof($getBuddyInformation) >= 1) {
                $buddyId = intval($getBuddyInformation->BUDDY_ID);

                /**
                 * En son gelen ScreenName'ini guncelle
                 */
                $MSN_Buddy->update(array(
                    'SCREEN_NAME' => $screenName, 'LAST_UPDATE_DATE' => time()
                ), array(
                    'ROBOT_ID = ' . $robotId, 'BUDDY_ID = ' . $buddyId
                ));

                /**
                 * Mesaji ekle
                 */
                $insert = $this->insert(array(
                    'BUDDY_ID' => $buddyId, 'MESSAGE' => $message, 'INSERT_DATE' => time()
                ));

                if ($insert) {
                    return true;
                }
            }
            unset ($MSN_Buddy);
        }
        return false;
    }
}

?>