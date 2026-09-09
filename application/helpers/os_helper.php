<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function getField($select, $table, $feild = "", $value = "", $where = null, $limit_from = 0, $limit_to = 0) {
    $ci = &get_instance();
    $ci->db->select($select);
    if ($value != '' AND $feild != '') {
        if ($limit_from > 0) {
            $rs = $ci->db->get_where($table, array($feild => $value), $limit_to, $limit_from);
        } else {
            $rs = $ci->db->get_where($table, array($feild => $value));
        }
    } else {
        if ($limit_from > 0) {
            $rs = $ci->db->get_where($table, $where, $limit_to, $limit_from);
        } else {
            $rs = $ci->db->get_where($table, $where);
        }
    }
    //echo $ci->db->last_query();
    $data = '';
    foreach ($rs->result() as $row) {
        $data = $row->$select;
    }
    return $data;
}

if (!function_exists('get_single_row')) {

    function get_single_row($table_name, $query = array(), $cols = "*") {
        $ci = &get_instance();
        $ci->db->select($cols)->from($table_name);
        if ($query AND is_array($query)) {
            $ci->db->where($query);
        }
        $res = $ci->db->get();

        $res_array = $res->row_array();

        if (is_array($res_array) AND count($res_array) === 0) {
            foreach ($res->list_fields() as $field) {
                $res_array[$field] = '';
            }
        }
        return $res_array;
    }

}

if (!function_exists('get_results')) {
	
    function get_results($table_name, $query = array(), $cols = "*", $offset = 0, $limit = 1, $order_by = array()) {
        $ci = &get_instance();
        $ci->db->select($cols)->from($table_name);
        if ($query AND is_array($query)) {
            $ci->db->where($query);
        }
        if (count($order_by) > 0 && is_array($order_by)) {
            foreach ($order_by as $key => $value) {
                $ci->db->order_by($key, $value);
            }
        }
        if (is_numeric($offset) && is_numeric($limit)) {
            $ci->db->limit($offset, $limit);
        }



        $rs = $ci->db->get();
        $rs = $rs->result_array();

        return $rs;
    }

}

if (!function_exists('insert_record')) {

    function insert_record($table = '', $params = array(), $insert_batch = FALSE) {
        if (!$table OR ! $params)
            return FALSE;

        $ci = & get_instance();
        $res = false;

        if (!$insert_batch) {
            $res = $ci->db->insert($table, $params);
        } else {
            $res = $ci->db->insert_batch($table, $params);
        }

        if ($res) {
            return $ci->db->insert_id();
        } else {
            return $res;
        }
    }

}

if (!function_exists('update_record')) {

    function update_record($table = '', $params = array(), $condition = array(), $update_batch = FALSE) {
        if (!$table OR ! $params)
            return FALSE;

        $ci = & get_instance();
        $res = false;

        if (is_array($condition) AND count($condition) > 0) {
            $ci->db->where($condition);
        }

        if (!$update_batch) {
            $res = $ci->db->update($table, $params);
        } else {
            $res = $ci->db->update_batch($table, $params);
        }

        return $res;
    }

}

if (!function_exists('in_search')) {

    function in_search($tbl_name = '', $cond = array(), $cols = '*', $offset = "", $limit = "", $order_by = array()) {
        if (!$tbl_name) {
            return FALSE;
        }

        $ci = &get_instance();


        if (is_array($cond)) {
            foreach ($cond as $key => $val) {
                if (is_array($val) AND count($val) > 0) {
                    $ci->db->where_in($key, $val);
                } elseif (!is_array($val) AND $val != "") {
                    $ci->db->where_in($key, explode(",", $val));
                } else {
                    return FALSE;
                }
            }
        }

        $ci->db->select($cols)->from($tbl_name);

        if (count($order_by) > 0 && is_array($order_by)) {
            foreach ($order_by as $key => $value) {
                $ci->db->order_by($key, $value);
            }
        }
        if (is_numeric($offset) && is_numeric($limit)) {
            $ci->db->limit($offset, $limit);
        }

        $res = $ci->db->get()->result_array();

        return $res;
    }

}
if (!function_exists('reverse_geocode')) {

    function reverse_geocode($address) {
        $address = str_replace(" ", "+", "$address");
        $url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";
        $result = file_get_contents("$url");
        $json = json_decode($result);
        $city = '';
        $state = '';
        $country = '';
        $latitude = '';
        $lngitude = '';

        foreach ($json->results as $result) {
            foreach ($result->address_components as $addressPart) {
                if ((in_array('locality', $addressPart->types)) && (in_array('political', $addressPart->types)))
                    $city = $addressPart->long_name;
                else if ((in_array('administrative_area_level_1', $addressPart->types)) && (in_array('political', $addressPart->types)))
                    $state = $addressPart->long_name;
                else if ((in_array('country', $addressPart->types)) && (in_array('political', $addressPart->types)))
                    $country = $addressPart->long_name;
            }
            /* foreach ($result->geometry as $geo_loc) {
              if ($geo_loc->lat != '' && $geo_loc->lng != '') {
              $latitude = $geo_loc->lat;
              $lngitude = $geo_loc->lng;
              }
              } */
            if ($latitude == '' && $lngitude == '') {
                $latitude = $result->geometry->location->lat;
                $lngitude = $result->geometry->location->lng;
            }
        }

        if (($city != '') && ($state != '') && ($country != ''))
            $address = $city . ', ' . $state . ', ' . $country;
        else if (($city != '') && ($state != ''))
            $address = $city . ', ' . $state;
        else if (($state != '') && ($country != ''))
            $address = $state . ', ' . $country;
        else if ($country != '')
            $address = $country;

        // return $address;
        return "$country/$state/$city/$latitude/$lngitude";
    }
}


function get_lat_long_from_zip($zip) {
    if (strlen($zip) > 0) {
        $url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($zip) . "&sensor=false";
        $result_string = file_get_contents($url);
        $result = json_decode($result_string, true);
        return $result['results'][0]['geometry']['location'];
    } else {
        return false;
    }
}

function getbanner($type = '', $limit_from = '', $limit_to = '') {
    $ci = &get_instance();
    $ci->db->select('*');
    $ci->db->limit($limit_to, $limit_from);
    $rs = $ci->db->get_where('banner', array('status' => 'Y', 'type' => $type));
    $data = array();
    foreach ($rs->result() as $row) {
        $data[] = array(
            'id' => $row->id,
            'type' => $row->type,
            'image' => $row->image
        );
    }
    return $data;
}

if (!function_exists('showdate')) {

    function showdate($date) {
        $date_format = get_auth('dtfrmt');
        return date($date_format, strtotime($date));
    }

}

function get_banner_advertisement($all_pages = 'All_Pages', $pos = 4, $limit = 1) {
    $ci = &get_instance();
    $class = $ci->router->fetch_class();
    $method = $ci->router->method;
    if ($class == 'user') {
        $class = 'index';
    }

    if ($method != "" && $method != "page" && $method != "index") {
        $class = $method;
    }
    $in_pages = array($all_pages, $class);
    $f_img = $ci->db->select("image,url")->from("banner")->where(array("STATUS" => "Y", "pos" => $pos))->where('end_date >=', date('Y-m-d'))->where_in('type', $in_pages)->order_by('id', 'RANDOM')->limit($limit, 0)->get();
//        /echo $ci->db->last_query();
    $banner = array();
    foreach ($f_img->result() as $row) {
        $banner[] = array(
            "image" => $row->image,
            "url" => $row->url
        );
    }
    return $banner;
}

function get_footer_links() {
    
    $ci = &get_instance();
    $data = get_f_links();
    $f_links = array();
    foreach ($data as $key => $val) {
        $f_links[] = array(
            'footer_id' => $val['footer_id'],
            'footer_cat_name' => $val['footer_cat_name'],
            'footer_link' => $val['footer_link'],
            'child_footer' => get_f_links($val['footer_id'])
        );
    }
    return $f_links;
}

function get_f_links($parent = 0) {
    $ci = &get_instance();
    $links = $ci->db->select("footer_id,footer_cat_name,footer_link")->from("footer_management")->where(array("footer_parent_id" => $parent, "footer_status" => "A"))->order_by("ord")->get();
    $data = array();
    foreach ($links->result() as $row) {
        $data[] = array(
            'footer_id' => $row->footer_id,
            'footer_cat_name' => $row->footer_cat_name,
            'footer_link' => $row->footer_link
        );
    }
    return $data;
}

if (!function_exists('getcleanurl')) {

    function getcleanurl($name) {
        $url = preg_replace('/[^A-Za-z0-9\-]/', '-', $name);
        $url = strtolower(str_replace("--", "-", $url));

        return $url;
    }

}

if (!function_exists('get_dbprefix')) {

    function get_dbprefix($table_name = '') {
        if (!$table_name)
            return FALSE;

        $ci = &get_instance();
        return $ci->db->dbprefix($table_name);
    }

}

if (!function_exists('get_print')) {

    function get_print($array = array(), $include_die = TRUE) {
        $ci = &get_instance();
        $ci->load->helper("text");
        echo "<pre>";
        if (is_array($array))
            print_r($array);
        else
            echo highlight_code($array);

        if ($include_die)
            die('</pre>');
    }

}

if (!function_exists('get_site_mode')) {

    function get_site_mode($file = '') {
        if (SMODE == 'D') {
            echo '<div style="background: #f00; color: #fff; font-size: 11px; width: 100%; float: left; padding: 2px 0;">';
            echo $file;
            echo "</div>";
        }
    }

}

if (!function_exists('my_set_session')) {

    function my_set_session($key = '', $value = '', $flash = FALSE) {
        if (!$key) {
            return FALSE;
        } elseif (is_array($key)) {
            foreach ($key as $k => $v) {
                my_set_session($k, $v, $flash);
            }
        }

        $ci = &get_instance();
        if (!$flash)
            $ci->session->set_userdata(SESS_PRE . $key, $value);
        else
            $ci->session->set_flashdata(SESS_PRE . $key, $value);
    }

}

if (!function_exists('my_unset_session')) {

    function my_unset_session($key = '') {
        if (!$key) {
            return FALSE;
        }

        $ci = &get_instance();
        $ci->session->unset_userdata(SESS_PRE . $key);
    }

}

if (!function_exists('my_session')) {

    function my_session($key = '', $flash = FALSE) {
        if (!$key) {
            return false;
        }

        $ci = &get_instance();
        if (!$flash) {
            return $ci->session->userdata(SESS_PRE . $key);
        } else {
            return $ci->session->flashdata(SESS_PRE . $key);
        }
    }

}

if (!function_exists('sub_string')) {

    function sub_string($str = '', $length = 150) {
        if (!$str) {
            return FALSE;
        }

        if (strlen($str) > $length)
            return substr($str, 0, $length) . '...';
        else
            return $str;
    }

}

if (!function_exists('print_price')) {

    function print_price($price = '') {
        $currency = '';
        if (is_numeric($price)) {
            if (CURPOS == 'L') {
                $currency = CURRENCY_SYMBOL . ' ' . number_format($price, 2);
            } else {
                $currency = number_format($price, 2) . ' ' . CURRENCY_SYMBOL;
            }
        } else {
            if (CURPOS == 'L') {
                $currency = CURRENCY_SYMBOL . ' ' . $price;
            } else {
                $currency = $price . ' ' . CURRENCY_SYMBOL;
            }
        }

        return $currency;
    }

}

function user_log_check($get_result = 'N') {
    $user_id = 0;
    $ci = &get_instance();
    $user_id = my_session('user_id');
    if ($user_id == '' || $user_id == 0) {
        if ($get_result == 'Y') {
            return FALSE;
        } else {
            $cur_controller = $ci->router->fetch_class() . "/";
            $get = $ci->input->get();
            if ($get) {
                $get = '?' . http_build_query($get);
            }

            if (!$ci->input->is_ajax_request()) {
                my_set_session("referrer", site_url() . uri_string() . $get);
                redirect(base_url());
            } else {
                echo "<script>
                    window.parent.location.href = '" . base_url() . "';
                </script>";
            }
            redirect(base_url());
        }
    } else {
        return TRUE;
    }
}

if (!function_exists('print_ad')) {

    function print_ad($attr = array()) {
        /*
         * If ad type is image
         */
        if ($attr['type'] == 'I') {
            $resolution = explode("X", $attr['resolution']);
            $ad = check_file('banner_image', $attr['image']);
            if ($ad) {
                echo '<a href="' . base_url($attr['url']) . '">';
                echo '<img src="' . base_url('assets/banner_image/' . $attr['image']) . '" height="' . $resolution[1] . '" width="' . $resolution[0] . '"/></a>';
            }
        }
    }

}

if (!function_exists('check_file')) {

    function check_file($file_path = '', $file_name = '', $thumb_required = TRUE, $check_only = FALSE) {
        $default_image = base_url("assets/images/no_image.png");
        $thumb_name = '';
        $thumb_str = '_thumb';


        if ($file_name == "" || $file_path == "") {
            return ($check_only) ? FALSE : $default_image;
        }

        if (!file_exists(APATH . "assets/" . $file_path . "/" . $file_name)) {
            return ($check_only) ? FALSE : $default_image;
        }

        if ($thumb_required) {
            $thumb_name = get_thumb_name($file_name, $thumb_str);
        }

        if ($thumb_required AND file_exists(APATH . "assets/" . $file_path . "/" . $thumb_name)) {
            $default_image = ASSETS . $file_path . "/" . $thumb_name;
            return $default_image;
        } else {
            $default_image = ASSETS . $file_path . "/" . $file_name;
            return $default_image;
        }
    }

}

function get_thumb_name($file_name = '', $thumb_str = "_thumb") {
    $img_name = '';

    if ($file_name == "") {
        return FALSE;
    }

    $ext = end(explode(".", $file_name));
    $ext_len = strlen($ext) + 1;
    $img_name = substr($file_name, 0, -$ext_len);

    return $img_name . $thumb_str . '.' . $ext;
}


function generate_profile_id($insert_id = '') {
    $user_det = get_single_row("user", array("id" => $insert_id), "full_name,dob");
    $unique_id = substr($user_det['full_name'], 0, 1);
    if ($user_det['dob'] != "" AND check_valid_date($user_det['dob'])) {
        $dob = explode("-", $user_det['dob']);
        $unique_id.=$dob[2] . $dob[1];
    } else {
        $unique_id.= str_pad(rand(1, 31), 2, "0", STR_PAD_LEFT) . str_pad(rand(1, 12), 2, "0", STR_PAD_LEFT);
    }

    $unique_id.=rand(1000, 9999);

    return $unique_id;
}

function get_lang($lang = '', $default_txt = '', $file = '') {
    $ci = &get_instance();

    if ($file != "") {
        $language = my_session("language");
        $ci->lang->load($file, $language);
    }

    $str = $ci->lang->line($lang);
    if ($str == "") {
        $str = $default_txt;
    }
    return $str;
}

if (!function_exists('get_auth')) {

    function get_auth($col = '*') {
        $ci = &get_instance();
        $auth = $ci->db->select($col)->from("auth")->get()->row_array();
        if ($col == '*') {
            return $auth;
        } else {
            return $auth[$col];
        }
    }

}

if (!function_exists('count_records')) {

    function count_records($table_names = '', $query = array()) {
        $ci = &get_instance();
        if ($table_names != "") {
            if (is_array($query)) {
                $ci->db->from($table_names);
                //$ci->db->where($query);				
                return $ci->db->count_all_results();
            } else {
                return $ci->db->count_all($table_names);
            }
        } else {
            return FALSE;
        }
    }

}

