<?php
class StatusTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->bot = $this->getMockBuilder('JabberBot_Bot')->disableOriginalConstructor()->getMock();
        $this->command = new JabberBot_Command_Status($this->bot);
    }
    public function testBackLinkToBot()
    {
        $this->assertObjectHasAttribute('_bot', $this->command);
        $this->assertAttributeInstanceOf('JabberBot_Bot', '_bot', $this->command);
    }
    public function testQuickHelp()
    {
        $this->assertObjectHasAttribute('quickHelp', $this->command);
        $this->assertAttributeInternalType('string', 'quickHelp', $this->command);
    }
    public function testRun()
    {
        $this->bot->expects($this->once())
                  ->method('getRandomQuote')
                  ->with('status')
                  ->will($this->returnValue('status message'));
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->expects($this->once())->method('getUsername')->will($this->returnValue('psmith'));
        $message->body = '*status';
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/status'))
                       ->will($this->returnValue(true));
        $message->expects($this->once())->method('reply');
        //		        ->with($this->contains('status message'));
        $this->command->run($message);
    }
}
