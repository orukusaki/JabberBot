<?php
/**
 * JabberBot_Command_Remind class
 *
 * Contains the JabberBot_Command_Remind class
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
 * Reminder command
 *
 * Sets a timed reminder message
 *
 * @package   JabberBot
 * @subpackage Command
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 * @link
 */
class JabberBot_Command_Remind extends JabberBot_Command
{
    /**
     * Quick Help
     *
     * @var string
     */
    public $quickHelp = '*remind <when>, <message> - Set a reminder.';

    /**
     * Excecute the command
     *
     * Excecute the command against a specific message object.
     *
     * @param JabberBot_Message The message to process
     *
     * @return void
     */
    public function run($message)
    {
        $this->checkAcl($message->getUsername(), '/bot/remind');
        $intMatch = preg_match('/^\*remind\s(.*?),\s(.*)/', $message->body, $matches);
        if ($intMatch == 0) {
            $message->reply('Couldn\'t understand that, try *remind <when>, <message> (don\'t forget the comma)');
            return;
        }
        $uxtTime = strtotime($matches[1]);
        if (!$uxtTime) {
            $message->reply(
                'Couldn\'t figure out when you want the reminder.  '
                . 'Check http://www.php.net/manual/en/datetime.formats.php'
            );
            return;
        }
        $senderSplit = explode('@', $message->getReplyAddress());
        $this->_bot->db->createQueuedMessage(
            array(
                'to' => $senderSplit[0],
                 'type' => $message->type,
                 'due' => date('Y-m-d H:i', $uxtTime),
                 'message' => 'Reminder set by ' . $message->getUsername() . ': ' . $matches[2]
           )
        );
        $message->reply('Reminder set for ' . date('Y-m-d H:i ', $uxtTime));
    }

    /**
     * Search message body for keywords.
     *
     * Search message body to detirmine whether we're interested in processing it.
     *
     * @param string $body The message body
     *
     * @return bool
     */
    public function search($body)
    {
        return preg_match('/^\*remind\b/', $body);
    }
}
