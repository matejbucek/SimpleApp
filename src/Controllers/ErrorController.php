<?php
namespace App\Controllers;

use SimpleFW\HttpBasics\AbstractController;
use SimpleFW\HttpBasics\Exceptions\PageNotFoundException;
use SimpleFW\Annotations\Controller;
use SimpleFW\HttpBasics\Exceptions\MethodNotSupportedException;
use SimpleFW\HttpBasics\HttpResponse;
use SimpleFW\Security\Exceptions\UserNotLoggedInException;
use SimpleFW\HttpBasics\Exceptions\AccessForbiddenException;

#[Controller]
class ErrorController extends AbstractController
{
    public function handleError(\Exception $exception): HttpResponse{
        $errorTitle = "Something went wrong :^(";
        if($exception instanceof PageNotFoundException){
            $errorTitle = "Page not found :^(";
        }else if ($exception instanceof MethodNotSupportedException){
            $errorTitle = "This method is not supported at this endpoint. :^(";
        }else if($exception instanceof UserNotLoggedInException){
            $errorTitle = "You are not logged in..";
        }else if($exception instanceof AccessForbiddenException){
            $errorTitle = "Access forbidden.";
        }
        
        return $this->render("error.latte", ["error" => $errorTitle]);
    }
}