if (!function_exists('upload_file')) {

    function upload_file($field_name = 'userfile', $path = '', $create_thumb = TRUE, $delete_original = FALSE, $allowed_types = 'gif|jpg|png|jpeg', $max_size = '', $restrict_size = TRUE, $savename = null) {
        echo is_array($_FILES[$field_name]['name']);
        // echo "string";die();
        $ci = &get_instance();
        if (isset($_FILES[$field_name]) AND is_array($_FILES[$field_name]['name'])) {
            check_directory($path);
            $config['upload_path'] = APATH . "assets/$path/";
            $config['allowed_types'] = $allowed_types;
            if ($max_size) {
                $config['max_size'] = $max_size;
            }
			
            $ci->load->library('upload');
			
			if($create_thumb){
				$configs['image_library'] = 'gd2';
				$configs['create_thumb'] = TRUE;
				if(is_array($create_thumb) && isset($create_thumb['maintain_ratio']))
					$configs['maintain_ratio'] = $create_thumb['maintain_ratio'];
				else
					$configs['maintain_ratio'] = TRUE;
				
				if(is_array($create_thumb) && isset($create_thumb['width']))
					$configs['width'] = $create_thumb['width'];
				else
					$configs['width'] = 320;
				
				if(is_array($create_thumb) && isset($create_thumb['height']))
					$configs['height'] = $create_thumb['height'];
				else
					$configs['height'] = 235;
				
				$ci->load->library('image_lib');
			}
			
            $image = array();



            for ($i = 0; $i < count($_FILES[$field_name]); $i++) {
                if (isset($_FILES[$field_name]['tmp_name'][$i]) AND $_FILES[$field_name]['tmp_name'][$i] != "") {
                    $_FILES['userfile' . $i] = array(
                        'tmp_name' => $_FILES[$field_name]['tmp_name'][$i],
                        'name' => $_FILES[$field_name]['name'][$i],
                        'type' => $_FILES[$field_name]['type'][$i],
                        'size' => $_FILES[$field_name]['size'][$i],
                        'error' => $_FILES[$field_name]['error'][$i],
                    );

                    if(!empty($savename)) { $config['file_name'] = $savename; $config['overwrite'] = true; } else $config['file_name'] = md5(date("Y-m-d H:i:s"));
                    $ci->upload->initialize($config);
                    $uploaded = $ci->upload->do_upload('userfile' . $i);
                    print_r($uploaded);die();
                    if ($uploaded) {
                        $upload_data = $ci->upload->data();
                        $image[] = $upload_data['file_name'];
						if($create_thumb){
							$configs['source_image'] = APATH . "assets/$path/" . $upload_data['file_name'];
							$ci->image_lib->initialize($configs);
							$ci->image_lib->resize();
						}
                    }
                }
            }

            return $image;
        } else {
            if (isset($_FILES[$field_name]) AND $_FILES[$field_name]['tmp_name'] != "") {
                check_directory($path);
                if(!empty($savename)) { $config['file_name'] = $savename; $config['overwrite'] = true; } else $config['file_name'] = md5(date('Y-m-d H:i:s'));
                $config['upload_path'] = APATH . "assets/$path/";
                $config['allowed_types'] = $allowed_types;
                if ($max_size) {
                    $config['max_size'] = $max_size;
                }

                $ci->load->library('upload', $config);
                $uploaded = $ci->upload->do_upload($field_name);
                $upload_data = $ci->upload->data();
                $image = $upload_data['file_name'];

                if (!$uploaded AND $image != '') {
                    $error = $ci->upload->display_errors();
                    my_set_session('error_msg', $error, TRUE);
                    return FALSE;
                }
                if (strpos($upload_data['file_type'], 'image') !== false && $upload_data && $image != "") {
					if($create_thumb){
						$rconfig['image_library'] = 'gd2';
						$rconfig['source_image'] = $upload_data['full_path'];
						$rconfig['create_thumb'] = TRUE;
						if(is_array($create_thumb) && isset($create_thumb['maintain_ratio']))
							$rconfig['maintain_ratio'] = $create_thumb['maintain_ratio'];
						else
							$rconfig['maintain_ratio'] = TRUE;
						
						if(is_array($create_thumb) && isset($create_thumb['width']))
							$rconfig['width'] = $create_thumb['width'];
						else
							$rconfig['width'] = 200;
						
						if(is_array($create_thumb) && isset($create_thumb['height']))
							$rconfig['height'] = $create_thumb['height'];
						else
							$rconfig['height'] = 200;
						
						$ci->load->library('image_lib', $rconfig);
						$ci->image_lib->resize();
						$ci->image_lib->clear();
					}
                }
                /*                 * ****  * ****restrict image size*********************** */
                if ($restrict_size AND strpos($upload_data['file_type'], 'image') !== false) {
                    $rconfig1 = array();
                    $ci = &get_instance();
                    $ab_path = (APATH . "assets/$path/$image");
                    $data = getimagesize($ab_path);
                    $width = $data[0];
                    $height = $data[1];
                    $my_size = get_auth("img_size");
                    $dt = @explode("X", $my_size);
                    $my_wd = $dt[0];
                    $my_ht = $dt[1];
                    if ($width > $my_wd || $height > $my_ht) {
                        $ext = pathinfo($image, PATHINFO_EXTENSION);
                        $rconfig1['image_library'] = 'gd2';
                        $rconfig1['source_image'] = APATH . "assets/$path/$image";
                        $rconfig1['create_thumb'] = TRUE;
                        $rconfig1['maintain_ratio'] = TRUE;
                        $rconfig1['width'] = $my_wd;
                        $rconfig1['height'] = $my_ht;
                        $rconfig1['new_image'] = "temp_file." . $ext;
                        $ci->image_lib->initialize($rconfig1);
                        $ci->image_lib->resize();
                        @unlink(APATH . "assets/$path/$image");
                        rename(APATH . "assets/$path/temp_file_thumb." . $ext, APATH . "assets/$path/$image");
                    }
                }
                /*                 * ******  * ****restrict image size*********************** */
                if ($delete_original && $upload_data && $image != "") {
                    @unlink(APATH . "assets/$path/$image");
                }

                return $image;
            } else {
                return FALSE;
            }
        }
    }

}

if (!function_exists('resize_crop')) {

    function resize_crop($field_name = 'userfile', $path = '', $create_thumb = TRUE, $delete_original = FALSE, $allowed_types = 'gif|jpg|png|jpeg', $max_size = '', $restrict_size = TRUE) {
        $ci = &get_instance();
        if (isset($_FILES[$field_name]) AND is_array($_FILES[$field_name]['name'])) {
            check_directory($path);
            $config['upload_path'] = APATH . "assets/$path/";
            $config['allowed_types'] = $allowed_types;
            if ($max_size) {
                $config['max_size'] = $max_size;
            }
            $ci->load->library('upload');
			
			if($create_thumb){
				$configs['image_library'] = 'gd2';
				$configs['create_thumb'] = TRUE;
				if(is_array($create_thumb) && isset($create_thumb['maintain_ratio']))
					$configs['maintain_ratio'] = $create_thumb['maintain_ratio'];
				else
					$configs['maintain_ratio'] = TRUE;
				
				if(is_array($create_thumb) && isset($create_thumb['width']))
					$configs['width'] = $create_thumb['width'];
				else
					$configs['width'] = 320;
				
				if(is_array($create_thumb) && isset($create_thumb['height']))
					$configs['height'] = $create_thumb['height'];
				else
					$configs['height'] = 235;
				
				$ci->load->library('image_lib');
			}
			
            $image = array();


            for ($i = 0; $i < count($_FILES[$field_name]); $i++) {
                if (isset($_FILES[$field_name]['tmp_name'][$i]) AND $_FILES[$field_name]['tmp_name'][$i] != "") {
                    $_FILES['userfile' . $i] = array(
                        'tmp_name' => $_FILES[$field_name]['tmp_name'][$i],
                        'name' => $_FILES[$field_name]['name'][$i],
                        'type' => $_FILES[$field_name]['type'][$i],
                        'size' => $_FILES[$field_name]['size'][$i],
                        'error' => $_FILES[$field_name]['error'][$i],
                    );

                    $config['file_name'] = md5(date("Y-m-d H:i:s"));
                    $ci->upload->initialize($config);
                    $uploaded = $ci->upload->do_upload('userfile' . $i);

                    if ($uploaded) {
                        $upload_data = $ci->upload->data();
                        $image[] = $upload_data['file_name'];
						if($create_thumb){
							$configs['source_image'] = APATH . "assets/$path/" . $upload_data['file_name'];
							$ci->image_lib->initialize($configs);
							$ci->image_lib->resize();
						}
                    }
                }
            }

            return $image;
        }
		else {
            if (isset($_FILES[$field_name]) AND $_FILES[$field_name]['tmp_name'] != "") {
                check_directory($path);
                $config['file_name'] = md5(date('Y-m-d H:i:s'));
                $config['upload_path'] = APATH . "assets/$path/";
                $config['allowed_types'] = $allowed_types;
                if ($max_size) {
                    $config['max_size'] = $max_size;
                }

                $ci->load->library('upload', $config);
                $uploaded = $ci->upload->do_upload($field_name);
                $upload_data = $ci->upload->data();
				
                if (!$uploaded AND $image != '') {
                    $error = $ci->upload->display_errors();
                    my_set_session('error_msg', $error, TRUE);
                    return FALSE;
                }
				
				//echo "<pre>"; print_r($upload_data); echo "</pre>"; exit();
				
				$image = $upload_data['file_name'];
				
				######## STEP 2
				$image_config["image_library"] = "gd2";
				$image_config["source_image"] = $upload_data["full_path"];
				$image_config['create_thumb'] = TRUE;
				$image_config['thumb_marker'] = '_main';
				$image_config['maintain_ratio'] = TRUE;
				$image_config['new_image'] = $upload_data["file_path"] . $image;
				$image_config['quality'] = "100%";
				$image_config['width'] = $create_thumb['width'];
				$image_config['height'] = $create_thumb['height'];
				$dim = (intval($upload_data["image_width"]) / intval($upload_data["image_height"])) - ($image_config['width'] / $image_config['height']);
				$image_config['master_dim'] = ($dim > 0)? "height" : "width";
				 
				$ci->load->library('image_lib');
				$ci->image_lib->initialize($image_config);
				$ci->image_lib->resize();
				
				
				
				
				######## STEP 3
				$image_config2['image_library'] = 'gd2';
				$image_config2['source_image'] = $upload_data["file_path"] . $upload_data["raw_name"] . "_main" . $upload_data["file_ext"];
				$image_config2['new_image'] = $upload_data["file_path"] . $upload_data["raw_name"] . "_main" . $upload_data["file_ext"];
				$image_config2['quality'] = "100%";
				$image_config2['create_thumb'] = FALSE;
				$image_config2['maintain_ratio'] = FALSE;
				$image_config2['width'] = $create_thumb['width'];
				$image_config2['height'] = $create_thumb['height'];
				$image_config2['x_axis'] = '0';
				$image_config2['y_axis'] = '0';
				 
				$ci->image_lib->clear();
				$ci->image_lib->initialize($image_config2); 
				$ci->image_lib->crop();
				 
				
				
                return $image;
            } else {
                return FALSE;
            }
        }
    }

}

function delete_file($file_path = '', $file_name = '', $thumb_also = TRUE) {
    $thumb_str = "_thumb";

    if ($file_name == "" || $file_path == "") {
        return FALSE;
    }

    if (!file_exists(APATH . "assets/" . $file_path . "/" . $file_name)) {
        return FALSE;
    }
    $unlink_path = APATH . "assets/" . $file_path . "/" . $file_name;
    chown($unlink_path, 666);
    @unlink($unlink_path);

    if ($thumb_also) {
        $thumb_name = APATH . "assets/" . $file_path . "/" . get_thumb_name($file_name, $thumb_str);
        if (file_exists($thumb_name)) {
            chown($thumb_name, 666);
            @unlink($thumb_name);
        }
    }
}

function check_directory($dir_name = '') {
    if ($dir_name == "") {
        return FALSE;
    }

    if (!file_exists(APATH . 'assets/' . $dir_name)) {
        mkdir(APATH . 'assets/' . $dir_name, 0777);
    }

    return TRUE;
}

if (!function_exists('get_marker')) {

    function get_marker($lat = '', $lng = '', $pointer_name = '', $map_id = 'map-canvas') {
        $initial_map = "<script src='https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false'></script>
    <script>
        function initialize() {
            var myLatlng = new google.maps.LatLng($lat,$lng);
            var mapOptions = {
                zoom: 15,
                center: myLatlng
            }
            var map = new google.maps.Map(document.getElementById('$map_id'), mapOptions);

            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                title: '$pointer_name'
            });
        }

        google.maps.event.addDomListener(window, 'load', initialize);

    </script>";
        echo $initial_map;
    }

}

if (!function_exists('get_marker_image')) {

    function get_marker_image($lat = '', $lng = '', $height = '200', $width = '100%') {
        $initial_map = "<img width='$width' height='$height' src='https://maps.googleapis.com/maps/api/staticmap?center=$lat,$lng&zoom=10&size=760x{$height}&maptype=roadmap&markers=color:red%7Clabel:S%7C$lat,$lng' alt='Map'>";
        echo $initial_map;
    }

}

if (!function_exists('upgrade_profile_completeness')) {

    function upgrade_profile_completeness($comp_id = '', $user_id = '') {
        if ($comp_id != "" AND $user_id != "") {
            $res = false;
            $ci = &get_instance();
            $nor = count_records("user_prof_comp", array("uid" => $user_id, "cid" => $comp_id));
            if ($nor == 0) {
                $completeness = getField("prcnt", "prof_comp_states", "", "", array("id" => $comp_id, "status" => "Y"));
                if ($completeness) {
                    $res = $ci->db->set("acc_comp", "acc_comp + $completeness", FALSE)
                            ->where("uid", $user_id)
                            ->update("user_meta");
                    if ($ci->db->affected_rows() == 0) {
                        $res = $ci->db->insert("user_meta", array("uid" => $user_id, "acc_comp" => $completeness));
                    }

                    if ($res) {
                        $insert_completeness = array(
                            'uid' => $user_id,
                            'cid' => $comp_id,
                            'cdate' => date('Y-m-d')
                        );

                        $res = $ci->db->insert("user_prof_comp", $insert_completeness);

                        return $res;
                    }
                }
            }
        }

        return FALSE;
    }

}

if (!function_exists('make_simple_array')) {

    function make_simple_array($marray = array(), $fields = '', $make_single = FALSE) {
        if ($fields == "") {
            return FALSE;
        }

        $fields = explode(",", $fields);
        $key = null;
        $val = null;
        if (count($fields) > 1) {
            $key = trim($fields[0]);
            $val = trim($fields[1]);
        } else {
            $key = $val = trim($fields[0]);
        }

        $sarray = array();
        if (is_array($marray) AND ! empty($marray)) {
            if (!$make_single) {
                foreach ($marray as $k => $v) {
                    $sarray[$v[$key]] = $v[$val];
                }
            } else {
                foreach ($marray as $k) {
                    $sarray[] = $k[$key];
                }
            }
        }

        return $sarray;
    }

}

if (!function_exists('get_user_name')) {

    function get_user_name($full_name = '') {
        if ($full_name == '') {
            return FALSE;
        }

        $name = explode(" ", $full_name);
        if (count($name) > 1) {
            $end_name = end($name);
            $full_name = null;

            for ($i = 0; $i < (count($name) - 1); $i++) {
                $full_name.=$name[$i] . ' ';
            }
            $full_name.=substr($end_name, 0, 1) . '.';
        }


        return $full_name;
    }

}

if (!function_exists('print_member_name')) {

    function print_member_name($full_name = '', $profile_id = '', $profile_seperator = ' ', $member_auth = null) {
        if ($full_name == '') {
            return FALSE;
        }

        if (!$member_auth) {
            if (my_session('pmem_nm') == '') {
                $member_auth = get_auth('pmem_nm');
                my_set_session('pmem_nm', $member_auth);
            } else {
                $member_auth = my_session('pmem_nm');
            }
        }

        if ($member_auth == 'B') {
            return $full_name . $profile_seperator . $profile_id;
        } elseif ($member_auth == 'N') {
            return $full_name;
        } else {
            return $profile_id;
        }
    }

}

if (!function_exists('generate_pagination')) {

    function generate_pagination($base_url = '', $per_page_records = 100, $table_name = '', $uri_segment = 3, $query = array(), $total_rows = 0, $query_stting = FALSE,$first_var='',$sec_var='') {

        if ($query_stting) {
            if (count($base_url) > 1) {
				if(!empty($first_var) && !empty($sec_var)){
					$config['base_url'] = base_url() . $base_url[0] ."/".$first_var."/".$sec_var."?page=next&" . $base_url[1];
				}
				else if(!empty($first_var) && empty($sec_var)) {
					$config['base_url'] = base_url() . $base_url[0] ."/".$first_var."?page=next&" . $base_url[1];
				}
				else {
					$config['base_url'] = base_url() . $base_url[0] . "?page=next&" . $base_url[1];
				}
				
            } else {
				if(!empty($first_var)){
					$config['base_url'] = base_url() . $base_url[0] ."/".$first_var."/".$sec_var."?page=next&";
				}else if(!empty($first_var) && empty($sec_var)) {
					$config['base_url'] = base_url() . $base_url[0] ."/".$first_var."?page=next&";
				}else {
                $config['base_url'] = base_url() . $base_url[0] . "?page=next";
				}
            }
        } else {
            $config['base_url'] = base_url() . $base_url;
        }
        if ($total_rows > 0) {
            $config['total_rows'] = $total_rows;
        } else {
            $config['total_rows'] = count_records($table_name, $query);
        }
        $config['per_page'] = $per_page_records;
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';

        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';

        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';

        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="active"><a href="javascript:void(0);">';
        $config['cur_tag_close'] = '</a></li>';

        if ($query_stting) {
            $config['page_query_string'] = TRUE;
            $config['query_string_segment'] = 'limit';
        }

        $config["uri_segment"] = $uri_segment;
	

        $ci = &get_instance();
        $ci->load->library('pagination');
        $ci->pagination->initialize($config);
        return $ci->pagination->create_links();

    }

}

