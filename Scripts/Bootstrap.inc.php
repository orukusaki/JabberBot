<?php
/**
 * JabberBot Bootstap
 *
 * Defines global functions and variables used in the Jabber Bot scripts and api
 *
 * Copyright (C) 2011  Plusnet
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
 *
 * @package   JabberBot
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */

/**
 * Autoloader
 *
 * Searches Library for files to include
 *
 * @param $strClassName
 */
function jabberBotAutoload($strClassName)
{
    $strClassName = str_replace('_', '/', $strClassName);
    if (file_exists(dirname(__FILE__) . '/../Library/' . $strClassName . '.class.php')) {
        require_once (dirname(__FILE__) . '/../Library/' . $strClassName . '.class.php');
    } elseif (file_exists(dirname(__FILE__) . '/../Library/' . $strClassName . '.php')) {
        require_once (dirname(__FILE__) . '/../Library/' . $strClassName . '.php');
    }
}

/**
 * Standard Error Handler - throw an ErrorException on any error
 * 
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 */
function jabberBotErrorHandler($errno, $errstr, $errfile, $errline ) 
{
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler("jabberBotErrorHandler");
spl_autoload_register('jabberBotAutoload');