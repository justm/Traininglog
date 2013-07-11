<?php

/**
 * This is the model class for table "user_fitness".
 *
 * The followings are the available columns in table 'user_fitness':
 * @property integer $id
 * @property integer $weight
 * @property integer $heght
 * @property integer $max_hr
 * @property integer $rest_hr
 *
 * The followings are the available model relations:
 * @property User[] $users
 */
class UserFitness extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserFitness the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_fitness';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('weight, height, max_hr, rest_hr', 'numerical', 'integerOnly'=>true, 'message'=>'{attribute} must be a number'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, weight, height, max_hr, rest_hr', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'users' => array(self::HAS_MANY, 'User', 'id_user_fittness'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'weight' => Yii::t('user','Weight'),
			'height' => Yii::t('user','Height'),
			'max_hr' => Yii::t('user','Max pulse'),
			'rest_hr' => Yii::t('user','Rest pulse'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('weight',$this->weight);
		$criteria->compare('height',$this->heght);
		$criteria->compare('max_hr',$this->max_hr);
		$criteria->compare('rest_hr',$this->rest_hr);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}