function show_view($page = '') {
    if ($page) {
        if (SHOW_VIEW == 'Y') {
            echo '<div style="width: 100%; float: left; background: #CE2531; color: #fff; font-size: 10px; padding 3px 0; margin-bottom: 1px; word-break: break-all;">';
            echo $page;
            echo '</div>';
        }
    }
}

function show_controller() {
    if (SHOW_CONTROLLER == 'Y') {
        $ci = &get_instance();
        echo '<div style="width: 100%; float: left; background: #74a4ec; color: #fff; font-size: 10px; padding 3px 0; margin-bottom: 1px;">';
        echo $ci->router->fetch_class() . '/' . $ci->router->fetch_method();
        echo '</div>';
    }
}

if (!function_exists('check_valid_date')) {

    function check_valid_date($date = '') {
        if ($date != "") {
            $dt = explode("-", $date);
            return checkdate($dt[1], $dt[2], $dt[0]);
        }

        return FALSE;
    }

}

if (!function_exists('get_menu')) {

    function get_menu($pid = '0', $pos = '1') {
        $ci = &get_instance();
        $visible = "vsblon IN ('E','B')";
        if (my_session('user_id') != "") {
            $visible = "vsblon IN ('E','A')";
        }
        //$mnu = get_results('menu', array('pid' => $pid, 'pos' => $pos, 'status' => 'Y'), 'id,name,url,wsit,trgt', '', '', array('ord' => 'ASC'));
        $mnu = $ci->db->select('id,name,url,wsit,trgt')
                ->from('menu')
                ->where(array('pid' => $pid, 'pos' => $pos, 'status' => 'Y'))
                ->where($visible)
                ->order_by('ord', "ASC")
                ->get()
                ->result_array();

        if (empty($mnu)) {
            return FALSE;
        }
        foreach ($mnu as $key => $val) {
            $menu[] = array(
                'id' => $val['id'],
                'name' => str_replace("{site_title}", SITE_TITLE, $val['name']),
                'core_url' => $val['url'],
                'url' => ($val['wsit'] == 'Y') ? base_url($val['url']) : prep_url($val['url']),
                'trgt' => $val['trgt'],
                'child' => get_menu($val['id'], $pos)
            );
        }

        return $menu;
    }

}

if (!function_exists('is_interested')) {

    function is_interested($rid = '', $sid = '') {
        if (!$rid OR ! $sid) {
            return FALSE;
        }
        $ci = &get_instance();
        $res = $ci->db->select("status", FALSE)
                ->from("user_noti")
                ->where("(rid = $rid AND sid = $sid) OR (rid = $sid AND sid = $rid)")
                ->get()
                ->row_array();
        return (isset($res['status'])) ? $res['status'] : '';
    }

}

if (!function_exists('watermarkImage')) {

    function watermarkImage($SourceFile, $WaterMarkText = '', $DestinationFile = '') {
        list($width, $height) = getimagesize($SourceFile);
        $image_p = imagecreatetruecolor($width, $height);
        $ext = strtolower(end(explode(".", $SourceFile)));
        $image = null;
        /*
         * Checking file types.
         */
        if ($ext == 'jpg' OR $ext == 'jpeg')
            $image = imagecreatefromjpeg($SourceFile);
        elseif ($ext == 'png')
            $image = imagecreatefrompng($SourceFile);
        elseif ($ext == 'gif')
            $image = imagecreatefromgif($SourceFile);
        else
            return FALSE;

        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width, $height);
        $black = imagecolorallocate($image_p, 0, 0, 0);

        /*
         * Please choose your font.
         */
        $font = 'assets/fonts/calibri.ttf';
        $font_size = ($width < 200) ? 13 : (($width < 700 AND $width > 200) ? 18 : (($width < 1500 AND $width > 700) ? 25 : 35));

        $marge_bottom = 50;
        for ($i = 0; $i < ((strlen($WaterMarkText) > 30) ? ((strlen($WaterMarkText) > 50) ? 1 : 3) : 4); $i++) {
            $WaterMarkText.=$WaterMarkText;
        }

        imagettftext($image_p, $font_size, 0, 0, imagesy($image) - $marge_bottom, $black, $font, $WaterMarkText);

        $DestinationFile = ($DestinationFile == "") ? $SourceFile : $DestinationFile;

        imagejpeg($image_p, $DestinationFile, 100);
        if ($ext == 'jpg' OR $ext == 'jpeg')
            imagejpeg($image_p, $DestinationFile, 100);
        elseif ($ext == 'png')
            imagepng($image_p, $DestinationFile, 100);
        elseif ($ext == 'gif')
            imagegif($image_p, $DestinationFile, 100);

        imagedestroy($image);
        imagedestroy($image_p);
    }

}

if (!function_exists('admin_permission')) {

    function admin_permission($url = '', $type = '1') {
        $CI = & get_instance();
        $permission = get_menu_json('', 'permission');
        $permission = (array) $permission;
		return TRUE;
		// ---------
        if (is_array($permission) AND !empty($permission)) {
            $array = $permission;
            if (isset($array[$url]) AND in_array($type, $array[$url])) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }

}


if (!function_exists('getBackgroundImage')) {

    function getBackgroundImage($page_name = 'default', $type = 'background', $hasBG = TRUE) {
		//	If the background Image is not coming from page_background table then $type = array(TableName, id)
		//get_print(func_get_args());
		$bg_HTML = "";
		if($hasBG){
			$CI = & get_instance();
			if($type == 'background'){
				$bg_image = $CI->db->select("image")->where(array('slug' => $page_name, 'status' => 'Y'))->get('page_background')->row_array();
				if(!empty($bg_image)){
					if(($bg_image['image'] != "") && file_exists(FCPATH . "assets/background_image/" . $bg_image['image'])){
						$bg_HTML = "<style>
										body {
											background: url(" . ASSETS . "background_image/" . $bg_image['image'] . ");
											background-size:cover; 
											background-attachment:fixed;
										}
									</style>";
						
					}
					else{
						return getDefaultBackgroundImage();
					}
				}
				else{
					return getDefaultBackgroundImage();
				}
			}
			else{
				$bg_image = $CI->db->select("background_image")->where(array('id' => $type['id'], 'status' => 'Y'))->get($type['table'])->row_array();
				if(!empty($bg_image)){
					if(($bg_image['background_image'] != "") && file_exists(FCPATH . "assets/background_image/" . $bg_image['background_image'])){
						$bg_HTML = "<style>
										body {
											background: url(" . ASSETS . "background_image/" . $bg_image['background_image'] . ");
											background-size:cover; 
											background-attachment:fixed;
										}
									</style>";
						
					}
					else{
						return getDefaultBackgroundImage();
					}
				}
				else{
					return getDefaultBackgroundImage();
				}
			}
		}
		return $bg_HTML;
    }

}

if (!function_exists('getDefaultBackgroundImage')) {

    function getDefaultBackgroundImage() {
        $bg_HTML = "";
		$CI = & get_instance();
		$bg_image = $CI->db->select("image")->where(array('slug' => 'default'))->get('page_background')->row_array();
		if(!empty($bg_image)){
			if(($bg_image['image'] != "") && file_exists(FCPATH . "assets/background_image/" . $bg_image['image'])){
				$bg_HTML = "<style>
								body {
									background: url(" . ASSETS . "background_image/" . $bg_image['image'] . ");
									background-size:cover; 
									background-attachment:fixed;
								}
							</style>";
				
			}
		}
		return $bg_HTML;
    }

}


function remaining_date($start_date = '') {
    $end_date = date("Y-m-d H:i:s");
    $diff = abs(strtotime($start_date) - strtotime($end_date));
    $years = floor($diff / (365 * 60 * 60 * 24));
    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

    $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
    $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / (60));
    $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));
    $str = 'few seconds';
    //echo $years.' Years '.$months.' Month '.$days.' Days'.$days.' Hour'.$hours.' Minutes'.$minutes.' Seconds' . $seconds;
    if ($years > 0) {
        if ($years < 2) {
            $str = '1 year';
        } else {
            $str = $years . ' years';
        }
    } else if ($months > 0) {
        if ($months < 2) {
            $str = '1 month';
        } else {
            $str = $months . ' months';
        }
    } else if ($days > 0) {
        if ($days < 2) {
            $str = '1 day';
        } else {
            $str = $days . ' days';
        }
    } else if ($hours > 0) {
        if ($hours < 2) {
            $str = '1 hour';
        } else {
            $str = "$hours hours";
        }
    } else if ($minutes > 0) {
        if ($minutes < 2) {
            $str = '1 minute';
        } else {
            $str = "$minutes minutes";
        }
    } else if ($seconds > 0) {
        if ($seconds < 10) {
            $str = '10 seconds';
        } else if ($seconds < 5) {
            $str = "5 seconds";
        } else {
            $str = "$seconds seconds";
        }
    }

    return $str;
}

