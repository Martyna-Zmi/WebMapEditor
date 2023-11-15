<?php
function theme(){
    if(isset($_COOKIE['theme']) && $_COOKIE['theme']=='Nocny marek'){
        printf("<link rel='stylesheet' href='layoutDark.css'>");
    }
    else{
        printf("<link rel='stylesheet' href='layout.css'>");
    }
}
?>