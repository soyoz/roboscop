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

class System_Robot_Service extends Zend_Db_Table_Abstract
{
    protected $_name = 'SYSTEM_ROBOT_SERVICE';
    protected $_primary = 'ROBOT_SERVICE_ID';

    /**
     * @param integer $robotId
     */
    public function getList($robotId)
    {
        if (isset ($robotId)) {
            $sql = '
			SELECT 
			SRS.*,
			SS.*
			FROM
			SYSTEM_ROBOT_SERVICE AS SRS
			INNER JOIN SYSTEM_SERVICE AS SS ON SRS.SERVICE_ID = SS.SERVICE_ID
			WHERE
			SRS.ROBOT_ID = ' . $robotId . '
			AND
			SRS.STATUS = 1
			';

            $getList = $this->_db->query($sql)->fetchAll();

            if ($getList) {
                return $getList;
            }
        }
        return false;
    }
}

?>
