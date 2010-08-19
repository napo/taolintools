<?php                                                                                                                                                                                            
class EnableUsersShell extends Shell { 
    var $uses = array('User');

    function help() {
            echo 'Enable users';
            echo "\n";
            echo "enableusers -> show all the activable users\n";
            echo "enableusers show_num_activable_users -> show a number of activable users\n";
            echo "  Ex: enableusers 1 -> show only the first activable user\n";
            echo "enableusers show_num_activable_users activate\n";
            echo "  Ex: enableusers 1 1 -> show the first user and activate it (0=no 1=yes)\n";
            echo "enableusers sender_email domain_email company_name subject_email url_taolin\n";
            echo "  Ex: enableusers risorseumane@fbk.eu fbk.eu FBK 'Welcome in FBK' http://desktop.fbk.eu -> activate everybody and send welcome message\n";
            echo "enableusers show_num_activable sender_email domain_email company_name subject_email url_taolin\n";
            echo "  Ex: enableusers 2 risorseumane@fbk.eu fbk.eu FBK 'Welcome in FBK' http://desktop.fbk.eu -> activate 2 users and send the welcome message\n";
    }


    function main() {
        $sender = "";
        $company  = "";
        $subject = "";
        $url_taolin = "";
        $domain = "";
        $limit = 0;
        $activate = 0; 
        $sendmessage = 0;
        switch (count($this->args)) {
            case 0;
                break;
            case 1:
                $limit = $this->args[0];
                break;
            case 2:
                $limit = $this->args[0];
                $activate = $this->args[1];
                break;
            case 5:
                $activate = 1;
                $sendmessage = 1;
                $sender = $this->args[0];
                $domain = $this->args[1];
                $company = $this->args[2];
                $subject = $this->args[3];
                $url_taolin = $this->args[4];
                break;
            case 6:
                $limit = $this->args[0];
                $activate = 1;
                $sendmessage = 1;
                $sender = $this->args[1];
                $domain = $this->args[2];
                $company = $this->args[3];
                $subject = $this->args[4];
                $url_taolin = $this->args[5];
                break;
            default:
                die("Not enough parameters");
                break;
        }
       /* 
        if (count($this->args) < 4) {
            die("Not enough parameters");
        }  else {
            $sender = $this->args[0];
            $domain = $this->args[1];
            $company = $this->args[2];
            $subject = $this->args[3];
            $url_taolin = $this->args[4]; 
        }
        */

        App::import('Core', 'Controller');
        App::import('Component','Acl');
        App::import('Component','Email');
        App::import('Controller', 'Users');
        App::import('Controller', 'Mailer');
        $UsersController = new UsersController();
        $UsersController->constructClasses();

        $this->Acl =& new AclComponent();
        $controller = null;
        $this->Acl->startup($controller);
        $this->Aco =& $this->Acl->Aco; 
       
        $conditions = array('active' => 0,'deleted' => 0, array('NOT' =>array('login ' => null)));
        if ($limit > 0) {
            $users = $this->User->find('all', array('limit' => $limit,'conditions' => $conditions,'fields' => array('User.active','User.created','User.id','User.name','User.surname','User.gender','User.login'),'order' => array('created DESC')));
        } else {
            $users = $this->User->find('all', array('conditions' => $conditions,'fields' => array('User.active','User.created','User.id','User.name','User.surname','User.gender','User.login'),'order' => array('created DESC')));
        }
        foreach ($users as $user) {
            $id = $user['User']['id'];
            $name = $user['User']['name'];
            $gender = $user['User']['gender'];
            $login = $user['User']['login'];
            $email = $user['User']['login'];
            $email = $login . "@" .  $domain;
            if ($activate ==1 ) {
                $UsersController->admin_activate($id,1);
            }
            if ($sendmessage == 1) {
               $MailController = new MailerController();
               $MailController->constructClasses();
               $MailController->sendWelcome($name,$email,$sender,$subject, $company, $url_taolin, $gender);
            }
            echo "$login\n";
        }
   }

}
?>
