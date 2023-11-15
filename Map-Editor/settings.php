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
if(isset($_POST['theme'])){
    $theme = $_POST['theme'];
    setcookie("theme", $theme, time()+86400 * 30);
    header("Location:settings.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Strona główna</title>
    <?php
    require_once "theme.php";
    theme();
    ?>
</head>
<body class="center">
<table><tr><td></td>
<a href="mainSite.php"><img src="images/arrow.png" height="30px" width="30px"></a>
<h1>Ustawienia użytkownika</h1>
        <p>✧⋄⋆⋅⋆⋄✧⋄⋆⋅⋆⋄✧</p>
<h3>Wygląd strony</h3>
<form method="post">
    <input class='normal' type='submit' name='theme' value='Piękny dzień' style="font-size: x-small">
    <input class='normal' type='submit' name='theme' value='Nocny marek' style="font-size: x-small">
</form>
        <p>✧⋄⋆⋅⋆⋄✧⋄⋆⋅⋆⋄✧</p>
<h3>Zmień hasło</h3>
<form method="post">
<label>Nowe hasło: </label><input type="password" name="firstPass" required><br>
<label>Powtórz hasło: </label><input type="password" name="secondPass" required><br>
<input class='normal' type='submit' name='changePass' value='Zmień hasło' style="font-size: x-small">
<input type='hidden' name='passID' value='<?$_SESSION["user_id"]?>'>
</form><br>
        <p>✧⋄⋆⋅⋆⋄✧⋄⋆⋅⋆⋄✧</p>
<h3>Zmień e-mail</h3>
<form method="post">
    <label>Nowy adres e-mail:</label><input type="email" name="newEmail" required><br>
    <input class='normal' type='submit' name='changeEmail' value='Zmień e-mail' style="font-size: x-small">
    <input type='hidden' name='passID' value='<?$_SESSION["user_id"]?>'>
</form>
        <p>✧⋄⋆⋅⋆⋄✧⋄⋆⋅⋆⋄✧</p>
<?php

if(isset($_POST['changePass']) && isset($_POST['passID']) && isset($_POST['firstPass']) && isset($_POST['secondPass'])){
    if($_POST['firstPass'] === $_POST['secondPass'] && preg_match("/^[[:graph:]]{6,}$/", $_POST['firstPass'])==1){
     $user_id = $_SESSION['user_id'];
     $password = password_hash($_POST['firstPass'], PASSWORD_DEFAULT);
    $changePass = $db->prepare("UPDATE User SET Password='$password' WHERE User_id=$user_id");
    try{
        $db->beginTransaction();
        $changePass->execute();
        $db->commit();
        printf("<p>Zmiana hasła zakończona poprawnie</p>");
    }
    catch (PDOException $exception){
        $db->rollBack();
    }
    }
else printf("<p>Te hasła nie są takie same, lub hasło nie spełnia kryteriów</p>");
}
if(isset($_POST['changeEmail']) && isset($_POST['newEmail'])){
    if(preg_match("/^\S+@\S+\.\S+$/", $_POST['newEmail'])==1){
        $email = $_POST['newEmail'];
        $user_id = $_SESSION['user_id'];
        $changeEmail = $db->prepare("UPDATE User SET Email='$email' WHERE User_id=$user_id");
        try{
            $db->beginTransaction();
            $users = $db->query("SELECT Email FROM User WHERE Email='$email'");
            if($users->rowCount() == 0){
                $changeEmail->execute();
                $db->commit();
                printf("<p>Zmiana adresu e-mail zakończona poprawnie</p>");
            }
            else printf("<p>Inne konto używa już tego adresu e-mail</p>");
        }
        catch (PDOException $exception){
            $db->rollBack();
            printf("<p>Nastąpił błąd</p>");
        }
    }
    else printf("<p>Podany adres e-mail nie istnieje!</p>");
}
?>
    </tr></table>
</body>
</html>
