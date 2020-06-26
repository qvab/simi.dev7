<?php
namespace VK;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\Scopes\VKOAuthGroupScope;
use VK\OAuth\VKOAuthResponseType;
require_once $_SERVER["DOCUMENT_ROOT"]."/vendor/autoload.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/config.php";


//$vk = new VKApiClient();
$oauth = new VKOAuth();
$client_id = 7521522;
$redirect_uri = 'https://vkapi.mishaninlab.ru/redirect.php';
$display = VKOAuthDisplay::PAGE;
$scope = array(VKOAuthGroupScope::MESSAGES);
$state = '6e77a6cf6e77a6cf6e77a6cfbc6e05623d66e776e77a6cf3083fd63743870c6d403d329';
$groups_ids = array(\Config::$groupId);
$browser_url = $oauth->getAuthorizeUrl(VKOAuthResponseType::CODE, $client_id, $redirect_uri, $display, $scope, $state, $groups_ids);
echo $browser_url;