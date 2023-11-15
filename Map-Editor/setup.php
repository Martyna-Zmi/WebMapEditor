//skrypt inicjalizujący wartości w bazie danych i generujący administratora
<?php
$adminLogin = "admin";
$adminPassword = "";
$adminPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
$adminEmail = "admin@gmail.com";
require_once "connect.php";
$details = dataBaseDetails();
$db_name = $details['name'];
$db_pass = $details['pass'];
$db_user = $details['user'];
$db = connectToDatabase($db_name, $db_pass, $db_user);

try {
    $db->query("USE $db_name");
}
catch (PDOException $exception){
    printf("Błąd: %s \n", $exception->getMessage());
}
try{
    $db->query("CREATE TABLE User(User_id INTEGER AUTO_INCREMENT,
                         Login varchar(255),
                         Password varchar(255),
                         Email varchar(255),
                         Maps_owned INTEGER,
                         Is_admin TINYINT(1),
                         Is_banned TINYINT(1),
                         Premium TINYINT(1),
                         PRIMARY KEY (User_id))");
    printf("Tabela User została utworzona\n");
}
catch (PDOException $exception){
    printf("Tabela User istnieje\n");
}
try {
    $db->query("CREATE TABLE Object(Object_id INTEGER AUTO_INCREMENT,
                         Info varchar(255),
                         PRIMARY KEY (Object_id))");
    //teren
    require_once "objects/OnMap.php";
    $grass = (serialize(new Terrain("images/grass.png", 'Trawa')));
    $db->query("INSERT INTO Object(Info) VALUES ('$grass')");
    $flowers = (serialize(new Terrain("images/flowery.jpg", 'Łąka')));
    $db->query("INSERT INTO Object(Info) VALUES ('$flowers')");
    $sand = (serialize(new Terrain("images/sand.png", 'Piasek')));
    $db->query("INSERT INTO Object(Info) VALUES ('$sand')");
    $water = (serialize(new Terrain("images/water.jpg", 'Woda')));
    $db->query("INSERT INTO Object(Info) VALUES ('$water')");
    $water = (serialize(new Terrain("images/swamp3.png", 'Bagno')));
    $db->query("INSERT INTO Object(Info) VALUES ('$water')");
    $water = (serialize(new Terrain("images/swamp1.png", 'Bagno z liliami war.1')));
    $db->query("INSERT INTO Object(Info) VALUES ('$water')");
    $water = (serialize(new Terrain("images/swamp2.png", 'Bagno z liliami war.2')));
    $db->query("INSERT INTO Object(Info) VALUES ('$water')");
    //przeszkody
    $lava = serialize(new Obstacle("images/lava.png", "Lawa", 1));
    $db->query("INSERT INTO Object(Info) VALUES ('$lava')");
    $tree = serialize(new Obstacle("images/tree.jpg", "Drzewko", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$tree')");
    $deadTree = serialize(new Obstacle("images/deadtreegrass.jpg", "Martwe drzewo", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$deadTree')");
    $deadTree = serialize(new Obstacle("images/deadtreesand.jpg", "Martwe drzewo pustynne", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$deadTree')");
    $wall = serialize(new Obstacle("images/walltop.png", "Mur wariant 1", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$wall')");
    $wall = serialize(new Obstacle("images/wallbottom.png", "Mur wariant 2", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$wall')");
    $wall = serialize(new Obstacle("images/wallleft.png", "Mur wariant 3", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$wall')");
    $wall = serialize(new Obstacle("images/wallright.png", "Mur wariant 4", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$wall')");
    $cactus = serialize(new Obstacle("images/cactus.png", "Kaktusy", 1));
    $db->query("INSERT INTO Object(Info) VALUES ('$cactus')");
    $cactus = serialize(new Obstacle("images/palm.png", "Palma", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$cactus')");
    $cactus = serialize(new Obstacle("images/rockgrass.png", "Skały", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$cactus')");
    $cactus = serialize(new Obstacle("images/rocksand.png", "Skały pustynne", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$cactus')");
    //dla gracza
    $chest = serialize(new ForPlayer("images/chest.png", "Skrzynia"));
    $db->query("INSERT INTO Object(Info) VALUES ('$chest')");
    $chest = serialize(new ForPlayer("images/treasure.png", "Skarb pustynny"));
    $db->query("INSERT INTO Object(Info) VALUES ('$chest')");
    $campfire = serialize(new ForPlayer("images/campfire.png", "Ognisko"));
    $db->query("INSERT INTO Object(Info) VALUES ('$campfire')");
    $spawn = serialize(new ForPlayer("images/spawn.png", "Spawn"));
    $db->query("INSERT INTO Object(Info) VALUES ('$spawn')");
    //zwierzeta
    $dragon = serialize(new Animal("images/dragonsand.png", "Smok pustynny", 1));
    $db->query("INSERT INTO Object(Info) VALUES ('$dragon')");
    $dragon = serialize(new Animal("images/dragongrass.png", "Smok", 1));
    $db->query("INSERT INTO Object(Info) VALUES ('$dragon')");
    $capy = serialize(new Animal("images/capybara.png", "Kapibara", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$capy')");
    $dog = serialize(new Animal("images/doggrass.png", "Pies", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$dog')");
    $dog = serialize(new Animal("images/dogsand.png", "Pies pustynny", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$dog')");
    $cow = serialize(new Animal("images/cow.png", "Krowa", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$cow')");
    $cow = serialize(new Animal("images/catgrass.png", "Kotek", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$cow')");
    $cow = serialize(new Animal("images/catsand.png", "Kotek pustynny", 0));
    $db->query("INSERT INTO Object(Info) VALUES ('$cow')");
    printf("Tabela Object została utworzona\n");
}
catch (PDOException $exception){
    printf("Tabela Object istnieje \n");
}
try {
    $db->query("CREATE TABLE Map(Map_id INTEGER AUTO_INCREMENT,
                         User_id INTEGER,
                         MapName varchar(255),
                         Creation_date varchar(255),
                         Fields LONGTEXT,
                         SizeX INTEGER,
                         SizeY INTEGER,
                         PRIMARY KEY (Map_id),
                         FOREIGN KEY (User_id) REFERENCES User(User_id))");
    printf("Tabela Map została utworzona\n");
}
catch (PDOException $exception){
    printf("Tabela Map istnieje\n");
}
try{
    $db->query("INSERT INTO User(Login, Password, Email, Maps_owned, Is_admin, Is_banned, Premium) VALUES ('$adminLogin', '$adminPassword','$adminEmail', 0, 1, 0, 1)");
    printf("Wygenerowano Konto administratora");
}
catch (PDOException $e){
    printf("Nie udało się wygenerować konta administratora %s", $e);
}
?>
