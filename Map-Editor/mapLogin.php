<?php
session_start();
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
    <title>Logowanie</title>
    <?php
    require_once "theme.php";
    theme();
    ?>
</head>
<body class="center">
<?php
if(isset($_SESSION['logged']) && isset($_SESSION['passed'])){
    $_POST['login'] = $_SESSION['logged'];
    $_POST['password'] = $_SESSION['passed'];
}
?>
<h1>🌠Witaj w edytorze map!🌠</h1>
<h3>┌──═━┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈━═──┐</h3>
    <h3>Logowanie</h3>
    <form method="post">
        <label>Login: </label><input type="text" name="login" required><br>
        <label>Hasło: </label><input type="password" name="password" required><br>
        <input type="submit" name="log-in" value="Zaloguj">
    </form>
    <p>Nie posiadasz jeszcze konta? <a href="mapRegister.php">Zarejestruj się!</a> </p>
<h3>└──═━┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈━═──┘</h3>
<?php
if(isset($_POST['login']) && isset($_POST['password'])){
    $login = $_POST['login'];
    $password = $_POST['password'];
    try{
        $user = $db->prepare("SELECT Password, Is_banned FROM User WHERE Login = '$login'");
        $user->execute();
    }
    catch (PDOException $exception){
        printf("UPS! Wygląda na to, że wystąpił błąd po naszej stronie");
        exit();
    }
    if($user->rowCount() == 1){
        $user = $user->fetch(PDO::FETCH_ASSOC);
        if(password_verify($password, $user['Password'])){
            $isBanned = $user['Is_banned'];
            if($isBanned==1){
                printf("Otrzymałeś BANA! Lepiej przemyśl swoje zachowanie");
            }
            else{
                $_SESSION['logged'] = $login;
                $_SESSION['passed'] = $password;
                header("Location:mainSite.php");
            }
        }
        else{
            printf("<p>Nieprawidłowy login lub hasło. Spróbój ponownie!</p>");
        }
    }
    else printf("<p>Nieprawidłowy login lub hasło. Spróbój ponownie!</p>");
}
?>
</body>
</html>