/* End of file os_helper.php  RAHUL */

    function all_category_images(){

        $pram = array();
        $data['cat_images'] = getData_table('file',$pram ,'file');
        $data['cat_images'] = array_column($data['cat_images'],'file');
        foreach ($data['cat_images'] as &$file) {
            $file = SITEFILES . "content/" . $file;
        }

        return $data['cat_images'];
    }

    function ip_details($manual_ip = ''){
        $server_ip = $_SERVER['REMOTE_ADDR'];
        $ip = (empty($manual_ip)) ? $server_ip : $manual_ip ;         
        $ip_details = file_get_contents('https://freegeoip.net/json/'.$ip);
        $rt_ip_details =  json_decode($ip_details, 1);
        return($rt_ip_details);
    }


	function getValue($tableName, $field_name, $param){
        //get main CodeIgniter object
        $CI = & get_instance();       
        $CI->db->select($field_name);
        $query = $CI->db->get_where($tableName, $param);
        $row = $query->row_array();
        return count($row)?$row[$field_name]:"";
    }

         #################### Return array of selected fields from specific table ###################
    
    function getsingleColumnData($tableName, $param, $field_name){
        //get main CodeIgniter object
        $CI = & get_instance();   
        $CI->db->select($field_name);    
        $query = $CI->db->get_where($tableName, $param);
        return $query->row_array();
    }

    ############################### Return a array from specific table ##################
    
    function getSingle($table,$where_clause,$order_by_fld='',$order_by='',$limit='',$offset='') {
        //get main CodeIgniter object
        $CI = & get_instance();       
        if($where_clause != '')
            $CI->db->where($where_clause);
        if($order_by_fld != '')
            $CI->db->order_by($order_by_fld,$order_by);
        if($limit != '' && $offset !='')
            $CI->db->limit($limit,$offset);     
        $CI->db->select('*');
        $CI->db->from($table);
        $query = $CI->db->get();  
         return $query->row_array(); 
    }
	
	 ############################### Return array from specific table ###################
     
    function getData_table($tableName, $param,$field_name = "*"){
        //get main CodeIgniter object
        $CI = & get_instance(); 
        $CI->db->select($field_name);      
        $query = $CI->db->get_where($tableName, $param);
        return $query->result_array();
    }  

    ############################### Return array from specific table ###################
    
    function getData_obj($tableName, $param){
        //get main CodeIgniter object
        $CI = & get_instance();       
        $query = $CI->db->get_where($tableName, $param);
        return $query->result();
    }


    ############################### Return array from multi table ###################
    
    function getData_multi($tableName1,$tb1_id,$tableName2,$tb2_id,$param,$join_type,$group_by = null,$field_name = '*'){
        //get main CodeIgniter object

        $condition = $tableName1.'.'.$tb1_id.' = '.$tableName2.'.'.$tb2_id;    

        $CI = & get_instance();
        $CI->db->select($field_name);
        $CI->db->from($tableName1);
        $CI->db->where($param);            
        $CI->db->join($tableName2, $condition , $join_type);
		if($group_by) $CI->db->group_by($group_by);
        $query = $CI->db->get()->result_array();
        return $query;

    }  

                
    ############################## Insert array for specific table #############

    function insertValue($table, $row)  {
        //get main CodeIgniter object
        $CI = & get_instance();       
        $str = $CI->db->insert_string($table, $row);        
        $query = $CI->db->query($str);    
        $insertid = $CI->db->insert_id();
        return $insertid;
    }
            

    ############################### Return number of rows ########################  
    
    function count_rows($tableName, $param) {
        //get main CodeIgniter object
        $CI = & get_instance();       
        if(isset($param) && $param!="")
            $CI->db->where($param);
        $query = $CI->db->get($tableName);
        $CI->db->last_query();;
        return $query->num_rows();
    }

        ############################### Update table record ###################
    
    function updateDataCondition($tableName, $data, $where) {
        //get main CodeIgniter object
        $CI = & get_instance();
        $CI->db->where($where);
        $CI->db->update($tableName, $data);
        return TRUE;
    }

    ############### Delete Record from a table ########################

    function Delete_data($table,$where) {
        //get main CodeIgniter object
        $CI = & get_instance();       
        $CI->db->delete($table, $where); 
    }

    function get_data_dropdown($table_name, $field_id, $field_name, $where="", $select_id="", $id="", $name="", $attr="", $order_by_fld="", $order_by="",$class=""){     
        if(!empty($table_name) && !empty($field_id) && !empty($field_name)) { 
            //get main CodeIgniter object
            $CI = & get_instance();       
            $CI->db->select($field_id.','.$field_name);
            $CI->db->from($table_name);
            if($where!="")
            $CI->db->where($where);
            if($order_by_fld != '')
                $CI->db->order_by($order_by_fld,$order_by);

            $CI->db->limit(100);

            $query = $CI->db->get(); 
            $row = $query->result_array();
            if(!empty($row)){
                $html = '<select class="form-control '.$class.'" id="'.$id.'" name="'.$name.'" '.$attr.'>';
                $html .= '<option >Select</option>';
                foreach($row as $val){
                    if($val[$field_id] == $select_id){$slected='selected="selected"';}else{$slected='';}
                    $html .= '<option value="'.$val[$field_id].'" '.$slected.'>'.$val[$field_name].'</option>';   
                }
                $html .= '</select>';
            } else  {
               $html = "";
           }
           return $html;
        }

    }
    
    //// rahul end ////
 

	/*
	**
	**		-------------------------------------------------------------------------------------------||
	**		-------------------------------------------------------------------------------------------||
	**		-------------------------------------------------------------------------------------------||
	**
	*/
	
	// BUILDS SELECT QUERY AND EXECUTES
	// @TABLE = TABLE TO SELECT FROM
	// @PARTICULARS = OPTIONAL COLUMNS TO SELECT
	// @CONDITIONS = OPTIONAL WHERE CONDITIONS AS ARRAY
	// @MULTI = OPTIONAL FORCE MULTI DATA FORMAT IN CASE OF SINGLE ROW RESULT
	function getData($table, $particulars = '*', $conditions = array(), $multi = FALSE, $limit = FALSE, $order_by = NULL) {
		if(!$particulars) $particulars = '*';
		$CI = & get_instance(); 
		$CI->db->from($table);
		$CI->db->select($particulars);
		$CI->db->where($conditions);
		if($limit) $CI->db->limit($limit);
		if($order_by) $CI->db->order_by($order_by);
		$query = $CI->db->get();
		if($query->num_rows() > 1) return $query->result_array();
		else { if($multi) return $query->result_array(); else return $query->row_array(); }
	}
	
	// QUICK PROC TO CHECK IF DATA EXISTS
	function exists($table, $column, $data) {
		$CI = & get_instance();
		$CI->db->from($table);
		$CI->db->where(array($column => $data));
		return ($CI->db->count_all_results() > 0);
	}
	
		
	function readModelList($model = array(), $request = array()) {
		if(empty($model['tables'])) return false;
		
		// the final result array
		$result = array();
		
		// primary table to select from
		$table = array();
		
		// secondary tables to select from (via join)
		$tables = array();

		// columns to select
		$select = array();
		
		// enable codeigniter query builder select protection; automatically set to FALSE if complex query is detected on cols
		$select_protect = TRUE;
		
		// columns to group by
		$group_by = array();
		
		// columns to order by
		$order_by = array();

		// where clause
		$where_search = array();
		
		// actual search string
		$search = NULL;
		
		// SET SEARCH FILTER WHERE CLAUSE
		if(!empty($request) && !empty($request['search'])) {
			$search = $request['search']['value'];
		}
		
		foreach($model['tables'] as $tk => $tv) {
			
			// SKIP IF NO COLUMN PRESENT
			if(empty($tv['cols'])) continue;
			
			$_table = (empty($tv['alias'])) ? $tk : $tk . " " . $tv['alias'];

			// SET PRIMARY TABLES
			if(empty($table)) $table[] = $_table;
			
			// SET SECONDARY TABLES
			if(empty($tv['joins'])) {
				// do nothing
			}
			else {
				foreach($tv['joins'] as $jk => $jv) {
					if(in_array($jv['table'], array_keys($model['tables']))) $tables[] = $_table;
				}
			}
			
			// SET SELECT, GROUP BY
			if(in_array($_table, $table) || in_array($_table, $tables)) {
				foreach($tv['cols'] as $k => $v) {
					if(empty($v['list'])) continue;
					if(empty($v['no_protect'])) {
						$select_protect = FALSE;
						$col = (empty($tv['alias'])) ? $tk . "." . $k : $tv['alias'] . "." . $k;
						$col .= (empty($v['alias'])) ? "" : " " . $v['alias'];
					}
					else {
						$col = $k;
						$col .= (empty($v['alias'])) ? "" : " " . $v['alias'];
					}
					$select[] = $col;
					if(!empty($v['group_by'])) $group_by[] = (empty($tv['alias'])) ? $tk . "." . $k : $tv['alias'] . "." . $k;
					if(!empty($search) && (!isset($v['searchable']) || $v['searchable'])) $where_search[] = ((empty($tv['alias'])) ? $tk . "." . $k : $tv['alias'] . "." . $k) . " LIKE '%{$search}%'";
				}
			}
			
		}

		if(!empty($model['action'])) {
			foreach($model['action'] as $ak => $av) {
				if(array_key_exists($av['table'], $model['tables'])) {
					$t = (empty($model['tables'][$av['table']]['alias'])) ? $av['table'] : $av['table'] . " " . $model['tables'][$av['table']]['alias'];
				}
				else {
					$t = $av['table'];
					$table[] = $t;
				}
				
				$t_ref = end(explode(" ", $t));
				
				$col = $t_ref . "." . $av['col'];
				if(!in_array($col, $select)) $select[] = $col;
			}
		}

		if(!empty($request) && !empty($request['order'])) {
			foreach($request['order'] as $order) {
				$order_col = end(explode(" ", $select[$order['column']]));
				$order_by[] = array($order_col, $order['dir']);
			}
		}
		
		// PREPARE QUERY
		$CI = & get_instance();
		$CI->db->select(implode(", ", $select), $select_protect);
		$CI->db->from(implode(", ", $table));
		
		// SET JOINS
		if(empty($tv['joins'])) {
			// do nothing
		}
		else {
			foreach($model['tables'] as $tk => $tv) {
				$p_table = $tk;
				$p_col = $tv['pk'];
				if(!empty($tv['joins'])) foreach($tv['joins'] as $jk => $jv) {
					$f_table = $jv['table'];
					$f_col = $jv['fk'];
					$join_left = empty($model['tables'][$f_table]['alias']) ? $f_table : $model['tables'][$f_table]['alias'];
					$join_right = empty($model['tables'][$p_table]['alias']) ? $p_table : $model['tables'][$p_table]['alias'];
					$join_type = empty($jv['type']) ? "inner" : $jv['type'];
					$CI->db->join(((empty($tv['alias'])) ? $tk : $tk . " " . $tv['alias']), "{$join_left}.{$f_col} = {$join_right}.{$p_col}", $join_type);
				}
			}
		}
		
		// SET ORDER BY CLAUSE
		foreach($order_by as $v) {
			$CI->db->order_by($v[0], $v[1]);
		}
		
		// SET GROUP BY CLAUSE
		foreach($group_by as $v) {
			$CI->db->group_by($v);
		}
		
		// SET LIMIT AND OFFSET
		if(!empty($request)) {
			$CI->db->limit($request['length'], $request['start']);
		}
		
		// SET WHERE CLAUSE
		if(!empty($where_search)){
			$CI->db->where(implode(" OR ", $where_search));
		}
		
		// p($request, 1);
		// EXECUTE QUERY
		$q = $CI->db->get();
		// q(1);
		
		// IF A SPECIAL REQUEST WAS NOT PROVIDED, PREPARE REGULAR RESULT ARRAY AS OUTPUT
		if(empty($request)) {
			$result = $q->result_array();
		}
		// OR ELSE PREPARE A SPECIAL ARRAY AS OUTPUT (AS A JSON-COMPLIANT DATATABLE SOURCE)
		else {
			
			// APPEND AN EXTRA 'ACTION' COLUMN FOR ANY DATAMODELS THAT REQUIRE IT
			foreach($q->result_array() as $k => $v) {
				if(!empty($model['action'])) {
					$html = "<div class='btn-toolbar'>";
					foreach($model['action'] as $ak => $av) {
						if(!empty($v[$av['col']])) {
							$id = $ak;
							$class = $av['class'];
							$icon = $av['icon'];
							$url = $av['url'] . $v[$av['col']] . "/";
							$label = $av['label'];
							$html .= "<a id='{$id}' href='{$url}' class='{$class}'><i class='{$icon}'></i>{$label}</a>";
						}
					}
					$html .= "</div>";
					$v[] = $html;
				}
				$result[] = array_values($v);
			}
			
			// GET TOTAL RECORD COUNT
			$CI->db->from($table)->select(implode(", ", $select));
			foreach($tables as $k => $v) {
				$p_table = empty($model['tables'][explode(" ", $v)[0]]['alias']) ? explode(" ", $v)[0] : $model['tables'][explode(" ", $v)[0]]['alias'];
				$p_col = $model['tables'][explode(" ", $v)[0]]['pk'];
				foreach($model['tables'][explode(" ", $v)[0]]['joins'] as $jk => $jv){
					$f_table = (empty($model['tables'][$jv['table']]['alias']) ? $jv['table'] : $model['tables'][$jv['table']]['alias']);
					$f_col = $jv['fk'];
					$CI->db->join($v, "{$f_table}.{$f_col} = {$p_table}.{$p_col}");
				}
			}
			foreach($group_by as $v) {
				$CI->db->group_by($v);
			}
			$totalLength = $CI->db->get()->num_rows();
			// q(1);
			
			// GET FILTERED RECORD COUNT
			$CI->db->from($table)->select(implode(", ", $select));
			foreach($tables as $k => $v) {
				$p_table = empty($model['tables'][explode(" ", $v)[0]]['alias']) ? explode(" ", $v)[0] : $model['tables'][explode(" ", $v)[0]]['alias'];
				$p_col = $model['tables'][explode(" ", $v)[0]]['pk'];
				foreach($model['tables'][explode(" ", $v)[0]]['joins'] as $jk => $jv){
					$f_table = (empty($model['tables'][$jv['table']]['alias']) ? $jv['table'] : $model['tables'][$jv['table']]['alias']);
					$f_col = $jv['fk'];
					$CI->db->join($v, "{$f_table}.{$f_col} = {$p_table}.{$p_col}");
				}
			}
			foreach($group_by as $v) {
				$CI->db->group_by($v);
			}
			if(!empty($where_search)){
				$CI->db->where(implode(" OR ", $where_search));
			}
			$filterLength = $CI->db->get()->num_rows();
			
			// ASSEMBLE ALL OUTPUT TO ARRAY
			$result = array(
				'draw' => isset ( $request['draw'] ) ? intval( $request['draw'] ) : 0,
				'recordsTotal' => $totalLength,
				'recordsFiltered' => $filterLength,
				'data' => $result	
			);
		}
		// p($result);
		return $result;
	}
	
	function readModelFields($model = array()) {
		if(empty($model['tables'])) return false;
		
		$result = array();

		foreach($model['tables'] as $tk => $tv) {

			foreach($tv['cols'] as $k => $v) {
				if(empty($v['list'])) continue;
				$result[]['label'] = empty($v['label']) ? ucfirst($k) : $v['label'];
			}
		}
		
		if(!empty($model['action'])) { array_push($result, array('label' => 'Action', 'action' => TRUE)); }
		return $result;
	}
	
	function readModelForm($model = array(), $read = array()) {
		
		$CI = & get_instance();
		
		$value = "";
		
		// todo: edit project
		if(empty($model['tables'])) return false;

		$result = array();

		$result['cancel_url'] = isset($model['meta']['cancel_url']) ? $model['meta']['cancel_url'] : SITE_URL;
		$result['scripts'] = isset($model['meta']['edit_script']) ? $model['meta']['edit_script'] : "";
		
		foreach($model['tables'] as $tk => $tv) {

			foreach($tv['cols'] as $k => $v) {
				if(empty($v['edit']) || empty($v['type'])) continue;
				
				// if read, get value
				if(!empty($read) && gettype($read) === "array") {
					
					$p_table = $tk;
					$p_col = $tv['pk'];
					
					$CI->db->from($tk);
					$CI->db->select("{$tk}.{$k}");
					
					if($v['type'] === "album") {
						$CI->db->select("{$tk}.meta");
						$CI->db->select("{$tk}.type");
						$CI->db->select("{$tk}.size");
					}

					// establish join relations (if any)
					if(empty($tv['joins'])) {
						$CI->db->where("{$p_table}.{$read[0]} = '{$read[1]}'");
					}
					else foreach($tv['joins'] as $jk => $jv ) {
						
						$f_table = $jv['table'];
						$f_col = $jv['fk'];
						
						$join_type = empty($jv['type']) ? "inner" : $jv['type'];
						
						$CI->db->join($f_table, "{$f_table}.{$f_col} = {$p_table}.{$p_col}", $join_type);
						$CI->db->where("{$f_table}.{$read[0]} = '{$read[1]}'");
					}
					
					$query = $CI->db->get()->row_array();
					
					// q();
					// p($query);
					
					$value = empty($query[$k]) ? "" : $query[$k];
					
					if($v['type'] === "album") {
						$size = ($query) ? $query['size'] : 0;
						$width = ($query) ? ((explode("/", $query['type'])[0] === "image") ? (explode("/", explode(":", $query['meta'])[1])[0]) . "px" : "") : "";
						$filename = ($query) ? $query[$k] : "";
						$fullpath = $v['absPath'] . $filename;
						$url = $v['urlPath'] . $filename;
						
						$v['initialPreview'] = array();
						$v['initialPreviewConfig'] = array();
						
						if($query && file_exists($fullpath)) {
							$v['initialPreview'][] = $url;
							$v['initialPreviewConfig'][] = array(
								'caption' => $filename , 
								'size' => $size,
								'width' => $width,
								'url' => $v['deleteUrl'],
								'key' => "{$filename}:" . urlencode($v['absPath']),
							);							
						}
					}
				}
				
				
				$result['fields'][$k] = array();
				$result['fields'][$k]['label'] = empty($v['label']) ? ucfirst($k) : $v['label'];
				$result['fields'][$k]['type'] = $v['type'];
				$result['fields'][$k]['label-width'] = 'col-lg-2';
				$result['fields'][$k]['field-width'] = 'col-lg-10';
				
				if($v['type'] === "album") {
					$result['fields'][$k]['initialPreview'] = empty($v['initialPreview']) ? array() : $v['initialPreview'];
					$result['fields'][$k]['initialPreviewConfig'] = empty($v['initialPreviewConfig']) ? array() : $v['initialPreviewConfig'];
				}
				else {
					$result['fields'][$k]['value'] = $value;
					
				}
				
				if(!empty($v['read_only'])) $result['fields'][$k]['readonly'] = 'readonly';
				if(!empty($v['required'])) $result['fields'][$k]['required'] = 'required';
				if(!empty($v['options'])) {
					switch(gettype($v['options'])) {
						case 'string': 
						{
							if(preg_match( '!\(([^\)]+)\)!', $v['options'], $match )) {
								$keyval = $match[1];
								
								$table = trim(explode("(", $v['options'])[0]);
								
								$key = trim(explode(":", $keyval)[0]);
								if(empty($key)) break;
								
								$val = trim(explode(":", $keyval)[1]);
								if(empty($val)) break;
								
								$data = $CI->db->select($key)->select($val)->from($table)->get()->result_array();
								
								$result['fields'][$k]['options'] = make_simple_array($data, "{$key},{$val}");
								
							}
							break;
						}
						case 'array': 
						{
							$result['fields'][$k]['options'] = $v['options'];
							break;
						}
					}
					
				}
			}
		}

		return $result;
	}
	
	function createModelData($model = array(), $submit = array()) {
		if(empty($model['tables']) || empty($submit['post'])) return false;
		
		p($submit, 1);
		
		$post = $submit['post'];
		$files = $submit['files'];
		
		$tables = array();
		$insert_pks = array();
		$CI = & get_instance();
		
		// set validation rules
		foreach($model['tables'] as $tk => $tv ) {
			foreach($tv['cols'] as $k => $v) {
				if(empty($v['edit'])) continue;
				$validations = "xss_clean";
				if(!empty($v['required'])) $validations .= "|required";
				if(!empty($v['unique'])) $validations .= "|is_unique[{$tk}.{$k}]";
				//todo: add collision proof - append number to a slug if it already exists, if attribute no_collision = TRUE is set
				$CI->form_validation->set_rules($k, ucfirst($k), $validations);
				$tables[$tk][] = $k;
			}
		}
		
		if($CI->form_validation->run()) {
			// insert all data
			foreach($model['tables'] as $tk => $tv ) {
				$values = array();
				if(!in_array($tk, array_keys($tables))) continue;
				foreach($tv['cols'] as $k => $v) {
					if(empty($v['edit'])) continue;
					if(in_array($k, $tables[$tk])) {
						if($v['type'] === 'album' || $v['type'] === 'file') {
							$path = empty($v['absPath']) ? "" : $v['absPath'];
							$uploadInfo = upload_files($path, $k);
							if(empty($uploadInfo['errors'])) {
								if(is_array($uploadInfo['saved']) && !empty($uploadInfo['saved'])) foreach($uploadInfo['saved'] as $info){
									$values[$k] = $info['file_name'];
									$values['text'] = NULL;
									$values['caption'] = NULL;
									$values['description'] = NULL;
									$values['meta'] =  ($info['is_image']) ? "dim:{$info['image_width']}/{$info['image_height']} " : "";
									$values['type'] = $info['file_type'];
									$values['size'] = intval($info['file_size']) * 1000;
									$values['uploaded'] = time();
								}
								else {
									$info = $uploadInfo['saved'];
									$values[$k] = $info['file_name'];
									$values['text'] = NULL;
									$values['caption'] = NULL;
									$values['description'] = NULL;
									$values['meta'] =  ($info['is_image']) ? "dim:{$info['image_width']}/{$info['image_height']} " : "";
									$values['type'] = $info['file_type'];
									$values['size'] = intval($info['file_size']) * 1000;
									$values['uploaded'] = time();
								}
							}
						}
						else {
							$values[$k] = $post[$k];
						}
					}
				}
				
				$CI->db->insert($tk, $values);
				$insert_pks[] = $CI->db->insert_id();
			}
			
			// update primary table to refer secondary data (if any)
			foreach($model['tables'] as $tk => $tv ) {

				if(!empty($tv['joins'])) foreach($tv['joins'] as $jk => $jv ) {
					
					if(empty($model['tables'][$jv['table']])) continue;

					$f_table = $jv['table'];
					$f_fk = $jv['fk'];
					$f_fk_val = array_combine(array_keys($model['tables']), $insert_pks)[$tk];
					$f_pk = $model['tables'][$f_table]['pk'];
					$f_pk_val = array_combine(array_keys($model['tables']), $insert_pks)[$jv['table']];
					$CI->db->where("$f_pk = '$f_pk_val'")->update($f_table, array($f_fk => $f_fk_val));
				}
			}
		}
		else {
			my_set_session('error_msg', validation_errors(), TRUE);
		}
	}
	
	function updateModelData($model = array(), $submit = array()) {
		if(empty($model['tables']) || empty($submit['post'])) return false;
		
		$post = $submit['post'];
		$files = $submit['files'];
		
		$tables = array();
		$insert_pks = array();
		$CI = & get_instance();
		
		// detect relations with other tables (if any) to get auxillery primary keys before updating all tables
		foreach($model['tables'] as $tk => $tv ) {

			if(!empty($tv['joins'])) foreach($tv['joins'] as $jk => $jv ) {
				
				if(empty($model['tables'][$jv['table']])) continue;
				
				$query = $CI->db->select($jv['fk'])->from($jv['table'])->where($model['tables'][$tk]['pk'], $post[$model['tables'][$tk]['pk']])->get()->row_array();
				$insert_pks[] = $query[$jv['fk']];
			}
			else {
				$insert_pks[] = $post[$model['tables'][$tk]['pk']];
			}
		}
		
		// set validation rules
		foreach($model['tables'] as $tk => $tv ) {
			foreach($tv['cols'] as $k => $v) {
				if(empty($v['edit'])) continue;
				$validations = "xss_clean";
				if(!empty($v['required'])) $validations .= "|required";
				if(!empty($v['unique'])) {
					$p_table = $tk;
					$p_col = $tv['pk'];
					$CI->db->select($k);
					$CI->db->from($tk);
					$CI->db->where($model['tables'][$tk]['pk'], array_combine(array_keys($model['tables']), $insert_pks)[$tk]);
					$query = $CI->db->get()->row_array();
					if(!empty($query[$k]) && ($post[$k] !== $query[$k])) $validations .= "|is_unique[{$tk}.{$k}]";
				}
				//todo: add collision proof - append number to a slug if it already exists, if attribute no_collision = TRUE is set
				$CI->form_validation->set_rules($k, ucfirst($k), $validations);
				$tables[$tk][] = $k;
			}
		}

		if($CI->form_validation->run()) {
			
			// update all data
			foreach($model['tables'] as $tk => $tv ) {
				$values = array();
				if(!in_array($tk, array_keys($tables))) continue;
				foreach($tv['cols'] as $k => $v) {
					if(empty($v['edit'])) continue;
					if(in_array($k, $tables[$tk])) {
						if($v['type'] === 'album' || $v['type'] === 'file') {
							$query = $CI->db->select($k)->from($tk)->where($model['tables'][$tk]['pk'], array_combine(array_keys($model['tables']), $insert_pks)[$tk])->get()->row_array();
							$force_name = (empty($query[$k])) ? "" : $query[$k];
							$path = empty($v['absPath']) ? "" : $v['absPath'];
							$uploadInfo = upload_files($path, $k, $force_name);
							if(empty($uploadInfo['errors'])) {
								// If there were no errors while uploading
								// Process the image and prepare data for storage
								if(is_array($uploadInfo['saved']) && !empty($uploadInfo['saved'])) foreach($uploadInfo['saved'] as $info){
									$values[$k] = $info['file_name'];
									$values['text'] = NULL;
									$values['caption'] = NULL;
									$values['description'] = NULL;
									$values['meta'] =  ($info['is_image']) ? "dim:{$info['image_width']}/{$info['image_height']} " : "";
									$values['type'] = $info['file_type'];
									$values['size'] = intval($info['file_size']) * 1000;
									$values['uploaded'] = time();
								}
								else {
									$info = $uploadInfo['saved'];
									$values[$k] = $info['file_name'];
									$values['text'] = NULL;
									$values['caption'] = NULL;
									$values['description'] = NULL;
									$values['meta'] =  ($info['is_image']) ? "dim:{$info['image_width']}/{$info['image_height']} " : "";
									$values['type'] = $info['file_type'];
									$values['size'] = intval($info['file_size']) * 1000;
									$values['uploaded'] = time();
								}
							}
							else {
								// If there were errors while uploading
								if(!empty($tv['joins'])) foreach($tv['joins'] as $jk => $jv ) {
									// If a join was involved, set the foreign table's value to NULL
									if(empty($model['tables'][$jv['table']])) continue;
									
									// ---------------------------------------------------------------------------- //
									// IMPORTANT:																	//
									// ---------------------------------------------------------------------------- //
									// THE FOLLOWING LINE THAT IS RESPONSIBLE FOR SETTING FOREIGN TABLE VALUES TO 	//
									// NULL, IS DISABLED. THIS IS TO ENSURE UPON FUTURE UPDATE WITH CORRECT VALUE, 	//
									// THE LINK WILL BE RETAINED.													//
									//																				//
									// ENABLE AT YOUR OWN RISK!														//
									// ---------------------------------------------------------------------------- //
									
									// $CI->db->where($model['tables'][$jv['table']]['pk'], array_combine(array_keys($model['tables']), $insert_pks)[$jv['table']])->update($jv['table'], array($jv['fk'] => NULL));
								}
								// Set error message
								my_set_session('error_msg', "There was a problem trying to upload selected file(s)", TRUE);
							}
						}
						else {
							$values[$k] = $post[$k];
						}
					}
				}

				if(count($values)) {
					$values[$model['tables'][$tk]['pk']] = array_combine(array_keys($model['tables']), $insert_pks)[$tk];
					if(empty($values[$model['tables'][$tk]['pk']])) {
						$CI->db->replace($tk, $values);
					}
					else $CI->db->where($model['tables'][$tk]['pk'], $values[$model['tables'][$tk]['pk']])->update($tk, $values);
				}
			}
		}
		else {
			my_set_session('error_msg', validation_errors(), TRUE);
		}
	}
	
    function hashid_encode($id, $enc = FALSE) {
        $hashid = base_convert($id, 10, 36);
        if($enc) $hashid = rawurlencode($hashid);
        return $hashid;
    }
    
    function hashid_decode($hashid, $enc = FALSE) {
        $id = base_convert($hashid, 36, 10);
        if($enc) $id = rawurldecode($id);
        return $id;
    }
    
	function crontest() {
		$CI = & get_instance();
		$CI->db->insert('tag', ['term_id' => time(), 'status' => 'N']);
	}
	
	function wishlist_alert_mail() {
		
		$is_mailed = FALSE;
		$template = 'wishlist-remainder';
		$request_signature = getRequestSignature();
		$exceptions = [
			'admin@giftrete.com', 
			'abarbosa@giftrete.com', 
			'admin@giftrete.com', 
			'deji@giftrete.com', 
			'gumtree@giftrete.com', 
			'rete@giftrete.com', 
			'subhajit@giftrete.com', 
			't.shodeinde@giftrete.com', 
			'thirdparty@giftrete.com', 
			'teaseabit99@giftrete.com', 
			'y.ayoola@giftrete.com'
		];
		
		$CI = & get_instance();
		$interval = $CI->db->select('mail_send_interval, wishlist_alert_interval')->get('setting')->row_array();
		$mail_freq = $interval['mail_send_interval'];
		$interval = $interval['wishlist_alert_interval'];
		$wishlists = $CI->db->where('modified < UNIX_TIMESTAMP( CURDATE() - INTERVAL ' . $interval . ' DAY )')->from('user_wishlist')->get()->result_array();

		foreach($wishlists as $wl) {
			if($wl['approved'] && $wl['deleted'] == 0) {
				
				$user = $CI->db->select('id, name, email')->where('id', $wl['user_id'])->from('user')->get()->row_array();
				if(in_array($user['email'], $exceptions)) continue;
				
				$CI->db->where('email', $user['email']);
				$CI->db->where('template', $template);
				$CI->db->where('ref_id', $wl['id']);
				$CI->db->where('type', 'WISHLIST');
				$CI->db->where('sent < ( NOW() - INTERVAL ' . $mail_freq . ' DAY )');
				$CI->db->from('alert_mail');
				
				if($CI->db->get()->num_rows()) continue;
				
				// construct mail
				
				$lastUpdated = ($wl['modified']) ? date('M/d/Y', $wl['modified']) : 'a long time ago';
				$url = VPATH . 'wishlist/items/' . $wl['user_id'] . '/';
				
				$site_logo 	= SITE_LOGO_PATH;
				$to 	 	= $user['email'];
				$subject 	= 'Your wishlist ' . $wl['name'] . ' is too old';

				$param = array(
					'{USER_NAME}' 	 				=> $user['name'],
					'{WISHLIST_NAME}' 				=> $wl['name'],
					'{WISHLIST_WARNING_INTERVAL}' 	=> $interval,
					'{WISHLIST_LAST_UPDATED}' 	 	=> $lastUpdated,
					'{WISHLIST_URL}' 	 			=> $url,
				);
				$CI->load->library("mailtemplete");

				/*sending mail to member who wants to contact*/
				$is_mailed = $CI->mailtemplete->send_mail('', $to, $template, $param);
				
				if($is_mailed) {
					$data = ['email' => $user['email'], 'template' => $template, 'ref_id' => $wl['id'], 'type' => 'WISHLIST', 'sent' => time(), 'request_signature' => $request_signature];
					$CI->db->insert('alert_mail', $data);
				}
			}
		}
		
		return $is_mailed;
	}
	
	function product_alert_mail() {
		
		
		
		$is_mailed = FALSE;
		$template = 'product-remainder';
		$request_signature = getRequestSignature();
		$exceptions = [
			'admin@giftrete.com', 
			'abarbosa@giftrete.com', 
			'admin@giftrete.com', 
			'deji@giftrete.com', 
			'gumtree@giftrete.com', 
			'rete@giftrete.com', 
			'subhajit@giftrete.com', 
			't.shodeinde@giftrete.com', 
			'thirdparty@giftrete.com', 
			'teaseabit99@giftrete.com', 
			'y.ayoola@giftrete.com'
		];
		
		
		$CI = & get_instance();
		$interval = $CI->db->select('mail_send_interval, product_alert_interval, product_expiry_interval')->get('setting')->row_array();
		$expiry = $interval['product_expiry_interval'];
		$mail_freq = $interval['mail_send_interval'];
		$interval = $interval['product_alert_interval'];
		
		$where = 'updatedon < ( NOW() - INTERVAL ' . $expiry . ' DAY ) AND user_id <> 227498';
		$update = ['public' => 'N', 'is_closed' => 'Y'];
		$CI->db->where($where)->update('user_product', $update);
		
		
		
		// $products = $CI->db->select('p.name, up.*')->from('user_product up')->where('up.updatedon < ( NOW() - INTERVAL ' . $interval . ' DAY )')->join('product p', 'p.id = up.product_id')->where('p.product_external_url IS NULL')->get()->result_array();
        $where = 'up.updatedon < ( NOW() - INTERVAL ' . $interval . ' DAY ) AND (p.product_external_url = "" or p.product_external_url IS NULL)';
		//$where = 'up.id = 284';
		$products = $CI->db->select('p.name, p.listing pl, up.*')->from('user_product up')->join('product p', 'p.id = up.product_id')->where($where)->get()->result_array();


		foreach($products as $up) {

			if($up['is_closed'] == 'N' && $up['public'] == 'Y' && $up['pl'] == 'Y') {

                 
				
				$user = $CI->db->select('id, name, email')->where('id', $up['user_id'])->from('user')->get()->row_array();
				if(in_array($user['email'], $exceptions)) continue;
				
				$CI->db->where('email', $user['email']);
				$CI->db->where('template', $template);
				$CI->db->where('ref_id', $up['id']);
				$CI->db->where('type', 'USER_PRODUCT');
				$CI->db->where('sent < ( NOW() - INTERVAL ' . $mail_freq . ' DAY )');
				$CI->db->from('alert_mail');
				
				if($CI->db->get()->num_rows()) continue;
                // q();
                // p($up);
                // p($products,1);
				
				// construct mail
				
				$lastUpdated = ($up['updatedon']) ? date('M/d/Y', strtotime($up['updatedon'])) : 'a long time ago';
				
				$url = VPATH . 'products/details/' . $up['product_id'] . '/';
				
				$repost_url = VPATH . 'account/product_manage/repost/' . $up['product_id'] . '/';
				$delete_url = VPATH . 'account/product_manage/delete/' . $up['product_id'] . '/';
				$close_url = VPATH . 'account/product_manage/close/' . $up['product_id'] . '/';
				
				$site_logo 	= SITE_LOGO_PATH;
				$to 	 	= $user['email'];
				$subject 	= 'Your product ' . $up['name'] . ' is too old';

				$param = array(
					'{USER_NAME}' 	 				=> $user['name'],
					'{PRODUCT_NAME}' 				=> $up['name'],
					'{PRODUCT_WARNING_INTERVAL}' 	=> $interval,
					'{PRODUCT_EXPIRY}' 				=> $expiry,
					'{PRODUCT_LAST_UPDATED}' 	 	=> $lastUpdated,
					'{PRODUCT_URL}' 	 			=> $url,
					'{PRODUCT_REPOST_URL}' 	 		=> $repost_url,
					'{PRODUCT_CLOSE_URL}' 	 		=> $close_url,
					'{PRODUCT_DELETE_URL}' 	 		=> $delete_url,
				);

				//p($param,1);
				$CI->load->library("mailtemplete");

				/*sending mail to member who wants to contact*/
				$is_mailed = $CI->mailtemplete->send_mail('', $to, $template, $param);
				
				if($is_mailed) {
					$data = ['email' => $user['email'], 'template' => $template, 'ref_id' => $up['id'], 'type' => 'USER_PRODUCT', 'sent' => time(), 'request_signature' => $request_signature];
					//$CI->db->insert('alert_mail', $data);
				}
			}
		}
		
		return $is_mailed;
	}
	
	function smtpmail($mail = array(), $body = "This mail is empty!", $template = FALSE) {
		$status = FALSE;
		if(empty($mail)) return $status;
		$config = array();
		$config['protocol'] = "smtp";
		$config['smtp_host'] = "ssl://smtp.gmail.com";
		$config['smtp_port'] = "465";
		$config['smtp_user'] = "inforahulchatterjee1986@gmail.com"; 
		$config['smtp_pass'] = "rahul12345";
		$config['charset'] = "utf-8";
		$config['mailtype'] = "html";
		$config['newline'] = "\r\n";
		
		$CI = & get_instance();
		$CI->load->library('email', $config);
		$CI->email->set_newline("\r\n");

		// Set to, from, message, etc.
		$CI->email->from($mail['from'], $mail['from_name']);
		$CI->email->to($mail['to']); 

		$CI->email->subject($mail['subject']);
		
		$body = ($template) ? $CI->load->view($body, $mail['data'], TRUE) : $body;
		
        $CI->email->message($body);  

		$status = $CI->email->send(FALSE);
		//$CI->email->print_debugger(); die;
		return $status;
	}

	function setTerm($term = NULL) {
		$status = FALSE;
		if($term) {
			$data = array(
				'id' => NULL,
				'slug' => $term,
				'term' => $term,
			);
			$CI = & get_instance();
			$status = $CI->db->replace("term", $data);
		}
		return $status;
	}
	
	function validate_invite_rewards($_user_email, $_code, $traceroot = FALSE) {
		if(empty($_code) || empty($_user_email)) return FALSE;
		
		$CI = & get_instance();
		$code = $_code;
		
		if($CI->db->from("user_invites")->where("inv_email", $_user_email)->where("code", $code)->where("accepted", 0)->get()->num_rows()){

			$invite = $CI->db->from("user_invites")->where("inv_email", $_user_email)->where("code", $code)->where("accepted", 0)->get()->row_array();
			
			reward_invite($invite);
			
			// p($invite);
		}
		else {
			// p("fail"); q(1);
		}
	}
	
	function reward_invite($invite = array(), $traceroot = FALSE){
		
		if(empty($invite)) return FALSE;
		
		$CI = & get_instance();
		
		$invitee = $CI->db->where("email", $invite["inv_email"])->get("user")->row_array();
		$inviter = $CI->db->where("id", $invite["user_id"])->get("user")->row_array();
		
		$CI->db->where("id", $invite["id"])->update("user_invites", array("accepted" => time()));
		
		if(empty($invitee) || empty($inviter)) return FALSE;
		
		if(!empty($invite["group_id"])) {
			if($traceroot) {
				invoke_rp_rule("RP_GROUPJOIN_INVITER_INDIRECT", $inviter["id"]);
			}
			else {				
				invoke_rp_rule("RP_GROUPJOIN_INVITER_DIRECT", $inviter["id"]);
				invoke_rp_rule("RP_GROUPJOIN_INVITEE", $invitee["id"]);
				
				// REMOVE CODES FROM ALL OTHER INVITES FOR JOINING THE SAME GROUP TO PREVENT ABUSE
				$CI->db->where("group_id", $invite["group_id"])->where("inv_email", $invite["inv_email"])->update("user_invites", array("code" => ""));
			}
			
			$rootinvite = $CI->db->from("user_invites")->where("inv_email", $inviter["email"])->where("group_id IS NOT NULL")->where("accepted > 0")->get()->row_array();
			reward_invite($rootinvite, TRUE);
		}
		else if(!empty($invite["wishlist_id"])) {
			invoke_rp_rule("RP_WISHLIST_INVITEE", $invitee["id"]);
			invoke_rp_rule("RP_WISHLIST_INVITER", $inviter["id"]);
		}
		else {
			if($traceroot) {
				invoke_rp_rule("RP_SIGNUP_INVITER_INDIRECT", $inviter["id"]);
			}
			else {				
				invoke_rp_rule("RP_SIGNUP_INVITER_DIRECT", $inviter["id"]);
				invoke_rp_rule("RP_SIGNUP_INVITEE", $invitee["id"]);
				
				// REMOVE CODES FROM ALL OTHER INVITES FOR SIGNUP TO PREVENT ABUSE
				$CI->db->where("group_id", NULL)->where("wishlist_id", NULL)->where("inv_email", $invite["inv_email"])->update("user_invites", array("code" => ""));
			}
			
			$rootinvite = $CI->db->from("user_invites")->where("inv_email", $inviter["email"])->where("group_id", NULL)->where("wishlist_id", NULL)->where("accepted > 0")->get()->row_array();
			reward_invite($rootinvite, TRUE);
		}
		
		// p("----");
		// p($invitee);
		// p($inviter);
		// p($invite);
	}
	
	// REWARD POINT INVOKE
	function invoke_rp_rule($_rule, $_user_id) {
		$CI = & get_instance();
		
		// check if rule exists, if so get reward value
		$rule = $CI->db->select("value, limit, description")->from("reward_point_rules")->where("rule", $_rule)->where("status", "Y")->get()->row_array();
		
		// get userdata from id
		$user = $CI->db->select("id, email, reward_points")->from("user")->where("id", $_user_id)->get()->row_array();
		
		if(!empty($rule) && !empty($user)) {
			
			$rule_invokes = $CI->db->from("user_rp_log")->where("user_id", $user["id"])->where("rule", $_rule)->get()->num_rows();
			$new_reward_points = intval($user["reward_points"]) + intval($rule["value"]);
			
			if(intval($rule['limit']) === 0 || $rule_invokes < intval($rule['limit'])) {
				
				$request_signature = getRequestSignature();
				
				// set reward log for user
				$CI->db->insert("user_rp_log", array("user_id" => $user["id"], "rule" => $_rule, "rule_invoked" => time(), "rp_awarded" => $rule["value"], "request_signature" => $request_signature));
				
				// set reward point for user
				$CI->db->where("id", $user["id"])->update("user", array("reward_points" => $new_reward_points));
				
				// also invoke badge rule so that a badge may be awarded
				invoke_badge_rule("BG_HAVE_REWARD_POINTS_EG", $_user_id);
			}

		}
	}
	
	function invoke_badge_rule($_rule, $_user_id) {
		$CI = & get_instance();
		
		// check if rule key exists
		$rulekey = $CI->db->select("description")->from("badge_rule_keys")->where("rule", $_rule)->where("status", "Y")->get()->row_array();
		
		// get userdata from id
		$user = $CI->db->select("id, email, reward_points")->from("user")->where("id", $_user_id)->get()->row_array();
		
		// if rule key and user are valid
		if(!empty($rulekey) && !empty($user)) {
			
			// determine badge from matching rules
			switch($_rule) {
				case 'BG_HAVE_REWARD_POINTS_EG':
				{
					$res = $CI->db->select("br.badge_id, br.value")->from("badge_rules br")->join("badge b", "b.id = br.badge_id")->where("b.status", "Y")->where("br.rule", $_rule)->get();
					if($res->num_rows()){
						foreach($res->result_array() as $rule) {
							
							// if currently matched rule satisfies condition
							if(intval($user['reward_points']) >= intval($rule['value'])) {
								
								// try to award badge to user
								if(award_badge($rule['badge_id'], $_user_id)) {
									$request_signature = getRequestSignature();
									
									// set badge log for user if successful
									$CI->db->insert("user_badge_log", array("user_id" => $_user_id, "rule" => $_rule, "rule_invoked" => time(), "badge_awarded" => $rule['badge_id'], "request_signature" => $request_signature));
								}
							}
						}
					}
					break;
				}
			}
		}
	}
	
	function award_badge($badge_id, $user_id) {
		
		$status = FALSE;
		
		$CI = & get_instance();
		
		$badge = $CI->db->select("id, limit")->from("badge")->where("id", $badge_id)->get()->row_array();
		
		$badges = $CI->db->from("user_badges")->where("user_id", $user_id)->where("badge_id", $badge_id)->get()->num_rows();
			
		// if badge is valid and limit has not exceeded
		if($badge && (intval($badge['limit']) === 0 || $badges < intval($badge['limit']))) {

			// set badge for user
			$status = $CI->db->insert("user_badges", array("user_id" => $user_id, "badge_id" => $badge_id, "achieved" => time(), "accepted" => 0));
		}
		
		return $status;
	}
	
	function get_unaccepted_badges($user_id) {
		
		$CI = & get_instance();
		
		$CI->db->select("ub.id, ub.accepted, b.name, b.description, b.file_image");
		$CI->db->from("user_badges ub");
		$CI->db->join("badge b", "b.id = ub.badge_id");
		$CI->db->where("ub.user_id", $user_id);
		$CI->db->where("ub.accepted", 0);
		$CI->db->where("b.status", "Y");
		$CI->db->order_by("ub.id");
		$badges = $CI->db->get()->result_array();
		
		foreach($badges as &$badge) $badge['file_image'] = UPLOADS . 'badges/' . $badge['file_image'];
		
		return $badges;
	}
	
	// CREATES USER SESSION
	function login($user) {
		$CI = & get_instance();
		$CI->load->library('session');
		$CI->session->set_userdata(SESS_PRE . 'user_id', $user['id']);
		$CI->session->set_userdata(SESS_PRE . 'user_email', $user['email']);
        $CI->session->set_userdata(SESS_PRE . 'user_name', $user['name']);
        // $CI->session->set_userdata(SESS_PRE . 'reward_points', $user['reward_points']);
		//$CI->session->set_userdata(SESS_PRE . 'online_user', $user['online_user']);
		$CI->session->set_userdata(SESS_PRE . 'login_time', time());
        return 1;
    }
	
	// DESTROYS USER SESSION
	function logout() {
		$CI = & get_instance();
		$CI->load->library('session');
		$CI->session->unset_userdata(SESS_PRE . 'user_id');
		$CI->session->unset_userdata(SESS_PRE . 'user_email');
        $CI->session->unset_userdata(SESS_PRE . 'user_name');
        // $CI->session->unset_userdata(SESS_PRE . 'reward_points');
		//$CI->session->unset_userdata(SESS_PRE . 'online_user');
        $CI->session->unset_userdata(SESS_PRE . 'login_time');
		$CI->session->unset_userdata(SESS_PRE . 'log_id');
        return 1;
    }
	
	// CHECK IF USER HAS LOGGED IN
   // @PARAM REDIRECT : REDIRECT TO THIS URL ON FAIL/SUCCESS
   // @PARAM FAIL_ONLY : REDIRECTS ON FAILURE ONLY
   // RETURNS TRUE OR FALSE
   function loginCheck($redirect = FALSE, $fail_only = FALSE) {
		$CI = & get_instance();
		$CI->load->library('session');
	    $user_id = $CI->session->userdata(SESS_PRE . 'user_id');
		$valid = (isset($user_id) && $user_id != '' && $user_id != 0);
        if (!$valid && $redirect) redirect(VPATH . 'login');
        else if ($valid && $redirect && !$fail_only && $CI->session->userdata("current_url") && $CI->session->userdata("current_url") != site_url() . uri_string()) redirect($CI->session->userdata("current_url"));
		else return $valid;
    }
	
   // CHECK IF USER HAS SPECIFIC TYPE
   // @PARAM TYPE : SUCCESS IF USER MATCHES THIS TYPE
   // @PARAM REDIRECT : REDIRECT TO THIS URL ON FAIL/SUCCESS
   // RETURNS TRUE OR FALSE
   function typeCheck($type = 'C', $redirect = FALSE) {
		$CI = & get_instance();
		$CI->load->library('session');
	    $user_type = $CI->session->userdata(SESS_PRE . 'user_type');
		$valid = (isset($user_type) && $user_type == $type);
        if (!$valid && $redirect) redirect(VPATH . 'dashboard');
		else return $valid;
    }
	
	// SPECIAL FUNCTION TO REDIRECT THE USER TO THE LAST SAVED URL
	// OR HOME PAGE IF NO URL IS SAVED
	function redir_default() {
		$CI = & get_instance();
		$CI->load->library('session');
		// p($CI->session->userdata,1);
		// p($CI->session->userdata);
		// p($CI->session->userdata("current_url"), 1);
		
		if($CI->session->userdata("current_url")) redirect($CI->session->userdata("current_url"));
		else redirect(VPATH . 'home');
	}
   
    // HASHING PROC INTENDED FOR PASSWORDS
	// SHA512 / BCRYPT PREFERRED
	function phash($raw) {
		$hash = hash('sha512', $raw);
		return $hash;
	}
	
	// RANDOM RAW PASSWORD GENERATOR
	function random_password( $length = 8, $caps_only = FALSE, $symbols = FALSE) {
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		if(!$caps_only) $chars = "abcdefghijklmnopqrstuvwxyz" . $chars;
		if($symbols) $chars = "!@#$%^&*()_-=+;:,.?" . $chars;
		$password = substr( str_shuffle( $chars ), 0, $length );
		return $password;
	}

    function random_num_gen($length) {
        $random= "";
        srand((double)microtime()*1000000);

        // Add the special characters to $char_list if needed
        //$char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        // $char_list .= "abcdefghijklmnopqrstuvwxyz";

        $timenow = time();
        $laststring = substr($timenow,-3);

        $char_list = strrev($timenow);
        $char_list .= "1234567890";
        

        for($i = 0; $i < $length; $i++) {    
        $random .= substr($char_list,(rand()%(strlen($char_list))), 1);  
        } 
        $random .= $laststring;
        return $random;
    }

	function getFriends($user_id) {
		$queryFriendsByCommunity = 
		"
		SELECT gm.member_id id
		FROM os_group_member gm
		WHERE gm.group_id IN (
			SELECT gmx.group_id
			FROM os_group_member gmx
			WHERE gmx.member_id = {$user_id}
		)
		AND gm.member_id <> {$user_id}
		AND gm.member_status = 'Y'
		GROUP BY gm.member_id
		";
		
		$queryFriendsByWishlistShared = 
		"
		SELECT u.id FROM os_user_invites ui
		JOIN os_user u ON u.email = ui.inv_email
		WHERE ui.user_id = {$user_id}
		AND ui.wishlist_id IS NOT NULL
		AND ui.accepted > 0
		GROUP BY u.email
		";
		
		$queryFriendsByWebsiteInvite = 
		"
		SELECT u.id FROM os_user_invites ui
		JOIN os_user u ON u.email = ui.inv_email
		WHERE ui.user_id = {$user_id}
		AND ui.wishlist_id IS NULL
		AND ui.group_id IS NULL
		AND ui.accepted > 0
		GROUP BY u.email
		";
		
		$friendIds = [];
		
		$CI = & get_instance();
		$friendIds[] = array_column($CI->db->query($queryFriendsByCommunity)->result_array(), 'id');
		$friendIds[] = array_column($CI->db->query($queryFriendsByWishlistShared)->result_array(), 'id');
		$friendIds[] = array_column($CI->db->query($queryFriendsByWebsiteInvite)->result_array(), 'id');
		
		$ids = implode(", ", array_unique(array_merge($friendIds[0], $friendIds[1], $friendIds[2])));

		$users = ($ids) ? $CI->db->select("id, name")->where("id IN ({$ids})")->get("user")->result_array() : array();
		
		return $users;
	}
   
   
   
	// RULES FOR REPLACING {MACRO_WORDS} WITH VALID CONSTANTS / VARIABLES
	function macroParse($item, $key = null) {
		if(gettype($item) == 'array' && array_key_exists($key, $item)) {
			$item = str_replace("{VPATH}", VPATH, $item[$key]);
			$item = str_replace("{SITETITLE}", SITE_TITLE, $item[$key]);
		}
		else if(gettype($item) == 'string') {
			$item = str_replace("{VPATH}", VPATH, $item);
			$item = str_replace("{SITETITLE}", SITE_TITLE, $item);
		}
		return $item;
	}
	
	/*
	**
	**	$path			-	Absolute path where file will be uploaded
	**	$input_name		-	Name of the input to read file from after upload
	**	$file_name		-	OPTIONAL rename file to this after upload
	**	$filter			-	OPTIONAL allow uploading only these files
	**	$thumb			-	On TRUE create thumbnail
	**	$thumb_vector	-	OPTIONAL dimentions for created thumbnails
	**
	*/
	
	function upload_files($path, $input_name, $file_name = "", $filter = "*", $thumb = FALSE, $thumb_vector = array(100, 100))
    {
        
        $result = array(
            'saved' => "",
            'errors' => "",
            'path' => $path,
        );
		
        if(empty($_FILES)) { 
            $result['errors'] = "No file was received. Make sure the form can upload files."; 
            return $result; 
        }
		
		$CI = & get_instance();
		$CI->load->library('upload');
		
		$config['upload_path']    = $path;
		$config['overwrite']      = TRUE;
		$config['allowed_types']  = $filter;
        
		$files = $_FILES[$input_name];
		$fileCount = count($files['name']); 
        for($i = 0; $i < $fileCount; $i++) {

			$_FILES[$input_name]['name'] 		= (gettype($files['name']) == 'array') ? $files['name'][$i] 	: $files['name'];
			$_FILES[$input_name]['type'] 		= (gettype($files['name']) == 'array') ? $files['type'][$i] 	: $files['type'];
			$_FILES[$input_name]['tmp_name'] 	= (gettype($files['name']) == 'array') ? $files['tmp_name'][$i] : $files['tmp_name'];
			$_FILES[$input_name]['error'] 		= (gettype($files['name']) == 'array') ? $files['error'][$i] 	: $files['error'];
			$_FILES[$input_name]['size'] 		= (gettype($files['name']) == 'array') ? $files['size'][$i] 	: $files['size'];
			
			$config['file_name'] = empty($file_name) ? uniqid($i) : $file_name . (($i) ? "_" . $i : "");
			
			$CI->upload->initialize($config);
			if($CI->upload->do_upload($input_name)) 
			{
				$fileData = $CI->upload->data();

                //Added by Chandan: 20180124 : starts



                //Added by Chandan: 20180124 : Ends

				if(gettype($files['name']) == 'array') $result['saved'][] = $fileData;
				else $result['saved'] = $fileData;
			}
			else 
			{
				$result['errors'] = array('error' => $CI->upload->display_errors());
			}
			
		}
		
		//p($thumb);
		//p($result, 1);
		
		if($thumb && !empty($result['saved'])) {
			if(gettype($result['saved']) == 'array') {
				foreach($result['saved'] as $image) {
					gen_thumb($path . $image['file_name'], NULL, $thumb_vector);
				}
			}
			else gen_thumb($path . $result['saved']['file_name'], NULL, $thumb_vector);
		}
		
		return $result;
    }
	
	function delete_files($path, $file_name, $thumb = FALSE)
	{
		if($file_name == "" || $path == "") return FALSE;
		if(!file_exists($path . $file_name)) return FALSE;
		
		$unlink_path = $path . $file_name;
		chown($unlink_path, 666);
		@unlink($unlink_path);

		if($thumb) $this->delete_files($path, $this->getThumbName($file_name));
	}
	
	function gen_thumb($filepath, $savepath = NULL, $vector = array(), $background = FALSE) {
		if(!file_exists($filepath)) return FALSE;
		list($original_width, $original_height, $original_type) = getimagesize($filepath);
		
		$thumbnail_width = isset($vector[0]) ? $vector[0] : 100;
		$thumbnail_height = isset($vector[1]) ? $vector[1] : 100;
		
		if(empty($savepath)) 
		{
			$savepath = pathinfo($filepath, PATHINFO_DIRNAME) . '/' . getThumbName($filepath);
		}
		
		if ($original_width > $original_height) {
			$new_width = $thumbnail_width;
			$new_height = intval($original_height * $new_width / $original_width);
		} 
		else {
			$new_height = $thumbnail_height;
			$new_width = intval($original_width * $new_height / $original_height);
		}
		
		$dest_x = intval(($thumbnail_width - $new_width) / 2);
		$dest_y = intval(($thumbnail_height - $new_height) / 2);

		if ($original_type === 1) {
			$imgt = "ImageGIF";
			$imgcreatefrom = "ImageCreateFromGIF";
		} 
		else if ($original_type === 2) {
			$imgt = "ImageJPEG";
			$imgcreatefrom = "ImageCreateFromJPEG";
		} 
		else if ($original_type === 3) {
			$imgt = "ImagePNG";
			$imgcreatefrom = "ImageCreateFromPNG";
		} 
		else {
			return false;
		}

		$old_image = $imgcreatefrom($filepath);
		$new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height); // creates new image, but with a black background

		// figuring out the color for the background
		if(is_array($background) && count($background) === 3) {
		  list($red, $green, $blue) = $background;
		  $color = imagecolorallocate($new_image, $red, $green, $blue);
		  imagefill($new_image, 0, 0, $color);
		
		} 
		// apply transparent background only if is a png image
		else if($background === 'transparent' && $original_type === 3) {
		  imagesavealpha($new_image, TRUE);
		  $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
		  imagefill($new_image, 0, 0, $color);
		}

		imagecopyresampled($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
		$imgt($new_image, $savepath);
		return file_exists($savepath);
	}
	
	function getFile($id = NULL) {
		if(empty($id)) return NULL;
		$CI = & get_instance();
		$res = $CI->db->select("file")->from("file")->where("id = {$id}")->get()->row_array();
		return empty($res["file"]) ? NULL : $res["file"];
	}
	if (!function_exists('deleteFile')) {
        //echo $defult_path = ASITEFILES ."content/";

    	function deleteFile($id, $path = '') {
    		$file = getFile($id);
    		$filepath = $path . $file;
    		return (!empty($file) && file_exists($filepath)) ? unlink($filepath) : FALSE;
    	}
    }
       
	/* 
	** IMAGE VALIDATORS
	** FOLLOWING FUNCTIONS CHECKS IF FILES EXIST IN THEIR ABSOLUTE PATHS
	** IF THEY EXIST, THE FILE IS RETURNED ON THEIR VPATHS
	** OTHERWISE EITHER FALSE IS RETURNED OR A PLACEHOLDER IMAGE IS RETURNED
	*/

    function getProductImage($product_id=0, $file='', $thumb = FALSE) {
        $CI = & get_instance();

        $returnData = FALSE;
        $imagePath  = '';
        //p($file);
        $file = get_imageName_fromUrl($file);
        //p($file);
        //p($product_id);
        if($thumb)  { 
            $file = getThumbName($file);
        }
        $contentApath = ASITEFILES . "content/";
        $contentPath  = SITEFILES . "content/";
        $noImagePath  = ASSETS . "defaults/no-image.png";


        //!file_exists($contentApath. $file) && 
        if(!empty($file) && file_exists($contentApath . $file)) {   
            $imagePath = SITEFILES . "content/" . $file;
            // p('NORMAL-ENTER',1);
        }else if(!empty($product_id)){
            //Get product Category image
            $CI->db->select('*');
            $CI->db->from('product');
            $CI->db->where('id', $product_id);
            $productQu   = $CI->db->get();
            $productRes  = $productQu->row_array();
            $category_id = $productRes['category_id'];
            // p('CAT-ENTER :'.$category_id );

            //Category Image:
            $CI->db->select('pc.id, pc.term_id, f.file');
            $CI->db->from('category pc');
            $CI->db->join("file f", "pc.file_id = f.id","left");
            $CI->db->where('pc.id', $category_id);
            $categoryQu   = $CI->db->get();
            $categoryRes  = $categoryQu->row_array();
            //q();
            // p($categoryRes);
            if($categoryQu->num_rows() && !empty($categoryRes['file'])){
                $file = $categoryRes['file'];
            }
            
            if(!empty($file) && file_exists($contentApath . $file)){
                //If category Image Exists
                $imagePath = SITEFILES . "content/" . $file;
            }else{
                //Show default Images
                $imagePath = $noImagePath;
            }
            

        }else{
            // p('ELSE-ENTER',1);
            //default no image path
            $imagePath = $noImagePath;
        }

        $returnData = $imagePath;
        // p($returnData,1);
        return $returnData;
    }

	function getContentImage($file, $thumb = FALSE, $bool_only = FALSE) {
		$value = FALSE;
		if($thumb)  { 
			$file = getThumbName($file);
		}
		if(!empty($file) && file_exists(ASITEFILES . "content/" . $file)) $value = ($bool_only) ? TRUE : SITEFILES . "content/" . $file;
		else $value = ($bool_only) ? FALSE : ASSETS . "defaults/no-image.png";
		return $value;
	}
	
	function getContentSubDirImage($file, $subdir, $thumb = FALSE, $bool_only = FALSE) {
		$value = FALSE;
		if($thumb)  { 
			$file = getThumbName($file);
		}
		if(!empty($file) && file_exists(ASITEFILES . "content/" . $subdir . $file)) $value = ($bool_only) ? TRUE : SITEFILES . "content/" . $subdir . $file;
		else $value = ($bool_only) ? FALSE : ASSETS . "defaults/no-image.png";
		return $value;
	}
	
	function getGalleryImage($file, $thumb = FALSE, $bool_only = FALSE) {
		$value = FALSE;
		if($thumb)  { 
			$file = getThumbName($file);
		}
		if(!empty($file) && file_exists(ASITEFILES . "gallery/" . $file)) $value = ($bool_only) ? TRUE : SITEFILES . "gallery/" . $file;
		else $value = ($bool_only) ? FALSE : ASSETS . "defaults/no-image.png";
		return $value;
	}
	
	function getUserGalleryImage($file, $thumb = FALSE, $bool_only = FALSE) {
		$value = FALSE;
		if($thumb)  { 
			$file = getThumbName($file);
		}
		if(!empty($file) && file_exists(AUSERFILES . "gallery/" . $file)) $value = ($bool_only) ? TRUE : USERFILES . "gallery/" . $file;
		else $value = ($bool_only) ? FALSE : ASSETS . "defaults/no-image.png";
		return $value;
	}
	
	function getProfileImage($file, $thumb = FALSE, $bool_only = FALSE) {
		$value = FALSE;
		if($thumb)  { 
			$file = getThumbName($file);
		}
		if(!empty($file) && file_exists(AUSERFILES . "profile/" . $file)) $value = ($bool_only) ? TRUE : USERFILES . "profile/" . $file;
		else $value = ($bool_only) ? FALSE : ASSETS . "defaults/no-image.png";
		return $value;
	}
	
	function getLogoImage($file, $bool_only = FALSE) {
		$value = FALSE;
		if(!empty($file) && file_exists(ASITEFILES . "logo/" . $file)) $value = ($bool_only) ? TRUE : SITEFILES . "logo/" . $file;
		else $value = ($bool_only) ? FALSE : ASSETS . "defaults/no-image.gif";
		return $value;
	}
	
	function getSliderImage($file, $thumb = FALSE, $bool_only = FALSE) {
		$value = FALSE;
		if($thumb)  { 
			$file = getThumbName($file);
		}
		if(!empty($file) && file_exists(ASITEFILES . "slider/" . $file)) $value = ($bool_only) ? TRUE : SITEFILES . "slider/" . $file;
		else $value = ($bool_only) ? FALSE : ASSETS . "defaults/no-image.png";
		return $value;
	}
	
	function getPartnerImage($file, $thumb = FALSE, $bool_only = FALSE) {
		$value = FALSE;
		if($thumb)  { 
			$file = getThumbName($file);
		}
		if(!empty($file) && file_exists(ASITEFILES . "partners/" . $file)) $value = ($bool_only) ? TRUE : SITEFILES . "partners/" . $file;
		else $value = ($bool_only) ? FALSE : ASSETS . "defaults/no-image.png";
		return $value;
	}
	
	function getBackgroundImage($file, $thumb = FALSE, $bool_only = FALSE) {
		$value = FALSE;
		if($thumb)  { 
			$file = getThumbName($file);
		}
		if(!empty($file) && file_exists(ASITEFILES . "background/" . $file)) $value = ($bool_only) ? TRUE : SITEFILES . "background/" . $file;
		else $value = ($bool_only) ? FALSE : ASSETS . "defaults/no-image.png";
		return $value;
	}
	
	function getThumbName($filename) {
		return 'thumb_'.pathinfo($filename, PATHINFO_BASENAME);
	}
	
	function truncate($string, $limit = 64, $postfix = ' ...'){
		return (strlen($string) > $limit) ? substr($string, 0, $limit) . $postfix : $string;
	}
	
	function slugify($string, $replace = array(), $delimiter = '-') {
		if (!extension_loaded('iconv')) {
			throw new Exception('iconv module not loaded');
		}
		// Save the old locale and set the new locale to UTF-8
		$oldLocale = setlocale(LC_ALL, '0');
		setlocale(LC_ALL, 'en_US.UTF-8');
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		if (!empty($replace)) {
			$clean = str_replace((array) $replace, ' ', $clean);
		}
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower($clean);
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
		$clean = trim($clean, $delimiter);
		
		// Revert back to the old locale
		setlocale(LC_ALL, $oldLocale);
		return $clean;
	}
	
	/**
	 * Translates a number to a short alhanumeric version
	 *
	 * Translated any number up to 9007199254740992
	 * to a shorter version in letters e.g.:
	 * 9007199254740989 --> PpQXn7COf
	 *
	 * specifiying the second argument true, it will
	 * translate back e.g.:
	 * PpQXn7COf --> 9007199254740989
	 *
	 * this function is based on any2dec && dec2any by
	 * fragmer[at]mail[dot]ru
	 * see: http://nl3.php.net/manual/en/function.base-convert.php#52450
	 *
	 * If you want the alphaID to be at least 3 letter long, use the
	 * $pad_up = 3 argument
	 *
	 * In most cases this is better than totally random ID generators
	 * because this can easily avoid duplicate ID's.
	 * For example if you correlate the alpha ID to an auto incrementing ID
	 * in your database, you're done.
	 *
	 * @author  Kevin van Zonneveld &lt;kevin@vanzonneveld.net>
	 * @author  Simon Franz
	 * @author  Deadfish
	 * @author  SK83RJOSH
	 * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
	 * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
	 * @link    http://kevin.vanzonneveld.net/
	 *
	 * @param mixed   $in   String or long input to translate
	 * @param boolean $to_num  Reverses translation when true
	 * @param mixed   $pad_up  Number or boolean padds the result up to a specified length
	 * @param string  $pass_key Supplying a password makes it harder to calculate the original ID
	 *
	 * @return mixed string or long
	 */
	function alphaID($in, $to_num = false, $pad_up = false, $pass_key = null)
	{
	  $out   =   '';
	  $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	  $base  = strlen($index);

	  if ($pass_key !== null) {
		// Although this function's purpose is to just make the
		// ID short - and not so much secure,
		// with this patch by Simon Franz (http://blog.snaky.org/)
		// you can optionally supply a password to make it harder
		// to calculate the corresponding numeric ID

		for ($n = 0; $n < strlen($index); $n++) {
		  $i[] = substr($index, $n, 1);
		}

		$pass_hash = hash('sha256',$pass_key);
		$pass_hash = (strlen($pass_hash) < strlen($index) ? hash('sha512', $pass_key) : $pass_hash);

		for ($n = 0; $n < strlen($index); $n++) {
		  $p[] =  substr($pass_hash, $n, 1);
		}

		array_multisort($p, SORT_DESC, $i);
		$index = implode($i);
	  }

	  if ($to_num) {
		// Digital number  <<--  alphabet letter code
		$len = strlen($in) - 1;

		for ($t = $len; $t >= 0; $t--) {
		  $bcp = bcpow($base, $len - $t);
		  $out = $out + strpos($index, substr($in, $t, 1)) * $bcp;
		}

		if (is_numeric($pad_up)) {
		  $pad_up--;

		  if ($pad_up > 0) {
			$out -= pow($base, $pad_up);
		  }
		}
	  } else {
		// Digital number  -->>  alphabet letter code
		if (is_numeric($pad_up)) {
		  $pad_up--;

		  if ($pad_up > 0) {
			$in += pow($base, $pad_up);
		  }
		}

		for ($t = ($in != 0 ? floor(log($in, $base)) : 0); $t >= 0; $t--) {
		  $bcp = bcpow($base, $t);
		  $a   = floor($in / $bcp) % $base;
		  $out = $out . substr($index, $a, 1);
		  $in  = $in - ($a * $bcp);
		}
	  }

	  return $out;
	}


	function set_geocode_components($ref_id, $type, $data = array()) {
		if(intval($ref_id) && "string" === gettype($type) && !empty($data)) {
			$CI = & get_instance();
			
			$CI->db->where("ref_id", $ref_id)->where("type", $type)->delete("geocode_components");
			
			foreach($data as $k => $v) {
				$post["ref_id"] = $ref_id;
				$post["type"] = $type;
				$post["key"] = $k;
				$post["long_name"] = $v["long_name"];
				$post["short_name"] = $v["short_name"];
				$CI->db->insert("geocode_components", $post);
			}
		}
	}
	
	function getRequestSignature() {
		$client = get_client_ip();
		$time = $_SERVER["REQUEST_TIME_FLOAT"];
		return hash("md2", $client . $time);
	}
	
	// Function to get the client IP address
	function get_client_ip() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	function curl($_url, $_params) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $_url);
		// curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge($data["params"], array("origin" => "hgb3VAbX83ca978g3")));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $_params);
		$result = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		$curl_error = curl_error($ch);
		curl_close($ch);
		
		return $result;
	}
   
   // DEBUG PROCS
   
   // PRINTS AN OBJECT/VARIABLE/ARRAY
   // VAR_DUMPS IF STUFF EMPTY
   // @PARAM STUFF : STUFF TO PRINTS
   // @PARAM DIE : IF TRUE, DIE AFTER PRINT
   // @PARAM VAR_DUMP : IF TRUE, USE VAR_DUMP INSTEAD OF PRINT_R
   // @PARAM PRE_WRAP : IF TRUE, WRAP OUTPUT IN PRE / DEBUG MARKERS
   // RETURNS NOTHING
   function p($stuff = null, $die = FALSE, $var_dump = FALSE, $pre_wrap = TRUE)
   {
		$CI = & get_instance();
		// echo $CI->router->fetch_class();
		// echo $CI->router->fetch_method();
		if($pre_wrap) {
			echo '<pre>';
			echo "<b>DEBUG:</b><br />";
		}
		if($var_dump || !$stuff) var_dump($stuff); else print_r($stuff);
		if($pre_wrap) {
			echo '</pre>';
		}
		if($die) die;
   }
   
   // PRINTS LAST SQL QUERY EXECUTED
   // @PARAM DIE : IF TRUE, DIE AFTER PRINT
   // RETURNS NOTHING
   function q($die = FALSE)
   {
	   $CI = & get_instance();
	   $query = $CI->db->last_query();
	   $CI = & get_instance();
	   echo '<pre>';
	   echo "<b>LAST QUERY:</b><br />" . $query;
	   echo '</pre>';
	   if($die) die;
	   return $query;
   }
   
   function j($stuff, $exit = FALSE) {
	   echo json_encode($stuff);
	   if($exit) exit;
   }


