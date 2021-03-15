<?php
namespace App;
use SimpleFW\AbstractKernel;

class Kernel extends AbstractKernel
{
    protected function configure(){
        $this->config = \yaml_parse_file(realpath("../config/config.yaml"));
        $this->dependency = \yaml_parse_file(realpath("../config/dependency.yaml"));
        $this->routes = \yaml_parse_file(realpath("../config/routes.yaml"));
        $this->firewallConfig = \yaml_parse_file(realpath("../config/firewall.yaml"));
    }
    
    protected function setAppBase(){
        $this->appBase = realpath(__DIR__."/..");
    }
}

