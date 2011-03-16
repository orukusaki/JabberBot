<?php
class GrammaCheckTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->bot = $this->getMockBuilder('JabberBot_Bot')->disableOriginalConstructor()->getMock();
        $this->bot->log = $this->getMockBuilder('XMPPHP_Log')->disableOriginalConstructor()->getMock();
        $this->command = new JabberBot_Command_GrammarCheck($this->bot);
    }
    public function testConstuction()
    {
        $this->assertObjectHasAttribute('_bolActive', $this->command);
        $this->assertAttributeEquals(true, '_bolActive', $this->command);
    }
    public function testQuickHelp()
    {
        $this->assertObjectHasAttribute('quickHelp', $this->command);
        $this->assertAttributeInternalType('string', 'quickHelp', $this->command);
    }
    public function testSearch()
    {
        $this->assertTrue($this->command->search('*grammar on') == true);
        $this->assertTrue($this->command->search('*grammar off') == true);
        $this->assertTrue($this->command->search('*grammar') == true);
        $this->assertTrue($this->command->search('*admin quit') == false);
        $this->assertTrue($this->command->search('dont correct me') == true);
    }
    /**
     *
     * @expectedException JabberBot_AccessDeniedException
     */
    public function testGrammarOffAccessDenied()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->expects($this->once())->method('getUsername')->will($this->returnValue('psmith'));
        $message->body = '*grammar off';
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/grammar'))
                       ->will($this->returnValue(false));
        $this->command->run($message);
        $this->assertAttributeEquals(true, '_bolActive', $this->command);
    }
    public function testGrammarOffAccessGranted()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->expects($this->once())->method('getUsername')->will($this->returnValue('psmith'));
        $message->body = '*grammar off';
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/grammar'))
                       ->will($this->returnValue(true));
        $message->expects($this->once())->method('reply')->with('Grammar check disabled');
        $this->command->run($message);
        $this->assertAttributeEquals(false, '_bolActive', $this->command);
        $this->assertTrue($this->command->search('dont correct me') == false);
    }
    /**
     *
     * @expectedException JabberBot_AccessDeniedException
     */
    public function testGrammarOnAccessDenied()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->expects($this->once())->method('getUsername')->will($this->returnValue('psmith'));
        $message->body = '*grammar on';
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/grammar'))
                       ->will($this->returnValue(false));
        $this->command->run($message);
    }
    public function testGrammarOnAccessGranted()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->expects($this->once())->method('getUsername')->will($this->returnValue('psmith'));
        $message->body = '*grammar on';
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/grammar'))
                       ->will($this->returnValue(true));
        $message->expects($this->once())->method('reply')->with('Grammar check activated');
        $this->command->run($message);
        $this->assertAttributeEquals(true, '_bolActive', $this->command);
    }
    public function testGrammarCheckingWithResults()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->body = 'dont correct me';
        $message->expects($this->once())->method('replyHtml')->with('<b>don\'t</b> correct me');
        $this->command->run($message);
    }
    public function testGrammarCheckingNoResults()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->body = 'don\'t correct me';
        $message->expects($this->never())->method('replyHtml');
        $this->command->run($message);
    }
    public function testStatus()
    {
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->body = '*grammar';
        $message->expects($this->once())->method('getUsername')->will($this->returnValue('psmith'));
        $this->bot->acl = $this->getMockBuilder('JabberBot_Acl')->getMock();
        $this->bot->acl->expects($this->once())
                       ->method('check')
                       ->with($this->equalTo('psmith'), $this->equalTo('/bot/grammar'))
                       ->will($this->returnValue(true));
        $message->expects($this->once())->method('reply')->with('Grammar check is on');
        $this->command->run($message);
    }
}
