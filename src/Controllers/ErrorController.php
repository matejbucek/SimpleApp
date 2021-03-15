<?php
namespace App\Controllers;

use SimpleFW\HttpBasics\AbstractController;
use SimpleFW\HttpBasics\Exceptions\PageNotFoundException;
use SimpleFW\Annotations\Controller;
use SimpleFW\HttpBasics\Exceptions\MethodNotSupportedException;
use SimpleFW\HttpBasics\HttpResponse;

#[Controller]
class ErrorController extends AbstractController
{
    public function handleError(\Exception $exception): HttpResponse{
        $errorTitle = "Something went wrong :^(";
        if($exception instanceof PageNotFoundException){
            $errorTitle = "Page not found :^(";
        }else if ($exception instanceof MethodNotSupportedException){
            $errorTitle = "This method is not supported at this endpoint. :^(";
        }
        
        echo $exception->__toString();
        
        $classname = get_class($exception);
        echo "<p>$classname</p>";
        
        return $this->render("error.latte", ["error" => $errorTitle]);
    }
}