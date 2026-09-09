<?php

class Sendinblue {

    private $CI;

    public function __construct() {
        $this->CI = &get_instance();
    }


    public function sendinblue_sms($recipient,$content){

        if (empty($recipient)){
            log_message('Debug', "Recepient is empty, Cannot Send Empty SMS");
            return "Recepient is empty, Cannot Send Empty SMS";            
        }


        $sender_id=SMS_SENDER_ID;

        $curl = curl_init();
        
        $type='transactional';
        $tag='Tag1';
        $api_key='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';
        $request_header= array(
            'Accept: application/json',
            'Content-Type: application/json',
            'api-key: '.$api_key,
        );
        
        
        //$content=str_replace("<br/>", "%0A", $content);

        $request_data= array(
            'sender'    => $sender_id, 
            'recipient' => $recipient,
            'content'   => $content,
            'type'      => $type,
            'tag'       => $tag,
        );
        $json_request_data=json_encode($request_data);

        $curl_params = array(
            CURLOPT_URL            => "https://api.sendinblue.com/v3/transactionalSMS/sms",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_POSTFIELDS     => $json_request_data,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER     => $request_header,
        );

        curl_setopt_array($curl, $curl_params);

        $response = curl_exec($curl);
        $err      = curl_error($curl);

        curl_close($curl);

        if ($err) {
            log_message('Debug', "cURL Error #:" . $err);
            return "cURL Error #:" . $err;
        } else {
            log_message('Debug', "SMS Send Successful");
            return $response;
        }
    }
}

?>