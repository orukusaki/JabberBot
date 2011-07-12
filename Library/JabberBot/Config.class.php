<?php
/**
* JabberBot_Command_Twitter class
*
* Contains the JabberBot_Command_Twitter class
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
* @subpackage Command
* @author    Stuart M. Grimshaw <stuart.grimshaw@gmail.com>
* @copyright 2011 Plusnet
* @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
*/

/**
* Jira command
*
* Watches the channel for patterns that look like unlinked Jira issues & provides
* a link to them.
*
* @package   JabberBot
* @subpackage Command
* @author    Stuart M. Grimshaw <stuart.grimshaw@gmail.com>
* @copyright 2011 Plusnet
* @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
*/
class JabberBot_Config {
	/**
	 * Holds the config data.
	 * @var array
	 */
	private $_config;
	
	public function __construct($config) {
		$_config = $config;
	}
	
	/**
	 * 
	 * Returns the value of the specified key, or Null if it's not found.
	 * @param String $key
	 * @return String
	 */
	public function getValue($key) {
		return in_array($key, $this->_config) ? $this->_config[$key] : Null;
	}
}
?>