<?php
/**
 * JabberBot_Command_Rsync class
 *
 * Contains the JabberBot_Command_Rsync class
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
 * Checks the daily rsync processon staging
 *
 * When run, calls to the podrefresh api, and replies giving the status of each 
 * process returned
 *
 * @package   JabberBot
 * @subpackage Command
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Peter Smith
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Command_Rsync extends JabberBot_Command
{
    /**
     * Quick Help
     * @var    string
     */
    public $quickHelp = '*rsync - Checks the status of the daily rsync process on staging.';
    
    /**
     * Excecute the command
     *
     * Excecute the command against a specific message object.
     *
     * @param  JabberBot_Message $message The message to process
     */
    public function run($message)
    {
        if (!$this->_bot->acl->check($message->getUsername(), '/bot/rsync')) {
            $message->reply($this->_bot->getRandomQuote('denied'));
            return;
        }
        $url = 'http://qa.plus.net/podrefresh/api/rsync.php';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if (!$strReturn = curl_exec($curl)) {
            $message->reply("Curl failed :(");
            return false;
        } else {
            $xml = new DOMDocument();
            $xml->loadXML($strReturn);
            $xp = new DOMXPath($xml);
            $dbList = $xp->query('/rsynccheck/db');
            $text = "";
            foreach ($dbList as $xmlDb) {
                if ($xmlDb->getElementsByTagName('status')->item(0)->nodeValue == "finished") {
                    $text .= 'Rsync on ' . $xmlDb->getAttribute('name') . ' completed at ' 
                              . date('G:i', $xmlDb->getElementsByTagName('timestamp')->item(0)->nodeValue) . PHP_EOL;
                } else {
                    $text.= 'Rsync on ' . $xmlDb->getAttribute('name') . ' is still running.' . PHP_EOL;
                }
            }
            $message->reply($text);
        }
    }
    
    /**
     * Search message body for keywords.
     *
     * Search message body to detirmine whether we're interested in processing it.
     *
     * @param  string $body The message body
     * @return boolean  Check result
     */
    public function search($body)
    {
        return preg_match('/^\*rsync\b/', $body);
    }
}
