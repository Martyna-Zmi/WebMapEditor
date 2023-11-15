<?php
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
    <title>Strona główna</title>
    <?php
    require_once "theme.php";
    theme();
    ?>
</head>
<body>
<form method='post'><input type='submit' class='normal' name='logOut' value='Wyloguj'></form>
<h1 class="center">𓆩⟡𓆪 Strona główna Edytora Map 𓆩⟡𓆪</h1>
<?php
$thisUser = $_SESSION['logged'];
$user = $db->query("SELECT * FROM User WHERE Login='$thisUser'");
$user = $user->fetch(PDO::FETCH_ASSOC);
$admin = $user['Is_admin'];
$id = $user['User_id'];
$_SESSION['user_id'] = $id;
printf("<h3 class='center'>Witaj, $thisUser! Przejdź do <a href='settings.php'>ustawień konta</a></h3>");
printf("<div class='center'>");
if($admin==1){
    printf("<a href='adminPanel.php'>Przejdź do panelu administratora</a>");
}
?>
<h3>︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵</h3>
<h3 class="center">Twoje Mapy:</h3>
<?php
$maps = $db->query("SELECT Map_id, MapName, SizeX, SizeY FROM Map WHERE User_id='$id'");
if($maps->rowCount() != 0){
    while($row = $maps->fetch(PDO::FETCH_ASSOC)){
        $mapName = $row['MapName'];
        $map_id = $row['Map_id'];
        printf("<form method='post'><input class='normal' type='submit' name='edit' value='$mapName'><input type='hidden' name='editID' value='$map_id'></form><br>");
    }
}
else{
    printf("<p class='center'><strong>Nie posiadasz jeszcze map...</strong></p>");
}
?>
<h3>︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵︵‿︵‿︵‿︵</h3>
<br>
<form method="post">
    <input type="submit" class="big" value="Stwórz nową mapę!" name="createMap">
</form>
</div>

<?php
if(isset($_POST['logOut'])){
    session_destroy();
    header('Location:mapLogin.php');
}
if(isset($_POST['createMap'])){
    header("Location:createMap.php");
}
if(isset($_POST['edit']) && isset($_POST['editID'])){
    $_SESSION['edit'] = $_POST['edit'];
    $_SESSION['editID'] = $_POST['editID'];
    header("Location:mapEditor.php");
}
?>
</body>
</html>
