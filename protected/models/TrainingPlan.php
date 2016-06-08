<?php

/**
 * This is the model class for table "training_plan".
 *
 * The followings are the available columns in table 'training_plan':
 * @property integer $id
 * @property integer $id_user
 * @property integer $id_activity
 * @property string $date
 * @property string $duration
 * @property string $description
 * @property integer $id_label
 */
class TrainingPlan extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TrainingPlan the static model class
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
		return 'training_plan';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_user, date', 'required'),
			array('id_user, id_activity, id_label', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>1000),

			array('duration', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, id_user, id_activity, date, duration, description, id_label', 'safe', 'on'=>'search'),
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
			'id_user' => 'Id User',
			'id_activity' => Yii::t('plan','Activity'),
			'date' => Yii::t('plan','Date'),
			'duration' => Yii::t('plan','Duration'),
			'description' => Yii::t('plan','Description'),
                        'id_label' => Yii::t('plan','Label'),
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
		$criteria->compare('id_user',$this->id_user);
		$criteria->compare('id_activity',$this->id_activity);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('duration',$this->duration,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('id_label',$this->id_label);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}