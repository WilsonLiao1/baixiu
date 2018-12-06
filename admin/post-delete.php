<?php 

require_once '../functions.php';

if(empty($_GET)) {
    exit('缺少必要参数');
}
$id = $_GET['id'];

$rows = xiu_execute('delete from posts where id in (' . $id . ');');

header('Location: ' . $_SERVER['HTTP_REFERER']);


//http中的referer用来标识当前请求的来源