/*|-----------------------------------------------------|
* | Used to Send Email through                          |
* | BY using this function: sendmail_by_thirdparty      |
* | @param type= message type                           |
* |  Added By: ChandaN Sharma                           |
* |-----------------------------------------------------|
*/
if (!function_exists('sendmail_by_thirdparty')) {

    function sendmail_by_thirdparty($mail_data=array()) {
        $returnData = false;

        if(!empty($mail_data)){
            $returnData = __sendmail_thirdparty_Mailin($mail_data);
        }
        return $returnData;
    }
}



/*
 *  #__sendmail_thirdparty_Mailin() for Mailin Third Party Mil Sender     
 *  #Multi Email Array Pattern
 *  [to] => Array(
                    [0] => Array( 'email' => 17@gmail.com, 'name' => test17)
                    [1] => Array( 'email' => 18@gmail.com, 'name' => test18 )
                )
 *
 *  Added By: ChandN Sharma
*/

if (!function_exists('__sendmail_thirdparty_Mailin')) {

    function __sendmail_thirdparty_Mailin($param=array() ) {

        //# RAW CODE:
        // $this->load->library('Mailin');
        // //$this->$mailin = new Mailin('admin@giftrete.com', '79bVBT6QFOSMm1Hg');
        // $this->mailin->
        // addTo('testpayment17@gmail.com', 'Test Gift Rete')->
        // setFrom('testpayment17@gmail.com', 'Test Gift Rete')->
        // setReplyTo('testpayment17@gmail.com','Test Gift Rete')->
        // setSubject('Enter the subject here')->
        // setText('Hello')->
        // setHtml('<strong>Hello</strong>');
        // $res = $this->mailin->send();


        $returnData = false;

        //For from Email
        $from = SITE_EMAIL_FROM;
        if(!empty($param) && !empty($param['from']) ){
            $from = $param['from'];
        }
         
        //For ReplyTO Email
        $replyTo = SITE_EMAIL_REPLYTO;
        if(!empty($param) && !empty($param['reply_to']) ){
            $replyTo = $param['reply_to'];
        }

        //For Subject Content
        $subject = '';
        if(!empty($param) && !empty($param['subject']) ){
            $subject = $param['subject'];
        }

        //For Message Content
        $message = '';
        if(!empty($param) && !empty($param['message']) ){
            $message = $param['message'];
        }

        //For Mail TO
        $to_emails = '';
        if(!empty($param) && !empty($param['to']) ){
            $to_emails = $param['to'];
        }

        //For Message Content Type; Normal=false, HTML=True
        $contentType_html = false;
        if(!empty($param) && !empty($param['content_type']) ){
            $contentType_html = $param['content_type'];
        }

        
        //Sending Mail Function Executes
        if(!empty($from) && !empty($to_emails) && !empty($message) ){
            $CI = &get_instance();

            $CI->load->library('Mailin');
            // $this->load->library('Mailin');

            //$this->$mailin = new Mailin('admin@giftrete.com', '79bVBT6QFOSMm1Hg');

            if(!is_array($to_emails)){

                // $CI->mailin->addTo($to_emails, '');
                $CI->mailin->setTo($to_emails, '');
                //p('----FNC---');
                //p($to_emails);
            }else{
                $count =1;
                foreach ($to_emails as $key => $value) {

                    if(is_array($value)){
                        $to_email = $value['email'];
                        $to_name  = (!empty($value['name']) ) ? $value['name'] : '';
                    }else{
                        $to_email = $value;
                        $to_name  =  '';
                    }
                    //p('----FNC---');
                    //p($to_email);

                    if($count==1){
                        $CI->mailin->setTo($to_email, $to_name);  
                    }else{
                       $CI->mailin->addTo($to_email, $to_name); 
                    }

                    
                    $count++;
                }
            }
            

            $CI->mailin->setFrom($from, SITE_TITLE);

            if(!empty($replyTo)){
                $CI->mailin->setReplyTo($replyTo, '');
            }
            
            if(defined('SITE_EMAIL_BCC')){
                $CI->mailin->setBcc(SITE_EMAIL_BCC);
            }
            
            $CI->mailin->setSubject($subject);
            
            if($contentType_html){
                $CI->mailin->setHtml($message);
            }else{
                $CI->mailin->setText($message);
            }
            // $CI->mailin->setText($message);
            // $CI->mailin->setHtml($message);
            
            
            $returnData = $CI->mailin->send();
        }

        return $returnData;
    }
}

