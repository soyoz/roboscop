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
class Soyoz_Microsoft_MSN_Adapter_Helper_NotificationQueue
{
    /**
     * @return array | boolean
     */
    public static function get()
    {
        if (self::isLocked()) {
            $details = array(
                'id' => $_SESSION ['system'] ['notification'] ['id'], 'buddyId' => $_SESSION ['system'] ['notification'] ['buddyId'], 'passport' => $_SESSION ['system'] ['notification'] ['passport'], 'header' => $_SESSION ['system'] ['notification'] ['header']
            );
            return $details;
        } else {
            return false;
        }
    }

    /**
     * @param integer $buddyId
     * @param string $passport
     * @param string $header
     *
     * @return boolean
     */
    public static function lock($notificationId, $buddyId, $passport, $header)
    {
        if (isset ($notificationId) && isset ($buddyId) && isset ($passport) && isset ($header)) {
            if (self::isLocked() == false) {
                $_SESSION ['system'] ['notification'] ['id'] = $notificationId;
                $_SESSION ['system'] ['notification'] ['buddyId'] = $buddyId;
                $_SESSION ['system'] ['notification'] ['passport'] = $passport;
                $_SESSION ['system'] ['notification'] ['header'] = $header;

                return true;
            }
        }
        return false;
    }

    /**
     * @return boolean
     */
    public static function unLock()
    {
        if (self::isLocked()) {
            unset ($_SESSION ['system'] ['notification'] ['id']);
            unset ($_SESSION ['system'] ['notification'] ['buddyId']);
            unset ($_SESSION ['system'] ['notification'] ['passport']);
            unset ($_SESSION ['system'] ['notification'] ['header']);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array | boolean
     */
    public static function isLocked()
    {
        if (isset ($_SESSION ['system'] ['notification'] ['id']) && isset ($_SESSION ['system'] ['notification'] ['buddyId']) && isset ($_SESSION ['system'] ['notification'] ['passport']) && $_SESSION ['system'] ['notification'] ['header']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $passport
     *
     * @return boolean
     */
    public static function isOffline($passport)
    {
        if (isset ($passport)) {
            if (isset ($_SESSION ['system'] ['notification'] ['offline'] [$passport])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $passport
     *
     * @return boolean
     */
    public static function addOffline($passport)
    {
        if (isset ($passport)) {
            if (self::isLocked()) {
                if (self::isOffline($passport) == false) {
                    $_SESSION ['system'] ['notification'] ['offline'] [$passport] = true;

                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $passport
     *
     * @return boolean
     */
    public static function deleteOffline($passport)
    {
        if (isset ($passport)) {
            if (self::isOffline($passport)) {
                unset ($_SESSION ['system'] ['notification'] ['offline'] [$passport]);

                return true;
            }
        }
        return false;
    }

    /**
     * @param integer $time
     *
     * @return boolean
     */
    public static function setSentTime($time)
    {
        if (isset ($time)) {
            $_SESSION ['system'] ['notification'] ['timeout'] = $time;

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return integer | boolean
     */
    public static function getSentTime()
    {
        if (self::checkSentTime()) {
            return $_SESSION ['system'] ['notification'] ['timeout'];
        } else {
            return false;
        }
    }

    /**
     * @return boolean
     */
    public static function checkSentTime()
    {
        if (isset ($_SESSION ['system'] ['notification'] ['timeout'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return boolean
     */
    public static function clearSentTime()
    {
        if (self::checkSentTime()) {
            unset ($_SESSION ['system'] ['notification'] ['timeout']);

            return true;
        } else {
            return false;
        }
    }
}

?>