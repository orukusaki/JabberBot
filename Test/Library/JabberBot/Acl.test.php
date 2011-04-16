<?php
/**
 * Unit test for JabberBot_Acl
 *
 * Contains the AclTest class and the MockDb class
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
 * Unit test for JabberBot_Acl
 *
 * @package   JabberBot
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class AclTest extends PHPUnit_Framework_TestCase
{
    /**
     * DataProvider for testRun
     */
    public function provider()
    {
        return array(
            array('/default/thing', 'unknown', false), 
            array('/bot', 'unknown', false), 
            array('/bot', 'normaluser', true), 
            array('/bot/admin', 'normaluser', false), 
            array('/bot/admin/quit', 'normaluser', false), 
            array('/bot/admin', 'adminuser', true), 
            array('/bot/admin/quit', 'adminuser', true), 
            array('/bot/admin/quit', 'unknown', false), 
            array('/bot/somecommand', 'unknown', false),
        );
    }
    /**
     * Set up mock objects
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        $aclDb = new MockDb();
        $userDb = new MockDb();
        $this->acl = new JabberBot_Acl($aclDb, $userDb);
    }
    /**
     * @dataProvider provider
     */
    public function testCheck($resource, $username, $expected)
    {
        $result = (bool)$this->acl->check($username, $resource);
        $this->assertEquals($expected, $result);
    }
}
/**
 *
 * Mock DB class
 *
 * @package   JabberBot
 * @author    Peter Smith <psmith@plus.net>
 * @copyright 2011 Plusnet
 * @license   http://www.opensource.org/licenses/gpl-3.0 GNU General Public License, version 3
 */
class MockDb
{
    /**
     *
     * Mock User Data
     * @var array
     */
    public $userData = array(
        array(
            'username' => 'unknown', 
            'return' => array(
                array('level' => 0,)
            )
         ), 
         array(
            'username' => 'normaluser', 
            'return' => array(
                array('level' => 2,)
            )
        ), 
        array(
            'username' => 'adminuser', 
            'return' => array(
                array('level' => 3,)
            )
        ),
    );
    /**
     *
     * Mock ACL data
     * @var array
     */
    public $aclData = array(
        array(
         'strPosition' => '/bot', 
            'return' => array(
                array('property' => 'level', 'fnName' => 'checkLevel', 'allow' => 1, 'value' => 2,), 
                array('property' => 'any', 'fnName' => 'checkAny', 'allow' => 0, 'value' => null,),
            )
        ), 
        array(
            'strPosition' => '/bot/admin', 
            'return' => array(
                array('property' => 'uname', 'fnName' => 'checkUname', 'allow' => 1, 'value' => 'adminuser',),
                array('property' => 'any', 'fnName' => 'checkAny', 'allow' => 0, 'value' => null,),
            ),
        ),
    );
    /**
     * Mock ACL rules method
     *
     * @param array $args
     * @return array Zero or more rows from the database
     */
    public function getRulesForPosition($args)
    {
        foreach ($this->aclData as $row) {
            if ($row['strPosition'] == $args['strPosition']) {
                return $row['return'];
            }
        }
        return array();
    }
    /**
     * Mock User lookup
     *
     * @param array $args
     * @return Zero or more rows from the database
     */
    public function getUserLevel($args)
    {
        foreach ($this->userData as $row) {
            if ($row['username'] == $args['username']) {
                return $row['return'];
            }
        }
        return array();
    }
}
