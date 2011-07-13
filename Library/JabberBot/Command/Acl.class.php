<?php
/**
 * JabberBot_Command_Acl class
 *
 * Contains the JabberBot_Command_Acl class
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
 * Access Controll List Command
 *
 * Used to view / edit the access control list
 *
 * @package   JabberBot
 * @subpackage Command
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Peter Smith
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Command_Acl extends JabberBot_Command
{
    /**
     * Quick Help
     *
     * @var string
     */
    public $quickHelp = '*acl - Access Control functions, type *acl help for more details';

    /**
     * Db Adaptor
     *
     * @var JabberBot_Db
     */
    private $_db;

    /**
     * Constructior
     * @param JabberBot_Bot $bot
     *
     * @return void
     */
    public function __construct($bot)
    {
        parent::__construct($bot);
        $this->_db = new JabberBot_Db('acl');
    }

    /**
     * Excecute the command
     *
     * Excecute the command against a specific message object.
     *
     * @param JabberBot_Message $message The message to process
     *
     * @return void
     */
    public function run($message)
    {
        $words = explode(' ', $message->body);
        switch ($words[1]) {
        case 'view':
            $this->checkAcl($message->getUsername(), '/bot/acl/view');
            $text = 'All Acl Rules' . PHP_EOL;
            $text.= self::drawTable($this->_db->getAllRules());
            $message->replyHTML('<span style="font-family:monospace;">' . nl2br($text) . '</span>');
            break;

        case 'add':
        case 'insert':
            try {
                $validProperties = array();
                $propertiesTable = $this->_db->getAllProperties();
                foreach ($propertiesTable as $row) {
                    $validProperties[] = $row['handle'];
                }
                if (isset($words[2]) && (strpos($words[2], '/') === 0)) {
                    $position = $words[2];
                } else {
                    throw new Exception('To few arguments');
                }
                if (isset($words[3]) && in_array($words[3], array('allow', 'deny'))) {
                    $directive = ($words[3] == 'allow') ? true : false;
                } else {
                    throw new Exception('Unknown directive');
                }
                if (isset($words[4]) && in_array($words[4], $validProperties)) {
                    $property = $words[4];
                } else {
                    throw new Exception('Unknown property. Valid properties:' . implode(', ', $validProperties));
                }
                $value = isset($words[5]) ? $words[5] : null;
            }
            catch(Exception $e) {
                $message->reply($e->getMessage() . 'Usage: *acl add <pos> <allow|deny> <property> <value>');
                return;
            }
            $this->checkAcl($message->getUsername(), '/acl/add' . $position);
            try {
                $this->_db->insertRule(
                    array('position' => $position, 'allow' => $directive, 'property' => $property, 'value' => $value)
                );
            }
            catch(Exception $e) {
                $message->reply('Db Error :( ' . PHP_EOL . $e->getMessage());
                return;
            }
            $message->reply('Rule inserted');
            break;

        case 'rm':
        case 'remove':
            if (!isset($words[2]) || !is_numeric($words[2])) {
                $message->reply('Usage: *acl rm <id>. Use *acl view to get the id');
                return;
            }
            $this->checkAcl($message->getUsername(), '/acl/rm' . $words[2]);
            try {
                $this->_db->deleteRule(array('id' => $words[2]));
            }
            catch(Exception $e) {
                $message->reply($e->getMessage());
                return;
            }
            $message->reply('Rule deleted');
            break;

        case 'check';
            if (isset($words[2])) {
                $username = $words[2];
            } else {
                $message->reply('Usage: *acl check <username> <resource>');
                return;
            }
            if (isset($words[3]) && (strpos($words[3], '/') === 0)) {
                $resource = $words[3];
            } else {
                $message->reply('Usage: *acl check <username> <resource>');
                return;
            }
            $result = $this->_bot->acl->check($username, $resource);
            $message->reply(
                'Check result is: ' . (($result) ? 'allow' : 'deny') . PHP_EOL
                . 'Trace:' . PHP_EOL . $this->_bot->acl->trace
            );
            break;

        case 'help':
            $message->reply("*acl <command>\ncommands:\nview\nadd\ncheck\nrm");
            break;
        }
    }

    /**
     * Search message body to detirmine whether we're interested in processing it.
     *
     * @param  string $body The message body
     *
     * @return boolean  Check result
     */
    public function search($body)
    {
        return preg_match('/^\*acl\b/', $body);
    }
}
