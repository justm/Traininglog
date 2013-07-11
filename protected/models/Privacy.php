<?php

/**
 * This is the model class for table "privacy".
 *
 * The followings are the available columns in table 'privacy':
 * @property integer $id
 * @property integer $lastname
 * @property integer $email
 * @property integer $phone
 * @property integer $street
 * @property integer $city
 * @property integer $country
 * @property integer $birthday
 * @property integer $weight
 * @property integer $height
 * @property integer $max_hr
 * @property integer $rest_hr
 * @property integer $basic_tr_data
 *
 * The followings are the available model relations:
 * @property User[] $users
 */
class Privacy extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Privacy the static model class
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
		return 'privacy';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('lastname, email, phone, street, city, country, birthday, weight, height, max_hr, rest_hr, basic_tr_data', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, lastname, email, phone, street, city, country, birthday, weight, height, max_hr, rest_hr, basic_tr_data', 'safe', 'on'=>'search'),
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
			'users' => array(self::HAS_MANY, 'User', 'id_privacy'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'lastname' => Yii::t('privacy','Lastname'),
			'email' => Yii::t('privacy','Email'),
			'phone' => Yii::t('privacy','Phone'),
			'street' => Yii::t('privacy','Street'),
			'city' => Yii::t('privacy','City'),
			'country' => Yii::t('privacy','Country'),
			'birthday' => Yii::t('privacy','Birthday'),
			'weight' => Yii::t('privacy','Weight'),
			'height' => Yii::t('privacy','Height'),
			'max_hr' => Yii::t('privacy','Max pulse'),
			'rest_hr' => Yii::t('privacy','Rest pulse'),
			'basic_tr_data' => Yii::t('privacy','Table / values'),
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
		$criteria->compare('lastname',$this->lastname);
		$criteria->compare('email',$this->email);
		$criteria->compare('phone',$this->phone);
		$criteria->compare('street',$this->street);
		$criteria->compare('city',$this->city);
		$criteria->compare('country',$this->country);
		$criteria->compare('birthday',$this->birthday);
		$criteria->compare('weight',$this->weight);
		$criteria->compare('height',$this->height);
		$criteria->compare('max_hr',$this->max_hr);
		$criteria->compare('rest_hr',$this->rest_hr);
		$criteria->compare('basic_tr_data',$this->basic_tr_data);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}