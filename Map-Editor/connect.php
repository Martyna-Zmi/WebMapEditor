<?php
function dataBaseDetails(){
    $db_user = 'yourUser';
    $db_pass = 'yourDbPass';
    $db_name = 'yourDbName';
    return array("name" => $db_name, "pass" => $db_pass, "user" => $db_user);
}
function connectToDatabase($db_name, $db_pass, $db_user){
    try{
        $db = new PDO("mysql:host=localhost; dbname=$db_name", $db_user,$db_pass);
        $db->query("Use $db_name");
        return $db;
    }
    catch (PDOException $exceptionPDO){
        return false;
    }
}
?>