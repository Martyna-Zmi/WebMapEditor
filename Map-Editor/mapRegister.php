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
    <title>Rejestracja</title>
    <?php
    require_once "theme.php";
    theme();
    ?>
</head>
<body>
<h2 class="center">Zarejestruj się za darmo</h2>
<h4 class="center">Zacznij tworzyć swoje własne mapy. To takie proste!</h4>
<table>
    <tr><td>
            <h3>⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒</h3>
    <h1>Formularz rejestracyjny</h1>
    <form method="post">
        <label>Login: </label><input type="text" name="login" required><br>
        <label>Hasło: </label><input type="password" name="password" required minlength="6">
        <label title="Powinno zawierać 6 znaków i nie powinno zawierać spacji"><strong>*</strong></label><br>
        <label>Email: </label><input type="email" name="email" required><br><br>
        <input type="submit" value="Zarejestruj się!" class="big" name="register">
    </form>
            <h3>⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒⌒</h3>
        </td></tr>
</table>
<?php
if(isset($_POST['register'])){
    if(preg_match("/^[[:alnum:]_-]*$/", $_POST['login'])==1){
        if(preg_match("/^[[:graph:]]{6,}$/", $_POST['password'])==1){
            if(preg_match("/^\S+@\S+\.\S+$/", $_POST['email'])==1){
                try{
                    $loginToCheck = $_POST['login'];
                    $users = $db->query("SELECT Login FROM User WHERE Login='$loginToCheck'");
                    if($users->rowCount() == 0){
                        $emailToCheck = $_POST['email'];
                        $users = $db->query("SELECT Email FROM User WHERE Email='$emailToCheck'");
                        if($users->rowCount() == 0){
                            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                            $db->query("INSERT INTO User(Login, Password, Email, Maps_owned, Is_admin, Is_banned, Premium) VALUES ('$loginToCheck', '$password', '$emailToCheck', 0, 0, 0, 0)");
                            $_SESSION['logged'] = $loginToCheck;
                            $_SESSION['passed'] = $password;
                            header("Location:mainSite.php");
                        }
                        else printf("<p>Inne konto używa już tego adresu email</p>");
                    }
                    else printf("<p>Konto o podanym loginie już istnieje</p>");
                }
                catch (PDOException $exception){
                    printf("<p>UPS! Wygląda na to, że mamy błąd po naszej stronie!</p>");
                }
            }
            else printf("Taki email nie istnieje!");
        }
        else printf("Wprowadzono niedozwolone hasło<br>");
    }
    else printf("Wprowadzono niedozwolony login<br>");
}
?>
</body>
</html>

