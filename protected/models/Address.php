<?php

/**
 * This is the model class for table "address".
 *
 * The followings are the available columns in table 'address':
 * @property integer $id
 * @property string $city
 * @property string $street
 * @property string $zip
 * @property integer $id_country
 *
 * The followings are the available model relations:
 * @property Country $idCountry
 * @property User[] $users
 */
class Address extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Address the static model class
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
		return 'address';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_country', 'numerical', 'integerOnly'=>true),
			array('city, street', 'length', 'max'=>40),
			array('zip', 'length', 'max'=>15),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, city, street, zip, id_country', 'safe', 'on'=>'search'),
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
			'idCountry' => array(self::BELONGS_TO, 'Country', 'id_country'),
			'users' => array(self::HAS_MANY, 'User', 'id_address'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'city' => Yii::t('address','City'),
			'street' => Yii::t('address','Street'),
			'zip' => Yii::t('address','Postcode'),
			'id_country' => Yii::t('address','Country'),
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
		$criteria->compare('city',$this->city,true);
		$criteria->compare('street',$this->street,true);
		$criteria->compare('zip',$this->zip,true);
		$criteria->compare('id_country',$this->id_country);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}