<?php

/**
 * This is the model class for table "training_entry".
 *
 * The followings are the available columns in table 'training_entry':
 * @property integer $id
 * @property string $duration
 * @property string $description
 * @property integer $feelings
 * @property integer $min_hr
 * @property integer $avg_hr
 * @property integer $max_hr
 * @property string $distance
 * @property string $avg_speed
 * @property integer $ascent
 * @property integer $max_altitude
 * @property integer $avg_watts
 * @property integer $max_watts
 * @property integer $id_activity
 * @property integer $id_user
 * @property integer $id_visibility
 * @property string $avg_pace
 * @property string $date
 * @property integer $id_label
 */
class TrainingEntry extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TrainingEntry the static model class
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
		return 'training_entry';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('duration, id_activity, id_user, id_visibility, date', 'required'),
			array('feelings, min_hr, avg_hr, max_hr, ascent, max_altitude, avg_watts, max_watts, id_activity, id_user, id_visibility, id_label', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>500),
			array('distance, avg_speed', 'length', 'max'=>10),
			array('avg_pace', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, duration, description, feelings, min_hr, avg_hr, max_hr, distance, avg_speed, ascent, max_altitude, avg_watts, max_watts, id_activity, id_user, id_visibility, avg_pace, date, id_label', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'date' =>  Yii::t('plan','Date'),
			'duration' => Yii::t('diary','Duration'),
			'description' => Yii::t('diary','Description'),
			'feelings' => Yii::t('diary','Feelings'),
			'min_hr' => Yii::t('diary','Min'),
			'avg_hr' => Yii::t('diary','Avg'),
			'max_hr' => Yii::t('diary','Max'),
                        'distance' => Yii::t('diary','Distance'),
			'avg_speed' => Yii::t('diary','Average Speed'),
			'avg_pace' => Yii::t('diary','Average Pace'),
			'ascent' => Yii::t('diary','Ascent'),
			'max_altitude' => Yii::t('diary','Max Altitude'),
			'avg_watts' => Yii::t('diary','Average Watts'),
			'max_watts' => Yii::t('diary','Max Watts'),
			'id_activity' => Yii::t('diary','Activity'),
			'id_user' => 'Id User',
			'id_visibility' => Yii::t('diary','Visibility'),
                        'id_label' => Yii::t('diary','Label'),
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
		$criteria->compare('duration',$this->duration,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('feelings',$this->feelings);
		$criteria->compare('min_hr',$this->min_hr);
		$criteria->compare('avg_hr',$this->avg_hr);
		$criteria->compare('max_hr',$this->max_hr);
		$criteria->compare('distance',$this->distance,true);
		$criteria->compare('avg_speed',$this->avg_speed,true);
		$criteria->compare('ascent',$this->ascent);
		$criteria->compare('max_altitude',$this->max_altitude);
		$criteria->compare('avg_watts',$this->avg_watts);
		$criteria->compare('max_watts',$this->max_watts);
		$criteria->compare('id_activity',$this->id_activity);
		$criteria->compare('id_user',$this->id_user);
		$criteria->compare('id_visibility',$this->id_visibility);
		$criteria->compare('avg_pace',$this->avg_pace,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('id_label',$this->id_label);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}