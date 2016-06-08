<?php
///Trieda, ktora obsluhuje tabulku User v databaze
/**
 * Nasledujuce stlpce su definovane v tabulke 'user':
 * @property integer $id
 * @property integer $id_role
 * @property integer $id_address
 * @property integer $id_primary_activity
 * @property integer $id_user_fitness
 * @property integer $id_privacy
 * @property string $name
 * @property string $lastname
 * @property string $fullname
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $gender
 * @property string $birthday
 * @property string $phone
 * @property string $about
 * @property string $profile_picture
 * @property integer $account_status
 *
 * Relacie s inymi tabulkami
 * @property Role $idRole
 * @property Address $idAddress
 * @property Activity $idPrimaryActivity
 * @property UserFitness $idUserFitness
 * @property Privacy $idPrivacy
 */
class User extends CActiveRecord
{
        protected $_oldPassword;
        public $confirmPassword;
        
        ///Metoda vrati model na pristup do databazy
	/**
	 * @param string $className Nazov ActiveRecord triedy
	 * @return User statický model tejto triedy
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        ///Metoda vrati nazov databazovej tabulky ku ktorej je priradena tato trieda
	/**
	 * @return string Retazec s nazvom databazovej tabulky
	 */
	public function tableName()
	{
		return 'user';
	}

        ///Metoda vrati retazec s pravidlami ukladania a nacitania udajov z databazy
	/**
	 * @return array Validacne udaje
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_role, name, fullname, username, password, confirmPassword, email, gender', 'required','message'=>'{attribute} is required'),
			array('id_role, id_address, id_primary_activity, id_user_fitness,  id_privacy', 'numerical', 'integerOnly'=>true),
			array('name, lastname', 'length', 'max'=>30),
			array('fullname', 'length', 'max'=>60),
			array('username', 'length', 'max'=>20),
			array('password, email', 'length', 'max'=>40),
			array('gender', 'length', 'max'=>1),
			array('phone', 'length', 'max'=>15),
			array('about', 'length', 'max'=>250),
                        array('profile_picture', 'length', 'max'=>100),
                        array('email','email'),
                        array('email','unique','message'=>'Acount with this email already exists'),
                        array('username','unique','message'=>'This username is already used'),
			array('birthday', 'safe'),
			
                        array('password', 'compare', 'compareAttribute'=>'confirmPassword', 'message'=>"Password repeated incorrectly"),
                        array('confirmPassword', 'safe'),
                    
                        array('id, id_role, id_address, id_primary_activity, id_user_fitness, id_privacy, name, lastname, fullname, username, password, email, gender, birthday, phone, about, account_status', 'safe', 'on'=>'search'),
		);
	}

        ///Metoda vrati relacie s pribuznymi tabulkami v databaze
	/**
	 * @return array Relacie
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'idRole' => array(self::BELONGS_TO, 'Role', 'id_role'),
			'idAddress' => array(self::BELONGS_TO, 'Address', 'id_address'),
			'idPrimaryActivity' => array(self::BELONGS_TO, 'Activity', 'id_primary_activity'),
			'idUserFitness' => array(self::BELONGS_TO, 'UserFitness', 'id_user_fitness'),
                        'idPrivacy' => array(self::BELONGS_TO, 'Privacy', 'id_privacy'),
		);
	}

        ///Metoda vrati oznacenie jednotlivych stlpcov databazy
	/**
	 * @return array Oznacenia stlpcov
         * Vdaka polu v navratovej premennej je mozne vyuzivat Yii metodu getAttributeLabel();
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_role' => Yii::t('user','Role'),
			'id_address' => 'Address',
			'id_primary_activity' => Yii::t('user','Primary Sport'),
			'id_user_fitness' => 'User Fitness',
                        'id_privacy' => 'User Privacy',
			'name' => Yii::t('user','Name'),
			'lastname' => Yii::t('user','Lastname'),
			'fullname' => 'Fullname',
			'username' => Yii::t('global','Username'),
			'password' => Yii::t('global','Password'),
                        'confirmPassword'=>Yii::t('user','Repeat password'),
			'email' => 'Email',
			'gender' => Yii::t('user','Gender'),
			'birthday' => Yii::t('user','Birthday'),
			'phone' => Yii::t('user','Phone'),
			'about' => Yii::t('user','About'),
                        'profile_picture'=>Yii::t('user','Profile picture'),
                        'account_status'=>'Account status',
		);
	}

        ///Metoda vrati zoznam modelov (riadkov databazy) zostavenych podla aktualneho vyhladavacieho filtra
	/**
	 * @return CActiveDataProvider Zoznam modelov 
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('id_role',$this->id_role);
		$criteria->compare('id_address',$this->id_address);
		$criteria->compare('id_primary_activity',$this->id_primary_activity);
		$criteria->compare('id_user_fitness',$this->id_user_fitness);
                $criteria->compare('id_privacy',$this->id_privacy);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('lastname',$this->lastname,true);
		$criteria->compare('fullname',$this->fullname,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('gender',$this->gender,true);
		$criteria->compare('birthday',$this->birthday,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('about',$this->about,true);
                $criteria->compare('about',$this->profile_picture,true);
                $criteria->compare('account_status',$this->account_status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
                            'pagination'=>array(
                                'pageSize'=>20,
                        ),
		));
	}
        
        
        ///Metoda hashuje heslo
        /**
         * 
         * @param string $password Nezahashovany retazec
         * @return string Zahashovany retazec
         */
        protected function getHash($password)
        {
                return md5("pinarello" . $password);
        }
        
        ///Pretazena povodna Yii metoda, ktora upravuje data po ich nacitani z databazy
        /**
         * @return parent::afterFind Rodicovska metoda
         */
        function afterFind() 
        {
                $this->_oldPassword = $this->password;
                $this->password = '';
                $this->confirmPassword = '';
                
                $this->birthday = strtotime($this->birthday);
                $this->birthday = date('d.m.Y',$this->birthday);

                return parent::afterFind();
        }

        ///Pretazena povodna Yii metoda, ktora upravuje data pred ich ulozenim z databazy
        /**
         * @return parent::beforeSave Rodicovska metoda
         */
        function beforeSave() 
        {              
                if($this->birthday == '0000-00-00' || $this->birthday == ''){
                    $this->birthday = null;
                }
                try{
                    $timestamp = strtotime($this->birthday."00:00:00");
                    $this->birthday = date("Y-m-d H:i:s",$timestamp);
                }
                catch(Exception $e){
                    $this->birthday = null;
                }
                if (!$this->password)
                        $this->password = $this->confirmPassword = $this->_oldPassword;
                else
                        if ($this->_oldPassword != $this->password) $this->password = $this->getHash($this->password);
                        

                return parent::beforeSave();
        }
        
        ///Metoda kontroluje zhodu hesla
        /**
         * 
         * @param string $password Retazec s heslom ktory je potrebny porovnat
         * @return bool True, false v závislosti od zhody, resp. nezhody
         */
        public function matchesPassword($password)
        {
                return ($this->getHash($password) == $this->_oldPassword);
        } 
        
        ///Metoda ziska protected premennu $_oldPassword
        /**
         * @return string $_oldPassword 
         */
        public function getOldPassword(){
            return $this->_oldPassword; 
        }
}