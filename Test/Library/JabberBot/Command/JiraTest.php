<?php
/**
* Unit test for JabberBot_Command_Remind
*
* Contains the RemindTest class
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
 * Unit test for JabberBot_Command_Remind
 *
 * @package   JabberBot
 * @author    Stuart Grimshaw <stuart.grimshaw@gmail.com>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Command_JiraTest extends PHPUnit_Framework_TestCase
{
    const MESSAGE1 = "Can you look at JIRA-182";
    const MESSAGE2 = "Can you look at jira-182";
    const MESSAGE3 = "Can you look at JIRA - 182";
    const MESSAGE4 = "Can you look at http://jira.local/browse/JIRA-182";

    const JIRA_URL = "http://jira.local/browse/JIRA-182";

    public function setUp() {
        // Create the config mock.
        $this->_mock_config['port'] = "5222";
        $this->_mock_config['ssl'] = false;
        $this->_mock_config['resource'] = "Bendr";

        $this->_mock_config['pinginterval'] = 60;
        $this->_mock_config['loglevel'] = 3;

        $this->_mock_config["jira"]['host'] = "http://jira.local/";

        $this->bot = $this->getMockBuilder('JabberBot_Bot')->disableOriginalConstructor()->getMock();

        $this->command = new JabberBot_Command_Jira($this->bot);
    }

    public function testSearchFindsJiraTickets() {
        $this->assertTrue($this->command->search(JabberBot_Command_JiraTest::MESSAGE1) == true);
        $this->assertTrue($this->command->search(JabberBot_Command_JiraTest::MESSAGE2) == false);
        $this->assertTrue($this->command->search(JabberBot_Command_JiraTest::MESSAGE3) == false);
        $this->assertTrue($this->command->search(JabberBot_Command_JiraTest::MESSAGE4) == false);
    }

    public function testJiraURLIsCorrect() {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->body = JabberBot_Command_JiraTest::MESSAGE1;

        // Mock the getConfig call.
        $config = new JabberBot_Config($this->_mock_config);
        $this->bot->expects($this->once())
                        ->method('getConfig')
                        ->will($this->returnValue($config));

        $message->expects($this->once())
                        ->method('reply')
                        ->with(JabberBot_Command_JiraTest::JIRA_URL);

        $this->command->run($message);
    }

}
