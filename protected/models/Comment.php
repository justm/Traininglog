<?php

/**
 * This is the model class for table "comment".
 *
 * The followings are the available columns in table 'comment':
 * @property integer $id
 * @property string $text
 * @property integer $id_trainingentry
 * @property integer $id_user
 * @property integer $id_visibility
 * @property string $date
 * @property integer $seen
 */
class Comment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Comment the static model class
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
		return 'comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_trainingentry, id_user, id_visibility, date, seen', 'required'),
			array('id_trainingentry, id_user, id_visibility, seen', 'numerical', 'integerOnly'=>true),
			array('text', 'length', 'max'=>300),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, text, id_trainingentry, id_user, id_visibility, date, seen', 'safe', 'on'=>'search'),
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
			'text' => Yii::t('dashboard','Add your opinion'),
			'id_trainingentry' => 'Id Trainingentry',
			'id_user' => 'Id User',
			'id_visibility' => Yii::t('dashboard','Visibility'),
                        'date' => 'Date',
			'seen' => 'Seen',
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
		$criteria->compare('text',$this->text,true);
		$criteria->compare('id_trainingentry',$this->id_trainingentry);
		$criteria->compare('id_user',$this->id_user);
		$criteria->compare('id_visibility',$this->id_visibility);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('seen',$this->seen);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}