<?php
error_reporting(0);
/*
  DB Table Stucture:
  type(varchar),subject(varchar),template(text),status(Y,N)

  from = samim@gmail.com, to = "anyone@gmail.com",
  templete_type = <template_type>
  param = array( "{name}" => "Samim Almamun" )

  Function call:
  send_mail($from,$to,$templete_type,$param)
 */

class Mailtemplete {

    public function __construct() {
        $CI = &get_instance();
        

        $val=$this->get_site_settings();

        define('SITE_CONTACT_PHONE',                   $val->contact_no); 
        define('SITE_CONTACT_EMAIL',                   $val->support_mail); 
        define('SITE_ADDRESS',                         $val->address); 


        #define('SITE_TITLE',                            $val->site_title); 
        define('META_TITLE',                            $val->meta_title); 
        define('SITE_KEY',                              $val->meta_keys); 
        define('SITE_DESC',                             $val->meta_desc); 
        define('LOGO',                                  $val->site_logo); 
        define('SMLOGO',                                $val->logo_sm); 
        define('FAV',                                   $val->fav_icon); 
        define('CURRENCY_SYMBOL',                       $val->currency_symbol); 
        define('CURRENCY',                              $val->currency); 
        define('PAYPAL_MODE',                           $val->paypal_mode);
        define('PAYPAL',                                $val->paypal_mail);
        define('FB_PAGE',                               $val->facebook); 
        define('FB_APP_ID',                             $val->fb_app_id); 
        define('FB_APP_SECRET',                         $val->fb_app_secret); 
        define('TWITTER_PAGE',                          $val->twitter); 
        define('SKYPE_URL',                             $val->skype);
        define('COPYRIGHTYEAR',                         $val->copyright_year);

        define('TUMBLR_PAGE',                           $val->tumblr); 
        define('GPLUS_PAGE',                            $val->google_plus); 
        define('GPLUS_APP_ID',                          $val->gplus_api);
        //Chandan Added
        define('SOCIAL_LINKEDIN',                       $val->social_linkedin); 
        define('SOCIAL_PINTEREST',                      $val->social_pinterest); 
        define('SOCIAL_INSTAGRAM',                      $val->social_instagram); 

        define('DEMO',                                  ($val->demo == "TRUE") ? true : false);
        define('DEMO_USER',                             (DEMO) ? 'demo@demo.com' : "");
        define('DEMO_MSG',                              (DEMO) ? "Sorry to say! For demo purpose you can't change this record." : "");


        //For Mailing Purpose
        define('SITE_EMAIL_FROM',       'noreply@giftrete.com');
        define('SITE_EMAIL_FROM_NAME',  SITE_TITLE);
        define('SITE_EMAIL_REPLYTO',    $val->support_mail);
        define('SITE_EMAIL_CONTACT'  ,  $val->support_mail);
        define('SITE_EMAIL_BCC'      ,  $val->admin_mail);

        //Mailin Credentials
        define('MAILIN_USERNAME',  'admin@giftrete.com');
        define('MAILIN_PASSWORD',  '79bVBT6QFOSMm1Hg');


    }

    public function send_mail($from = "", $to = '', $templete_type = '', $param = array()) {
       $CI = &get_instance();
        //p($to);
        //p($param,1);
        $templete = $this->get_templete($templete_type, $param);

        if (!$templete) {
            return false;
        }
        
        $subject = $templete['subject'];
        $body    = $templete['template'];

         //# RAW CODE:
        $CI->load->library('Mailin');

        //For from Email
        // $from       = SITE_EMAIL_FROM;             
        if($from == ""){
            $from = SITE_EMAIL_FROM;
        }
        
        //For ReplyTO Email
        $replyTo    = SITE_EMAIL_REPLYTO;

         //For pram name for  Email
        $name = '';
        if(!empty($param) && !empty($param['{name}']) ){
            $name = $param['{name}'];
        }
            
        //$this->$mailin = new Mailin('admin@giftrete.com', '79bVBT6QFOSMm1Hg');

        // $CI->mailin->
        // setTo($to, $name)->
        // setFrom(SITE_EMAIL_FROM, SITE_TITLE)->
        // setReplyTo(SITE_EMAIL_REPLYTO, SITE_TITLE)->
        // setSubject('Enter the subject here')->
        // setText($subject)->
        // setHtml($body);
        // $send = $CI->mailin->send();


        // $CI->mailin->
        // setTo($to, $name)->
        // setFrom(SITE_EMAIL_FROM, SITE_TITLE)->
        // setReplyTo(SITE_EMAIL_REPLYTO, SITE_TITLE)->
        // setSubject($subject)->
        // setHtml($body);

        //p($body,1);

        $CI->mailin->setTo($to, $name);
        $CI->mailin->setFrom(SITE_EMAIL_FROM, SITE_TITLE);
        $CI->mailin->setReplyTo(SITE_EMAIL_REPLYTO, SITE_TITLE);
        $CI->mailin->setSubject($subject);
        $CI->mailin->setHtml($body);

        if(defined('SITE_EMAIL_BCC')){
            $CI->mailin->setBcc(SITE_EMAIL_BCC);
        }
        $send = $CI->mailin->send();

        $data = array(
            'subject'            => $subject,
            'body'               => $body,
            'from'               => $from,
            'replyTo'            => $replyTo,
            'to'                 => $to,
            'name'               => $name,
            'SITE_EMAIL_FROM'    => SITE_EMAIL_FROM,
            'SITE_TITLE'         => SITE_TITLE,
            'SITE_EMAIL_REPLYTO' => SITE_EMAIL_REPLYTO,
            'SITE_EMAIL_BCC'     => SITE_EMAIL_BCC,
        );
        $data=json_encode($data);
        $mydata = array('maillog' => $data,'response' => $send);
        $CI->db->insert('maillog',$mydata);
        return $send;
    }

