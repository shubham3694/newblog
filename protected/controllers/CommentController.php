<?php
class CommentController extends Controller {

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
    public function actionCount($id){


        $counts = Comment::model()->findAllByAttributes(array('post_id'=>$id));
        $users_data = array();
        foreach ($counts as $count) {
            $users_data[] = array('user_id'=>$count->user_id,'user_name'=>$count->user->name);
        }
        echo "No. of Comments = ".count($counts)."  "."<br>";
        echo CJSON::encode(array('status'=>'SUCCESS',

                'users_data'=>$users_data,
            ));
    }



                                      //post_id
    public function actionTopComments($id){

        $comments = Comment::model()->findAll(array('condition'=>"post_id = :post_id", 'params'=>array('post_id'=>$id), 'order'=>'created_at DESC', 'limit'=>5));
        $comments_data = array();
        foreach($comments as $comment){
            $comments_data[] = array('user_name'=>$comment->user->name, 'content'=>$comment->content);
        }
        echo CJSON::encode(array('status'=>'SUCCESS', 'Comments_information'=>$comments_data));
    }

}
