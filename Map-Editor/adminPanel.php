<?php
require_once "objects/OnMap.php";
session_start();
if(!isset($_SESSION['logged']) || !isset($_SESSION['passed'])){
    header("Location:mapLogin.php");
}
require_once "connect.php";
$details = dataBaseDetails();
$db_name = $details['name'];
$db_pass = $details['pass'];
$db_user = $details['user'];
$db = connectToDatabase($db_name, $db_pass, $db_user);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Administratora</title>
    <?php
    require_once "theme.php";
    theme();
    ?>
</head>
<body>
<a href="mainSite.php"><img src="images/arrow.png" height="30px" width="30px"></a>
<h1>Witaj w panelu administratora!</h1>
<h3>Stan Bazy Danych: Działa</h3>
<h3>Lista użytkowników:</h3>
<ul>
    <?php
    $users = $db->query("SELECT * FROM User");
    if($users->rowCount() != 0){
    while($row = $users->fetch(PDO::FETCH_ASSOC)) {
        if (!$row['Is_admin']) {
           printf("<li>Id: %s ||| Nazwa użytkownika: %s ||| Ilość map: %s ||| Czy ma bana: %s", $row['User_id'], $row['Login'], $row['Maps_owned'], (1 == $row['Is_banned'] ? "Tak" : "Nie"));
            $id = $row['User_id'];
            printf("<form method='post'>");
            printf("<input type='submit' name='showMaps' value='Zobacz mapy użytkownika'><input type='hidden' name='idShowMaps' value='$id'>");
            if($row['Premium']==0){
                printf("<input type='submit' value='Daj Premium' name='givePremium'><input type='hidden' name='id' value='$id'>
                <input type='submit' value='Usuń' name='ToDo'>");
            }
            else{
                printf("<input type='submit' value='Zabierz Premium' name='takePremium'><input type='hidden' name='id' value='$id'>
                <input type='submit' value='Usuń' name='ToDo'>");
            }
            if($row['Is_banned']==1){
                printf("<input type='submit' value='Odbanuj' name='ToDo'>");
            }
            else{
            printf("<input type='submit' value='Zbanuj' name='ToDo'>");
           }
            printf("<input type='hidden' value='$id' name='idToDo'>
        </form>
        </li>");
        }
        if(isset($_POST['showMaps']) && isset($_POST['idShowMaps']) && $_POST['idShowMaps']==$id){
            $_SESSION['idShowMap']=$_POST['idShowMaps'];
            printf("<p><a href='showMaps.php'>przejdź do map i wyjdź z panelu administratora</a></p>");
        }
    }
    }
    else printf("<p>Brak użytkowników</p>");


    if(isset($_POST['ToDo'])){
        if($_POST['ToDo']=="Usuń"){
            $delete = $_POST['idToDo'];
            $db->query("DELETE FROM Map WHERE User_id=$delete");
            $db->query("DELETE FROM User WHERE User_id=$delete");
        }
        else if($_POST['ToDo']=="Zbanuj"){
            $ban = $_POST['idToDo'];
            $db->query("UPDATE User Set Is_banned=true WHERE User_id=$ban");
        }
        else if($_POST['ToDo']=="Odbanuj"){
            $unban = $_POST['idToDo'];
            $db->query("UPDATE User Set Is_banned=false WHERE User_id=$unban");
        }
        printf("<meta http-equiv='refresh' content='0.1'>");
    }
    if(isset($_POST['givePremium'])){
        $id = $_POST['id'];
        $db->query("UPDATE User SET Premium=1 WHERE User_id='$id'");
        printf("<meta http-equiv='refresh' content='0.1'>");
    }
    if(isset($_POST['takePremium'])){
        $id = $_POST['id'];
        $db->query("UPDATE User SET Premium=0 WHERE User_id='$id'");
        printf("<meta http-equiv='refresh' content='0.1'>");
    }
    ?>
</ul>
    <h3>Obecnie dostępne dla graczy obiekty:</h3>
    <?php
    function displayObject($object){
        $object = unserialize($object["Info"]);
        $info = $object->displayForAdmin();
        printf("<li>$info</li>");
    }
    $objects = $db->query("SELECT Info FROM Object");
    $objectsArray = $objects->fetchAll(PDO::FETCH_ASSOC);
    printf("<ul>");
    array_walk($objectsArray, "displayObject");
    printf("</ul>");
    ?>
</body>
</html>