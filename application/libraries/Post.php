<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post {

    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->model('Post_Model');
        $this->ci->load->model('Category_Model');
        $this->ci->load->model('Item_Messages_Model');
            $this->ci->load->model('Review_Messages_Model');
    }

    public function create_model(array $data) {
        $model = $this->ci->Post_Model->get_instance();
      $model->userid = $data['userid']; 
        $model->title = $data['title']; 
               $model->groupid = $data['groupid']; 
        $model->description = $data['description']; 
        $model->categoryone = $data['categoryone']; 
        $model->categorytwo = $data['categorytwo']; 
        $model->imageurl = $data['imageurl']; 
             $model->imageurl2 = $data['imageurl2']; 
                  $model->imageurl3 = $data['imageurl3']; 
                       $model->imageurl4 = $data['imageurl4']; 
        $model->type = $data['type']; 
        $model->posteddate = $data['posteddate']; 
        $model->enddate = $data['enddate']; 
        $model->location = $data['location']; 
      $model->lat = $data['lat']; 
        $model->long = $data['long']; 
        $model->country = $data['country']; 
        $model->weight = $data['weight']; 
                $model->website = $data['website']; 
                            $model->status = $data['status']; 
        return $model;
    }

public function create_Category_Model(array $data) {
        $model = $this->ci->Category_Model->get_instance();
        $model->name = $data['name']; 
        $model->description = $data['description']; 
        $model->imageurl = $data['imageurl']; 
        $model->posteddate = $data['posteddate'];  
        return $model;
    }

public function Item_Messages_Model(array $data) {
        $model = $this->ci->Item_Messages_Model->get_instance();
          $model->sendbyid = $data['sendbyid']; 
        $model->replyid = $data['replyid']; 
        $model->ownerid = $data['ownerid']; 
        $model->itemid = $data['itemid'];  
          $model->messages = $data['messages'];  
            $model->posteddate = $data['posteddate'];  
        return $model;
    }

public function Review_Messages_Model(array $data) {
        $model = $this->ci->Review_Messages_Model->get_instance();
          $model->sendbyid = $data['sendbyid']; 
        $model->itemid = $data['itemid'];  
          $model->messages = $data['messages'];  
            $model->posteddate = $data['posteddate'];  
        return $model;
    }

    public function save(Post_Model $model) {
        return $model->save();
    }

            public function edit($postid,$title,$description,$categoryone,$categorytwo,$imageurl,$imageurl2,$imageurl3,$imageurl4, $type, $location,$country, $weight, $website) {
        return $this->ci->Post_Model->edit($postid,$title,$description,$categoryone,$categorytwo,$imageurl, $imageurl2, $imageurl3, $imageurl4, $type,  $location,$country, $weight, $website);
    }
     public function mobileedit($postid, $title, $description, $location,$weight, $website) {
        return $this->ci->Post_Model->mobileedit($postid, $title, $description, $location,$weight, $website);
    }
public function deletesingleitem($productid) {
    return $this->ci->Post_Model->delete_item($productid);
    }

public function deletecategorysingleitem($productid) {
    return $this->ci->Post_Model->deletecategory_item($productid);
    }


public function category_save(Post_Model $model) {
        return $model->category_save();
    }
    public function winner_update($winneruserid,$productid,$status) {
        return $this->ci->Post_Model->winner_update($winneruserid,$productid,$status);
    }
 public function updateprofile($userid,$name,$first,$last,$mobile,$about) {
        return $this->ci->Post_Model->updateprofile($userid,$name,$first,$last,$mobile,$about);
    }
 public function updateprofileimage($userid,$pic) {
        return $this->ci->Post_Model->updateprofileimage($userid,$pic);
    }

    public function approved_disapproved_all($productid,$status) {
        return $this->ci->Post_Model->approved_disapproved($productid,$status);
    }

public function getSingleuserPost_all($itemid){
    return $this->ci->Post_Model->getSingleuserPost_all($itemid);

}
public function getitemmessage($itemid,$userid,$my){
    return $this->ci->Post_Model->getitemmessage($itemid,$userid,$my);

}


