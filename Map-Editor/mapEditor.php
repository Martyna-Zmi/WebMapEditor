<?php
require_once "objects/OnMap.php";
session_start();
if(!isset($_SESSION['logged']) || !isset($_SESSION['passed'])){
    header("Location:mapLogin.php");
}
if(!isset($_SESSION['user_id'])){
    header("Location:mainSite.php");
}
require_once "connect.php";
$details = dataBaseDetails();
$db_name = $details['name'];
$db_pass = $details['pass'];
$db_user = $details['user'];
$db = connectToDatabase($db_name, $db_pass, $db_user);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edytor mapy</title>
    <?php
    require_once "theme.php";
    theme();
    ?>
</head>
<body class="center">
<a href="mainSite.php"><img src="images/arrow.png" height="30px" width="30px"></a>
<?php
$user_id = $_SESSION['user_id'];
if(isset($_SESSION['edit']) && isset($_SESSION['editID'])){
    $mapID = $_SESSION['editID'];
        $map = $db->query("SELECT MapName, Fields, SizeX, SizeY, Creation_date FROM Map WHERE Map_id='$mapID'");
        $map = $map->fetch(PDO::FETCH_ASSOC);
        $mapName = $map['MapName'];
        $mapFields = $map['Fields'];
        printf("<h2 class='main'>╰┈➤ $mapName</h2>");
        printf("<table>");
        $mapFieldsArray = explode("+", $mapFields);
        for($y=1; $y<=$map['SizeY']; $y++){
            printf("<tr>");
            for ($x=1; $x<=$map['SizeX']; $x++){
                $temp = ($map['SizeX']*($y-1))+$x;
                $field = unserialize($mapFieldsArray[$temp]);
                $img = $field->getImg();
                printf("<td><form method='post'>
               <input style='background-image: url($img); background-size: cover; height: 40px; width: 40px' type='submit' name='fieldEdit' value=' '><input type='hidden' value='$temp' name='toEdit'></form></td>");
            }
            printf("</tr>");
        }
        printf("</table>");
    }
else header("Location:mainSite.php");
?>
<h3>Dostępne pola:</h3>
<?php
    $objects = $db->query("SELECT * FROM Object");
    while ( $row = $objects->fetch(PDO::FETCH_ASSOC)){
        $objID = $row['Object_id'];
        $asObj = unserialize($row['Info']);
        $image = $asObj->getImg();
        $display = $asObj->info();
        printf("<form style='display: inline' method='post'><input title='$display' style='background-image: url($image); background-size: cover; height: 70px; width: 70px' type='submit' name='new' value=' '>
        <input type='hidden' name='newField' value='$objID'></form>");
    }
if(isset($_POST['fieldEdit']) && isset($_POST['toEdit'])){
    $_SESSION['toEdit'] = $_POST['toEdit'];
}
if(isset($_POST['new']) && isset($_POST['newField'])){
    $_SESSION['newField'] = $_POST['newField'];
}
if(isset($_SESSION['toEdit']) && isset($_POST['newField'])){
    $mapID = $_SESSION['editID'];
    $newID = $_SESSION['newField'];
    $key = $_SESSION['toEdit'];
    $newField = $db->query("SELECT Info FROM Object WHERE Object_id=$newID");
    $newField = $newField->fetch(PDO::FETCH_ASSOC);
    $newField = $newField['Info'];
    $mapFieldsArray[$key] = $newField;
    $fields = implode('+', $mapFieldsArray);
    try{
        $db->beginTransaction();
        $update = $db->prepare("UPDATE Map SET Fields = '$fields' WHERE Map_id=$mapID");
        $update->execute();
        $db->commit();
    }
    catch (PDOException $exception){
        $db->rollBack();
        printf("<p>Nastąpił błąd</p>");
    }
    unset($_SESSION['toEdit']);
    unset($_SESSION['newField']);
    printf("<meta http-equiv='refresh' content='0.1'>");
}
$creationDateString = $map['Creation_date'];
$creationDate = strtotime($creationDateString);
$now = time();
$dateDiff = $now - $creationDate;
$dateDiff = round($dateDiff / (60 * 60 * 24));
printf("<p>Data utworzenia: $creationDateString || $dateDiff dni temu</p>");
?>
<h3>︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵</h3>
<h2>Co powinna zawierać dobra mapa:</h2>
<table>
    <tr>
        <td style="text-align: left">
            <ul>
                <?php
                $fields = $db->query("SELECT Fields FROM Map WHERE Map_id=$mapID");
                $fields = $fields->fetch(PDO::FETCH_ASSOC);
                $fieldsArray = explode("+", $fields['Fields']);
                array_splice($fieldsArray, 0, 1);
                printf("<li>Co najmniej 3 źródła wody ");
                $count = 0;
                foreach ($fieldsArray as $field){
                    $field = unserialize($field);
                    if(get_class($field)=="Terrain" && $field->getBiome()=="Woda"){
                        $count++;
                    }
                    if($count==3){
                        printf("<img src='images/yes.png' style='width: 25px; height: 20px'></li>");
                        break;
                    }
                }
                printf("<li>Co najmniej 2 spawny graczy ");
                $count = 0;
                foreach ($fieldsArray as $field){
                    $field = unserialize($field);
                    if(get_class($field)=="ForPlayer" && $field->getForPlayerType()=="Spawn"){
                        $count++;
                    }
                    if($count==2){
                        printf("<img src='images/yes.png' style='width: 25px; height: 20px'></li>");
                        break;
                    }
                }
                printf("<li>Co najmniej 3 skrzynie ");
                $count = 0;
                foreach ($fieldsArray as $field){
                    $field = unserialize($field);
                    if(get_class($field)=="ForPlayer"){
                        if($field->getForPlayerType()=="Skrzynia" || $field->getForPlayerType()=="Skarb pustynny")
                        {
                            $count++;
                        }
                    }
                    if($count==3){
                        printf("<img src='images/yes.png' style='width: 25px; height: 20px'></li>");
                        break;
                    }
                }
                printf("<li>Co najmniej 1 ognisko ");
                $count = 0;
                foreach ($fieldsArray as $field){
                    $field = unserialize($field);
                    if(get_class($field)=="ForPlayer" && $field->getForPlayerType()=="Ognisko"){
                        $count++;
                    }
                    if($count==1){
                        printf("<img src='images/yes.png' style='width: 25px; height: 20px'></li>");
                        break;
                    }
                }
                printf("<li>Co najmniej 2 kapibary");
                $count = 0;
                foreach ($fieldsArray as $field){
                    $field = unserialize($field);
                    if(get_class($field)=="Animal" && $field->getAnimalType()=="Kapibara"){
                        $count++;
                    }
                    if($count==2){
                        printf("<img src='images/yes.png' style='width: 25px; height: 20px'></li>");
                        break;
                    }
                }
                ?>
            </ul>
        </td>
    </tr>
</table>
<h3>︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵</h3>
</body>
</html>
