<?php
/**
 * JabberBot_Command_Jira class
 *
 * Contains the JabberBot_Command_Jira class
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
 * @author    Stuart M. Grimshaw <stuart.grimshaw@gmail.com>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
/**
 * Jira command
 *
 * Watches the channel for patterns that look like unlinked Jira issues & provides
 * a link to them.
 *
 * You need to add a section to the config for this command.
 *
 * [jira]
 * host = http://jira.host/
 * user = username
 * password = password
 *
 * Note the trailing /
 *
 * @package   JabberBot
 * @subpackage Command
 * @author    Stuart M. Grimshaw <stuart.grimshaw@gmail.com>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Command_Jira extends JabberBot_Command
{
    /**
     * Regex string for Jira tickets
     *
     * @var string
     */
    const re = '/(\s|^)([[:upper:]]+-[[:digit:]]+)/';

    /**
     * Quick Help
     *
     * @var string
     */
    public $quickHelp = 'XXX-123 - parse a JIRA link';

    /**
     * Executes this command, takes the matched pattern
     * and turns it into a Jira link which is then displayed
     * to the channel.
     *
     * @see JabberBot_Command::run()
     *
     * @return void
     */
    public function run($message)
    {
        $issue = preg_match(JabberBot_Command_Jira::re, $message->body, $matches);
        $jiraConfig = $this->_bot->getConfig()->getValue("jira");

        $message->reply($jiraConfig["host"] . "browse/" . $matches[2]);
    }

    /**
     * Searches the message body to decide if should be processed.
     *
     * @see JabberBot_Command::search()
     *
     * @return bool
     */
    public function search($body)
    {
        return preg_match(JabberBot_Command_Jira::re, $body);
    }
}