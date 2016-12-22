<?php

/* @property string $id
/* @property string $user_id
/* @property string $content
/* @property integer $status
/* @property integer $created_at
/* @property integer $updated_at
*/

class Post extends CActiveRecord {
	const STATUS_ACTIVE = 1;
	const STATUS_DEACTIVATED = 2;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'post';
	}

	public function rules() {
		return array(
			array('user_id, content', 'required'),
			array('status, created_at, updated_at', 'numerical', 'integerOnly'=>true),
			array('user_id', 'length', 'max'=>11),
			array('content', 'length', 'max'=>255),
			);
	}

	public function relations() {
		return array(
			'likes'=> 			array(self::HAS_MANY, 'Like', 'post_id'),
			'comments'=> 		array(self::HAS_MANY, 'Comment', 'post_id'),
			'comments_count'=> 	array(self::STAT, 'Comment', 'post_id'),
			'likes_count' => 	array(self::STAT, 'Like', 'post_id'),
			'user'=> 			array(self::BELONGS_TO, 'User', 'user_id'),
			);
	}

	public function scopes() {
		return array(
			'active'=>array('condition'=>"{$this->tableAlias}.status = :status_active", 'params'=>array('status_active'=>self::STATUS_ACTIVE)),
			'deactivated'=>array('condition'=>"status = :status_deactivated", 'params'=>array('status_deactivated'=>self::STATUS_DEACTIVATED)),
			);
	}


	public function deactivate() {
		$this->status = Post::STATUS_DEACTIVATED;
		$this->save();
	}

	public function activate() {
		$this->status = Post::STATUS_ACTIVE;
		$this->save();
	}

	public function beforeSave() {
		if($this->isNewRecord) {
			$this->status = self::STATUS_ACTIVE; 
			$this->created_at = time();
		}
		$this->updated_at = time();
		return parent::beforeSave();
	}


	public function updateColumns($column_value_array) {
		$column_value_array['updated_at'] = time();
		foreach($column_value_array as $column_name => $column_value)
			$this->$column_name = $column_value;
		$this->update(array_keys($column_value_array));
	}

	public static function create($attributes) {
		$model = new Post;
		$model->attributes = $attributes;
		$model->save();
		return $model;
	}
}