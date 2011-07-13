<?php
/**
 * JabberBot_Bot class
 *
 * Contains the JabberBot_Bot class
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
 * PHP Jabber Bot
 *
 * A ChatBot designed to run on XMPP (Jabber) networks
 *
 * @package   JabberBot
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Peter Smith
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Bot extends XMPPHP_XMPP
{
    /**
     * Overriding protected property
     *
     * @var array
     * @see XMPPHP_XMPP::$resource
     */
    public $resource;

    /**
     * Array containing any conference rooms we are in
     *
     * @var array
     */
    public $rooms;

    /**
     * The name of the default room
     *
     * @var string
     */
    public $defaultRroom = null;

    /**
     * A list of available commands
     *
     * @var array
     */
    public $arrCommands;

    /**
     * The Access Control List object
     *
     * @var JabberBot_Acl
     */
    public $acl;

    /**
     * Our db connection
     *
     * @var JabberBot_Db
     */
    public $db;

    /**
     * Ping interval (seconds)
     *
     * @var int
     */
    public $pingInverval;

    /**
     * The time at which the last message was received (or sent)
     *
     * @var int
     */
    public $lastPing = 0;

    /**
     * Holds the config for the bot.
     *
     * @var JabberBot_Config
     */
    private $_config;

    /**
     * Constructor
     *
     * Creates connection to the XMPP server, and loads all command classes.
     *
     * @return void
     */
    public function __construct()
    {
        // Load config
        $conf = parse_ini_file(dirname(__FILE__) . '/../../Config/JabberBot.ini', true);

        $this->_config = new JabberBot_Config($conf);

        $server = $this->_config->getValue('server');
        $this->rooms = array();
        $this->db = new JabberBot_Db('bot');
        $this->acl = new JabberBot_Acl();

        $botConf = $this->_config->getValue('bot');

        $this->defaultRoom = (isset($botConf['defaultroom'])) ? $botConf['defaultroom'] : null;
        $this->pingInverval = $botConf['pinginterval'];

        // Call parent constructor, and set variables
        parent::__construct(
            $server['host'],
            $server['port'],
            $server['user'],
            $server['password'],
            $server['resource'],
            null,
            true,
            $botConf['loglevel']
        );

        $this->addEventHandler('reconnect', 'handleReconnect', $this);
        $this->useEncryption(($server['ssl'] == 'true'));
        // Set up Commands
        $commandBase = dirname(__FILE__) . '/Command/';
        foreach (scandir($commandBase) as $filename) {
            if (preg_match('/^(.*).class.php/', $filename)) {
                $this->log->log('Including ' . $commandBase . $filename, XMPPHP_Log::LEVEL_INFO);
                require_once ($commandBase . $filename);
            }
        }
        $this->arrCommands = array();
        foreach (get_declared_classes() as $className) {
            if (get_parent_class($className) == 'JabberBot_Command') {
                $this->arrCommands[] = new $className($this);
                $this->log->log('Loaded Command: ' . $className, XMPPHP_Log::LEVEL_INFO);
            }
        }
        // Connect to the server
        try {
            $this->connect();
            $this->processUntil('session_start');
        }
        catch(Exception $e) {
            die('Failed to connect: ' . $e->getMessage());
        }
        // Connect to the default conference room, if set.
        if ($this->defaultRoom) {
            $this->enterRoom($this->defaultRoom);
        }
        // Announce our presence for private chats
        $this->presence();
        $this->resetPing();
    }

    /**
     * Get a random message
     *
     * @param string $handle The message handle
     *
     * @return string A randomly selected message
     */
    public function getRandomQuote($handle)
    {
        $table = $this->db->getRandomQuote(array('handle' => $handle));
        return $table[0]['message'];
    }

    /**
     * Join a named conference room
     *
     * @param string $room Room name
     *
     * @return void
     */
    public function enterRoom($room)
    {
        if ($this->inRoom($room)) {
            return;
        }
        $this->presence(
            null,
            'available',
            $room . '@conference.' . $this->host . '/' . $this->resource,
            'available', 1
        );
        $this->rooms[] = $room;
        $this->sendToRoom($room, $this->getRandomQuote('greeting'));
    }

    /**
     * Leave a conference room
     *
     * @param string $room Room name
     *
     * @return void
     */
    public function leaveRoom($room)
    {
        if (!$this->inRoom($room)) {
            return;
        }
        $this->sendToRoom($room, $this->getRandomQuote('parting'));
        $this->presence(
            null, 'unavailable',
            $room . '@conference.' . $this->host . '/' . $this->resource, 'unavailable', 1
        );
        unset($this->rooms[array_search($room, $this->rooms) ]);
    }

    /**
     * Send a message to a conference room.  Join it if we're not already in it.
     *
     *
     * @param string $room    The room name
     * @param string $message The message to send
     *
     * @return void
     */
    public function sendToRoom($room, $message)
    {
        if (!$this->inRoom($room)) {
            $this->enterRoom($room);
        }
        $this->message($room . '@' . 'conference.' . $this->host, $message, 'groupchat');
    }

    /**
     * Are we in a named room?
     *
     * @param string $room Room name
     *
     * @return bool Check result
     */
    public function inRoom($room)
    {
        return in_array($room, $this->rooms);
    }

    /**
     * Send a message in HTML format
     *
     * Sends a message with an HTML payload attached.
     *
     * @param string $to   Address to send the message to
     * @param string $msg  The message text (including any HTML tags)
     * @param string $type 'chat' or 'groupchat'
     *
     * @return void
     */
    public function messageHtml($to, $msg, $type)
    {
        $payload = '<html xmlns="http://jabber.org/protocol/xhtml-im">
            <body xmlns="http://www.w3.org/1999/xhtml">' . $msg . '</body></html>';
        $this->message($to, strip_tags($msg), $type, null, $payload);
    }

    /**
     * Process an inbound message
     *
     * For each loaded command object, check whether it's interested in processing the message.
     * Run each interested command.
     *
     * @param  JabberBot_Message $message The inbound message.
     *
     * @return void
     */
    private function _processMessage(JabberBot_Message $message)
    {
        if ($message->wasFromMe()) {
            $this->log->log('msg from me ignored', XMPPHP_Log::LEVEL_VERBOSE);
            return;
        }
        $this->log->log('Received Message : ' . $message->body . ' from: ' . $message->from, XMPPHP_Log::LEVEL_INFO);
        if ($message->wasDelayed()) {
            $this->log->log('But ignored it as it was delayed', XMPPHP_Log::LEVEL_INFO);
            return;
        }
        foreach ($this->arrCommands as $command) {
            if ($command->search($message->body)) {
                try {
                    $command->run($message);
                }
                catch(JabberBot_AccessDeniedException $e) {
                    $this->log->log($e->getMessage(), XMPPHP_Log::LEVEL_INFO);
                    $this->log->log($e->trace, XMPPHP_Log::LEVEL_VERBOSE);
                    $message->reply($this->getRandomQuote('denied'));
                }
                catch (Exception $e) {
                    $message->reply($e->getMessage());
                }
            }
        }
        $message = null;
        unset($message);
    }
    /**
     * Handle Reconnect Event
     *
     * If we reconnected becasuse of a server timeout, ensure that room presences are restored
     *
     * @return void
     */
    public function handleReconnect()
    {
        $this->processUntil('session_start');
        $rooms = $this->rooms;
        foreach ($rooms as $room) {
            $this->log->log('Rejoining room ' . $room, XMPPHP_Log::LEVEL_INFO);
            $this->leaveRoom($room);
            $this->enterRoom($room);
        }
        $this->presence();
    }

    /**
     * Reset Ping timeout
     *
     * Resets the time since the last message was sent or received.
     *
     * @return void
     */
    public function resetPing()
    {
        $this->lastPing = time();
    }

    /**
     * Ping If Necessary
     *
     * Sends a ping mesasge to the server if there has not been a message
     * sent or received since the timeout interval.
     *
     * @return void
     */
    public function pingIfNecessary()
    {
        if (
            $this->pingInverval != 0
            && $this->lastPing + $this->pingInverval < time()
            ) {
            $this->ping();
            $this->resetPing();
        }
    }

    /**
     * Check Inbound messages
     *
     * Receives inbound messages and processes them.
     *
     * @return void
     */
    public function readInbound()
    {
        $payloads = $this->processUntil(array('message', 'presence', 'end_stream', 'session_start', 'vcard'), 2);
        foreach ($payloads as $event) {
            $this->resetPing();
            $pl = $event[1];
            switch ($event[0]) {
            case 'message':
                $this->_processMessage(new JabberBot_Message($pl, $this));
                break;

            case 'presence':
                $this->log->log("Presence: {$pl['from']} [{$pl['show']}] {$pl['status']}", XMPPHP_Log::LEVEL_INFO);
                break;

            case 'session_start':
                $this->getRoster();
                $this->presence($this->getRandomQuote('status'));
                break;
            }
        }
    }

    /**
     * Check oubound message queue
     *
     * Checks the database for messages queued up to send.
     *
     * @return void
     */
    public function checkMessageQueue()
    {
        $arrQueue = $this->db->checkMessageQueue();
        foreach ($arrQueue as $queuedMessage) {
            $this->resetPing();
            $this->db->markMessageSent(array('intMessageQueueId' => $queuedMessage['intMessageQueueId']));
            if ($queuedMessage['strType'] == 'groupchat') {
                $this->sendToRoom($queuedMessage['strTo'], $queuedMessage['strMessage']);
            } elseif ($queuedMessage['strType'] == 'chat') {
                $this->message($queuedMessage['strTo'] . '@' . $this->host, $queuedMessage['strMessage'], 'chat');
            }
        }
        unset($arrQueue);
    }

    /**
     * Get the config object
     *
     * @return JabberBot_Config
     */
    public function getConfig() {
        return $this->_config;
    }

    /**
     * Set the config object
     *
     * @param JabberBot_Config $config
     *
     * @return void
     */
    public function setConfig($config) {
        $this->_config = $config;
    }
}
