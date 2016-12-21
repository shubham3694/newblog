<?php
class PostController extends Controller {

    public function actionCreate() {
        if(isset($_POST['Posted'])) {
            $post = Post::create($_POST['Posted']);
            if(!$post->errors) {
                $this->renderSuccess(array('post_id'=>$post->id,'content'=>$post->content));
            } else {
                $this->renderError($this->getErrorMessageFromModelErrors($post));
            }
        } else {
            $this->renderError('Please send post data!');
        }
    }

                               //post_id
     public function actionView($id) {
       $view =  Post::model()->findByPk($id);
       if(!$view){
           echo 'This id does not exists';
       }   else {
                
                echo CJSON::encode(array('id'=>$view->id,'status'=>'SUCCESS','content'=>$view->content));
            }
    }
                              //user_id
    public function actionNews($id) {
      $posts = Post::model()->findAll(array('condition'=>"user_id != :user_id", 'params'=>array('user_id'=>$id), 'order'=>'created_at DESC', 'limit'=>10));
    	//$posts = Post::model()->findAllByAttributes(array('user_id'=>$id));
    	$posts_data = array();
    	foreach ($posts as $post) {
    		$posts_data[] = array('id'=>$post->id, 'content'=>$post->content);
    	}
    	echo CJSON::encode(array('status'=>'SUCCESS',
    		'posts_data'=>$posts_data,

    	));
    }


    public function actionSearch($str){


    	$posts = Post::model()->findAll(array('condition'=>"content LIKE :str", 'params'=>array('str'=>"%$str%")));
    	
    	//echo count($posts);
    	$posts_data = array();
        foreach ($posts as $post) {
            $posts_data[] = array('id'=>$post->id, 'content'=>$post->content);
        }
        echo CJSON::encode(array('status'=>'SUCCESS',

        		'posts_data'=>$posts_data,
        	));
    	
    }



                                  //post_id
    public function actionComments($id) {
     $post = Post::model()->findByPk($id);
     $comments = $post->comments;
       foreach ($comments as $comment) {
          echo CJSON::encode(array('user_id'=>$comment->user_id, 'content'=>$comment->content));
        }

    }

}