    public function send_mail_($from = "", $to = '', $templete_type = '', $param = array()) {
        $CI = &get_instance();
        //p($to);
        //p($param,1);
        $templete = $this->get_templete($templete_type, $param);

        if (!$templete) {
            return false;
        }
        
        $subject = $templete['subject'];
        $body    = $templete['template'];

         //# RAW CODE:
        $CI->load->library('Mailin');

        //For from Email
        // $from       = SITE_EMAIL_FROM;             
        if($from == ""){
            $from = SITE_EMAIL_FROM;
        }
        
        //For ReplyTO Email
        $replyTo    = SITE_EMAIL_REPLYTO;

         //For pram name for  Email
        $name = '';
        if(!empty($param) && !empty($param['{name}']) ){
            $name = $param['{name}'];
        }
        
        $CI->mailin->setTo($to, $name);
        $CI->mailin->setFrom(SITE_EMAIL_FROM, SITE_TITLE);
        $CI->mailin->setReplyTo(SITE_EMAIL_REPLYTO, SITE_TITLE);
        $CI->mailin->setSubject($subject);
        $CI->mailin->setHtml($body);

        if(defined('SITE_EMAIL_BCC')){
            $CI->mailin->setBcc(SITE_EMAIL_BCC);
        }
        $send = $CI->mailin->send();


        $data = array(
            'subject'            => $subject,
            'body'               => $body,
            'from'               => $from,
            'replyTo'            => $replyTo,
            'to'                 => $to,
            'name'               => $name,
            'SITE_EMAIL_FROM'    => SITE_EMAIL_FROM,
            'SITE_TITLE'         => SITE_TITLE,
            'SITE_EMAIL_REPLYTO' => SITE_EMAIL_REPLYTO,
            'SITE_EMAIL_BCC'     => SITE_EMAIL_BCC,
        );

        $data=json_encode($data);

        $mydata = array('maillog' => $data,'response' => $send);


        $CI->db->insert('maillog',$mydata);

       
            
        return $send;
    }

    public function get_site_settings(){
        $CI = &get_instance();
        return $CI->db->get('os_setting')->row();
    }

    public function get_templete($template_type = '', $param = array()) {
        $CI = &get_instance();
        $mail_body = $CI->db->select("subject,template")->get_where('os_email_template', array("type" => $template_type, "status" => "Y"))->row_array();
        if (is_array($mail_body) && count($mail_body) > 0) {
			
            $template = html_entity_decode($mail_body['template']);
			$frame = $CI->load->view('templates/frame', '', true);
			// $_html = '<html><head><link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" /><style>@import url("https://fonts.googleapis.com/css?family=PT+Sans"); body { font-family: "PT Sans", sans-serif; color: #00AA22; } </style></head><body>{html_body}</body></html>';
			
			$template = str_replace("{HTML_BODY}", $template, $frame);
			
            $subject = $mail_body['subject'];

            foreach ($param as $key => $value) {
                $template = str_replace($key, $value, $template);
                $subject = str_replace($key, $value, $subject);
            }

			// p($template, 1, 0, 0);
            
            //For Template Body Section::
            $site_link = '<a href="'.base_url().'" target="_blank">'.SITE_TITLE.'</a>';
            $site_logo_image = "<img src='".SITE_LOGO_PATH."' alt='".SITE_TITLE."'/>";

            $template  = str_replace("{site_logo}", SITE_LOGO_PATH , $template);
            $template  = str_replace("{site_title}", SITE_TITLE, $template);
            $template  = str_replace("{site_link}", $site_link, $template);

            $template  = str_replace("{SITE_LOGO}", SITE_LOGO_PATH , $template);
            $template  = str_replace("{SITE_TITLE}", SITE_TITLE, $template);
            $template  = str_replace("{SITE_LINK}", $site_link, $template);



            //For Site Logo Image Show
            $template  = str_replace("{site_logo_image}", $site_logo_image, $template);
            $template  = str_replace("{SITE_LOGO_IMAGE}", $site_logo_image, $template);

            //For Template Subject Section::
            $subject   = str_replace("{SITE_TITLE}", SITE_TITLE, $subject);

            $mail = array(
                'subject' => $subject,
                'template' => $template
            );
           
            return $mail;
        } else {
            return FALSE;
        }
    }

}

?>