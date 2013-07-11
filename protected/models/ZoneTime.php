<?php

/**
 * This is the model class for table "zone_time".
 *
 * The followings are the available columns in table 'zone_time':
 * @property integer $id
 * @property string $p_time
 * @property integer $id_trainingentry
 * @property integer $id_hrzone
 *
 * The followings are the available model relations:
 * @property TrainingEntry $idTrainingentry
 * @property HrZone $idHrzone
 */
class ZoneTime extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ZoneTime the static model class
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
		return 'zone_time';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('p_time, id_trainingentry, id_hrzone', 'required'),
			array('p_time, id_trainingentry, id_hrzone', 'numerical', 'integerOnly'=>true),
                        array('p_time', 'length', 'max'=>3),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, p_time, id_trainingentry, id_hrzone', 'safe', 'on'=>'search'),
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
			'idTrainingentry' => array(self::BELONGS_TO, 'TrainingEntry', 'id_trainingentry'),
			'idHrzone' => array(self::BELONGS_TO, 'HrZone', 'id_hrzone'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'p_time' => 'Time',
			'id_trainingentry' => 'Id Trainingentry',
			'id_hrzone' => 'Id Hrzone',
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
		$criteria->compare('p_time',$this->time,true);
		$criteria->compare('id_trainingentry',$this->id_trainingentry);
		$criteria->compare('id_hrzone',$this->id_hrzone);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}