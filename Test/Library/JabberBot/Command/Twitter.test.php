<?php
class TwitterTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->bot = $this->getMockBuilder('JabberBot_Bot')
                          ->disableOriginalConstructor()
                          ->setMethods(array('getRandomQuote'))
                          ->getMock();
        $this->command = new JabberBot_Command_Twitter($this->bot);
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
    public function testSearch()
    {
        $this->assertTrue($this->command->search('*twitter qahatesyou') == true);
        $this->assertTrue($this->command->search('*twitter') == true);
        $this->assertTrue($this->command->search('twitter') == false);
        $this->assertTrue($this->command->search('*botsnack gravy') == false);
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
        $message->body = '*twitter qahatesyou';
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/twitter'))
                       ->will($this->returnValue(false));
        $this->command->run($message);
    }
    public function testRun()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->expects($this->once())->method('getUsername')->will($this->returnValue('psmith'));
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/twitter'))
                       ->will($this->returnValue(true));
        $message->body = '*twitter qahatesyou';
        $message->expects($this->once())->method('reply');
        $this->command->run($message);
    }
    public function testRunNoUsername()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->expects($this->once())->method('getUsername')->will($this->returnValue('psmith'));
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/twitter'))
                       ->will($this->returnValue(true));
        $message->body = '*twitter';
        $message->expects($this->once())->method('reply')->with('What username?');
        $this->command->run($message);
    }
    public function testRunUserNotFound()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->expects($this->once())->method('getUsername')->will($this->returnValue('psmith'));
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/twitter'))
                       ->will($this->returnValue(true));
        $message->body = '*twitter sof7gsiudygf';
        $message->expects($this->once())->method('reply')->with('Failed to find an update for user sof7gsiudygf');
        $this->command->run($message);
    }
}
