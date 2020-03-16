<?php
while (true){
    echo file_get_contents('./txt.txt')."\n";
    sleep(3);
}