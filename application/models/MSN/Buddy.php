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

class MSN_Buddy extends Zend_Db_Table_Abstract
{
    protected $_name = 'MSN_BUDDY';
    protected $_primary = 'BUDDY_ID';

    /**
     * @param $data array
     * @return boolean
     */
    public function add($robotId, $buddyList)
    {
        if (isset ($robotId) && isset ($buddyList) && is_array($buddyList)) {
            if (sizeof($buddyList) >= 1) {
                foreach ($buddyList as $value) {
                    $passport = $value ['passport'];
                    $screenName = $value ['screenName'];
                    $list = $value ['list'];
                    $groupId = $value ['groupId'];

                    if ($passport) {
                        $Lookup_RobotGroup = new Lookup_RobotGroup ();
                        $checkRobotGroupIsExists = $Lookup_RobotGroup->fetchRow(array(
                            'ROBOT_ID = ' . $robotId, 'ID = ' . $groupId
                        ));

                        if (sizeof($checkRobotGroupIsExists) >= 1) {
                            $groupId = $checkRobotGroupIsExists->ROBOT_GROUP_ID;
                        } else {
                            $groupId = $Lookup_RobotGroup->insert(array(
                                'ROBOT_ID' => $robotId, 'ID' => $groupId
                            ));
                        }
                        unset ($Lookup_RobotGroup);
                        $FL = null;
                        $AL = null;
                        $BL = null;
                        $RL = null;

                        foreach ($list as $name => $binary) {
                            ${$name} = $binary;
                        }

                        $checkBuddyIsExists = $this->fetchRow(array(
                            'ROBOT_ID = ' . $robotId, 'PASSPORT = "' . $passport . '"'
                        ));

                        if (sizeof($checkBuddyIsExists) <= 0) {
                            $insert = $this->insert(array(
                                'ROBOT_ID' => $robotId, 'ROBOT_GROUP_ID' => $groupId, 'PASSPORT' => $passport, 'SCREEN_NAME' => $screenName, 'FL' => $FL, 'AL' => $AL, 'BL' => $BL, 'RL' => $RL, 'INSERT_DATE' => time(), 'STATUS' => 1
                            ));
                        } else {
                            $buddyId = $checkBuddyIsExists->BUDDY_ID;

                            $update = $this->update(array(
                                'SCREEN_NAME' => $screenName, 'FL' => $FL, 'AL' => $AL, 'BL' => $BL, 'RL' => $RL, 'LAST_UPDATE_DATE' => time()
                            ), array(
                                'ROBOT_ID = ' . $robotId, 'BUDDY_ID = ' . $buddyId
                            ));
                        }
                    }
                }

                if (isset ($insert) || isset ($update)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param integer $robotId
     * @param string $passport
     * @param string $status
     *
     * @return void
     */
    public function updateStatus($robotId, $passport, $status)
    {
        if (isset ($robotId) && isset ($passport) && isset ($status)) {
            $MSN_Buddy = new MSN_Buddy ();
            $getBuddyInformation = $MSN_Buddy->fetchRow(array(
                'ROBOT_ID = ' . $robotId, 'PASSPORT = "' . $passport . '"'
            ));

            if (sizeof($getBuddyInformation) >= 1) {
                $MSN_Buddy->update(array(
                    'STATUS' => Soyoz_Microsoft_MSN_Adapter_Helper_Buddy::$status [$status]
                ), array(
                    'ROBOT_ID = ' . $robotId, 'PASSPORT = "' . $passport . '"'
                ));
            }
            unset ($MSN_Buddy);
        }
    }
}

?>
