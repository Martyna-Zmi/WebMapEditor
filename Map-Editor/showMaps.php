<?php
require_once "objects/OnMap.php";
session_start();
if(!isset($_SESSION['logged']) || !isset($_SESSION['passed']) || !isset($_SESSION['idShowMap'])){
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
<html>
<head>
    <title>Mapy użytkownika</title>
    <meta charset="UTF-8">
    <?php
    require_once "theme.php";
    theme();
    ?>
</head>
<body class="center">
<a href="adminPanel.php"><img src="images/arrow.png" height="30px" width="30px"></a>
<?php
try{
    $userID = $_SESSION['idShowMap'];
    $maps = $db->query("SELECT * FROM Map WHERE User_id=$userID");
    $user = $db->query("SELECT Login FROM User WHERE User_id=$userID");
}
catch (PDOException $exception){
    printf("<p>Coś poszło nie tak...</p>");
    exit();
}
$user = $user->fetch(PDO::FETCH_ASSOC);
$userName = $user['Login'];
printf("<h1 class='center'>Mapy użytkownika $userName:</h1>");
$maps = $maps->fetchAll(PDO::FETCH_ASSOC);
array_walk($maps, "readMap");
function readMap($map){
    $name = $map['MapName'];
    printf("<h2 class='center'>╰┈➤$name</h2>");
    $fields = $map['Fields'];
    $fieldsArray = explode("+", $fields);
    printf("<table>");
    for($y=1; $y<=$map['SizeY']; $y++){
        printf("<tr>");
        for ($x=1; $x<=$map['SizeX']; $x++){
            $temp = ($map['SizeX']*($y-1))+$x;
            $field = unserialize($fieldsArray[$temp]);
            $img = $field->getImg();
            printf("<td><img src='$img' width='40px' height='40px' style='border: solid black'></td>");
        }
        printf("</tr>");
    }
    printf("</table>");
    printf("<h3 class='center'>⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒</h3>");
}
?>
</body>
</html>
