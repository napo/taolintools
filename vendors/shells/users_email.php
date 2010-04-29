<?php                                                                                                                                                                                            
class UsersEmailShell extends Shell { 
    var $uses = array('User');
    function help() {
            print 'Usage:\n\tusers_email domain [blacklist.txt]\n';
            echo 'If you give a blacklist file this must contain the logins to exclude each for lines\n';
            echo 'Example:\n';
            echo '\tuser1\n';
            echo '\tuser2\n';
            echo '\t...\n';
            echo '\tuserN';
    }

    function main() {
        if (count($this->args) == 0) {
            echo "Please give me the domain name\n\tExample: users_email fbk.eu\n";
            die();
        } else {
            $blacklist = array();
            if (count($this->args == 2)) {
                $blacklist = file($this->args[1],FILE_IGNORE_NEW_LINES);
            }

            $domain = $this->args[0];
            $conditions = array('active' => 1,'deleted' => 0, array('NOT' =>array('login ' => null)));
            $users = $this->User->find('all', array('conditions' => $conditions,'fields' => array('User.name','User.surname','User.login','COALESCE(mod_email, email) AS "User__email"'))); 
            foreach ($users as $user) {
               $exclude = False;
               $login = $user['User']['login'];
               foreach ($blacklist as $blackuser) {
                   if ($login == $blackuser) {
                       $exclude = True;
                       break;
                   }
               }
               if ($exclude == False) {
                   $name = $user['User']['name'];
                   $surname = $user['User']['surname'];
                   $email = "";
                    if ($user['User']['email']) {
                        $email = $user['User']['email'];
                    } else {
                        $addtomail = '@'.$domain;
                        $email = $user['User']['login'].$addtomail;                                                                                                                         
                    }
                    echo "$name,$surname,$email\n";
                    $black = False;                
               }
            }
        }
    }
}
?>
