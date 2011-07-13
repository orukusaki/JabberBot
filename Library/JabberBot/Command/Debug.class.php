<?php
/**
 * JabberBot_Command_Debug class
 *
 * Contains the JabberBot_Command_Debug class
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
 * Debug command
 *
 * Live debugging. Use with care
 *
 * @package   JabberBot
 * @subpackage Command
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Peter Smith
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Command_Debug extends JabberBot_Command
{
    /**
     * Quick Help
     *
     * @var string
     */
    public $quickHelp = '*debug - Only causes bad things to happen.';

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
        $this->checkAcl($message->getUsername(), '/bot/debug');
        $words = explode(' ', $message->body);
        switch ($words[1]) {
        case 'eval':
            eval(substr($message->body, 12));
            break;
        }
    }

    /**
     * Search message body for keywords.
     *
     * Search message body to detirmine whether we're interested in processing it.
     *
     * @param string $body The message body
     *
     * @return boolean Check result
     */
    public function search($body)
    {
        return preg_match('/^\*debug\b/', $body);
    }
}
