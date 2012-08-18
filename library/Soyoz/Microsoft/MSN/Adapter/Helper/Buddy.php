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
class Soyoz_Microsoft_MSN_Adapter_Helper_Buddy
{
    /**
     * @var integer
     */
    public static $total;

    /**
     * @var array
     */
    public static $status = array(
        'NLN' => 1, 'BRB' => 2, 'BSY' => 3, 'AWY' => 4, 'IDL' => 5, 'PHN' => 6, 'LUN' => 7, 'HDN' => 8, 'FLN' => 9
    );

    /**
     * @var array
     */
    private static $_list = array(
        'FL' => 1, 'AL' => 2, 'BL' => 4, 'RL' => 8
    );

    /**
     * @var array
     */
    private static $_power = array(
        0 => 0, 1 => true, 2 => true, 4 => true, 8 => true
    );

    /**
     * @var integer
     */
    private static $_buddyId = 0;

    /**
     * @var array
     */
    private static $_buddyList = array(
        array(
            'passport' => false, 'screenName' => false, 'list' => array(
            'FL' => false, 'AL' => false, 'BL' => false, 'RL' => false
        ), 'groupId' => false
        )
    );

    /**
     * @var array
     */
    private static $_allowedBuddyListParameters = array(
        'passport' => 'MANDATORY', 'screenName' => 'MANDATORY', 'list' => 'MANDATORY', 'groupId' => 'OPTIONAL'
    );

    /**
     * @param array $buddy
     * @return boolean
     */
    public static function deployList($buddy)
    {
        if (Soyoz_Microsoft_MSN_Adapter_Helper_Check::parameter($buddy, self::$_allowedBuddyListParameters)) {
            $passport = $buddy ['passport'];
            $screenName = $buddy ['screenName'];
            $list = $buddy ['list'];

            if (isset ($buddy ['groupId'])) {
                $groupId = $buddy ['groupId'];
            } else {
                $groupId = 0;
            }

            if (isset ($passport) && isset ($screenName) && isset ($list)) {
                self::$_buddyList [self::$_buddyId] ['passport'] = $passport;
                self::$_buddyList [self::$_buddyId] ['screenName'] = $screenName;
                self::$_buddyList [self::$_buddyId] ['list'] ['FL'] = self::$_power [$list & self::$_list ['FL']];
                self::$_buddyList [self::$_buddyId] ['list'] ['AL'] = self::$_power [$list & self::$_list ['AL']];
                self::$_buddyList [self::$_buddyId] ['list'] ['BL'] = self::$_power [$list & self::$_list ['BL']];
                self::$_buddyList [self::$_buddyId] ['list'] ['RL'] = self::$_power [$list & self::$_list ['RL']];
                self::$_buddyList [self::$_buddyId] ['groupId'] = $groupId;

                self::$_buddyId++;

                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public static function getList()
    {
        return self::$_buddyList;
    }
}

?>