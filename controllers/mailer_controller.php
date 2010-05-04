<?php 
class MailerController extends AppController {
    var $name = 'Mailer';
    var $uses = '';
    var $components = array('Email');
    function sendWelcome($name,$to, $sender, $subject,$company,$url_taolin,$gender) {
        App::import('Core', 'Controller');
        App::import('Component', 'Email');
        $this->Controller =& new Controller();
        $this->Email =& new EmailComponent(null);
        $this->Email->initialize($this->Controller); 
        Configure::write('debug', '2');
        $this->Email->to = $to;
        $this->Email->subject = $subject;
        $this->Email->replyTo = $sender;
        $this->Email->from = $sender;
        $this->Email->template = 'welcome'; 
        $this->Email->Controller->set('subject',$subject);
        $this->Email->Controller->set('name',$name);
        $this->Email->Controller->set('sender',$sender);
        $this->Email->Controller->set('company',$company);
        $this->Email->Controller->set('url_taolin',$url_taolin);
        $this->Email->Controller->set('gender',$gender);
        $this->Email->send();
    }
}
?> 
