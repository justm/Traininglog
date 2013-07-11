<?php

/**
 * This is the model class for table "hr_zone".
 *
 * The followings are the available columns in table 'hr_zone':
 * @property integer $id
 * @property string $name
 * @property integer $min
 * @property integer $max
 * @property integer $id_user
 * @property integer $id_default
 *
 * The followings are the available model relations:
 * @property User $idUser
 * @property DefaultHrZone $idDefault
 */
class HrZone extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return HrZone the static model class
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
		return 'hr_zone';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('min, max, id_user', 'required'),
			array('min, max, id_user, id_default', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>30),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, min, max, id_user, id_default', 'safe', 'on'=>'search'),
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
			'idUser' => array(self::BELONGS_TO, 'User', 'id_user'),
			'idDefault' => array(self::BELONGS_TO, 'DefaultHrZone', 'id_default'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'min' => 'Min',
			'max' => 'Max',
			'id_user' => 'Id User',
			'id_default' => 'Id Default',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('min',$this->min);
		$criteria->compare('max',$this->max);
		$criteria->compare('id_user',$this->id_user);
		$criteria->compare('id_default',$this->id_default);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}