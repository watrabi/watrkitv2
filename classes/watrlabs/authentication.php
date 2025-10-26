<?php

namespace watrlabs;

use watrlabs\sitefunctions;
use watrlabs\watrkit\sanitize;
use watrlabs\logging\discord;
use Pixie\Connection;
use Pixie\QueryBuilder\QueryBuilderHandler;

global $db;



class authentication {

    private $cookiename = "";
    private $currentuser = false;

    public function __construct(){
        $this->cookiename = $_ENV["CookieName"];
    }

    public function getCookie(){
        return $_COOKIE[$this->cookiename];
    }

    public function getUserInfo($id = null){

        global $db;

        if($id){
            return $db->table("users")->where("id", $id)->first();
        } else {
            $sessionInfo = $this->getSessionInfo($this->getCookie());

            if($sessionInfo){
                $userInfo = $db->table("users")->where("id", $sessionInfo->userid)->first();

                if($userInfo){
                    $this->currentuser = $userInfo;
                    return $userInfo;
                }

            } else {
                return null;
            }

        }
    }

    public function isLoggedIn(){ 
        $result = $this->getUserInfo();

        if($return){
            return true;
        } else {
            return false;
        }

    }

    public function getSessionInfo($Session){
        global $db;
        $sessionInfo = $db->table("sessions")->where("session", $Session)->first();
        return $sessionInfo;
    }


    
}