//Get Site total user count
if(!function_exists('get_total_site_user_count')){
    function get_total_site_user_count(){
        $returnData = false;

        $tableName  = 'user';
        $param = '';
        $user_count = count_rows($tableName, $param);
        $returnData = $user_count;
        return $returnData; 
    }
}

//Get Site total user count
if(!function_exists('get_community_types_options')){
    function get_community_types_options(){
        $returnData = false;

        $tableName  = 'group_type';
        $param      = "gtype_status ='Y'";
        $group_types = getData_table($tableName, $param);
        $returnData = $group_types;
        return $returnData; 
    }
}

//Get Site total user count
if(!function_exists('get_amazon_category')){
    function get_amazon_category(){
        $returnData = false;

        $tableName  = 'amazon_categories';
        $param      = "status ='Y'";
        $group_types = getData_table($tableName, $param);
        $returnData = $group_types;
        return $returnData; 
    }
}

/*|-----------------------------------------------------------------|
* | Used For Seller Related Functions Starts                        |
* | Added By: ChandaN Sharma                                        |
* |-----------------------------------------------------------------|
*/

//For Seller Latest Products
// Currently Function Not Using , If useing anyone please mark...!!!
if(!function_exists('get_custom_seller_latest_products')){
    function get_custom_seller_latest_products($seller_id=''){
        $returnData = false;

        $CI = & get_instance();
        
        //p.*, up.*

        $select = " p.id as product_id,
                    p.name as product_name, p.name, p.category_id,  p.brand,
                    p.image, p.features, p.short_description, p.free as product_free,
                    p.listing, p.reviewing, p.featured,p.best_seller,
                    p.created as product_created,p.modified as product_modified,
                    p.stock,p.back_order,p.offer_type,p.in_stock as product_in_stock,p.product_location,
                    up.id as user_product_rowid,
                    up.user_id, up.regular, up.sale, up.free, up.in_stock, up.public, up.added";

        $CI->db->select($select);
        $CI->db->from('user_product up');
        $CI->db->join("product p", "p.id = up.product_id");
        $CI->db->where("p.listing = 'Y'");
        $CI->db->where("p.own_product_status = 'N'");
        $CI->db->where("up.added > " . strtotime("-12 week"));
        $CI->db->where('up.public','Y');
        $CI->db->where('up.group_id','0');

        //For Seller Information
        if(!empty($seller_id)){
            $CI->db->where("up.user_id = '$seller_id'");
        }

        $CI->db->limit(12);
        $CI->db->order_by("up.sale","asc");
        $CI->db->group_by("p.id");
        $product_query = $CI->db->get();
        


        $product_found  = $product_query->num_rows();
        if($product_found){
            $product_result = $product_query->result_array();
            

            //----- Modifying Product Data : Starts -----
            foreach($product_result as &$product) {

                $product['image'] = getContentImage($product['image']);
                $product['url']   = VPATH . "products/details/" . $product['product_id'] . "/";
                // $product['product_location'] = $this->products_model->get_custom_productLocation_by_cityname($product['product_location'])['custom'];
                $product['price'] = ($product['regular'] > $product['sale']) ? $product['sale'] : $product['regular'];
                $product['price'] = (floatval($product['price']) > 0.00) ? $product['price'] : "FREE";

            } 
            //----- Modifying Product Data : Ends   ----- 
            $returnData     = $product_result;
        }
        return $returnData; 
    }
}

