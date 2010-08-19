<?php                                                                                                                                                                                            
class CheckEnabledUserShell extends Shell { 
    var $uses = array('User');

    function help() {
            echo 'checks whether a user is allowed\n';
            echo "\n";
            echo "checkenableduser login\n";
            echo "0 = no\n 1 = yes";
    }

    function main() {
        $login = "";
        $answer = 0;
        if (count($this->args) != 1) {
            die("Not enough parameters");
        } else {
            $login = $this->args[0];
        }
        
        App::import('Core', 'Controller');
        App::import('Component','Acl');
        App::import('Controller', 'Users');
        $UsersController = new UsersController();
        $UsersController->constructClasses();

        $this->Acl =& new AclComponent();
        $controller = null;
        $this->Acl->startup($controller);
        $this->Aco =& $this->Acl->Aco; 
       
        $conditions = array('login ' => $login);
        $users = $this->User->find('all', array('conditions' => $conditions,'fields' => array('User.active')));
        $num_users = count($users);
        if ($num_users > 0) {
            if ($num_users == 1) {
                $active = $users[0]['User']['active'];
                if ($active == 0) {
                    $answer = 1;
                }
            }
        }
        echo $answer;
   }

}
?>
