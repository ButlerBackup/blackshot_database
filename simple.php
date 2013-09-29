<?php 
include "config.php";

$player = new Blackshot();
$player->setID(15718425);
$player->getData();
$data = $player->parseData();
print_r($data);