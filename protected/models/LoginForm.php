<?php

///LoginfForm je datova struktura pre uchovavanie premennych prihlasovacieho formulara
class LoginForm extends CFormModel
{
	public $username;
	public $password;

	private $_identity;

	///Metoda deklaruje validacne pravidla
	public function rules()
	{
		return array(
			// username and password are required
			array('username, password', 'required'),
			// password needs to be authenticated
			array('password', 'authenticate'),
		);
	}

	///Metoda deklaruje Popisy jednotlivych atributov
	public function attributeLabels()
	{
		return array(
                        'username'=>Yii::t('global','Username'),
                        'password'=>Yii::t('global','Password'),
		);
	}

	///Autentifikacia uzivatela
	public function authenticate($attribute,$params)
	{
		$this->_identity=new UserIdentity($this->username,$this->password);

                if(!$this->_identity->authenticate())
                {
                        switch($this->_identity->errorCode)
                        {
                        case UserIdentity::ERROR_USERNAME_INVALID:
                                $this->addError('username','Login failed, wrong username'); break;
                        case UserIdentity::ERROR_PASSWORD_INVALID:
                                $this->addError('password','Login failed, wrong password'); break;
                        }
                }
	}

	///Prihlasenie pouzivatela
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration= 3600*24; // 24 hours
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}
