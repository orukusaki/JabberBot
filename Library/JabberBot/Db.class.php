<?php
/**
 * Simple Db adaptor class
 *
 * Comtains the Simple Db adaptor class
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
 * Simple Db adaptor
 *
 * Provides a simple interface to a mysql database using stored statement files and running them as methods.
 * Variable names prepended by a colon are substituted for the method arguments.
 *
 * e.g:
 * Statements/test/getLogin.sql:
 * SELECT username, name, email, level from users where username = :user and password = md5(:pass)
 *
 * PHP code:
 * $db = new JabberBot_Db('test');
 * $result = $db->getLogin(array('user' => $user, 'pass' => $password));
 *
 * @package   JabberBot
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Db
{
    /**
     * Array containing db connection details
     *
     * @var array
     */
    private $_conf;

    /**
     * Mysql connection resource
     *
     * @var resource
     */
    private $_conn;

    /**
     * Db connection name
     *
     * @var string
     */
    private $_name;

    /**
     * Constructor
     *
     * Initialises the object by loading the config from the database.ini file
     *
     * @param  string $name The db connection name, as stored in the database.ini file
     * @throws Exception Exception
     *
     * @return void
     */
    public function __construct($name)
    {
        $conf = parse_ini_file(dirname(__FILE__) . '/../../Config/database.ini', true);
        if (!isset($conf[$name])) {
            throw new Exception('No db config found for ' . $name);
        }
        $this->_conf = $conf[$name];
        $this->_name = $name;
    }

    /**
     * Connect to db
     *
     * Open the Mysql connection and select the appropriate database ready for querying
     *
     * @return void
     */
    private function _connect()
    {
        $this->_conn = mysql_connect($this->_conf['host'], $this->_conf['user'], $this->_conf['pass']);
        mysql_select_db($this->_conf['db'], $this->_conn);
    }

    /**
     * Close the db connection
     *
     * @return void
     */
    private function _disconnect()
    {
        mysql_close($this->_conn);
    }

    /**
     * Run a named query
     *
     * Magic method, loads a statement file matching the called method name.  Performs substitutions
     * according to the arguments passed, then runs the query.
     *
     * @param string $statement Method name is used as the statement name
     * @param array  $args      Array containing key => value pairs of arguments to substitute into
     *                          the statement.
     * @throws Exception Exception
     *
     * @return array Results tabulated as an array (empty if the statement was INSERT or UPDATE)
     */
    public function __call($statement, $args)
    {
        if (!file_exists(dirname(__FILE__) . '/../../Statements/' . $this->_name . '/' . $statement . '.sql')) {
            throw new Exception('Statement ' . $statement . ' not found');
        }
        $query = file_get_contents(dirname(__FILE__) . '/../../Statements/' . $this->_name . '/' . $statement . '.sql');
        preg_match_all("/:.+?\b/", $query, $matches);
        $this->_connect();
        foreach ($matches[0] as $key) {
            $value = mysql_real_escape_string($args[0][substr($key, 1) ]);
            $query = str_replace($key, '"' . $value . '"', $query);
        }
        $result = mysql_query($query);
        if ($result === false) {
            throw new Exception('Query failed: ' . $query . PHP_EOL . ' : ' . mysql_error());
        } elseif ($result === true) {
            return true;
        }
        $table = array();
        while ($row = mysql_fetch_assoc($result)) {
            $table[] = $row;
        }
        $this->_disconnect();
        mysql_free_result($result);
        $result = null;
        unset($result);
        return $table;
    }
}
