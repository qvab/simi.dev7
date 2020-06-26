<?php
header("Content-type: text/html; charset=UTF-8");
require_once $_SERVER["DOCUMENT_ROOT"] . "/config.php";

$arParams = [
        "user_id" => Config::$managerId,
        "access_token" => Config::$token,
        "message" => "Testing",
        "v"=> "5.37"
];
var_dump("https://api.vk.com/method/messages.send?".http_build_query($arParams));
$res = file_get_contents("https://api.vk.com/method/messages.send?".http_build_query($arParams));
var_dump($res);

/*
echo "https://api.vk.com/method/messages.send?user_id=112324212&message=Test_автоматического_сообщения&v=5.37&acces*s_token=a4f6e4276c7dc76d33a13e913d7b1a4aa11b0f05b2baf61ef0962f9f419735ecd446e485ed36be51c6025";
*/