<?php
error_reporting(0);
/********************************
Developer: Adeleye Samuel Adedeji
*********************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Model extends CI_Model {

    public function __construct(){
    	$this->load->library('Cencryption');
		$this->user_table            = "pty_users";
		$this->user_activation_table = "os_user_verification";
		$this->access_code_table     = "pty_activationcodes";
        parent::__construct();
    }

    public function get_instance() {
		return new self;
	}

    public function register_user($user_info){
        if($this->db->insert($this->user_table, $user_info)) {
        	return $this->db->insert_id();
        }else{
        	return FALSE;
        }
    }

    public function generateactivationcode($user_id) {
        try {
			$code               = $this->random_string();
			$this->db->db_debug = false;
			$code_info          = array('access_code' =>$code, 'user_id' =>$user_id);
			if($this->db->insert($this->access_code_table, $code_info)) {
				$id   = $this->db->insert_id();
				$data = $this->db->select("access_code")->from($this->access_code_table)->where("id=$id")->get()->row_array();
	    		return $data['access_code'];
	        }else{
	        	return $this->generateactivationcode($user_id);
	        }
        } catch (Exception $e) {
            return $this->generateactivationcode($user_id);
        }
    }

    public function generateactivationcode_unit_test($user_id,$code='') {
        try {
        	if ($code==''){
				$code      = $this->random_string();
        	}
        	$this->db->db_debug = false;
			$code_info = array('access_code' =>$code, 'user_id' =>$user_id);
			if($this->db->insert($this->access_code_table, $code_info)) {
				$id   = $this->db->insert_id();
				$data = $this->db->select("access_code")->from($this->access_code_table)->where("id=$id")->get()->row_array();
	    		return $data['access_code'];
	        }else{
	        	return $this->generateactivationcode($user_id,'');
	        }
        } catch (Exception $e) {
            return $this->generateactivationcode($user_id,'');
        }
    }

    function random_string($length=6){
        return substr(str_repeat(md5(rand()), ceil($length/32)), 0, $length);
    }

    public function set_activation_period($user_info){
        return $this->db->replace($this->user_activation_table, $user_info);
    }

    public function update_activation_period($user_id){
    	#return $this->db->where("user_id = $user_id")->where("string = '$string'")->update($this->user_activation_table, array("string" => NULL, "validity" => 0));
    	return $this->db->where("user_id = $user_id")->update($this->user_activation_table, array("string" => NULL, "validity" => 0));
    }

	public function activate($hashid = "", $string = ""){
		$user_table  = $this->user_table;
        if(empty($hashid) || empty($string)) {
            #redirect(VPATH);
            $response = array('status' => false,'message' => 'Something went wrong! Please copy the activation link and paste in the browser and try again');
            return  $this->response_status($response);
        }else{
            $user_id = $this->cencryption->cdecrypt($hashid);
        }

        $data = $this->db->select("string, validity")->from($this->user_activation_table)->where("user_id = $user_id")->where("string = '$string'")->get()->row_array();

        if(empty($data)) {
            #redirect(VPATH);
            $response = array('status' => false,'message' => 'Something went wrong! Invalid Activation parameters! Please copy the activation link and paste in the browser and try again');
            return $this->response_status($response);
        }else {

            if(time() < intval($data['validity'])) {
                $this->db->where("id = $user_id")->update("os_user", array("activation" => 1));
                $user= $this->db->select("mobile")->from($user_table)->where("id = $user_id")->get()->row_array();
                $mobile=$user['mobile'];
                #$this->session->set_flashdata('msg_login_success', 'Your account is successfully activated. You may login now!');

                $message_view = 'Your account is successfully activated. You may login now!';
                if (!empty($mobile)){
					//$this->sendinblue->sendinblue_sms($mobile,$message_view);
                }

                $response = array('status' => true,'message' => $message_view);
                return $this->response_status($response);
            }else {
				$message = "Your token has expired.! Please click on Resend Activation link to re-send your activation link.";
                $response = array('status' => false,'message' => $message);
				return $this->response_status($response);
            }
            #redirect(VPATH . 'login#focus');
        }
    }	

    public function activate_via_smscode($hashid = "", $string = ""){
		$user_table  = $this->user_table;
        if(empty($hashid) || empty($string)) {
            #redirect(VPATH);
            $response = array('status' => false,'message' => '!Oops! Something went wrong! Please try again later');
            return  $this->response_status($response);
        }else{
        	$hashid = urldecode($hashid);
            $user_id = $this->cencryption->cdecrypt($hashid);
        }

        $data = $this->db->select("string, validity")->from($this->user_activation_table)->where("user_id = $user_id")->where("string = '$string'")->get()->row_array();

        if(empty($data)) {
            $response = array('status' => false,'message' => 'Something went wrong! Invalid Activation parameters! Please check your entry and try again Or Click on Resend Activation Code to resend activation code to your mobile');
            return $this->response_status($response);
        }else {
            if(time() < intval($data['validity'])) {
                if ($this->db->where("id = $user_id")->update($user_table, array("activation" => 1))){
                	$this->update_activation_period($user_id);
	                $message  = 'Your account is successfully activated. You may login now!';
	                $response = array('status' => true,'message' => $message);
	                return $this->response_status($response);
                }else{
	                $message  = 'Oops! Something went wrong! Please try again later.';
	                $response = array('status' => false,'message' => $message);
	                return $this->response_status($response);
                }
            }else {
				$message        = "Your token has expired.! Please click on Resend Activation Code to re-send your activation code. ";
                $response = array('status' => false,'message' => $message);
				return $this->response_status($response);
            }
        }
    }

    public function get_verification($user_id,$string){
    	return $this->db->select("string,validity")->from($this->user_activation_table)->where("user_id = $user_id")->where("string = '$string'")->get()->row_array();
    }

    public function is_activated($user_id){
    	return $this->db->from($this->user_table)->where("id = $user_id")->where("activation > 0")->get()->num_rows();
    }

    public function resend_activation_code($hashid='') {
    	if (!empty($hashid)){
		    //---- preparing activation hash code again-----
		    $user_table		  = $this->user_table;
			$user_id          = $this->cencryption->cdecrypt($hashid);
			$user             = $this->db->select("email,name")->from($user_table)->where("id = $user_id")->get()->row_array();
			
			$secret           = uniqid();
			$hashid           = $this->cencryption->cencrypt($user_id);
			$dispatch_time    = time();
			
			$activation             = array();
			$activation['user_id']  = $user_id;
			$activation['string']   = $secret;
			$activation['validity'] = $dispatch_time + (24 * 60 * 60);
			#$veri['validity'] = $dispatch_time + (60*60);
			$this->db->replace($this->user_activation_table, $activation);
			#$activation_link = VPATH . "activate/$hashid/$secret/";
			$activation_link  = CPATH . "Account/activate/$hashid/$secret";


		    #Send mail # This should be replaced as soon as views are sorted out
	        #But temporarily it should be used for testing purposes
			$from_email     = "admin@giftrete.com";
			$to_email       = $user['email'];
			$subject        = "Welcome To Giftere";
			$site_title     = "Giftere";
			$site_link      = '<a href="'.CPATH.'" target="_blank">giftrete.com</a>';
			$activation_url = '<a href="'.$activation_link.'" target="_blank">Activate Account</a>';
			
			$message        = "Hi, ".$user['name']."\r\n";
			$message        .= "Thank you for signing up! Please click ".$activation_url." to activate your account OR Click copy the link below and paste in the browser ".$activation_link;

	        //$this->send_mail($site_title,$from_email,$to_email,$subject,$message);


	        #$this->session->set_flashdata('msg_reg_success', 'Thank you for signing up! Please check your mail to activate your account.');

	        $response = array('status' => true,'message' => 'Thank you for signing up! Activation code has been sent to your mail. Please check your mail to activate your account OR Click on this link to activate your account. '.$activation_url);
			return $this->response_status($response);
    	}else{
    		$response = array('status' => false,'message' => 'Something went wrong! Please copy the activation link and paste in the browser and try again');
            return $this->response_status($response);
    	}
    }

	public function login_check($data, $social = FALSE) {
		$user       = array();
		$user_table = $this->user_table;
		$email      = $data['email'];
		$user       = $this->db->select("id, email, name, password")->from($user_table)->where("status = 'Y'")->where("email = '$email'")->get()->row_array();

		$status = FALSE; 
		if(empty($data) OR empty($user)){
			return $status;
		}

		$handle = $data['email'];
		#return  print_r($user);
		if($social) {
			$status = $this->db->from($user_table)->where("status = 'Y'")->where("email = '$handle'")->where("online_user = 'Y'")->get()->num_rows();
		}else {
	        if (phash($data['password'])==$user['password']){
	            $status=TRUE;
	        }else{
	            $status=FALSE;
	        }
		}
		return $status;
	}
	
	public function activation_check($data) {
		$user_table = $this->user_table;
		$status     = FALSE; 
		if(empty($data)) return $status;
		$handle = $data['email'];
		$status = $this->db->from($user_table)->where("status = 'Y'")->where("email = '$handle'")->where("activation > 0")->get()->num_rows();
		if($status==TRUE){
			$this->db->where("email = '$handle'")->update($user_table, array("login" => time()));
		}
		return $status;
	}

	public function get_user_info_by_email($email=''){
		$user_table = $this->user_table;
		if (!empty($email)){
			$user = $this->db->select("*")->from($user_table)->where("email = '$email'")->get()->row_array();
			if (isset($user) AND is_array($user)){
				return $user;
			}else{
				return '';
			}
		}
	}

	public function get_user_info_by_mobile($mobile=''){
		$user_table = $this->user_table;
		if (!empty($mobile)){
			$user = $this->db->select("*")->from($user_table)->where("mobile = '$mobile'")->get()->row_array();
			if (isset($user) AND is_array($user)){
				return $user;
			}else{
				return '';
			}
		}
	}

	public function get_user_info_by_activation_code($code){
		if (!empty($code)){
			$code_info = $this->db->select("*")->from($this->user_activation_table)->where("string = '$code'")->get()->row_array();
			if (isset($code_info) AND is_array($code_info)){
				$user_id = $code_info['user_id'];
				$user=$this->db->select("*")->from($this->user_table)->where("id=$user_id")->get()->row_array();
				return $user;
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function get_user_info_by_mobile_special($mobile,$full_mobile){
		$user_table = $this->user_table;
		if (!empty($mobile) && !empty($full_mobile) ){
			$user = $this->db->select("*")->from($user_table)->where("mobile = '$mobile'")->get()->row_array();
			if (isset($user) AND is_array($user)){
				return $user;
			}else{
				#check if full_mobile number exist 
				$user = $this->db->select("*")->from($user_table)->where("mobile = '$full_mobile'")->get()->row_array();
				if (isset($user) AND is_array($user)){
					return $user;
				}else{
					#Now perform special search
					$sqlstm ="SELECT * FROM $user_table WHERE mobile LIKE '%$mobile%' LIMIT 1";
					$q = $this->db->query($sqlstm);
					if ($q->num_rows()==1){
						return $q->row_array();	
					}else{
						return '';
					}
				}
			}
		}
	}

	public function update_user_record_by_user_id($user_id,$post){
		return $this->db->where("id = $user_id")->update($this->user_table, $post);
	}

	public function update_user_record_by_email($email,$post){
		return $this->db->where("email = '$email'")->update($this->user_table, $post);
	}

	public function delete_address($post){
		return $this->db->delete('os_user_address', $post);
	}


	public function make_address_default($user_id,$id){
		#make default the id
		$sql="UPDATE os_user_address SET `default`='Y' WHERE id=$id AND user_id=$user_id";
		$q=$this->db->query($sql);

		#make not default others other than the id
		$sql="UPDATE os_user_address SET `default`='N' WHERE id!=$id AND user_id=$user_id";
		$this->db->query($sql);

		return $q;
	}

	public function make_address_default_with_add($user_id){
		#1stly check if there is any default address
		#$sqlstm="SELECT * FROM os_user_address WHERE user_id=$user_id AND `default`='Y' ORDER BY id Desc;";
		$sqlstm  ="SELECT id FROM os_user_address WHERE user_id=$user_id AND `default`='Y' ORDER BY id Asc;";
		$q       = $this->db->query($sqlstm);
		$mycount =$q->num_rows();
        if($mycount > 0) {
        	return 0;
        }else{
        	$sqlstm="SELECT id FROM os_user_address WHERE user_id=$user_id ORDER BY id Asc LIMIT 1;";
	        $q = $this->db->query($sqlstm);
	        if($q->num_rows() > 0) {
	        	foreach($q->result() as $tr) {
	            	$id=$tr->id;
	            	break;
	            }
        		return $this->make_address_default($user_id,$id);
	        }else{
	        	return 0;
	        }
        }
	}

	public function getaddresses_new($user_id){
		$default =$this->make_address_default_with_add($user_id);		
		$sn      =0;
		$content ="";
		$sqlstm  ="SELECT * FROM os_user_address WHERE user_id=$user_id ORDER BY `default` Asc;";
		$q       = $this->db->query($sqlstm);
		$mdata   =array();
        if($q->num_rows() > 0) {
        	return $q->result_array();
            /*foreach($q->result() as $tr) {
            	if ($tr->default=='Y'){
            		$icon='<input type="button" value="Active" title="This is your default or active address " class="add-p pl--20" style="text-align: center; width: 100%" >';
            		$bg='style="background-color: #C8EEE0"';
            		$del="";
            	}else{
            		$icon='<input type="button" tag='.$tr->id.' value="Make Active"  title="Make this address your default or active address"  class="add-p pl--20 but_default" style="background-color:#40AAD2; cursor: pointer;" style="text-align: center; width: 100%" >';
            		$bg='';
            		$del="<a tag='$tr->id' class='but_remove' title='Remove Address' href='#'><i class='fa fa-trash pt--0'></i></a>";
            	}
            	++$sn;
            	$content.="<tr $bg>
                          <td><center> ".urldecode($tr->address)."</center></td>
                          <td><center>$icon</center></td>
                          <td><center>$del</center></td>
						  </tr>";
            }
            return $content;*/
        }else{
        	return 0 ;
        }
    }	

	public function getaddresses($user_id){
		$default =$this->make_address_default_with_add($user_id);		
		$sn      =0;
		$content ="";
		$sqlstm  ="SELECT * FROM os_user_address WHERE user_id=$user_id ORDER BY id Desc;";
		$q       = $this->db->query($sqlstm);
		$mdata   =array();
        if($q->num_rows() > 0) {
            foreach($q->result() as $tr) {
            	if ($tr->default=='Y'){
            		$icon='<input type="button" value="Active" title="This is your default or active address " class="add-p pl--20" style="text-align: center; width: 100%" >';
            		//$icon='<div class="switchToggleaddy"><input type="checkbox" id="switch" name="public_status"><label for="switch">Toggle</label></div>';
            		//$icon='fa fa-address-card-o pt--0';
            		$bg='style="background-color: #C8EEE0"';
            		$del="";
            		#$icon='fa fa-address-card-o pt--0';
            	}else{
            		$icon='<input type="button" tag='.$tr->id.' value="Make Active"  title="Make this address your default or active address"  class="add-p pl--20 but_default" style="background-color:#40AAD2; cursor: pointer;" style="text-align: center; width: 100%" >';
            		//$icon='fa fa-address-card pt--0';
            		$bg='';
            		$del="<a tag='$tr->id' class='but_remove' title='Remove Address' href='#'><i class='fa fa-trash pt--0'></i></a>";
            	}
            	++$sn;
            	$content.="<tr $bg>
                          <td><center> ".urldecode($tr->address)."</center></td>
                          <td><center>$icon</center></td>
                          <td><center>$del</center></td>
						  </tr>";
            }
            return $content;
        }
        return '';
    }	

    public function getaddresses2($user_id){
		$default =$this->make_address_default_with_add($user_id);		
		$sn      =0;
		$content =array();
		$sqlstm  ="SELECT * FROM os_user_address WHERE user_id=$user_id ORDER BY id Desc;";
		$q       = $this->db->query($sqlstm);
		$mdata   =array();
        if($q->num_rows() > 0) {
            foreach($q->result() as $tr) {
            	if ($tr->default=='Y'){
            		$icon='<input type="button" value="Active" title="This is your default or active address " class="add-p pl--20" style="text-align: center; width: 100%" >';
            		//$icon='<div class="switchToggleaddy"><input type="checkbox" id="switch" name="public_status"><label for="switch">Toggle</label></div>';
            		//$icon='fa fa-address-card-o pt--0';
            		$bg='style="background-color: #C8EEE0"';
            		$del="";
            		#$icon='fa fa-address-card-o pt--0';
            	}else{
            		$icon='<input type="button" tag='.$tr->id.' value="Make Active"  title="Make this address your default or active address"  class="add-p pl--20 but_default" style="background-color:#40AAD2; cursor: pointer;" style="text-align: center; width: 100%" >';
            		//$icon='fa fa-address-card pt--0';
            		$bg='';
            		$del="<a tag='$tr->id' class='but_remove' title='Remove Address' href='#'><i class='fa fa-trash pt--0'></i></a>";
            	}
            	++$sn;
            	$content[]="<tr $bg>
            			  <td><center>$sn</center></td>
                          <td>".urldecode($tr->address)."</td>
                          <td><center>$icon</center></td>
                          <td><center>$del</center></td>
						  </tr>";
            }
            return json_encode($content) ;
        }
        return '';
    }

	public function get_user_info_by_user_id($user_id=''){
		$user_table = $this->user_table;
		if (!empty($user_id)){
			$user = $this->db->select("*")->from($user_table)->where("id = $user_id")->get()->row_array();
			if (isset($user) && is_array($user)){
				return $user;
			}else{
				return '';
			}
		}
	}

	/* Clean passed parameter*/
    public function clean($string){
        # code...
        return preg_replace('/[^A-Za-z0-9]/', '', $string);
    }

	public function clean_num($string){
		# code...
		return preg_replace('/[^0-9]/', '', $string);
	}

	public function runquery($sqlstm){
		if (!empty($sqlstm)){
			return  $this->db->query($sqlstm);
		}else{
			return '';
		}
	}

	public function insert_record($table,$user_info){
        if($this->db->insert($table, $user_info)) {
        	return $this->db->insert_id();
        }else{
        	return FALSE;
        }
    }

	public function delete_record($table,$user_info){
        if($this->db->delete($table, $user_info)) {
        	return 1;
        }else{
        	return 0;
        }
    }

	public function get_reward_by_rule($rule){ #"RP_SIGNUP_INVITEE"
		return $this->db->select("*")->from("os_reward_point_rules")->where("rule", $rule )->where("status", "Y")->get()->row_array();
	}

	public function add_to_invite ($user_invites) {
		return $this->db->insert("os_user_invites", $user_invites);
	}
	
	public function send_mail($site_title,$from_email,$to_email,$subject,$message){
        $headers = "From:" . $from_email;
        $result=mail($to_email,$subject,$message, $headers);
    	return 1;
	}

	public function check_duplicates($field,$value,$table){
		# code...
		$this->db->where($field, $value);
		$query = $this->db->get_where($table);
		//return $field.'='.$value.'='.$table.'='.$query->num_rows();
		if ($query->num_rows()==1){
			return 1;
		}else{
			return 0;
		}
	}

	public function getGeocodeData_save($user_id, $params = array(), $cache = TRUE) {
		
		// prepare default response
		$response = array("cached" => FALSE, "status" => NULL);
		
		// reutrn if nothing to lookup
		if(empty($params["lookup"])) return $response;
		
		// if cache is TRUE, try to fetch data from cache
		if($cache) {
			$data = $this->db->select("dump")->from("os_geocode_cache")->where("LOWER(lookup)", strtolower($params["lookup"]))->get()->result_array();
			if(!empty($data)) {
				foreach($data as $result) {
					
					// Previous Originial Code::
					// $response["results"][] = json_decode($result["dump"], 1);

					//Extra Data Add user's Actual Address : Chandan :2018-01-02 : Starts 
					$result_dump_decoded = json_decode($result["dump"], 1);

					//Extra Data Add user's Actual Address
					$result_dump_decoded['actual_address'] = $params["lookup"];
					$response["results"][] = $result_dump_decoded;
					//Extra Data Add user's Actual Address : Chandan :2018-01-02 : Endss 
					
				}

				$response["cached"] = TRUE;
				$response["status"] = "OK";
			} 
		}
		
		// if no data was fetched from cache...
		if(!$response["cached"] || $response["cached"]) {
			
			$geocodeAPI = "https://maps.googleapis.com/maps/api/geocode/json";
			$geocodeKey = "AIzaSyC8oLT4NLof9bzZfkfyzASE9LgWIt0d_5E";
			
			$address = str_replace(" ", "+", urlencode($params["lookup"]));
			$url = "{$geocodeAPI}?address={$address}&key={$geocodeKey}";
			
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$response_a = curl_exec($ch);
			curl_close($ch);
			$response = array_merge($response, json_decode($response_a,1));

			//return print_r($response);
			
			//p($response,1);
			// if cache is TRUE and download was successful, store data in cache
			if($cache && $response["status"] === "OK") {
				foreach($response["results"] as $result) {
					
					#return  $result["address_components"][3]["long_name"]."=".$result["address_components"][2]["long_name"]."=".$result["geometry"]["location"]["lat"]."=".$result["geometry"]["location"]["lng"]."=".$result["formatted_address"];

					$data = array(
						"place_id" => $result["place_id"],
						"lookup"   => $params["lookup"],
						"dump"     => json_encode($result),
						"updated"  => time(),
					);
					$this->db->replace("os_geocode_cache", $data);


					$street  =$result["address_components"][0]["long_name"];
					$city    =$result["address_components"][1]["long_name"];
					$state   =$result["address_components"][2]["long_name"];
					$country =$result["address_components"][3]["long_name"];
					$lat     =$result["geometry"]["location"]["lat"];
					$lng     =$result["geometry"]["location"]["lng"];
					$addres  =urldecode($result["formatted_address"]);
					

					$user_id=$params["user_id"];
					$addydata = array(
						"user_id" => $user_id,
						"default" => 'N',
						"country" => $country,
						"state"   => $state,
						"city"    => $city,
						"street"  => $street,
						"address" => $address,
						"lat"     => $lat,
						"lng"     => $lng
					);

					$this->db->insert("os_user_address", $addydata);
					$this->make_address_default_with_add($user_id);
					$addressdata=$this->getaddresses_new($user_id);
					$response = array('status' => true,'message' => "Address Saved Successfully",'data'=>$addressdata);
					return $this->response_status($response);
				}
			}

			//Extra Data Add user's Actual Address : Chandan :2018-01-02 : Starts 
			/*if($response["status"] === "OK") {
				foreach($response["results"] as $rkey => $result) {
					$response["results"][$rkey]['actual_address'] = $params["lookup"];
				}
			}*/
			//Extra Data Add user's Actual Address : Chandan :2018-01-02 : Endss 
		}
		
		// return
		$addressdata=$this->getaddresses($user_id);
		$response = array('status' => false,'message' => "Oops ! Something wen wrong!.",'data'=>$addressdata);
		return $this->response_status($response);
	}
	
	public function getinvitees($user_id){
        $sqlstm="SELECT * FROM os_user_invites WHERE user_id=$user_id ORDER BY invited Desc;";
        $q = $this->db->query($sqlstm);
        $mdata=array();
        if($q->num_rows() > 0) {
            foreach($q->result() as $tr) {
            	$mdata[]=$tr;
            }
        }
        return $mdata;
    }

	public function getrewarddetails($user_id){
        $sqlstm="SELECT rp.rule_invoked, rr.description,rp.rp_awarded FROM os_user_rp_log rp INNER JOIN os_reward_point_rules rr ON rp.rule=rr.rule WHERE rp.user_id=$user_id ORDER BY rp.rule_invoked Desc;";
        $q = $this->db->query($sqlstm);
        $mdata=array();
        if($q->num_rows() > 0) {
            foreach($q->result() as $tr) {
            	$mdata[]=$tr;
            }
        }
        return $mdata;
    }

    public function getrewardhowitworks(){
    	$mdata=array();
        $sqlstm="SELECT * FROM os_reward_point_rules WHERE status='Y'";
        $q = $this->db->query($sqlstm);
        $mdata=array();
        if($q->num_rows() > 0) {
            foreach($q->result() as $tr) {
            	$mdata[]=$tr;
            }
        }
        return $mdata;
    }

    public function getbadgedetails($user_id){
    	$this->db->select("ob.file_image,ob.description,ob.name");
		$this->db->from("os_user_badges oub");
		$this->db->join("os_badge ob", "ob.id = oub.badge_id");
		$this->db->join("os_badge_rules obr", "obr.badge_id = ob.id");
		$this->db->where("oub.user_id", $user_id);
		$this->db->where("oub.accepted >", '0');
		// $this->db->order_by("CAST(`obr`.`value` AS INTEGER)", 'DESC');
		$this->db->group_by("oub.id");
		return $this->db->get()->result_array();
    }

	public function response_status($content=array()){
		return json_encode($content);
	}

	public function custom_send_mail($from_email,$to_email,$subject,$mailbody){
		$this->load->library('email');
		$this->email->from($from_email, SITE_TITLE);
		$this->email->to($to_email);
		$this->email->subject($subject);
		$this->email->message($mailbody);
		$this->email->send();
	}

	public function get_product_count($user_id){
        $query = null;
        $query = $this->db->get_where('product', array('userid' => $user_id));
        return $query->num_rows();
	}

	public function get_usergroup_member_count($user_id){
        $query = $this->db->get_where('os_group_member', array('member_id' => $user_id));
        return $query->num_rows();
	}

	public function get_userwishlist_member_count($user_id){
        $query = $this->db->get_where('os_user_wishlist', array('user_id' => $user_id));
        return $query->num_rows();
	}

	public function update_user_image($user_id,$image_name){
		$sql="UPDATE pty_users SET image_name='$image_name' WHERE id=$user_id";
		$q=$this->db->query($sql);
		return $q;
	}

	public function update_user_banner($user_id,$image_name){
		$sql="UPDATE pty_users SET banner_name='$image_name' WHERE id=$user_id";
		$q=$this->db->query($sql);
		return $q;
	}
	

	public function get_user_reward_points($user_id){
		if (!empty($user_id)){
			$user_info = $this->db->select("reward_points")->from($this->user_table)->where("id = $user_id")->get()->row_array();
			if (isset($user_info) && is_array($user_info)){
				return $user_info['reward_points'];
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}

	public function add_santa_wishlist($post){
		return $this->db->insert("os_secret_santa_wishlist", $post);
	}

	public function get_secret_santa_wishlist_info_by_user_id($user_id,$santa_id){
		if (!empty($user_id) && !empty($santa_id)){
			$details = $this->db->select("*")->from('os_secret_santa_wishlist')->where("user_id = $user_id AND santa_id = $santa_id")->get()->result_array();
			if (isset($details) && is_array($details)){
				return $details;
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function delete_secret_santa_member($post){
		return $this->db->delete('os_secret_santa_details', $post);
	}

	public function delete_secret_santa_matched_result($post){
		return $this->db->delete('os_secret_santa_matched_result', $post);
	}

	public function add_to_secret_santa_member($members) {
		return $this->db->insert_batch("os_secret_santa_details", $members);
	}

	public function add_to_secret_santa_matched_result($mi,$matched_result,$santa_id){

		if (is_array($mi) && count($mi)>0 && is_array($matched_result) && count($matched_result)>0){
            # Giver 1 and Taker 1
            $giver_value=0;
            $taker_value=0;

			for ($i=0; $i < count($matched_result['givers1']) ; $i++) { 
				$giver_value= $matched_result['givers1'][$i];
				$taker_value= $matched_result['takers1'][$i];
				# code...
			 	
				$insert_record[] = array(
					'santa_id'           => $santa_id, 
					'user_id'            => $mi[$giver_value]['user_id'], 
					'member_name_giver'  => $mi[$giver_value]['member_name'], 
					'member_email_giver' => $mi[$giver_value]['member_email'], 
					'member_name_taker'  => $mi[$taker_value]['member_name'], 
					'member_email_taker' => $mi[$taker_value]['member_email'],  
					'matched_date'       => time(), 
				);

			}
		}
		return $this->db->insert_batch("os_secret_santa_matched_result", $insert_record);
	}	

	public function add_to_secret_santa_matched_result_old($mi,$matched_result,$santa_id){

		if (is_array($mi) && count($mi)>0 && is_array($matched_result) && count($matched_result)>0){

			/*$result=array(
                'givers1' => $givers_1,
                'takers1' => $takers_1,
                'givers2' => $givers_2,
                'takers2' => $takers_2,
                'all'    => $all_members
            );*/

            # Giver 1 and Taker 1
            $giver_value=0;
            $taker_value=0;

			for ($i=0; $i < count($matched_result['givers1']) ; $i++) { 
				$giver_value= $matched_result['givers1'][$i];
				$taker_value= $matched_result['takers1'][$i];
				# code...
			 	
				$insert_record[] = array(
					'santa_id'           => $santa_id, 
					'user_id'            => $mi[$giver_value]['user_id'], 
					'member_name_giver'  => $mi[$giver_value]['member_name'], 
					'member_email_giver' => $mi[$giver_value]['member_email'], 
					'member_name_taker'  => $mi[$taker_value]['member_name'], 
					'member_email_taker' => $mi[$taker_value]['member_email'],  
					'matched_date'       => time(), 
				);

			}

            # Giver 2 and Taker 2
			$giver_value =0;
			$taker_value =0;
			$i           =0;
			for ($i=0; $i < count($matched_result['givers2']) ; $i++) { 
				$giver_value= $matched_result['givers2'][$i];
				$taker_value= $matched_result['takers2'][$i];
				# code...
			 	
				$insert_record[] = array(
					'santa_id'           => $santa_id, 
					'user_id'            => $mi[$giver_value]['user_id'], 
					'member_name_giver'  => $mi[$giver_value]['member_name'], 
					'member_email_giver' => $mi[$giver_value]['member_email'], 
					'member_name_taker'  => $mi[$taker_value]['member_name'], 
					'member_email_taker' => $mi[$taker_value]['member_email'],  
					'matched_date'       => time(), 
				);

			}
		}
		return $this->db->insert_batch("os_secret_santa_matched_result", $insert_record);
	}

	public function get_secret_santa_info_by_santa_id($santa_id){
		if (!empty($santa_id)){
			$details = $this->db->select("*")->from('os_secret_santa')->where("santa_id = $santa_id")->get()->result_array();
			if (isset($details) && is_array($details)){
				return $details;
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function get_secret_santa_info_by_user_id($user_id=''){
		if (!empty($user_id)){
			$details = $this->db->select("*")->from('os_secret_santa')->where("user_id = $user_id")->order_by("santa_id DESC")->get()->result_array();
			if (isset($details) && is_array($details)){
				return $details;
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function get_first_secret_santa_id_user_id($user_id,$v_santa_id){
		if (!empty($user_id)){
			if (!empty($v_santa_id)){
				$details = $this->db->select("*")->from('os_secret_santa')->where("user_id = $user_id AND santa_id=$v_santa_id")->order_by("santa_id DESC")->limit(1)->get()->result_array();
			}else{
				$details = $this->db->select("*")->from('os_secret_santa')->where("user_id = $user_id")->order_by("santa_id DESC")->limit(1)->get()->result_array();
			}

			if (isset($details) && is_array($details)){
				return $details;
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function get_secret_santa_detail_info_by_user_id($user_id,$santa_id){
		if (!empty($user_id) && !empty($santa_id)){
			$details = $this->db->select("*")->from('os_secret_santa_details')->where("user_id = $user_id AND santa_id = $santa_id")->get()->result_array();
			if (isset($details) && is_array($details)){
				return $details;
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function get_secret_santa_result_info_by_user_id($user_id,$santa_id){
		if (!empty($user_id) && !empty($santa_id)){
			$details = $this->db->select("*")->from('os_secret_santa_matched_result')->where("user_id = $user_id AND santa_id = $santa_id")->get()->result_array();
			if (isset($details) && is_array($details)){
				return $details;
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function get_secret_santa_invited_info_by_user_id($email){
		if (!empty($email)){
			$details = $this->db->select("*")->from('os_secret_santa_matched_result')->where("member_email_giver = '$email'")->get()->result_array();
			if (isset($details) && is_array($details)){
				return $details;
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	#invited santas
	public function get_secret_santa_invited_info_by_email($email){
		if (!empty($email)){
			$sqlstm="SELECT DISTINCT m.santa_id, s.*  FROM os_secret_santa_matched_result m 
				     INNER JOIN  os_secret_santa s ON m.santa_id=s.santa_id
				     WHERE m.member_email_giver = '$email' ORDER BY s.santa_id DESC;";
			$q = $this->db->query($sqlstm);
			$mycount =$q->num_rows();
			if($mycount>0){
				return $q->result_array();
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function get_first_secret_santa_id_email($email,$v_santa_id){
		if (!empty($email)){
			if (!empty($v_santa_id)){
				$sqlstm="SELECT DISTINCT m.santa_id, s.*  FROM os_secret_santa_matched_result m 
				     INNER JOIN  os_secret_santa s ON m.santa_id=s.santa_id
				     WHERE m.member_email_giver = '$email' AND m.santa_id=$v_santa_id 
				     ORDER BY s.santa_id DESC
				     LIMIT 1 ";
			}else{
				$sqlstm="SELECT DISTINCT m.santa_id, s.*  FROM os_secret_santa_matched_result m 
				     INNER JOIN  os_secret_santa s ON m.santa_id=s.santa_id
				     WHERE m.member_email_giver = '$email'
				     ORDER BY s.santa_id DESC
				     LIMIT 1 ";
			}
			$q = $this->db->query($sqlstm);
			$mycount =$q->num_rows();
			if($mycount>0){
				return $q->result_array();
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function get_secret_santa_detail_info_by_santa_id($santa_id){
		if (!empty($santa_id)){
			$details = $this->db->select("*")->from('os_secret_santa_details')->where("santa_id=$santa_id")->get()->result_array();
			if (isset($details) && is_array($details)){
				return $details;
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function get_secret_santa_result_info_by_santa_id($email, $santa_id){
		if (!empty($email) && !empty($santa_id)){
			$details = $this->db->select("*")->from('os_secret_santa_matched_result')->where("member_email_giver = '$email' AND santa_id = $santa_id")->get()->result_array();
			if (isset($details) && is_array($details)){
				return $details;
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function get_secret_santa_result_info_by_santa_id_giver($email, $santa_id){
		if (!empty($email) && !empty($santa_id)){
			$details = $this->db->select("*")->from('os_secret_santa_matched_result')->where("member_email_taker = '$email' AND santa_id = $santa_id")->get()->result_array();
			if (isset($details) && is_array($details)){
				return $details;
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function get_secret_santa_oranizer_info_by_santa_id($santa_id){
		if (!empty($santa_id)){
			$details = $this->db->select("os_user.*")->from('os_secret_santa')->join('os_user','os_secret_santa.user_id=os_user.id')->where("santa_id=$santa_id")->get()->result_array();
			if (isset($details) && is_array($details)){
				return $details;
			}else{
				return '';
			}
		}else{
			return '';
		}
	}

	public function update_secret_santa($user_id,$santa_id,$post){
		return $this->db->where("user_id = $user_id")->where("santa_id = $santa_id")->update('os_secret_santa', $post);
	}

	public function update_notification_sent($member_email_taker,$santa_id){
		$post=array('wishlist_notification_sent' => 1);
		return $this->db->where("member_email_taker = '$member_email_taker'")->where("santa_id = $santa_id")->update('os_secret_santa_matched_result', $post);
	}

	public function is_notification_sent($member_email_taker,$santa_id){
		if (!empty($santa_id) && !empty($member_email_taker)){
			$details = $this->db->select("*")->from('os_secret_santa_matched_result')->where("member_email_taker = '$member_email_taker' AND santa_id = $santa_id AND wishlist_notification_sent=1")->get()->result_array();
			if (isset($details) && is_array($details) && count($details)>0){
				return 1;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}

	public function delete_secret_santa($post){
		return $this->db->delete('os_secret_santa', $post);
	}	


	public function delete_secret_santa_wishlist($post){
		return $this->db->delete('os_secret_santa_wishlist', $post);
	}


    function get_time_ago( $time ){
	    $time_difference = time() - $time;

	    if( $time_difference < 1 ) { return 'less than 1 second ago'; }
	    $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
	                30 * 24 * 60 * 60       =>  'month',
	                24 * 60 * 60            =>  'day',
	                60 * 60                 =>  'hour',
	                60                      =>  'minute',
	                1                       =>  'second'
	    );

	    foreach( $condition as $secs => $str )
	    {
	        $d = $time_difference / $secs;

	        if( $d >= 1 )
	        {
	            $t = round( $d );
	            return 'about ' . $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
	        }
	    }
	}

	public function log_operation($userid,$operation,$systemIP,$BrowserOS,$devicetype){
        $logdata = array(
            'log_user_id'       => $userid,
            'log_operation'     => $operation,
            'log_ip'            => $systemIP,
            'log_browser_data'  => $BrowserOS,
            'log_from'        => $devicetype
        ); 

        $this->db->set($logdata); 
        if ($this->db->insert('os_sitelog'))
        {
            return  1;
        }   
        else
            return 0;   
    }

    public function fetchtrendingitems(){ 
      $query =  $this->db->query("SELECT * FROM os_sales_items WHERE categoryid=1 LIMIT 5");
        return $query->result();
    }
    
    public function fetchwomenitems(){ 
      $query =  $this->db->query("SELECT * FROM os_sales_items WHERE categoryid=2 LIMIT 5");
        return $query->result();
    }
    
    public function fetchmenitems(){ 
      $query =  $this->db->query("SELECT * FROM os_sales_items WHERE categoryid=3 LIMIT 5");
        return $query->result();
    }

    public function get_amazon_item_categories(){
		$details = $this->db->select("*")->from('os_sales_category')->get()->result_array();
		if (isset($details) && is_array($details)){
			return $details;
		}else{
			return '';
		}
	}

    public function get_amazon_items(){
		$details = $this->db->select("*")->from('os_sales_items')->get()->result_array();
		if (isset($details) && is_array($details)){
			return $details;
		}else{
			return '';
		}
	}

    public function get_amazon_items_with_category($category_id){
		$details = $this->db->select("*")->from('os_sales_items')->where("categoryid IN ($category_id)")->get()->result_array();
		if (isset($details) && is_array($details)){
			return $details;
		}else{
			return '';
		}
	}
}
