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

class Lookup_RobotGroup extends Zend_Db_Table_Abstract
{
    protected $_name = 'LOOKUP_ROBOTGROUP';
    protected $_primary = 'ROBOT_GROUP_ID';

    public function add($robotId, $groupList)
    {
        if (isset ($robotId) && isset ($groupList) && is_array($groupList)) {
            if (sizeof($groupList) >= 1) {
                foreach ($groupList as $value) {
                    $groupId = intval($value ['groupId']);
                    $name = $value ['name'];

                    if ($name) {
                        $checkGroupIsExists = $this->fetchRow(array(
                            'ROBOT_ID = ' . $robotId, 'ID = ' . $groupId
                        ));

                        if (sizeof($checkGroupIsExists) <= 0) {
                            $this->insert(array(
                                'ROBOT_ID' => $robotId, 'ID' => $groupId, 'NAME' => $name
                            ));
                        } else {
                            $this->update(array(
                                'ID' => $groupId, 'NAME' => $name
                            ), array(
                                'ROBOT_ID = ' . $robotId, 'ID = ' . $groupId
                            ));
                        }
                    }
                }
            }
        }
    }
}

?>