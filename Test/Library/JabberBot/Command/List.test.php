<?php
class ListTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->bot = $this->getMockBuilder('JabberBot_Bot')->disableOriginalConstructor()->getMock();
        $this->command = new JabberBot_Command_List($this->bot);
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
        $this->assertTrue($this->command->search('*list beer') == true);
        $this->assertTrue($this->command->search('*list') == true);
        $this->assertTrue($this->command->search('list') == false);
        $this->assertTrue($this->command->search('*botsnack gravy') == false);
    }
    public function testRun()
    {
        $cmd1 = new StdClass;
        $cmd2 = new StdClass;
        $cmd1->quickHelp = 'command1';
        $cmd2->quickHelp = 'command2';
        $this->bot->arrCommands = array($cmd1, $cmd2);
        $message = $this->getMockBuilder('JabberBot_Message')->disableOriginalConstructor()->getMock();
        $message->body = '*list';
        $message->expects($this->once())
                ->method('reply')
                ->with('Available Commands: ' . PHP_EOL . 'command1' . PHP_EOL . 'command2' . PHP_EOL);
        $this->command->run($message);
    }
}
