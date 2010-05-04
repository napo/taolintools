<?php
/**
  * This file is part of taolin project (http://taolin.fbk.eu)
  * Copyright (C) 2008, 2009 FBK Foundation, (http://www.fbk.eu)
  * Authors: SoNet Group (see AUTHORS.txt)
  *
  * Taolin is free software: you can redistribute it and/or modify
  * it under the terms of the GNU Affero General Public License as published by
  * the Free Software Foundation version 3 of the License.
  *
  * Taolin is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  * GNU Affero General Public License for more details.
  *
  * You should have received a copy of the GNU Affero General Public License
  * along with Taolin. If not, see <http://www.gnu.org/licenses/>.
  *
  */
?>
<?php

uses('sanitize');

class UsersController extends AppController {
    var $name = 'Users';
    var $helpers = array('Html','Form','Javascript');
    var $components = array('Email');
    var $paginate = array(
        'limit' => 50,
        'order' => 'User.surname'
    );
    /*****************************************************
     *****************************************************
     * ADMIN
     *****************************************************
     *****************************************************/


    function admin_activate($uid, $active = 1){
        Configure::write('debug', '0');     //turn debugging off; debugging breaks ajax

        $aro = new Aro();

        //find the id of this user's aco
        $aro->create();
        $user_aro = $aro->find('first', array(
            'conditions' => array(
                'model' => 'User',
                'foreign_key' => $uid
            ),
            'fields' => array('id')
        ));
        
        $new_aro = array('model' => 'User', 'foreign_key' => $uid);
        if ($user_aro)
            $new_aro['id'] = $user_aro['Aro']['id'];

        if ($active){ // add this user to the users Aro group
            // find the id of the users group
            $aro->create();
            $users_aro = $aro->findByAlias('users');

            $users_aro_id = $users_aro['Aro']['id'];
        
            $new_aro['parent_id'] = $users_aro_id;
            

        } else {
            $new_aro['parent_id'] = NULL;
        }

        $aro->save($new_aro);

        $user['id'] = $uid;
        $user['active'] = $active;

        $this->User->save($user);

    }
}
?>
