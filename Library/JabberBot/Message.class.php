<?php
/**
 * JabberBot_Message class
 *
 * Contains the JabberBot_Message class.
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
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
/**
 * A message received from the XMPP server
 *
 * An instance of this class represents a message recieved from the XMPP server.
 *
 * @package   JabberBot
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Peter Smith
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Message
{
    /**
     * Text body of the message
     *
     * @var string
     */
    public $body;

    /**
     * Content of the 'from' attribute of the message
     *
     * @var string
     */
    public $from;

    /**
     * Xml representation of the message
     *
     * @var string
     */
    public $xml;

    /**
     * Type of message ('chat' / 'groupchat')
     *
     * @var string
     */
    public $type;

    /**
     * Reference link back to the JabberBot object
     *
     * @var JabberBot_Bot
     */
    private $_bot;

    /**
     * Constructor
     *
     * @param array         $pl   The message payload as returned by XMPPHP_XMPP::process()
     * @param JabberBot_Bot $bot  Reference link back to the JabberBot object
     *
     * @return void
     */
    public function __construct($pl, $bot)
    {
        $this->body = $pl['body'];
        $this->from = $pl['from'];
        $this->xml = $pl['xml'];
        $this->type = $pl['type'];
        $this->_bot = $bot;
    }

    /**
     * Was the messasge from me
     *
     * Determines whether the message is actually from us, and was echoed back from the XMPP server
     *
     * @return boolean Check result
     */
    public function wasFromMe()
    {
        if ($this->type == 'chat') {
            return false;
        }
        $parts = explode('/', $this->from);
        if (!isset($parts[1])) {
            return false;
        }
        return ($parts[1] == $this->_bot->resource);
    }

    /**
     * Get reply address
     *
     * Returns to where a reply should be addressed
     *
     * @return string The reply address
     */
    public function getReplyAddress()
    {
        switch ($this->type) {
        case 'chat':
            return $this->from;
            break;

        case 'groupchat':
            $parts = explode('/', $this->from);
            return $parts[0];
            break;
        }
    }

    /**
     * Get The username of the sender
     *
     * Examines the roster to detirmine the username of the sender.
     *
     * @return string The username of the sender
     */
    public function getUsername()
    {
        $presence = $this->_bot->roster->getPresence($this->from);
        return $presence['username'];
    }

    /**
     * Was this message delayed?
     *
     * Examines the message to detirmine whether it was sent while we were offline
     * or not in to the present conference room.
     *
     * @return boolean Check result
     */
    public function wasDelayed()
    {
        $wasDelayed = false;
        foreach ($this->xml->subs as $sub) {
            if ($sub->name == 'x' && $sub->ns == 'jabber:x:delay') {
                $wasDelayed = true;
            }
        }
        return $wasDelayed;
    }

    /**
     * Reply
     *
     * Send a message back to the sender of this message.
     *
     * @param string $text The message to send
     *
     * @return void
     */
    public function reply($text)
    {
        $this->_bot->message($this->getReplyAddress(), $text, $this->type);
    }

    /**
     * Reply in HTML
     *
     * Send a message back to the sender of this message with HTML formatting
     *
     * @param string $text The message to send
     *
     * @return void
     */
    public function replyHTML($text)
    {
        $this->_bot->messageHtml($this->getReplyAddress(), $text, $this->type);
    }
}
