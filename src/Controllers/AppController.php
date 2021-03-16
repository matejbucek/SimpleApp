<?php
namespace App\Controllers;
use SimpleFW\HttpBasics\AbstractController;
use SimpleFW\Annotations\Route;
use SimpleFW\Annotations\Controller;
use SimpleFW\Annotations\Autowired;
use SimpleFW\HttpBasics\HttpResponse;
use SimpleFW\HttpBasics\HttpRequest;
use App\Services\UserService;

#[Controller]
class AppController extends AbstractController
{
    #[Autowired("@UserService")]
    private UserService $userService;
    
    #[Route("/", methods: ["GET"], name: "main")]
    public function getMain(): HttpResponse{
        return $this->render("index.latte", ["loggedIn" => $this->userService->isLoggedIn()]);
    }
    
    #[Route("/secured", methods: ["GET"])]
    public function getAhoj(){
        return $this->render("secured.latte", ["loggedIn" => $this->userService->isLoggedIn()]);
    }
    
    #[Route("/login", methods: ["GET"])]
    public function getLogin(){
        if($this->userService->isLoggedIn())
            return $this->redirect("/");
            
        return $this->render("login.latte", ["error" => FALSE]);
    }
    
    #[Route("/login", methods: ["POST"])]
    public function postLogin(HttpRequest $request){
        if($this->userService->isLoggedIn()){
            return $this->redirect("/");
        }
        if(isset($request->getParams()["email"]) && isset($request->getParams()["password"])){
            if($this->userService->login($request->getParams()["email"], $request->getParams()["password"])){
                return $this->redirect("/");
            }
        }
        return $this->render("login.latte", ["error" => TRUE]);
    }
    
    #[Route("/register", methods: ["GET"])]
    public function getRegister() {
        if($this->userService->isLoggedIn())
            return $this->redirect("/");
        
        return $this->render("register.latte", ["error" => FALSE]);
    }
    
    #[Route("/register", methods: ["POST"])]
    public function postRegister(HttpRequest $request){
        if($this->userService->isLoggedIn()){
            return $this->redirect("/");
        }
        
        if(isset($request->getParams()["email"]) && isset($request->getParams()["password"]) && isset($request->getParams()["re-password"])){
            $email = $request->getParams()["email"];
            $password = $request->getParams()["password"];
            $repassword = $request->getParams()["re-password"];
            
            if($password != $repassword){
                return $this->render("register.latte", ["error" => TRUE, "errorMessage" => "Password does not match."]);
            }
            
            if($this->userService->register($email, $password)){
                return $this->redirect("/login");
            }
        }
        return $this->render("register.latte", ["error" => TRUE]);
    }
    
    #[Route("/logout", methods: ["GET"])]
    public function getLogout(){
        if(!$this->userService->isLoggedIn()){
            return $this->redirect("/");
        }
        $this->userService->logout();
        return $this->redirect("/");
    }
}

