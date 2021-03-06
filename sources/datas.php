<?php

class Datas {

    /*************************************
     *                                   *
     *   stockage des données de Datas   *
     *                                   *
     *************************************/
    private static $config = array(
        "password" => "PASSWORD",
        "path" => "data/"
    );


    /*************************************
     *                                   *
     *          Fonction public          *
     *                                   *
     *************************************/
    public static function get_ALL ($password, $db) {
        if(self::connect($password)){
            return json_decode(self::get_content($db), true);
        }
        else {
            return false;
        }
    }
    public static function set_ALL ($password, $db, $value, $isJSON = false) {
        if(self::connect($password)){
            if($isJSON == true){
                self::set_content($db, $value);
            }
            else {
                self::set_content($db, json_encode($value));
            }
            return true;
        }
        else {
            return false;
        }
    }

    /**
    * @param string $password mot de passe
    * @param string $db nom de base de données
    * @return boolean true si réussi, false en cas de pb
    */
    public static function new_DB ($password, $db) {
        if(self::connect($password)){
            return self::set_content($db, '{"INFO":{"VERSION":"1.0.0","LASTCHANGE":"01/01/2000"},"DATA":[]}');
        }
        else {
            return false;
        }
    }
    public static function delete_DB ($password, $db) {
        return unlink(self::$config['path'] . $db . ".json");
    }

    public static function get_INFO ($password, $db) {
        if(self::connect($password)){
            $result = json_decode(self::get_content($db), true);
            return $result['INFO'];
        }
        else {
            return false;
        }
    }
    public static function set_INFO ($password, $db, $version, $lastchange) {
        if(self::connect($password)){
            $result = json_decode(self::get_content($db), true);
            $result['INFO']['VERSION'] = $version;
            $result['INFO']['LASTCHANGE'] = $lastchange;
            self::set_content($db, json_encode($result));
            return true;
        }
        else {
            return false;
        }
    }
    
    public static function get_DATA ($password, $db) {
        if(self::connect($password)){
            $result = json_decode(self::get_content($db), true);
            return $result['DATA'];
        }
        else {
            return false;
        }
    }
    public static function set_DATA ($password, $db, $value, $isJSON = false) {
        if(self::connect($password)){
            $result = json_decode(self::get_content($db), true);
            if($isJSON){
              $value = json_decode($value, true);
            }
            $result['DATA'] = $value;
            self::set_content($db, json_encode($result));
            return true;
        }
        else {
            return false;
        }
    }

    public static function add_ITEM ($password, $db, $value, $isJSON = false) {
        if(self::connect($password)){
            $content = json_decode(self::get_content($db), true);
            if($isJSON){
                $content['DATA'][] = json_decode($value, true);
            } else {
                $content['DATA'][] = $value;
            }
            self::set_content($db, json_encode($content));
            return count($content['DATA']) - 1;
        }
        else {
            return false;
        }
    }
    public static function delete_ITEM ($password, $db, $ID) {
        if(self::connect($password)){
            $content = json_decode(self::get_content($db), true);
            array_splice($content['DATA'], $ID, 1);
            self::set_content($db, json_encode($content));
            return true;
        }
        else {
            return false;
        }
    }
    public static function get_ITEM ($password, $db, $ID) {
        if(self::connect($password)){
            $content = json_decode(self::get_content($db), true);
            return $content['DATA'][$ID];
        }
        else {
            return false;
        }
    }
    public static function set_ITEM ($password, $db, $ID, $value, $isJSON = false) {
        if(self::connect($password)){
            $content = json_decode(self::get_content($db), true);
            if($isJSON){
                $content['DATA'][$ID] = json_decode($value, true);
            } else {
                $content['DATA'][$ID] = $value;
            }
            self::set_content($db, json_encode($content));
            return true;
        }
        else {
            return false;
        }
    }
    
    // $value = array('["USER"]["PSEUDO"] == "loulou"','["USER"]["MDP" == "password"')
    public static function find_ID ($password, $db, $value) {
      if(self::connect($password)){
            $content = json_decode(self::get_content($db), true);
            $result = array();
            foreach($content['DATA'] as $ID => $item){
              $valid = true;
              foreach($value as $rule){
                if(eval("if(\$item".$rule."){return true;}else{return false;}") == false){
                  $valid = false;
                }
              }
              if($valid){
                $result[] = $ID;
              }
            }
            return $result;
        }
        else {
            return false;
        }
    }
    // $value = array('["USER"]["PSEUDO"] == "loulou"','["USER"]["MDP" == "password"')
    public static function find_ITEM ($password, $db, $value) {
      if(self::connect($password)){
            $content = json_decode(self::get_content($db), true);
            $result = array();
            foreach($content['DATA'] as $ID => $item){
              $valid = true;
              foreach($value as $rule){
                if(eval("if(\$item".$rule."){return true;}else{return false;}") == false){
                  $valid = false;
                }
              }
              if($valid){
                $result[] = $item;
              }
            }
            return $result;
        }
        else {
            return false;
        }
    }

    public static function setKey ($password, $key, $value) {
        if(self::connect($password)){
            $content = json_decode(self::get_content("__keys__"), true);
            $content[$key] = $value;
            self::set_content("__keys__", json_encode($content));
            return true;
        }
        else {
            return false;
        }
    }
    public static function getKey ($password, $key) {
        if(self::connect($password)){
            $keys = json_decode(self::get_content("__keys__"), true);
            return $keys[$key];
        }
        else {
            return false;
        }
    }
    public static function isKey ($password, $key) {
        if(self::connect($password)){
            $keys = json_decode(self::get_content("__keys__"), true);
            return isset($keys[$key]);
        }
        else {
            return false;
        }
    }


    /*************************************
     *                                   *
     *    Allow you to connect on DB     *
     *                                   *
     *************************************/
    private static function connect ($password) {
        if(self::$config['password'] === $password){
            return true;
        }
        else {
            return false;
        }
    }


    /*************************************
     *                                   *
     *   Fonction d'accés au fichiers    *
     *                                   *
     *************************************/
    private static function get_content ($db) {
        if(file_exists(self::$config['path'] . $db . ".json")){
            $file = fopen(self::$config['path'] . $db . ".json", "r");
            $content = fread($file, filesize(self::$config['path'] . $db . ".json"));
            fclose($file);
            return $content;
        }
        else {
            return false;
        }
    }
    private static function set_content ($db, $value = "") {
        $file = fopen(self::$config['path'] . $db . ".json", "w");
        fwrite($file, $value);
        fclose($file);
        return true;
    }




}
?>