<?php
    
    namespace Hcode;

    header('Content-Type: text/html; charset=UTF-8');

    use Rain\Tpl;

    class Mailer {

        const USERNAME = "pedroflu04@gmail.com";
        const PASSWORD = "nense0304080998";
        const NAME_FROM = "Hcode Store";

        private $mail;

        public function __construct($toAddress, $toName, $subject, $tplName, $data = array())
        {

            $config = array(
                "tpl_dir"=>$_SERVER["DOCUMENT_ROOT"] . "/views/email/",
                "cache_dir"=>$_SERVER["DOCUMENT_ROOT"] . "/views-cache/",
                "debug"=>false
            );
            Tpl::configure($config);

            $tpl = new Tpl;

            foreach($data as $key => $value)
            {
                $tpl->assign($key, $value);
            }

            $html = $tpl->draw($tplName, true);

            $this->mail = new \PHPMailer;
            
            $this->mail->isSMTP();
            $this->mail->SMTPDebug = 0;
            $this->mail->CharSet = 'UTF-8';

            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->Port = 587;

            $this->mail->SMTPSecure = 'tls';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = Mailer::USERNAME;
            $this->mail->Password = Mailer::PASSWORD;

            $this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);
            $this->mail->addAddress($toAddress, $toName);
            $this->mail->Subject = $subject;
            $this->mail->msgHTML($html);
            
            $this->mail->AltBody = 'This is a plain-text message body';
           
            $this->mail->SMTPOptions = array(
                'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
                );
        }

        public function send()
        {
            return $this->mail->send();
        }

    }

?>