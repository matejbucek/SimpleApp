<?php
namespace App\Services;
use SimpleFW\Annotations\Service;
use SimpleFW\Database\EntityManager;
use SimpleFW\Annotations\Autowired;
use SimpleFW\Security\Principal;

#[Service("UserService")]
class UserService
{
    #[Autowired("@SessionUserBinder")]
    private SessionUserBinder $binder;
    
    #[Autowired("@EntityManager")]
    private EntityManager $em;
    
    public function isLoggedIn(): bool{
        if($this->binder->getUser() == NULL)
            return FALSE;
        return TRUE;
    }
    
    public function login(string $email, string $password): bool{
        $user = $this->getUser($email);

        if($user == NULL){
            return FALSE;
        }
        
        $user = $user[0];
        if (!password_verify($password, $user["password"])) {
            return FALSE;
        }
        
        $roleSql = "SELECT roles.*
                    FROM `roles`
                    INNER JOIN user_role ON user_role.role_id = roles.id
                    WHERE user_role.user_id = :id;"; 
        
        $roleStmt = $this->em->prepare($roleSql);
        $roleStmt->execute(array(":id" => $user["id"]));
        $roles = $roleStmt->fetchAll()[0];
        
        $principal = new Principal($user["email"], $roles);
        $this->binder->setUser($principal);
        print_r($principal);
        return TRUE;
    }
    
    private function getUser($email){
        $userSql = "SELECT users.*
                FROM users
                WHERE email=:email;";
        $userStmt = $this->em->prepare($userSql);
        $userStmt->execute(array(":email" => $email));
        
        $user = $userStmt->fetchAll();
        if(empty($user)){
            return NULL;
        }
        
        return $user;
    }
    
    public function register($email, $password){
        if($this->getUser($email) !== NULL){
            return FALSE;
        }
        
        $this->em->begindTransaction();
        
        $options = [
            'cost' => 12,
        ];
        $password = password_hash($password, PASSWORD_BCRYPT, $options);
        
        $userSql = "INSERT INTO users
                    (email, password)
                    VALUES (:email, :password);
                   ";
        
        $userStmt = $this->em->prepare($userSql);
        try {
            $userStmt->execute(array(":email" => $email, ":password" => $password));
        } catch (\Exception $e) {
            $this->em->rollBack();
            return FALSE;
        }
        
        $user = $this->getUser($email);
        
        if($user == NULL){
            $this->em->rollBack();
            return FALSE;
        }
        
        $user = $user[0];
        $roleSql = "INSERT INTO user_role
                    (user_id, role_id)
                    VALUES (:user_id, :role_id);
                   ";
        
        $roleStmt = $this->em->prepare($roleSql);
        try{
            $roleStmt->execute(array(":user_id" => $user["id"], ":role_id" => "1"));
        } catch (\Exception $ex){
            $this->em->rollBack();
            return FALSE;
        }
        $this->em->commit();
        return TRUE;
    }
    
    public function logout(){
        $this->binder->destroy();
    }
}

