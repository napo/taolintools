<?php                                                                                                                                                                                            
class EnableUsersShell extends Shell { 
    var $uses = array('User');

    function help() {
            echo 'Enable users';
            echo "";
            echo "enableusers sender_email domain_email company_name subject_email url_taolin";
    }

    function main() {
        $sender = "";
        $company  = "";
        $subject = "";
        $url_taolin = "";
        $domain = "";
        if (count($this->args) < 4) {
            die("Not enough parameters");
        }  else {
            $sender = $this->args[0];
            $domain = $this->args[1];
            $company = $this->args[2];
            $subject = $this->args[3];
            $url_taolin = $this->args[4]; 
        }
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
        $users = $this->User->find('all', array('limit' => 2,'conditions' => $conditions,'fields' => array('User.active','User.created','User.id','User.name','User.surname','User.gender','User.login'),'order' => array('created DESC')));
        foreach ($users as $user) {
            $id = $user['User']['id'];
            $UsersController->admin_activate($id,1);
            $name = $user['User']['name'];
            $gender = $user['User']['gender'];
            $email = $user['User']['login'];
            $email = $email + "@" +  $domain;
            $MailController = new MailerController();
            $MailController->constructClasses();
            $MailController->sendWelcome($name,$email,$sender,$subject, $company, $url_taolin, $gender);
            echo "$user['User']['login']";
        }
   }

}
?>
