<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
    
        private $_id;
        
        public function getId()
        {
                return $this->_id;
        }
        public function setId($id){
            $this->_id = $id;
        }


        public function authenticate()
	{
		//kontrola prihlasenia uzivatela na zaklade pouzivatelskeho mena v databaze
                $u = User::model()->find('username = "'.$this->username.'" and account_status = 0');

		if (!$u)
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		else if( !$u->matchesPassword($this->password) )
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		else
		{
			$this->_id = $u->id;
			$this->username = $u->name;
                        
                        //priradenie role
                        $role = Role::model()->findByPk($u->id_role);
                        $this->setState('roles',$role->name);
			$this->errorCode = self::ERROR_NONE;
		}
		
		return !$this->errorCode;
                
	}
}