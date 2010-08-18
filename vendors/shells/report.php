<?php 
class ReportShell extends Shell {
    var $uses = array('Userhistory','User','Photo');
    function help() {
        echo "use:\n";
        echo "\treport\t\tprint the report of the current month\n";
        echo "\treport month\tprint the report of the gived month of this year\n\t\t\tEXAMPLE: report 1\n";
        echo "\treport from to\tprint the report from a day to another\n\t\t\tEXAMPLE report 2009-11-01 2009-12-31\n";
    }
    function main() {
        $months_eng =array ('01' => 'January', '02' => 'February', '03' => 'March', '04'=> 'April','05' => 'May','06' => 'June','07' => 'July','08' => 'August','09' => 'September','10' =>'October', '11' => 'November', '12' => 'December');
        $months_ita =array ('01' => 'gennaio', '02' => 'febbraio', '03' => 'marzo', '04'=> 'aprile','05' => 'maggio','06' => 'giugno','07' => 'luglio','08' => 'agosto','09' => 'settembre','10' =>'ottobre', '11' => 'novembre', '12' => 'dicembre');

        Configure::write('debug', '0');
        $month = date('m');
        $year = date('Y');
        $day = date('d');
        $url = "http://desktop.fbk.eu";
        switch (count($this->args)) {
            case 0:
                echo "Please give the url name (ex. report http://desktop.fbk.eu)\n";
                die();
                break;
            case 1:
                $firstday = $year . "-" . $month . "-" . "01 00:00:00";
                $lastday = $year . "-" . $month . "-" . date('t',strtotime('today')) . " 00:00:00";
                break;
            case 2:
                $month = $this->args[1];
                if (strlen($month) == 1) {
                    $month = "0" . $month;
                }
                $firstday = date('Y-m-d G:i:s', mktime (0,0,0, $month, 1, $year));
                $lastday = date('Y-m-d G:i:s', mktime(0, 0, 0, $month+1, 0, $year));
                break;
            case 3:
                $month = "12"; #works only for december
                $firstday = $this->args[1];
                $lastday = $this->args[2];
                break;
        }
        $url = $this->args[0];
        #users for the email
        $conditions = array('active' => 1,'deleted' => 0);
        #$users = $this->User->find('all', array('conditions' => $conditions,'fields' => array('name','surname','COALESCE(login) as "User__login"')));
        
        #Total users
        $total_users = $this->User->find('count',array('conditions' => $conditions));
    
        #Total modified descriptions
        $fields = 'DISTINCT (login)';
        $conditions = array("NOT" => array("OR" => array("Userhistory.mod_description" => "NULL","Userhistory.mod_personal_page" => "NULL","Userhistory.mod_working_place" => "NULL","Userhistory.mod_home_address"=> "NULL", "Userhistory.facebook" => "NULL", "Userhistory.twitter" => "NULL")));
        $tot_mod_description = $this->Userhistory->find('count',array('recursive' =>-1,'conditions' => $conditions));
        
        #Total pictures
        $conditions = array('deleted' =>0);
        $total_photos = $this->Photo->find('count',array('fields' => 'DISTINCT(user_id)','recursive' => -1, 'conditions' => $conditions));
        
        $conditions = array('active' => 1,'deleted'=>0,'created >=' => $firstday,'created <' => $lastday, 'NOT' => array('login' => null));
        $fields = array('User.login','User.name','User.surname','User.corporate_unit','User.login');
        $newusers = $this->User->find('all',array('recursive' =>-1,'conditions' => $conditions,'fields' => $fields,'order' => 'created DESC'));
        $tot_newusers = count($newusers);
        
        echo "
        [Testo in italiano nel seguito]


        This the report about $url for $months_eng[$month]

        The number of users at the present time are $total_users.
        $tot_mod_description Users modified their profile entering information about themselves and their interests. 
        ";

        if ($tot_newusers > 0) {
        echo "
        In this month, there are $tot_newusers user.
        ";
        }
        echo "
        Thanks for your help in helping us making
        $url a better service for all of us! We keep waiting for your feedback and suggestions (use the 'Feedback provider' widget). 
        
        If you don't want to receive the next monthly updates, please reply to this email by simply writing 'Please unsubscribe me'.

        Thanks!

        ----


        Questo e' il report di $url per il mese di $months_ita[$month]

        Gli utenti ad oggi sono $total_users.
        $tot_mod_description utenti hanno modificato il loro profilo descrivendo se' stessi e i loro interessi. 
        ";
        if ($tot_newusers > 0) {  
            echo"
        In questo mese, sono presenti $tot_newusers utenti.
        ";
        }
        echo "
        Grazie per il tuo aiuto nel rendere $url servizio migliore per tutti noi! 
        Rimaniamo in attesa dei tuoi preziosi suggerimenti (usa il widget 'Feedback provider'). 
        

        Se non vuoi piu' ricevere questi aggiornamenti mensili, rispondi a questa email scrivendo 'Per favore disiscrivetemi'.

        Grazie! 
        
        ---- list of the new users / lista dei nuovi arrivati ----
        ";
        echo "\n";
        if (count($newusers) > 0) {
            foreach ($newusers as $newuser) {
                echo "\t" . $newuser["User"]["name"];
                echo "\n";
                echo "\t" . $newuser["User"]["surname"];echo "\n";
                echo "\t" . $newuser["User"]["corporate_unit"];
                echo "\n\n";
            }
        }



    }

}
?>
