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
date_default_timezone_set('Europe/Istanbul');

defined('APPLICATION_PATH') || define ('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
defined('APPLICATION_ENV') || define ('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

set_include_path(implode(PATH_SEPARATOR, array(realpath(APPLICATION_PATH . '/../library'), get_include_path())));

require_once 'Zend/Application.php';
require_once 'Zend/Console/Getopt.php';

try {
    unset ($_SESSION);

    $Zend_Console_Getopt = new Zend_Console_Getopt (array('passport=s' => 'Robot Passport'));

    $passport = $Zend_Console_Getopt->getOption('passport');

    $_SESSION ['system'] ['robot'] ['passport'] = $passport;
} catch (Zend_Console_Getopt_Exception $exception) {
    $exception->getMessage();

    $_SESSION ['system'] ['robot'] ['passport'] = false;
}

$application = new Zend_Application (APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap()->run();