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
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
/**
 * Unit test for JabberBot_Command_Remind
 *
 * @package   JabberBot
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Command_RemindTest extends PHPUnit_Framework_TestCase
{
    /**
     * Set up mock objects
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $this->bot = $this->getMockBuilder('JabberBot_Bot')->disableOriginalConstructor()->getMock();
        $this->bot->db = new MockReminderDb();
        $this->command = new JabberBot_Command_Remind($this->bot);
    }
    /**
     * Checks that the backlink was stored correctly
     */
    public function testBackLinkToBot()
    {
        $this->assertObjectHasAttribute('_bot', $this->command);
        $this->assertAttributeInstanceOf('JabberBot_Bot', '_bot', $this->command);
    }
    /**
     * Check that the quickhelp string is available
     */
    public function testQuickHelp()
    {
        $this->assertObjectHasAttribute('quickHelp', $this->command);
        $this->assertAttributeInternalType('string', 'quickHelp', $this->command);
    }
    /**
     * Check the search method
     */
    public function testSearch()
    {
        $this->assertTrue($this->command->search('*remind now, test') == true);
        $this->assertTrue($this->command->search('*remind') == true);
        $this->assertTrue($this->command->search('*admin quit') == false);
        $this->assertTrue($this->command->search('remind gravy') == false);
    }
    /**
     *
     * Test attempted use by an unauthorised user
     * @expectedException JabberBot_AccessDeniedException
     */
    public function testRunAccessDenied()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->expects($this->once())->method('getUsername')->will($this->returnValue('psmith'));
        $message->body = '*remind tomorrow, do something devious';
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/remind'))
                       ->will($this->returnValue(false));
        $this->command->run($message);
    }
    /**
     * Test processing a message with a bad syntax
     */
    public function testBadSyntax()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->body = '*remind me some stuff';
        $message->expects($this->once())
                ->method('reply')
                ->with('Couldn\'t understand that, try *remind <when>, <message> (don\'t forget the comma)');
        $message->expects($this->once())
                ->method('getUsername')
                ->will($this->returnValue('psmith'));
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/remind'))
                       ->will($this->returnValue(true));
        $this->command->run($message);
    }
    /**
     * Test processing a message with an invalid date
     */
    public function testBadTime()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->body = '*remind blah, do some stuff';
        $message->expects($this->once())
                ->method('reply')
                ->with(
                    'Couldn\'t figure out when you want the reminder. '
                    .' Check http://www.php.net/manual/en/datetime.formats.php'
                );
        $message->expects($this->once())
        ->method('getUsername')->will($this->returnValue('psmith'));
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/remind'))
                       ->will($this->returnValue(true));
        $this->command->run($message);
    }
    /**
     *
     * Test Successfull message creation.
     * @dataProvider dataProvider
     * @param string $time
     * @param string $message
     */
    public function testSuccessfulCreation($time, $messageText, $to, $type)
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->body = '*remind ' . $time . ', ' . $messageText;
        $message->type = $type;
        $message->expects($this->once())->method('getReplyAddress')->will($this->returnValue($to));
        $message->expects($this->exactly(2))->method('getUsername')->will($this->returnValue('psmith'));
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/remind'))
                       ->will($this->returnValue(true));
        $message->expects($this->once())
                ->method('reply')
                ->with('Reminder set for ' . date('Y-m-d H:i ', strtotime($time)));
        $this->command->run($message);
        $dataInserted = $this->bot->db->getData();
        $this->assertEquals(substr($to, 0, strpos($to, '@')), $dataInserted['to']);
        $this->assertEquals($type, $dataInserted['type']);
        $this->assertEquals(date('Y-m-d H:i', strtotime($time)), $dataInserted['due']);
        $this->assertContains($messageText, $dataInserted['message']);
        $this->assertContains('psmith', $dataInserted['message']);
    }
    public function dataProvider()
    {
        return array(
            array('now', 'Do something really important', 'agroup@someserver.com', 'groupchat'), 
            array('tomorrow', 'Do something, with a comm)a', 'psmith@someserver.com', 'chat'), 
            array('tomorrow 9am', 'Do something, with <b>tags</b>', 'agroup@someserver.com', 'groupchat'), 
            array(
                'tomorrow 18:00', 'Do something with SQL injection, like \';drop table tblAcl;', 
                'psmith@someserver.com', 'chat'),
        );
    }
}
class MockReminderDb
{
    public $data;
    public function createQueuedMessage($arrArgs)
    {
        $this->data = $arrArgs;
    }
    public function getData()
    {
        return $this->data;
    }
}
