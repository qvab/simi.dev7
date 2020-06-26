<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/miwr/classes/class.log.php";
function vd($data, $bPrint = false)
{
    echo '<pre>';
    if (empty($bPrint)) {
        var_dump($data);
    } else {
        print_r($data);
    }
    echo '</pre>';
}


function check_utf8($charset)
{
    if (strtolower($charset) != "utf-8") {
        return false;
    }
    return true;
}

function convert_to_utf8($in_charset, $str)
{
    return iconv(strtolower($in_charset), "utf-8", $str);
}

function get_imap_title($str)
{
    $mime = imap_mime_header_decode($str);
    $title = "";
    foreach ($mime as $key => $m) {
        if (!check_utf8($m->charset)) {
            $title .= convert_to_utf8($m->charset, $m->text);
        } else {
            $title .= $m->text;
        }
    }
    return $title;
}

function recursive_search($structure)
{

    $encoding = "";

    if ($structure->subtype == "HTML" ||
            $structure->type == 0
    ) {

        if ($structure->parameters[0]->attribute == "charset") {

            $charset = $structure->parameters[0]->value;
        }

        return array(
                "encoding" => $structure->encoding,
                "charset" => strtolower($charset),
                "subtype" => $structure->subtype
        );
    } else {

        if (isset($structure->parts[0])) {

            return recursive_search($structure->parts[0]);
        } else {

            if ($structure->parameters[0]->attribute == "charset") {

                $charset = $structure->parameters[0]->value;
            }

            return array(
                    "encoding" => $structure->encoding,
                    "charset" => strtolower($charset),
                    "subtype" => $structure->subtype
            );
        }
    }
}

function structure_encoding($encoding, $msg_body)
{

    switch ((int)$encoding) {

        case 4:
            $body = imap_qprint($msg_body);
            break;

        case 3:
            $body = imap_base64($msg_body);
            break;

        case 2:
            $body = imap_binary($msg_body);
            break;

        case 1:
            $body = imap_8bit($msg_body);
            break;

        case 0:
            $body = $msg_body;
            break;

        default:
            $body = "";
            break;
    }

    return $body;
}

function addMessages($data, $arMessages)
{
    $obLogs = new \MIWR\Logs;
    $obLogs->getList([
            "where" => "WHERE time IN (" . implode(",", $data) . ")",
            "limit" => "LIMIT 99999"
    ]);
    $arValues = [];
    while ($arList = $obLogs->fetch()) {
        if (!empty($arMessages[$arList["time"]])) {
            unset($arMessages[$arList["time"]]);
        }
    }
    foreach ($arMessages as $arMessage) {
        $arValues[] = [
                $arMessage["time"]
        ];
    }
    //vd($arMessages);
    if (!empty($arValues)) {
        $res = $obLogs->insert([
                "set" => ["time"],
                "values" => $arValues
        ]);
        sendMessage($arMessages);
    }
}

function sendMessage($arData)
{
    $sText = "Привет, проверьте почту! :)\nНовый заказ уже ждёт";
    if (count($arData) > 1) {
        $sText = "Привет, проверьте почту! :)\nНовый заказ уже ждёт\n Даты заказов:";
        foreach ($arData as $arDatum) {
            $sText .= "\n".$arDatum["date"];
        }
    }
    $arParams = [
            "user_id" => Config::$managerId,
            "access_token" => Config::$token,
            "message" => $sText,
            "v"=> "5.37"
    ];
    $res = file_get_contents("https://api.vk.com/method/messages.send?".http_build_query($arParams));
}
