<?php
/**
 * JabberBot_Command_GrammarCheck class
 *
 * Contains the JabberBot_Command_GrammarCheck class
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
 * Grammar Checker
 *
 * Inspects all inbound messages against an online grammar check api, from afterthedeadline.com
 * If it gets too annoying, it can be turned off with *grammar off
 *
 * @package   JabberBot
 * @subpackage Command
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Peter Smith
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Command_GrammarCheck extends JabberBot_Command
{
    /**
     * Quick Help
     *
     * @var string
     */
    public $quickHelp = '*grammar <on|off> - Controls grammar checking';

    /**
     * Grammar checker state
     *
     * @var    boolean
     */
    private $_bolActive;

    /**
     * Constructor
     *
     * Set the default state to active
     *
     * @param  JabberBot_Bot $bot Backlink to owning bot object
     *
     * @return void
     */
    public function __construct($bot)
    {
        parent::__construct($bot);
        $this->_bolActive = true;
    }

    /**
     * Last time we sent a Grammar Check
     *
     * afterthedeadline have implimented some kind of anti-DoS system
     * if we send two requests close together, the second one fails.
     * This var is used to check that we're not sending requests too
     * close together.
     *
     * @var int
     */
    private static $_lastCheck = 0;

    /**
     * Minumum time gap in seconds
     *
     * @var int
     */
    const MIN_GAP = 1;

    /**
     * Excecute the command
     *
     * Excecute the command against a specific message object.
     *
     * @param JabberBot_Message $message The inbound message
     *
     * @return void
     */
    public function run($message)
    {
        if (preg_match("/^\*grammar\b/", $message->body)) {
            $this->checkAcl($message->getUsername(), '/bot/grammar');
            $words = explode(' ', $message->body);
            $cmd = isset($words[1]) ? $words[1] : '';
            if ($cmd == "on") {
                $this->_bolActive = true;
                $message->reply("Grammar check activated");
            } elseif ($cmd == "off") {
                $this->_bolActive = false;
                $message->reply("Grammar check disabled");
            } else {
                $status = $this->_bolActive ? "on" : "off";
                $message->reply("Grammar check is " . $status);
            }
        } else {
            $text = htmlspecialchars($message->body);
            $encoded = urlencode($text);
            $apikey = 'JabberBot' . md5(getmypid());
            $url = 'http://service.afterthedeadline.com/checkGrammar?key=' . $apikey . '&data=' . $encoded;

            if (time() <= self::$_lastCheck + self::MIN_GAP) {
                sleep(1);
            }

            $strReturn = $this->curlGet($url);

            self::$_lastCheck = time();

            $xml = new DOMDocument();

            $xml->loadXML($strReturn);
            $xp = new DOMXPath($xml);
            $errorList = $xp->query('/results/error[count(suggestions/option)>0]');
            $errorCount = $errorList->length;
            $this->_bot->log->log('Found ' . $errorCount . ' error(s)', XMPPHP_Log::LEVEL_INFO);
            if ($errorCount == 0) {
                return;
            }
            foreach ($errorList as $xmlError) {
                $badString = $xp->evaluate('string', $xmlError)->item(0)->nodeValue;
                $goodString = $xp->query('suggestions/option', $xmlError)->item(0)->nodeValue;
                $this->_bot->log->log($badString . ' => ' . $goodString, XMPPHP_Log::LEVEL_INFO);
                $text = preg_replace('/\b' . $badString . '\b/', '<b>' . $goodString . '</b>', $text, 1);
            }
            $message->replyHTML(nl2br($text));
        }
    }

    /**
     * Search message body for keywords.
     *
     * Search message body to detirmine whether we're interested in processing it.
     *
     * @param string $body The message body
     *
     * @return boolean  Check result
     */
    public function search($body)
    {
        return (($this->_bolActive == true && !preg_match('/^\*\b/', $body)) || preg_match('/^\*grammar\b/', $body));
    }
}
