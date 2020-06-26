<?php
header("Content-Type: text/html; charset=utf-8");
require_once $_SERVER["DOCUMENT_ROOT"]."/functions.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/config.php";

//error_reporting(0);

//require_once("functions.php");

$mail_login    = Config::$yandexLogin;
$mail_password = Config::$yandexPass;
$mail_imap     = "{imap.yandex.ru:993/imap/ssl}";

// Список учитываемых типов файлов
$mail_filetypes = array(
        "MSWORD"
);

$connection = imap_open($mail_imap, $mail_login, $mail_password);
//var_dump($connection);
if(!$connection){

    echo("Ошибка соединения с почтой - ".$mail_login);
    exit;
}else{

    $msg_num = imap_num_msg($connection);
    $mails_data = array();
    $arSearch = [];
    for($i = 1; $i <= $msg_num; $i++){
        $msg_header = imap_header($connection, $i);
        $testAddress = "";
        foreach($msg_header->from as $data){
            $testAddress =  $data->mailbox."@".$data->host;
        }
        if ($testAddress == Config::$address) {
            $mails_data[strtotime($msg_header->MailDate)]["address"];
            $mails_data[strtotime($msg_header->MailDate)]["time"] = $arSearch[] = strtotime($msg_header->MailDate);
            $mails_data[strtotime($msg_header->MailDate)]["date"] = date("Y-m-d H:i:s", strtotime($msg_header->MailDate));

            $mails_data[strtotime($msg_header->MailDate)]["subject"] = get_imap_title($msg_header->subject);
            //$mails_data[$i]["name_user"] = get_imap_title($msg_header->fromaddress);

            $msg_structure = imap_fetchstructure($connection, $i);
            $msg_body = imap_fetchbody($connection, $i, 1);
            $body = "";
            $recursive_data = recursive_search($msg_structure);

            if ($recursive_data["encoding"] == 0 ||
                    $recursive_data["encoding"] == 1
            ) {

                $body = $msg_body;
            }

            if ($recursive_data["encoding"] == 4) {

                $body = structure_encoding($recursive_data["encoding"], $msg_body);
            }

            if ($recursive_data["encoding"] == 3) {

                $body = structure_encoding($recursive_data["encoding"], $msg_body);
            }

            if ($recursive_data["encoding"] == 2) {

                $body = structure_encoding($recursive_data["encoding"], $msg_body);
            }

            if (!check_utf8($recursive_data["charset"])) {

                $body = convert_to_utf8($recursive_data["charset"], $msg_body);
            }

            $body = str_replace(["\t", "\r", "  "], "", $body);

            //$mails_data[$i]["body"] = strip_tags($body);
        }
    }
}

/*vd($arSearch, true);
vd($mails_data, true);*/
addMessages($arSearch, $mails_data);
imap_close($connection);