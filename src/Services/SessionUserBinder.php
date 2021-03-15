<?php
namespace App\Services;

use SimpleFW\Annotations\Service;
use SimpleFW\Annotations\Autowired;
use SimpleFW\Security\Principal;
use SimpleFW\Security\UserDataBinder;
use SimpleFW\Containers\SessionContext;
use SimpleFW\Security\Exceptions\UserNotLoggedInException;

#[Service("SessionUserBinder")]
class SessionUserBinder implements UserDataBinder
{
    #[Autowired("@SessionContext")]
    private SessionContext $sessionContext;
    
    public function getUser(): ?Principal
    {
        if($this->sessionContext->get("USER_ID") !== null){
            return unserialize($this->sessionContext->get("USER_PRINCIPAL"));
        }
        return NULL;
    }
    
    public function setUser(Principal $user){
        $this->sessionContext->put("USER_ID", serialize($user));
        $this->sessionContext->put("USER_PRINCIPAL", serialize($user));
    }
    
    public function destroy(){
        $this->sessionContext->destroy();
    }
}

