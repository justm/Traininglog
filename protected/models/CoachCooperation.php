<?php

/**
 * This is the model class for table "coach_cooperation".
 *
 * The followings are the available columns in table 'coach_cooperation':
 * @property integer $id
 * @property integer $id_coach
 * @property integer $id_athlete
 * @property integer $status
 * @property string $cooperatin_since
 * @property integer $athlete_changes_made
 * @property string $athlete_changes_date
 * @property integer $athlete_changes_confirmed
 */
class CoachCooperation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CoachCooperation the static model class
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
		return 'coach_cooperation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_coach, id_athlete, status, cooperatin_since', 'required'),
			array('id_coach, id_athlete, status, athlete_changes_made, athlete_changes_confirmed', 'numerical', 'integerOnly'=>true),
			array('athlete_changes_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, id_coach, id_athlete, status, cooperatin_since, athlete_changes_made, athlete_changes_date, athlete_changes_confirmed', 'safe', 'on'=>'search'),
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
			'id_coach' => 'Id Coach',
			'id_athlete' => 'Id Athlete',
			'status' => 'Status',
			'cooperatin_since' => 'Cooperatin Since',
			'athlete_changes_made' => 'Athlete Changes Made',
			'athlete_changes_date' => 'Athlete Changes Date',
			'athlete_changes_confirmed' => 'Athlete Changes Confirmed',
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
		$criteria->compare('id_coach',$this->id_coach);
		$criteria->compare('id_athlete',$this->id_athlete);
		$criteria->compare('status',$this->status);
		$criteria->compare('cooperatin_since',$this->cooperatin_since,true);
		$criteria->compare('athlete_changes_made',$this->athlete_changes_made);
		$criteria->compare('athlete_changes_date',$this->athlete_changes_date,true);
		$criteria->compare('athlete_changes_confirmed',$this->athlete_changes_confirmed);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}