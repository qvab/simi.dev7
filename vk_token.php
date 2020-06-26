<?php
namespace VK;
use VK\OAuth\VKOAuth;
require_once $_SERVER["DOCUMENT_ROOT"]."/vendor/autoload.php";
$oauth = new VKOAuth();
$client_id = 7521522;
$client_secret = '7MCHkOjExWN13746VZXg';
$redirect_uri = 'https://vkapi.mishaninlab.ru/redirect.php';
$code = '62799ebaefae6a1215&state=6e77a6cf6e77a6cf6e77a6cfbc6e05623d66e776e77a6cf3083fd63743870c6d403d329';
$response = $oauth->getAccessToken($client_id, $client_secret, $redirect_uri, $code);
$access_token = $response['access_token'];
var_dump($access_token);