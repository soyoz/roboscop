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
class Soyoz_Microsoft_MSN_Adapter_Helper_Error
{
    /**
     * @var array
     */
    public static $information = array(
        200 => 'Invalid Syntax', 201 => 'Invalid parameter', 205 => 'Invalid principal', 206 => 'Domain name missing', 207 => 'Already logged in', 208 => 'Invalid principal', 209 => 'Nickname change illegal', 210 => 'Principal list full ', 213 => 'Invalid rename request?', 215 => 'Principal already on list', 216 => 'Principal not on list', 217 => 'Principal not online', 218 => 'Already in mode', 219 => 'Principal is in the opposite list', 223 => 'Too many groups', 224 => 'Invalid group', 225 => 'Principal not in group', 227 => 'Group not empty', 228 => 'Group with same name already exists', 229 => 'Group name too long', 230 => 'Cannot remove group zero', 231 => 'Invalid group', 240 => 'Empty domain', 280 => 'Switchboard failed', 281 => 'Transfer to switchboard failed', 282 => 'P2P Error?', 300 => 'Required field missing', 302 => 'Not logged in', 402 => 'Error accessing contact list', 403 => 'Error accessing contact list', 420 => 'Invalid Account Permissions', 500 => 'Internal server error', 501 => 'Database server error', 502 => 'Command disabled', 510 => 'File operation failed', 511 => 'Banned', 520 => 'Memory allocation failed', 540 => 'Challenge response failed', 600 => 'Server is busy', 601 => 'Server is unavailable', 602 => 'Peer nameserver is down', 603 => 'Database connection failed', 604 => 'Server is going down', 605 => 'Server unavailable', 700 => 'Could not create connection', 710 => 'Bad CVR parameters sent', 711 => 'Write is blocking', 712 => 'Session is overloaded', 713 => 'Calling too rapidly', 714 => 'Too many sessions', 715 => 'Not expected', 717 => 'Bad friend file', 731 => 'Not expected', 800 => 'Changing too rapidly', 910 => 'Server too busy', 911 => 'Server is busy', 912 => 'Server too busy', 913 => 'Not allowed when hidden', 914 => 'Server unavailable', 915 => 'Server unavailable', 916 => 'Server unavailable', 917 => 'Authentication failed', 918 => 'Server too busy', 919 => 'Server too busy', 920 => 'Not accepting new principals', 921 => 'Server too busy', 922 => 'Server too busy', 923 => 'Kids Passport without parental consent', 924 => 'Passport account not yet verified', 928 => 'Bad ticket', 931 => 'Account not on this server'
    );
}

?>