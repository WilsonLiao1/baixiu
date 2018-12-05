<?php 

require_once '../functions.php';

if(empty($_GET)) {
    exit('缺少必要参数');
}
$id = $_GET['id'];

$rows = xiu_execute('delete from categories where id in (' . $id . ');');

header('Location: /admin/categories.php');

