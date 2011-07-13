<?php
/**
 * JabberBot_Command_Admin class
 *
 * Contains the JabberBot_Command_Admin class
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
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
/**
 * Admin command
 *
 * Provides access to several admin sub-commands, such as joining/leaving conference rooms
 *
 * @package   JabberBot
 * @subpackage Command
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Peter Smith
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Command_Admin extends JabberBot_Command
{
    /**
     * Quick Help
     *
     * @var string
     */
    public $quickHelp = '*admin - admin functions, type *admin help for more details';

    /**
     * Excecute the command
     *
     * Excecute the command against a specific message object.
     *
     * @param  JabberBot_Message The message to process
     *
     * @return void
     */
    public function run($message)
    {
        $words = explode(' ', $message->body);
        if (!isset($words[1])) {
            $message->reply("*admin <command>\ncommands:\njoin <room>\nleave <room>\nquit");
            return;
        }
        switch ($words[1]) {
        case 'join':
            if ($words[2]) {
                $this->checkAcl($message->getUsername(), '/bot/admin/join');
                if ($this->_bot->inRoom($words[2])) {
                    $message->reply('I think I\'m already in that room.');
                } else {
                    $this->_bot->enterRoom($words[2]);
                    $message->reply('OK, joining room ' . $words[2]);
                }
            } else {
                $message->reply("Which room?");
            }
            break;

        case 'leave':
            if ($words[2]) {
                $this->checkAcl($message->getUsername(), '/bot/admin/leave');
                if (!$this->_bot->inRoom($words[2])) {
                    $message->reply('I\'m not in that room.');
                } else {
                    $this->_bot->leaveRoom($words[2]);
                    $message->reply('OK, Leaving room ' . $words[2]);
                }
            } else {
                // TODO: if the message came from a conference room, leave it.
                $message->reply("Which room?");
            }
            break;

        case 'quit':
            $this->checkAcl($message->getUsername(), '/bot/admin/quit');
            $message->reply("I'll be back");
            sleep(2);
            $this->_bot->disconnect();
            break;

        case 'restart':
            $this->checkAcl($message->getUsername(), '/bot/admin/restart');
            eval(die(exec(dirname(__FILE__) . '/../../../jabberbot restart')));
            break;

        case 'help':
            $message->reply("*admin <command>\ncommands:\njoin <room>\nleave <room>\nquit");
            break;
        }
    }

    /**
     * Search message body for keywords.
     *
     * Search message body to detirmine whether we're interested in processing it.
     *
     * @param  string $body The message body
     *
     * @return boolean  Check result
     */
    public function search($body)
    {
        return preg_match('/^\*admin\b/', $body);
    }
}
