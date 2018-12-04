<?php 

    require_once '../../config.php';

    if (empty($_GET['email'])) {
        exit('缺少必要参数');
    }
    $email = $_GET['email'];

    $conn = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASS, XIU_DB_NAME);

    if (!$conn) {
        exit('数据库连接失败');
    }

    $res = mysqli_query($conn, "select avatar from users where email = '{$email}' limit 1;");

    if (!$res) {
        exit('数据查询失败');
    }

    $row = mysqli_fetch_assoc($res);

    echo $row['avatar'];



