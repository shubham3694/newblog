<?php
class Controller extends CController {

	public $_user;

	public function filters() {
		return array(
			'setCurrentUser',
			'logCurrentUserActivity',
		);
	}

	public function filterSetCurrentUser($filterChain) {
		$this->_user = User::model()->findByPk(3);
		$filterChain->run();
	}

	public function filterLogCurrentUserActivity($filterChain) {
		$this->_user->updateLastRequestAt();
		$filterChain->run();
	}

	public function getErrorMessageFromModelErrors($model, $implode_by='<br />') {
		$messages = array();
		foreach($model->errors as $error)
			$messages[] = $error[0];
		return implode($implode_by, $messages);
	}

	public function renderSuccess($data) {
		echo CJSON::encode(array_merge(array('status'=>'SUCCESS'), $data));
		exit();
	}

	public function renderError($error_message) {
		echo CJSON::encode(array('status'=>'ERROR', 'message'=>$error_message));
		exit();
	}

}