<?php
/**
* Unit test for the Config class.
*
* Contains the Config class
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
* @author    Stuart Grimshaw <stuart.grimshaw@gmail.com>
* @copyright 2011 Plusnet
* @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
*/
/**
 * Unit test for Config class
 *
 * @package   JabberBot
 * @author    Stuart Grimshaw <stuart.grimshaw@gmail.com>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{
	/**
	 * 
	 * Config data for all the tests.
	 * @var array
	 */
	private $_mock_config;
	
	/**
	 * 
	 * Set up the mock data.
	 */
	public function setUp() {
		$this->_mock_config['port'] = "5222";
		$this->_mock_config['ssl'] = false;
		$this->_mock_config['resource'] = "Bendr";
		
		$this->_mock_config['pinginterval'] = 60;
		# log level 0 (least info) to 4 (most info)
		$this->_mock_config['loglevel'] = 3;
		
		$this->_mock_config['jira_host'] = "http://jira.local";
	}
	
	public function testConfigObjectIsCreated() {
		$config = new JabberBot_Config($this->_mock_config);
		$this->assertEquals("5222", $config->getValue("port"));
	}
}