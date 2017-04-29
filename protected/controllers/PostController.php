<?php

class PostController extends Controller {

	public $_post;

	public function filters() {
		return array_merge(parent::filters(), array(
			'checkAndSetUser + view, comments, delete, update, topComments, likes',
			'restoreAccount + restore',
		));
	}

	public function filterCheckAndSetUser($filterChain) {
		if(!$_GET['id'])
			$this->renderError("Invalid Data!");
		else {
			$this->_post = Post::model()->active()->findByPk($_GET['id']);
			if(!$this->_post)
				$this->renderError("Invalid Data!");			
		}
		$filterChain->run();
	}



	public function filterRestoreAccount($filterChain) {
		if(!$_GET['id'])
			$this->renderError("Invalid Data!");
		else {

			$this->_post = Post::model()->deactivated()->findByPk($_GET['id']);
			if(!$this->_post)
				$this->renderError("Invalid Data!");			
		}
		$filterChain->run();
	}



	public function actionCreate() {
		if(isset($_GET['Post'])) {
			$post = Post::create($_GET['Post']);
			if(!$post->errors) {
				$this->renderSuccess(array('post_id'=>$post->id,'content'=>$post->content));
			} else {
				$this->renderError($this->getErrorMessageFromModelErrors($post));
			}
		} else {
			$this->renderError('Please send post data!!');
		}
	}



	public function actionView($id) {
		$this->renderSuccess(array('status'=>'SUCCESS', 'id'=>$this->_post->id,'content'=>$this->_post->content));
	}



	public function actionNewsfeeds() {
		$posts = Post::model()->active()->findAll(array('order'=>'created_at DESC', 'limit'=>10));
		if(!$posts) {
			$this->renderError('There is no posts to show');
		}
		else{
			$posts_data = array();
			foreach ($posts as $post) {
				$posts_data[] = array('id'=>$post->id, 'content'=>$post->content, 'user_name'=>$post->user->name);				
			}
			$this->renderSuccess(array('posts_data'=> $posts_data));
		}
	}

	public function actionNewsfeedsCached() {
		$posts = Yii::app()->cache->get('post_newsfeeds');
		if(!$posts) { //Data not found in cache
			$posts = Post::model()->active()->findAll(array('order'=>'created_at DESC', 'limit'=>10));
			if($posts) {
				Yii::app()->cache->set('post_newsfeeds', $posts, 60);
			}
		}

		if(!$posts) {
			$this->renderError('There is no posts to show');
		} else {
			$posts_data = array();
			foreach ($posts as $post) {
				$posts_data[] = array('id'=>$post->id, 'content'=>$post->content, 'user_name'=>$post->user->name);
			}
			$this->renderSuccess(array('posts_data'=> $posts_data));
		}
	}

	public function actionNewsfeedsWithReplication() {
		Yii::app()->params['use_slave'] = true;
		$posts = Post::model()->active()->findAll(array('order'=>'created_at DESC', 'limit'=>10));
		Yii::app()->params['use_slave'] = false;
		if(!$posts) {
			$this->renderError('There is no posts to show');
		}
		else{
			$posts_data = array();
			foreach ($posts as $post) {
				$posts_data[] = array('id'=>$post->id, 'content'=>$post->content, 'user_name'=>$post->user->name);				
			}
			$this->renderSuccess(array('posts_data'=> $posts_data));
		}
	}

	public function actionSearch($str) {
		$posts = Post::model()->active()->findAll(array('condition'=> "content LIKE :str", 'params'=> array('str'=>"%$str%")));
		if(!$posts) {

			$this->renderError('There is no posts to show which has '.$str);
		}
		else{
			$posts_data = array();
			foreach ($posts as $post) {
				$posts_data[] = array('id'=>$post->id, 'content'=>$post->content);
			}
			$this->renderSuccess(array('status'=> 'SUCCESS', 'posts_data'=> $posts_data,));
		}
	}


	public function actionTopComments($id) {
		$comments_data = array();
		foreach ($this->_post->comments(array('scopes'=>'active', 'order'=>'created_at DESC', 'limit'=>5)) as $comment) {
			$comments_data[] = array('user_name'=>$comment->user->name, 'content'=>$comment->content, 'created_at'=>$comment->created_at);
		}
		$this->renderSuccess(array('comments'=>$comments_data));
	}

	public function actionComments($id) {
		$comments_data = array();
		foreach ($this->_post->comments(array('scopes'=>'active')) as $comment) {
			$comments_data[] = array('user_id'=>$comment->user_id, 'user_name'=>$comment->user->name, 'content'=>$comment->content);
		}
		$this->renderSuccess(array('comments'=>$comments_data));
	}


	public function actionLikes($id){

		$likes_data = array();
		foreach ($this->_post->likes(array('scopes'=>'active')) as $like) {
			$likes_data[] = array('user_name'=>$like->user->name,);
		}
		$this->renderSuccess(array('No_of_Likes'=>count($likes_data), 'likes'=>$likes_data));

	}


	public function actionDelete($id) {
		$this->_post->deactivate();
		$this->renderSuccess(array('message'=>'Post Deleted Successfully'));
	}



	public function actionRestore($id) {
		$this->_post->activate();
		$this->renderSuccess(array('message'=>'Post Activated Successfully'));
	}



	public function actionUpdate($str, $id){
		$this->_post->content = $str;
		$this->_post->save();
		$this->renderSuccess(array('message'=>'Updated Successfully'));
	}
}