public function getReviewAndTestimonyAndFeedback_all($itemid){
    return $this->ci->Post_Model->getReviewAndTestimonyAndFeedback_all($itemid);

}
public function getGivenAWantedItem_all($itemid){
    return $this->ci->Post_Model->getGivenAWantedItem_all($itemid);

}




public function getGivenAWantedItembyuserid_all($userid){
    return $this->ci->Post_Model->getGivenAWantedItembyuserid_all($userid);

}
public function getGivenAWantedItem_all2($itemid,$ownerid,$userid){
    return $this->ci->Post_Model->getGivenAWantedItem_all2($itemid,$ownerid,$userid);

}

public function getallcommentwithout($userid,$itemid){
    return $this->ci->Post_Model->getallcommentwithout($userid,$itemid);

}


public function getSingleuserdetail_all($userid){
    return $this->ci->Post_Model->getSingleuserdetail_all($userid);

}

public function getSingleusercategoryPost_all($itemid){
    return $this->ci->Post_Model->getSingleusercategoryPost_all($itemid);

}

public function getcategory_all(){
    return $this->ci->Post_Model->getcategory_all();

}


public function getSingleusergroupPost_all($itemid){
    return $this->ci->Post_Model->getSingleusergroupPost_all($itemid);

}

public function getmycommunity($userid){
    return $this->ci->Post_Model->getmycommunity($userid);

}




public function getAlluserPost_all($userid,$prod) {
        return $this->ci->Post_Model->getAlluserPost_all($userid,$prod);
}

public function getposty($type) {
        return $this->ci->Post_Model->getposty($type);
}

public function getcountry($name) {
        return $this->ci->Post_Model->getcountry($name);
}
public function getitemmessages($item){
   return $this->ci->Post_Model->getitemmessages($item);
}



    public function getpostbyfield($value,$field) {
        return $this->ci->Post_Model->getPostByField_all($value,$field);
    }

 public function getpostbycategory($value) {
        return $this->ci->Post_Model->getPostByFieldcategory_all($value);
 }



    public function getpostbyfieldallfield($value) {
        return $this->ci->Post_Model->getPostByField_all2($value);
    }


    public function getpostbyfieldallfield2($searchstring) {
        return $this->ci->Post_Model->getPostByField_all3($searchstring);
    }



   public function  find_all_close_item(){
        return $this->ci->Post_Model->all_closed();
    }

public function find_all_approved() {
        return $this->ci->Post_Model->all_approved();
    }
public function find_all_disapproved() {
        return $this->ci->Post_Model->all_disapproved();
    }

    public function find_all() {
        return $this->ci->Post_Model->all();
    }

public function newsletterfind_all() {
        return $this->ci->Post_Model->newsletterall();
    }



public function closed_item($productid) {
    return $this->ci->Post_Model->close_item($productid);
    }


public function productitemmessagesupdate($itemid,$cou){
     return $this->ci->Post_Model->productitemmessagesupdate($itemid,$cou);
}

public function updatedeliverydate($productid,$one,$two,$three){
     return $this->ci->Post_Model->updatedeliverydate($productid,$one,$two,$three);
}

public function updatedeliverydate2($productid,$val){
     return $this->ci->Post_Model->updatedeliverydate2($productid,$val);
}



    public function br() {
        return $this->ci->Post_Model->br();
    }

    public function getmygroup($userid){
    return $this->ci->Post_Model->getmygroup($userid);

    }

   public function getmygroupdetail($id){
    return $this->ci->Post_Model->getmygroupdetail($id);

    }



//MOBILE API

 public function getpostbycategorymobile($value,$offset,$lenght) {
        return $this->ci->Post_Model->getPostByFieldcategory_allmobile($value,$offset,$lenght);
 }




  public function getSingleUserAllFromFromEmail($email){
    return $this->ci->Post_Model->getSingleUserAllFromFromEmail($email);

    }
    

}