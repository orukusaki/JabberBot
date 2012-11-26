<?php
/**
 * JabberBot ACL
 *
 * Contains the class JabberBot_Acl
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
 * JabberBot Acl class
 *
 * A basic Access Control List implimentation. An instance of this class represents an
 * access control processor
 *
 * @package   JabberBot
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class JabberBot_Acl
{
    /**
     * Trace of the checking process for debugging purposes
     *
     * @var string
     */
    public $trace;

    /**
     * Db Adaptor for the ACL
     *
     * @var JabberBot_Db
     */
    private $_acldb;

    /**
     * Db Adaptor for performing user queries
     *
     * @var JabberBot_Db
     */
    private $_userdb;

    /**
     * Constructor
     *
     * Set up database adaptors
     *
     * @return void
     */
    public function __construct($aclDb = null, $userDb = null)
    {
        if ($aclDb) {
            $this->_acldb = $aclDb;
        } else {
            $this->_acldb = new JabberBot_Db('acl');
        }
        if ($userDb) {
            $this->_userdb = $userDb;
        } else {
            $this->_userdb = new JabberBot_Db('users');
        }
    }

    /**
     * Perform ACL check
     *
     * Check the ACL for rules at the position given by $resource.
     * If a relevant rule is found, check whether it applies to the user specified by $username.
     * If not, shift up the tree and try again until a relevent rule is found.
     * If/when a new
     *
     * @param string $username Username requesting access
     * @param string $resource The resource name requested, separated by '/', e.g '/admin/user/add'
     *
     * @return boolean
     */
    public function check($username, $resource)
    {
        $this->trace = 'Checking whether username: ' . $username . ' can access resource: ' . $resource . PHP_EOL;
        $tree = explode('/', $resource);
        do {
            $position = implode('/', $tree);
            if ($position == '') {
                $position = '/';
            }
            $this->trace.= 'Looking for rules at position: ' . $position . PHP_EOL;
            // Check for rules at the present position
            $arrRules = $this->_acldb->getRulesForPosition(array('strPosition' => $position));
            foreach ($arrRules as $rule) {
                $this->trace.= 'Found rule: ' . ($rule['allow'] ? 'allow' : 'deny') . ' '
                                        . $rule['property'] . ' ' . $rule['value'] . ' - ';
                if ($this->{'_' . $rule['fnName']}($username, $rule['value'])) {
                    $this->trace.= 'Rule applied.' . PHP_EOL;
                    return $rule['allow'];
                } else {
                    $this->trace.= 'Rule does not apply.' . PHP_EOL;
                }
            }
            // If no relevent rules were found, move one position up the tree and try again
            array_pop($tree);
        } while (count($tree) > 0);
        // If we got here, then there are no relevent rules at all, default to deny.
        $this->trace.= 'No applicable rule was found';
        return false;
    }

    /**
     * Check against a given username
     *
     * The rule applies to the specified user.
     *
     * @param string $username Username being tested
     * @param string $value    Username assigned to the rule
     *
     * @return boolean Test result
     */
    private function _checkUname($username, $value)
    {
        return $username == $value;
    }

    /**
     * Check a user belongs to a group
     *
     * The rule applies to the user because they are a member of a group
     *
     * @param string $username Username being tested
     * @param string $handle   The Group Handle
     *
     * @return boolean Test result
     */
    private function _checkGroup($username, $handle)
    {
        $usersInGroup = $this->_userdb->getGroupMembersByHandle(array('handle' => $handle));
        foreach ($usersInGroup as $member) {
            if ($member['username'] == $username) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check user level
     *
     * The rule applies to the user because their access 'level' is equal to or greater than
     * the specified level.
     *
     * @param string $username Username being tested
     * @param int    $value    Level being tested against
     *
     * @return boolean Test result
     */
    private function _checkLevel($username, $value)
    {
        $result = $this->_userdb->getUserLevel(array('username' => $username));
        if (count($result) != 1) {
            return false;
        }
        return $result[0]['level'] >= $value;
    }

    /**
     * Any user
     *
     * Defaults to true for any user
     *
     * @param string $username Username being tested
     * @param mixed  $value    Always null
     *
     * @return boolean Test result
     */
    private function _checkAny($username, $value)
    {
        return true;
    }
}