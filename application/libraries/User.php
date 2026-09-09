<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User {

	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->model('User_Model');
	}

	public function create_model(array $data)
	{
		$model = $this->ci->user_model->get_instance();
		$model->email = $data['email'];
		$model->forename = $data['forename'];
		$model->surname = $data['surname'];
		$model->password = password_hash('passw0rd', PASSWORD_BCRYPT);
		$model->username = $data['username'];
		$model->gender = $data['gender'];
		return $model;
	}

	public function authenticate(array $user)
	{
		$model = $this->ci->user_model->get_instance();
		$model->email = $user['email'];
		$result = $this->ci->user_model->find_by_email( $model );
		if (!$result) return false;
		if (!password_verify($user['password'], $result->password)) return false;

		return $result;
	}

	public function save(User_Model $model)
	{
		return $model->save();
	}

	public function find_all()
	{
    return $this->ci->user_model->all();
  }

}
