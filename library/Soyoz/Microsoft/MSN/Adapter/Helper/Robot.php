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
class Soyoz_Microsoft_MSN_Adapter_Helper_Robot
{
    /**
     * @var integer
     */
    public static $totalGroup;

    /**
     * @var integer
     */
    private static $_groupId = 0;

    /**
     * @var array
     */
    private static $_allowedRobotGroupParameters = array(
        'groupId' => 'MANDATORY', 'name' => 'MANDATORY'
    );

    /**
     * @var array
     */
    private static $_groupList = array(
        array(
            'groupId' => false, 'name' => false
        )
    );

    /**
     * @param array $group
     * @return boolean
     */
    public static function deployGroupList($group)
    {
        if (Soyoz_Microsoft_MSN_Adapter_Helper_Check::parameter($group, self::$_allowedRobotGroupParameters)) {
            $groupId = $group ['groupId'];
            $name = $group ['name'];

            if (isset ($groupId) && is_numeric($groupId) && isset ($name)) {
                self::$_groupList [self::$_groupId] ['groupId'] = $groupId;
                self::$_groupList [self::$_groupId] ['name'] = $name;

                self::$_groupId++;

                return true;
            }
        }
        return false;
    }

    public static function getGroupList()
    {
        return self::$_groupList;
    }
}

?>