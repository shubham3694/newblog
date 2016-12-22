<?php
class CommentController extends Controller {


    public $_pcomment;
    public function filters() {
        return array(
            'checkAndSetPost + count, topComments',  
            );
    }

    public function filterCheckAndSetPost($filterChain) {
        if(!$_GET['pid'])
            $this->renderError("Invalid Data!");
        else {

            $this->_pcomment = Comment::model()->active()->findAllByAttributes(array('post_id'=>$_GET['pid']));
            if(!$this->_pcomment)
                $this->renderError("Invalid Data!");            
        }

        $filterChain->run();
    }


    public function actionCreate() {
        if(isset($_POST['Comment'])) {
            $comment = Comment::create($_POST['Comment']);
            if(!$comment->errors) {
                $this->renderSuccess(array('post_id'=>$comment->post_id,'content'=>$comment->content,'user_id'=>$comment->user_id));
            } else {
                $this->renderError($this->getErrorMessageFromModelErrors($comment));
            }
        } else {
            $this->renderError('Please send post data!');
        }
    }

                               //post_id
    public function actionCount($pid){
        $users_data = array();
        
        foreach ($this->_pcomment as $comment) { 
                $users_data[] = array('user_id'=>$comment->user_id,'user_name'=>$comment->user->name);    
        }
        $this->renderSuccess(array(
            'No_of_Comments'=>count($this->_pcomment),
           'users_data'=>$users_data,
           ));
           

    }
                                //comment id
    public function actionDelete($id){

      $comment = Comment::model()->findByPk($id);
      $comment->status = Comment::STATUS_DEACTIVATED;
      $comment->save();
  }

  public function actionUpdate($str, $id){

      $comment = Comment::model()->findByPk($id);
      if($comment->status == 1) {
        $comment->content = $str;
        $comment->save();
    }
}

}
