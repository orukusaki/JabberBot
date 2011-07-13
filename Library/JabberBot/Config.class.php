<?php
/**
* JabberBot_Config class
*
* Contains the JabberBot_Config class
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
* Config Class
*
* Not a very complex class, but it does what it needs to for now, and that's provide a
* way to get at the info stored in the bot's config.
*
* @package   JabberBot
* @subpackage Command
* @author    Stuart M. Grimshaw <stuart.grimshaw@gmail.com>
* @copyright 2011 Plusnet
* @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
*/
class JabberBot_Config {
    /**
     * Holds the config data.
     *
     * @var array
     */
    private $_config;

    /**
     * Constructor
     *
     * @param array $config
     *
     * @return void
     */
    public function __construct(array $config) {
        $this->_config = $config;
    }

    /**
     * Returns the value of the specified key, or Null if it's not found.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getValue($key) {
        return isset($key, $this->_config) ? $this->_config[$key] : Null;
    }
}