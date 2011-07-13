<?php
/**
 * JabberBot_Command_Status class
 *
 * Contains the JabberBot_Command_Status class
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
 * Show Bot Status command
 *
 * Returns the current uptime and memory usage
 *
 * @package   JabberBot
 * @subpackage Command
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Peter Smith
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Command_Status extends JabberBot_Command
{
    /**
     * Quick Help
     *
     * @var string
     */
    public $quickHelp = '*status - Report on my status.';

    /**
     * The time at which the object was created.
     * @var number
     */
    private $_startTime;

    /**
     * Constructor
     *
     * Saves the start time for use later.
     *
     * @param JabberBot_Bot $bot
     *
     * @return void
     */
    public function __construct($bot)
    {
        parent::__construct($bot);
        $this->_startTime = time();
    }
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
        $this->checkAcl($message->getUsername(), '/bot/status');
        $upTime = time() - $this->_startTime;
        $days = floor($upTime / 86400);
        $upTime%= 86400;
        $hours = floor($upTime / 3600);
        $upTime%= 3600;
        $minutes = floor($upTime / 60);
        $upTime%= 60;
        $text = 'Uptime: ' . $this->_pl('day', $days) . ', '
                           . $this->_pl('hour', $hours) . ', '
                           . $this->_pl('minute', $minutes) . '. ';
        $text.= 'Memory usage: ' . memory_get_usage() . ' bytes.';
        $text.= ' Status: ' . $this->_bot->getRandomQuote('status') . '.';
        $message->reply($text);
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
        return preg_match('/^\*status\b/', $body);
    }

    /**
     * Pluralise a unit
     *
     * Convenience function, pluralise a unit based on the value
     *
     * @param string $txt unit name
     * @param mixed  $num value
     *
     * @return string The value followed by the unit, pluralised if necessary.
     */
    private function _pl($txt, $num)
    {
        return $num . ' ' . $txt . (($num != 1) ? 's' : '');
    }
}
