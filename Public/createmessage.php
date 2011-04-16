<?php
/**
 * Create an outbound message
 *
 * Inserts a message into the database
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
require_once (dirname(__FILE__) . '/../Scripts/Bootstrap.inc.php');
$db = new JabberBot_Db('bot');
if (isset($_REQUEST['to']) 
 && isset($_REQUEST['type']) 
 && in_array($_REQUEST['type'], array('chat', 'groupchat')) 
 && isset($_REQUEST['message'])
) {
    $db->createMessage(
        array(
            'to' => stripslashes($_REQUEST['to']), 
            'type' => stripslashes($_REQUEST['type']), 
            'message' => stripslashes($_REQUEST['message']),
        )
    );
    echo "Success";
} else {
    echo "Failure";
}
