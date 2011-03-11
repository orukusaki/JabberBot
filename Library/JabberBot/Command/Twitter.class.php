<?php
/**
 * JabberBot_Command_Twitter class
 *
 * Contains the JabberBot_Command_Twitter class
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
 * Twitter command
 *
 * Fetches the lastest Twitter update for a given user
 *
 * @package   JabberBot
 * @subpackage Command
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Peter Smith
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Command_Twitter extends JabberBot_Command
{
    /**
     * Quick Help
     * @var    string
     */
    public $quickHelp = '*twitter <username> - Fetch the latest twitter update for a user.';
    
    /**
     * Excecute the command
     *
     * Excecute the command against a specific message object.
     *
     * @param  JabberBot_Message The message to process
     */
    public function run($message)
    {
        $this->checkAcl($message->getUsername(), '/bot/twitter');
        $words = explode(' ', $message->body);
        if (!isset($words[1])) {
            $message->reply("What username?");
            return;
        }
        $url = 'http://api.twitter.com/1/users/show.xml?screen_name=' . $words[1];
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if (!$strReturn = curl_exec($curl)) {
            $this->_bot->log->log("Curl failed", XMPPHP_Log::LEVEL_WARNING);
            return;
        } else {
            $xml = new DOMDocument();
            $xml->loadXML($strReturn);
            $xp = new DOMXPath($xml);
            $nodelist = $xp->query('/user/status');
            if ($nodelist->length != 1) {
                $message->reply('Failed to find an update for user ' . $words[1]);
                return;
            }
            $xmlStatus = $nodelist->item(0);
            $text = 'Last update from user ' . $words[1] . ' at ' 
                   . $xmlStatus->getElementsByTagName('created_at')->item(0)->nodeValue . ':' . PHP_EOL 
                   . $xmlStatus->getElementsByTagName('text')->item(0)->nodeValue;
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
        return preg_match('/^\*twitter\b/', $body);
    }
}
