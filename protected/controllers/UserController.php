<?php

class UserController extends Controller {

	public $_user;

	public function filters() {
		return array(
			'checkAndSetUser + login, profile, delete',
			'restoreAccount + restore',
			);
	}

	public function filterCheckAndSetUser($filterChain) {
		if(!$_GET['id'])
			$this->renderError("Invalid Data!");
		else {

			$this->_user = User::model()->active()->findByPk($_GET['id']);
			if(!$this->_user)
				$this->renderError("Invalid Data!");			
		}

		$filterChain->run();
	}


	public function filterRestoreAccount($filterChain){

		if(!$_GET['id'])
			$this->renderError("Invalid Data!");
		else {

			$this->_user = User::model()->deactivated()->findByPk($_GET['id']);
			if(!$this->_user)
				$this->renderError("Invalid Data!");			
		}

		$filterChain->run();

	}

	public function actionCreate() {
		if(isset($_POST['new_user'])) {
			$new_user = User::create($_POST['new_user']);
			if(!$new_user->errors) {
				$this->renderSuccess(array('user_id'=>$new_user->id, 'name'=>$new_user->name, 'password'=>$new_user->password, 'email'=>$new_user->email));
			} else {
				$this->renderError($this->getErrorMessageFromModelErrors($new_user));
			}
		} else {
			$this->renderError('Please send post data!');
		}
	}



	public function actionLogin($id) {	
		$this->renderSuccess(array('Message'=>'Login Successful', 'name'=>$this->_user->name, 'email'=>$this->_user->email));
	}


	public function actionProfile($id) {   
		$this->renderSuccess(array('name'=>$this->_user->name, 'email'=>$this->_user->email));
	}


	public function actionSearchProfile($name) {

		$users = User::model()->active()->findAllByAttributes(array('name'=>$name));
		if(!$users){
			$this->renderError('Account does not exits');
		}
		else{

			$users_profile = array();
			foreach($users as $user){
				$users_profile[] = array('user_id'=>$user->id, 'user_name'=>$user->name, 'email'=>$user->email);
			}   
			$this->renderSuccess(array('status'=>'SUCCESS', 'users_profile'=>$users_profile));
		}
	}


	public function actionDelete($id) {
		$this->_user->deactivate($id);
		$this->renderSuccess(array('user_id'=>$this->_user->id, 'Message'=>'User Account Deleted Successfully'));
	}

	public function actionRestore($id) {
		$this->_user->activate($id);
		$this->renderSuccess(array('user_id'=>$this->_user->id, 'Message'=>'User Account Activated Successfully'));

	}

}
