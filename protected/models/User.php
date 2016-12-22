<?php
/* @property string $id
/* @property string $name
/* @property string $email
/* @property string $password
/* @property integer $status
/* @property integer $created_at
/* @property integer $updated_at
*/
class User extends CActiveRecord {

	const STATUS_ACTIVE = 1;
	const STATUS_DEACTIVATED = 2;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'user';
	}

	public function rules() {
		return array(
			array('name, email, password', 'required'),
			array('status, created_at, updated_at', 'numerical', 'integerOnly'=>true),
			array('name, email, password', 'length', 'max'=>255),
			);
	}

	public function relations() {
		return array(
			'posts' => 			array(self::HAS_MANY, 'Post', 'user_id'),
			'comments' => 		array(self::HAS_MANY, 'Comment', 'user_id'),
			'likes' => 			array(self::HAS_MANY, 'Like','user_id'),
			'posts_count' =>	array(self::STAT, 'Post', 'user_id'),
			'comments_count' => array(self::STAT, 'Comment', 'user_id'),
			'likes_count' => 	array(self::STAT, 'Like', 'user_id'),
			);
	}

	public function scopes() {
		return array(
			'active'=>array('condition'=>"status = :status_active", 'params'=>array('status_active'=>self::STATUS_ACTIVE)),
			'deactivated'=>array('condition'=>"status = :status_deactivated", 'params'=>array('status_deactivated'=>self::STATUS_DEACTIVATED)),
			);
	}

	public function deactivate($id) {
		$this->status = 2;
	    $this->save();
	}

	public function activate($id) {
		$this->status = 1;
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
		$model = new User;
		$model->attributes = $attributes;
		$model->save();
		return $model;
	}

}