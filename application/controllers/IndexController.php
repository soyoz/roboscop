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
require_once 'Zend/Controller/Action.php';

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        if (isset ($_SESSION ['system'] ['robot'] ['passport'])) {
            Zend_Loader::loadClass('Soyoz_Microsoft_MSN_MSNP8');

            $passport = $_SESSION ['system'] ['robot'] ['passport'];

            $System_Robot = new System_Robot ();
            $getRobotInformation = $System_Robot->fetchRow(array('PASSPORT = "' . $passport . '"', 'STATUS = 1'));
            unset ($System_Robot);

            if (sizeof($getRobotInformation) >= 1) {
                try {
                    $robotId = $getRobotInformation->ROBOT_ID;
                    $limit = $getRobotInformation->LIMIT;
                    $passport = $getRobotInformation->PASSPORT;
                    $password = $getRobotInformation->PASSWORD;
                    $screenName = $getRobotInformation->SCREEN_NAME;

                    $_SESSION ['system'] ['robot'] ['id'] = $robotId;
                    $_SESSION ['system'] ['robot'] ['limit'] = $limit;
                    $_SESSION ['system'] ['robot'] ['passport'] = $passport;
                    $_SESSION ['system'] ['robot'] ['screenName'] = $screenName;
                    $_SESSION ['system'] ['robot'] ['service'] = array();

                    $System_Robot_Service = new System_Robot_Service ();
                    $getRobotServicelist = $System_Robot_Service->getList($robotId);
                    unset ($System_Robot_Service);

                    if ($getRobotServicelist) {
                        foreach ($getRobotServicelist as $value) {
                            array_push($_SESSION ['system'] ['robot'] ['service'], $value ['NAME']);
                        }

                        $Soyoz_Microsoft_MSN_MSNP8 = new Soyoz_Microsoft_MSN_MSNP8 ();
                        $Soyoz_Microsoft_MSN_MSNP8->factory(array('passport' => $passport, 'password' => $password, 'screenName' => $screenName));
                    }
                } catch (Exception $exception) {
                    $System_Error_Log = new System_Error_Log ();
                    $System_Error_Log->insert(array('ROBOT_ID' => $robotId, 'FILE' => $exception->getFile(), 'LINE' => $exception->getLine(), 'CODE' => $exception->getCode(), 'MESSAGE' => $exception->getMessage(), 'INSERT_DATE' => time()));
                    unset ($System_Error_Log);

                    die ('Error excepted.');
                }
            }
        }
    }
}
