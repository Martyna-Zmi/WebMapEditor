<?php
require_once "objects/OnMap.php";
require_once "connect.php";
session_start();
if(!isset($_SESSION['logged']) || !isset($_SESSION['passed'])){
    header("Location:mapLogin.php");
}
if(!isset($_SESSION['user_id'])){
    header("Location:mainSite.php");
}
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
    <title>Stwórz mapę</title>
    <?php
    require_once "theme.php";
    theme();
    ?>
</head>
<body>
<div class="center">
    <a href="mainSite.php"><img src="images/arrow.png" height="30px" width="30px"></a>
    <h3>°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。</h3>
    <h1>Stwórz nową mapę:</h1>
</div>
<table style="margin-left: auto; margin-right: auto">
    <tr>
        <td>
<form method='post'>
    <label>Rozmiar mapy: </label><select name='sizes' required>
        <option>15x10</option>
        <option>20x10</option>
        <option>25x10</option>
        <option>30x10</option>
    </select><br>
    <label>Nazwa mapy: </label><input type='text' name='mapName' required maxlength="8"><br>
    <input type='submit' value='Utwórz!' class="normal" name='createMap' required>
</form>
        </td>
    </tr>
</table>
<h3 class="center">°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。°。</h3>
<?php
$possible_sizes = array("15x10", "20x10", "25x10", "30x10");
if(isset($_POST['createMap']) && isset($_POST['mapName']) && isset($_POST['sizes'])) {
    $user_id = $_SESSION['user_id'];
    $mapsNumber = $db->query("SELECT Maps_owned, Premium FROM User WHERE User_id='$user_id'");
    $mapsNumber = $mapsNumber->fetch(PDO::FETCH_ASSOC);
    if($mapsNumber['Maps_owned']>=3 && $mapsNumber['Premium']==0){
        printf("<h4 class='center'>Maksymalna ilość map dla użytkownika to 3. Kup wersję premium i uzyskaj nieograniczoną ilość map!</h4>");
        exit();
    }
    $name = $_POST['mapName'];
    $sizes = $_POST['sizes'];
    if(in_array("$sizes", $possible_sizes)){
            $sizes = explode("x", $sizes);
            $sizeX = $sizes[0];
            $sizeY = $sizes[1];
            $date = date("d-m-Y");
            $grass = $db->query("SELECT Info FROM Object WHERE Object_id=1"); //pobiera trawe
            $info = $grass->fetch(PDO::FETCH_ASSOC);
            $info = $info["Info"];
            $emptyMap = '';
            for ($i = 0; $i < $sizeY * $sizeX; $i++) {
                $emptyMap = $emptyMap . "+" . $info;
            }
            try {
                $createMap = $db->prepare("INSERT INTO Map(User_id, MapName, Creation_date, Fields, SizeX, SizeY) VALUES ('$user_id', '$name', '$date', '$emptyMap', '$sizeX', '$sizeY')");
                $createMap->execute();
                $db->query("UPDATE User SET Maps_owned=Maps_owned + 1 WHERE User_id='$user_id'");
                header("Location:mainSite.php");
            } catch (PDOException $exception) {
                printf("<p>Coś poszło nie tak...</p>");
            }
        }
    else{
        printf("<p>Widzę, że majstrujesz przy html... Nic z tego!!!</p>");
    }
}
?>
</body>
</html>