/* Not using Now*/
if(!function_exists('___main_search_product_result')){
    function ___main_search_product_result($seller_id=null, $slug = '',$free = '',  $keywords = '',$category_id='', $price_ranges = '' , $featured = '', $best_seller = '', $count=null, $limit=null, $page=null, $offer_type='',$location='', $group_id=null, $show_latest_product=FALSE ) {

        $CI = & get_instance();

        if(!empty($page)){
            $offset = ($page - 1) * $limit;
        }else{
            $offset = 0;
        }
        

        $select = "*";

        $CI->db->select($select);
        $CI->db->from('product p');
        $CI->db->join('category c', 'c.id = p.category_id');
        $CI->db->join('term t', 't.id = c.term_id');
        $CI->db->join('user_product up', 'up.product_id = p.id');

        if (!empty($limit)) {
            $CI->db->limit($limit,$offset);   
        }

        $CI->db->where("p.listing = 'Y'");

        if (!empty($slug)) {
            $CI->db->where("t.slug = '$slug'");           
        }

        //For Free Products Offer Type
        if (!empty($offer_type)) {
            $CI->db->where("p.offer_type = '$offer_type'");           
        }

        //For Products location
        if (!empty($location)) {
            //$this->db->like('p.product_location', $location);         
            $CI->db->like('LOWER(p.product_location)', strtolower($location));
            //like('LOWER(' .$field. ')', strtolower($value))           
        }

        //For Searched Keywords 
        if(!empty($keywords)) { 
        
            $where = "(";
            foreach(explode("-", $keywords) as $keyword) {
                if(reset(explode("-", $keywords)) !== $keyword) $where .= " OR ";
                    $keyword1 = ucfirst($keyword);
                    $keyword2 = ucwords($keyword);      
                    $keyword3 = lcfirst($keyword);      
                    $keyword4 = strtoupper($keyword);
                    $keyword5 = strtolower($keyword);
                $where .= "p.name LIKE '%{$keyword}%' or p.name LIKE '%{$keyword1}%' or p.name LIKE '%{$keyword2}%'  or p.name LIKE '%{$keyword3}%'   or p.name LIKE '%{$keyword4}%'  or p.name LIKE '%{$keyword5}%'";
            }
            $where .= ")";
            $CI->db->where($where);
        }

        //For Price Ranges Option
        if(!empty($price_ranges)) {
            $where = "(";
            $range = explode(",", $price_ranges);
            if($range[0] === $range[1]) {
                $where .= "up.sale = {$range[0]}";
            }
            else {
                $where .= "up.sale >= {$range[0]} AND up.sale <= {$range[1]}";
            }
            /*
            foreach(   as $key => $price_range) {
                if( $key == 0) { 
                }
                if( $key == 1) { $where .= " And ";
                }
            }
            */
            $where .= ")";
            $CI->db->where($where);
        }   

        //For Best seller type
        if(!empty($best_seller)) {  
            $where = "(";
                if( $best_seller == 'on') { 
                $where .= "p.best_seller = 'Y'";
                }
            $where .= ")";
            $CI->db->where($where);
        }

        //for category_id
        if(!empty($category_id)) {  
            $where = "(";    
                $where .= "p.category_id = '$category_id'";
            $where .= ")";
            $CI->db->where($where);
        }

        //For Features Products
        if(!empty($featured)) { 
            $where = "(";
                if( $featured == 'on') { 
                $where .= "p.featured = 'Y'";
                }
            $where .= ")";
            $CI->db->where($where);
        }

        //For Free Products Options
        if(!empty($free)) { 
            $where = "(";
                if( $free == 'on') { 
                $where .= "p.own_product_status = 'Y'";
                }
            $where .= ")";
            $CI->db->where($where);
        } else {
            $where = "(";               
                $where .= "p.own_product_status = 'N'";
            $where .= ")";
            $CI->db->where($where);
        }

        //For Seller Information
        if(!empty($seller_id)){
            $CI->db->where("up.user_id = '$seller_id'");
        }

        //For Group Id
        if(!empty($group_id)){
            $CI->db->where("up.group_id = '$group_id'");
        }else{
            $CI->db->where("up.group_id = 0");
        }

        //For Latest Products
        if($show_latest_product){
            $CI->db->where("up.added > " . strtotime("-4 week"));
        }


        //For User Product as Public
        $CI->db->where("up.public = 'Y'");
        $CI->db->group_by("p.id");
        $CI->db->order_by("up.sale",'asc');
        
        if(!empty($count)) {
            return $CI->db->get()->num_rows();
        } else {
            return $CI->db->get()->result_array();
        }
    }
}



