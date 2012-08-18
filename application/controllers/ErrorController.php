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
class ErrorController extends Zend_Controller_Action
{
    public function init()
    {
        $this->getHelper('layout')->disableLayout();
    }

    public function indexAction()
    {
    }

    public function errorAction()
    {
        $getError = $this->_getParam('error_handler');

        $this->initView()->request = $getError->request;
        $this->initView()->exception = $getError->exception;

        switch ($getError->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER :
                $this->getResponse()->setHttpResponseCode(404);
                $this->initView()->message = 'Controller not found';
                break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION :
                $this->getResponse()->setHttpResponseCode(404);
                $this->initView()->message = 'Action not found';
                break;
            default :
                $this->getResponse()->setHttpResponseCode(500);
                $this->initView()->message = 'Application error';
                break;
        }
    }
}

?>