<?php
/**
 * JabberBot_Command class
 *
 * Contains the JabberBot_Command abstract class
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
 * JabberBot_Command abstract class
 *
 * All JabberBot Command classes should extend this one.  If a constructor is specified, remember
 * to call parent::__construct($bot)
 *
 * @package   JabberBot
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
abstract class JabberBot_Command
{
    /**
     * Reference link back to the owning bot.
     *
     * @var object
     */
    protected $_bot;

    /**
     * Quick Help
     *
     * @var string
     */
    public $quickHelp;

    /**
     * Constructor
     *
     * Stores the backlink for the calling bot.
     *
     * @param JabberBot_Bot $bot Reference link back to the owning bot.
     *
     * @return void
     */
    public function __construct($bot)
    {
        $this->_bot = $bot;
    }

    /**
     * Excecute the command
     *
     * Excecute the command against a specific message object.
     *
     * @param JabberBot_Message $message The inbound message
     */
    public abstract function run($message);

    /**
     * Search message body for keywords.
     *
     * Search message body to detirmine whether we're interested in processing it.
     *
     * @param string $body The message body
     *
     * @return boolean  Check result
     */
    public abstract function search($body);

    /**
     * Formats a two-dimentional array as a table for printing
     *
     * @param array $table
     *
     * @return void
     */
    public static function drawTable($table)
    {
        if (count($table) == 0) {
            return 'No Results';
        }
        $colWidths = array();
        // Get the width of each column.
        foreach ($table as $row) {
            $i = 0;
            foreach ($row as $key => $value) {
                if (!isset($colWidths[$i])) {
                    $colWidths[$i] = 0;
                }
                if (strlen($key) > $colWidths[$i]) {
                    $colWidths[$i] = strlen($key);
                }
                if (strlen($value) > $colWidths[$i]) {
                    $colWidths[$i] = strlen($value);
                }
                $i++;
            }
        }
        if (count($table) == 0) {
            return 'No Results';
        }
        $arrTitles = self::padArray(array_keys($table[0]), $colWidths);
        $text = implode(' | ', $arrTitles) . PHP_EOL;
        $text.= str_repeat('-', array_sum($colWidths) + (count($colWidths) - 1) * 3) . PHP_EOL;
        foreach ($table as $row) {
            $row = self::padArray($row, $colWidths);
            $text.= implode(' | ', $row) . PHP_EOL;
        }
        return $text;
    }

    /**
     * Fetch a url with an HTTP GET request
     *
     * @param string $url
     *
     * @return string
     */
    protected function curlGet($url) {

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        return curl_exec($curl);
    }

    /**
     * Pad the text in an array according to the widths in another array
     *
     * @param array $row       The array to format
     * @param array $colWidths The column widths
     *
     * @return array The formatted array
     */
    public static function padArray($row, $colWidths)
    {
        $j = 0;
        foreach ($row as $key => $value) {
            $row[$key] = sprintf('%-' . $colWidths[$j] . 's', $value);
            $j++;
        }
        return $row;
    }

    /**
     * Check ACL
     *
     * Checks the with the ACL whether a given user can access a
     * particular resource.  Throws an exception on failure
     *
     * @param  $username
     * @param  $resource
     * @throws JabberBot_AccessDeniedException
     *
     * @return void
     */
    protected function checkAcl($username, $resource)
    {
        if ($this->_bot->acl->check($username, $resource) == false) {
            throw new JabberBot_AccessDeniedException($username, $resource, $this->_bot->acl->trace);
        }
    }
}
