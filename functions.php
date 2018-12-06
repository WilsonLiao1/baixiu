<?php 

require_once 'config.php';

/**
 * 封装公用函数
 */

 session_start();

/**
 * 获取当前用户的用户信息，如果没有则自动跳转登录页
 * @return [type] [description]
 */


 function xiu_get_current_user () {
     if(empty($_SESSION['current_login_user'])){
         header('Location: /admin/login.php');
         exit();
     }
     return $_SESSION['current_login_user'];
 }


/**
 * 根据配置文件信息创建一个数据库连接，注意用完以后需要关闭
 * @return mysqli 数据库连接对象
 */
 function xiu_connect () {
  $connection = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASS, XIU_DB_NAME);

  if (!$connection) {
    // 如果连接失败报错
    die('<h1>Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '</h1>');
  }

  // 设置数据库编码
  mysqli_set_charset($connection, 'utf8');

  return $connection;
}

/**
 * 通过一个数据库查询多条数据
 * =>索引数组套关联数组
 */

 function xiu_fetch_all($sql){
    $conn = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASS, XIU_DB_NAME);
    if (!$conn) {
      exit('连接失败');
    }
    mysqli_set_charset($conn, 'utf8');
    $query = mysqli_query($conn, $sql);
    if (!$query) {
      return false;
    }

    while ($row = mysqli_fetch_assoc($query)) {
        $result[] = $row;
      }
    
    mysqli_free_result($query);
    mysqli_close($conn);

    return $result;
 }

 /**
 * 获取单条数据
 * => 关联数组
 */
function xiu_fetch_one ($sql) {
    $res = xiu_fetch_all($sql);
    return isset($res[0]) ? $res[0] : null;
  }

/**
 * 执行一个增删改语句
 */
function xiu_execute ($sql) {
    $conn = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASS, XIU_DB_NAME);
    if (!$conn) {
      exit('连接失败');
    }
  
    mysqli_set_charset($conn, 'utf8');
    $query = mysqli_query($conn, $sql);
    if (!$query) {
      return false;
    }
  
    // 对于增删修改类的操作都是获取受影响行数
    $affected_rows = mysqli_affected_rows($conn);
  
    mysqli_close($conn);
  
    return $affected_rows;
  }


  /**
 * 执行一个查询语句，返回查询到的数据（关联数组混合索引数组）
 * @param  string $sql 需要执行的查询语句
 * @return array       查询到的数据（二维数组）
 */
  function xiu_query ($sql) {
    // 获取数据库连接
    $connection = xiu_connect();

    mysqli_set_charset($connection, 'utf8');
  
    // 定义结果数据容器，用于装载查询到的数据
    $data = array();
  
    // 执行参数中指定的 SQL 语句
    if ($result = mysqli_query($connection, $sql)) {
      // 查询成功，则获取结果集中的数据
    
      // 遍历每一行的数据
      while ($row = mysqli_fetch_array($result)) {
        // 追加到结果数据容器中
        $data[] = $row;
      }
    
      // 释放结果集
      mysqli_free_result($result);
    }
  
    // 关闭数据库连接
    mysqli_close($connection);
  
    // 返回容器中的数据
    return $data;
  }
  
  