/*|-----------------------------------------------------------------|
* | Used For Upload the excel.csv file only  (dated:2017-11-16)     |
* | Added By: ChandaN Sharma <devchandansh@gmail.com>               |
* | Parameter @filename (filename of uploaded file)                 | 
* | Return Data in Array Format.                                    |
* |-----------------------------------------------------------------|
*/

if(!function_exists('upload_excel_file')){
    function upload_excel_file($filename=""){
        $returnData = array();
        $returnData['status']  = false;
        $returnData['message'] = '';
        $returnData['data']    = array();

        if(!empty($_FILES) && !empty($filename))
        {
            $posted_file = $_FILES;

            $getExcel_data = array();

            $posted_temp_filename = $_FILES[$filename]["tmp_name"]; // temp Name
            $posted_filename      = $_FILES[$filename]["name"];     //filename
            //file extension
            $file_ext = pathinfo($posted_filename, PATHINFO_EXTENSION); 
            //Allowed File type
            $allowed_files =  array('csv','CSV');

            //check uploaded file formats
            if(in_array( $file_ext, $allowed_files) ) {
                    
                if($_FILES[$filename]["size"] > 0)
                {       
                    //---------------------------
                    $file = fopen($posted_temp_filename, "r");
                    $count = 0;                                         
                    while (($eachRowData = fgetcsv($file, 10000, ",")) !== FALSE)
                    {
                        $count++;
                        $getExcel_data[] = $eachRowData;
                    }
                    //--------------------------

                    //Set returned Success Data 
                    if($count >0){
                        $returnData['status'] = true;
                        $returnData['message']= '';
                        $returnData['data']   = $getExcel_data;
                    }
                }else{
                    //Error Message Data
                    $returnData['message']   = "File Must have a Size.";
                }

            }else{
                //Error Message Data
                $returnData['message']   = 'Invalid File: Please Upload CSV File';
            }   
        }else{
            //Error Message Data
            $returnData['message']   = 'File Not Found';
        }

        return $returnData;
    }
}

/*|-----------------------------------------------------|
* | Used to Fro custom City Country Da ta               |
* | Return data like: Kolkata-IN in array               |
* | developed by: Chandan Sharma                        |
* |-----------------------------------------------------|
*/
if (!function_exists('getCustom_filterSearch_location_city')) {
    function getCustom_filterSearch_location_city($cityData='', $makeSimpleArray=TRUE) {

        $CI = & get_instance();

        $returnData = array();

        $city_name    = '';
        $country_code = '';
        if(!empty($cityData)){
            $location = get_custom_city_country_data($cityData);
            $city_name    = $location['city'];
            $country_code = $location['country'];
        }
        

        $CI->db->select(" CONCAT(c.name,'-',co.code) as id,CONCAT(c.name,' ,',s.name,' ,',co.name) as text",FALSE);

        $CI->db->from("city c");
        $CI->db->join('state s', 's.id=c.state_id', 'left');
        $CI->db->join('countries co', 'co.id=s.country_id', 'left');
        $CI->db->where('c.status','Y');

        //For Country
        if(!empty($country_code)){
            $CI->db->where('co.code',$country_code);
        }

        //For City Name
        if(!empty($city_name)){
           $CI->db->like('c.name',$city_name); 
        }
        $CI->db->order_by('c.id', 'ASC');
        
    
        // $CI->db->limit($limit,$offset);
        $query = $CI->db->get();
        $query_found = $query->num_rows();

        $locationArray = array();

        if($query_found){
            $query_result = $query->result_array();

            if(isset($makeSimpleArray) && $makeSimpleArray){
                foreach ($query_result as $key => $value) {
                    $cKey   = $value['id'];
                    $cValue = $value['text'];
                    $locationArray[$cKey] = $cValue;
                }

                $returnData = $locationArray;
            }else{
                $returnData = $query_result;
            }      
        }

        return $returnData;
    }
}


// Helper Function For Customization Of Raw GeoCode Data::
if (!function_exists('custom_geocode_data')){ 
    function custom_geocode_data($geocode_data = array()){

        //Default Returned Data::
        $returnData = array();
        $returnData['location'] = array('lat' => 0, 'lng' => 0 );
        $returnData['address']  = array(
                                    'formatted' => '',
                                    'components' => array()
                                    );
        $returnData['viewport'] = array(
                                    'ne' => array('lat' => 0,'lng' => 0 ),
                                    'sw' => array('lat' => 0,'lng' => 0 )
                                    );

        $returnData['custom_location'] = array('seg1' => '', 'seg2' => '' );

        /*
        window.result.location.lat = apiResults[0].geometry.location.lat;
        window.result.location.lng = apiResults[0].geometry.location.lng;
        
        window.result.viewport.ne.lat = apiResults[0].geometry.viewport.northeast.lat;
        window.result.viewport.ne.lng = apiResults[0].geometry.viewport.northeast.lng;
        window.result.viewport.sw.lat = apiResults[0].geometry.viewport.southwest.lat;
        window.result.viewport.sw.lng = apiResults[0].geometry.viewport.southwest.lng;
        
        window.result.address.formatted = apiResults[0].formatted_address;
        
        $.each(apiResults[0].address_components, function(acKey, acVal){
            
            let key = "";
            
            $.each(acVal.types, function(typeKey, typeVal){
                if(typeVal !== "political") { 
                    key = typeVal; 
                    return false;
                }
            });
            
            window.result.address.components[key] = { "long_name" : acVal.long_name, "short_name" : acVal.short_name };
        });
        */

        // Customizing the geocode data::
        if($geocode_data['status'] == 'OK' && count($geocode_data['results']) >0){
            $geocode_result = $geocode_data['results'];
            // p($geocode_result);

            //geometry data
            $geometry = $geocode_result[0]['geometry'];

            //Assigning Data
            $locationData = array(); 
            $locationData['lat'] = $geometry['location']['lat']; 
            $locationData['lng'] = $geometry['location']['lng']; 

            $viewportData = array(); 
            $viewportData['ne']['lat'] = $geometry['viewport']['northeast']['lat']; 
            $viewportData['ne']['lng'] = $geometry['viewport']['northeast']['lng'];
            $viewportData['sw']['lat'] = $geometry['viewport']['southwest']['lat']; 
            $viewportData['sw']['lng'] = $geometry['viewport']['southwest']['lng']; 

            $addressData    = array(); 
            $componentsData = array();
            $address_components = $geocode_result[0]['address_components'];
            if(count($address_components)){
                foreach ($address_components as $key => $eachOne) {

                    $custom_key = '';

                    $component_types = $eachOne['types'];
                    foreach ($component_types as $key => $eachType) {
                        if($eachType !== "political") { 
                            $custom_key = $eachType; 
                            break;
                        }
                    }

                    //Assigning Address Components::
                    $componentsData[$custom_key]['long_name'] = $eachOne['long_name'];
                    $componentsData[$custom_key]['short_name'] = $eachOne['short_name'];

                }
            }

            $addressData['formatted'] = $geocode_result[0]['formatted_address']; 
            $addressData['components']= $componentsData; 

            //Custom location Data::
            if(count($componentsData)){
                $segment1 = '';
                if(!empty($componentsData['locality'])){
                    $segment1 = $componentsData['locality']['long_name'];

                }else if(!empty($componentsData['sublocality'])){
                    $segment1 = $componentsData['sublocality']['long_name'];

                }else if(!empty($componentsData['administrative_area_level_2'])){
                    $segment1 = $componentsData['administrative_area_level_2']['long_name'];

                }else if(!empty($componentsData['administrative_area_level_1'])){
                    $segment1 = $componentsData['administrative_area_level_1']['long_name'];

                }

                $segment2 = '';
                if(!empty($componentsData['country'])){
                    $segment2 = $componentsData['country']['short_name'];

                }

                $custom_location['seg1'] = $segment1;
                $custom_location['seg2'] = $segment2;
            }



            //Assigning Final Return Data::
            $returnData['location']         = $locationData;
            $returnData['viewport']         = $viewportData;
            $returnData['address']          = $addressData;
            $returnData['custom_location']  = $custom_location;
        }
        return $returnData;
    }
}
?>