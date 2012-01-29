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
     *
     * @var string
     */
    public $quickHelp = '*twitter <username|status_id> - Fetch the latest twitter update for a user.';

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
        $this->checkAcl($message->getUsername(), '/bot/twitter');
        $words = explode(' ', $message->body);
        if (!isset($words[1])) {
            $message->reply("What username/status id?");
            return;
        }

        if (preg_match('/^\d{18}$/', $words[1])) {
            $update = $this->fetchStatusById($words[1]);
        } else {
            $update = $this->fetchStatusByUsername($words[1]);
        }

        $text = 'Update from '
               . $update['user'] . ' at '
               . $update['time'] . ':' . PHP_EOL
               . $update['text'];

        $message->reply($text);
    }

    /**
     * Fetch a twitter status by status id
     *
     * @param string $id
     *
     * @throws Exception
     *
     * @return array
     */
    protected function fetchStatusById($id) {

        $url = "http://api.twitter.com/1/statuses/show/$id.json?include_entities=false";
        $return = $this->curlGet($url);
        $returnData = json_decode($return, true);

        if (!is_array($returnData) || !isset($returnData['text'])) {

            throw new JabberBot_RemoteDataException("Fetching Status Id $id Failed");
        }

        return array(
            'user'   => $returnData['user']['name'],
            'text'   => $returnData['text'],
            'time'   => $returnData['created_at'],
        );
    }

    /**
     * Fetch the latest twitter status for a given username
     *
     * @param string $username
     *
     * @throws Exception
     *
     * @return array
     */
    protected function fetchStatusByUsername($username) {

        $url = "http://api.twitter.com/1/users/show.json?include_entities=false&screen_name=$username";
        $return = $this->curlGet($url);
        $returnData = json_decode($return, true);

        if (!is_array($returnData)
            || !isset($returnData['status'])
            || !isset($returnData['status']['text'])
        ) {
            throw new JabberBot_RemoteDataException("Fetching Status For User $username Failed");
        }

        return array(
            'user'   => $returnData['name'],
            'text'   => $returnData['status']['text'],
            'time'   => $returnData['status']['created_at'],
        );
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
        return preg_match('/^\*twitter\b/', $body);
    }
}
