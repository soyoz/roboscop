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
Zend_Loader::loadClass('Zend_Feed');

class Soyoz_Microsoft_MSN_Adapter_Command_Feed_Parser
{
    /**
     * @param $feed string
     * @return array | boolean
     */
    public static function parse($feed)
    {
        if (isset ($feed)) {
            try {
                $getFeed = Zend_Feed::import($feed);
            } catch (Zend_Feed_Exception $exception) {
                $exception->getMessage();

                return false;
            } catch (Zend_Uri_Exception $exception) {
                $exception->getMessage();

                return false;
            } catch (Zend_Http_Client_Exception $exception) {
                $exception->getMessage();

                return false;
            }

            if ($getFeed) {
                $channel = array(
                    'title' => $getFeed->title(), 'link' => $getFeed->link(), 'description' => $getFeed->description(), 'items' => array()
                );

                foreach ($getFeed as $item) {
                    $channel ['items'] [] = array(
                        'title' => $item->title(), 'link' => $item->link(), 'description' => $item->description()
                    );
                }

                return $channel;
            }
        }
        return false;
    }
}

?>
