<?php
class UserController extends Controller {

    public function actionCreate() {
        if(isset($_POST['new_user'])) {
            $new_user = User::create($_POST['new_user']);
            if(!$new_user->errors) {
                $this->renderSuccess(array('user_id'=>$new_user->id,'name'=>$new_user->name,'password'=>$new_user->password,'email'=>$new_user->email));
            } else {
                $this->renderError($this->getErrorMessageFromModelErrors($new_user));
            }
        } else {
            $this->renderError('Please send post data!');
        }
    }



    public function actionLogin($id){
    	$user = User::model()->findbyPK($id);
    	if($user!=NULL){

            if($user->status ==1){
            echo CJSON::encode(array('user_name'=>$user->name,'status'=>'SUCCESS'));
            }
            else{
                echo "Account Deactivated";
            }
    	}
    	else{
    		echo "Account does not exist";
    	}
    }


    public function actionProfile($id) {
       $user = User::model()->findbyPK($id);
       if(!$user) {
           echo "Account does not exist.";
        }
       else {
          echo CJSON::encode(array('name'=>$user->name,'email'=>$user->email,'status'=>'SUCCESS'));
        }
    }


    public function actionSearchProfile($name){
        $users = User::model()->findAllByAttributes(array('name'=>$name));
        $users_profile = array();
        foreach($users as $user){
            if($user->status ==1){
            $users_profile[] = array('user_id'=>$user->id, 'user_name'=>$user->name, 'email'=>$user->email);
            }    
        }
        echo CJSON::encode(array('status'=>'SUCCESS', 'users_profile'=>$users_profile));
    }


    public function actionDelete($id){

      $user = User::model()->findByPk($id);
      $user->status = 2;
      $user->save();
    }

    public function actionRestore($id){

      $user = User::model()->findByPk($id);
      $user->status = 1;
      $user->save();
    }

}
