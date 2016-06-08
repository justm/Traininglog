<?php

class WebUser extends CWebUser
{
    /**
     * Overrides a Yii method that is used for roles in controllers (accessRules).
     *
     * @param string $operation Name of the operation required (here, a role).
     * @param mixed $params (opt) Parameters for this operation, usually the object to access.
     * @return bool Permission granted?
     */
    public function checkAccess($operation, $params=array())
    {
        if (empty($this->id)) {
            // Neidentifikovaný => ziadne prava
            return false;
        }
        $role = $this->getState("roles");
        
        // povoli pristup ak vyzadovanu operaciu moze vykonat rola priradena userovi
        return ($operation === $role);
    }
}
?>
