<?php
/**
 * JabberBot_AccessDeniedException class
 *
 * Contains the JabberBot_AccessDeniedException class
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
 * Access Denied Exception
 *
 * Thrown when an ACL check fails.
 *
 * @package   JabberBot
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Peter Smith
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_AccessDeniedException extends Exception
{
    /**
     * Username who requested access
     *
     * @var string
     */
    public $user;

    /**
     * Resource requested
     *
     * @var string
     */
    public $resource;

    /**
     * Backtrace detailing the steps taken by the ACL checker
     *
     * @var string
     */

    public $trace;
    /**
     * Constructor
     *
     * @param string $user
     * @param string $resource
     * @param string $trace
     *
     * @return void
     */
    public function __construct($user, $resource, $trace)
    {
        $this->user = $user;
        $this->resource = $resource;
        $this->trace = $trace;
        $this->message = 'Access denied for user ' . $user . ' to resource ' . $resource;
    }
}
