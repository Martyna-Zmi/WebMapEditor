<?php
interface DisplayForAdmin{
    public function displayForAdmin();

}
trait Dangerous{
    protected $isDangerous;
    public function getIsDangerous(){
        return $this->isDangerous;
    }
    public function infoDanger(){
        if($this->isDangerous==1){
            return "Niebezpieczny! ";
        }
    }
}
abstract class OnMap implements DisplayForAdmin {
    protected $imgPath;
    protected $type;
    public function __construct($imgPath, $type){
        $this->imgPath = $imgPath;
        $this->type = $type;
    }
    public function getType(){
        return $this->type;
    }
    public function getImg(){
        return $this->imgPath;
    }
    public abstract function info();
    public function displayForAdmin(){
        return $this->info()."<img src='".$this->getImg()."' width='30px' height='30px' border='2px'>";
    }
}
class Terrain extends OnMap{
    protected $biome;
    public function __construct($imgPath, $biome){
        $this->biome = $biome;
        parent::__construct($imgPath, "Terrain");
    }
    public function getBiome(){
        return $this->biome;
    }
    public function info(){
        return "Typ: Teren, Biom: $this->biome";
    }
}
class Obstacle extends OnMap{
    use Dangerous;
    protected $obstacleName;
    public function __construct($imgPath, $obstacleName, $isDangerous){
        $this->obstacleName = $obstacleName;
        $this->isDangerous = $isDangerous;
        parent::__construct($imgPath, "Obstacle");
    }
    public function getObstacleName(){
        return $this->obstacleName;
    }
    public function info(){
        return $this->infoDanger()."Typ: Przeszkoda, Rodzaj: $this->obstacleName ";
    }
}
class ForPlayer extends OnMap {
    protected $forPlayerType;
    public function __construct($imgPath, $forPlayerType){
        $this->forPlayerType = $forPlayerType;
        parent::__construct($imgPath, "ForPlayer");
    }
    public function getForPlayerType(){
        return $this->forPlayerType;
    }
    public function info()
    {
        return "Typ: obiekt dla gracza, Rodzaj: $this->forPlayerType";
    }
}
class Animal extends OnMap{
    use Dangerous;
    protected $animalType;

    public function getAnimalType(){
        return $this->animalType;
    }
    public function __construct($imgPath, $animalType, $isDangerous){
        $this->isDangerous = $isDangerous;
        $this->animalType = $animalType;
        parent::__construct($imgPath, "Animal");
    }
    public function info(){
        return $this->infoDanger()."Typ: zwierzę, Rodzaj: $this->animalType";
    }
}
?>