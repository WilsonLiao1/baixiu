<?php

require_once '../functions.php';

xiu_get_current_user();

function post_add(){
  if(empty($_POST['slug'])
  || empty($_POST['title'])
  || empty($_POST['created'])
  || empty($_POST['content'])
  || empty($_POST['status'])
  || empty($_POST['category'])){
    $GLOBALS['message'] = '请填写完整的内容';
    return;
  }


    $slug = $_POST['slug'];
    $title = $_POST['title'];
    $created = $_POST['created'];
    $content = $_POST['content'];
    $status = $_POST['status'];


  $xiu_repeat = xiu_query("select count(1) from posts where slug ={$slug}");

  if($xiu_repeat){
    $GLOBALS['message'] = '别名已存在';
    return;
  }

  //接受并保存文件

  if (empty($_FILES['feature']['error'])) {
    $temp_file = $_FILES['feature']['tmp_name'];
    $target_file = '../static/uploads/' . $_FILES['feature']['name'];
    if (move_uploaded_file($temp_file, $target_file)) {
      $image_file = '/static/uploads/' . $_FILES['feature']['name'];
    }
  }

  $feature = isset($image_file) ? $image_file : '';
  $user_id = $current_user['id'];
  $category_id = $_POST['category'];


  $sql = sprintf(
    "insert into posts values (null, '%s', '%s', '%s', '%s', '%s', 0, 0, '%s', %d, %d)",
    $slug,
    $title,
    $feature,
    $created,
    $content,
    $status,
    $user_id,
    $category_id
  );

  if(xiu_execute($sql) > 0) {
    header('Location: /admin/posts.php');
    exit;
  }else{
    $message = '保存失败，请重试';
  }
}


if($_SERVER['REQUEST_METHOD'] == 'POST'){
  post_add();
}


$categories = xiu_query('select * from categories');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>

    <div class="container-fluid">
      <div class="page-title">
        <h1>写文章</h1>
      </div>
      <?php if (isset($message)) : ?>
      <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $message; ?>
      </div>
      <?php endif; ?>
      <form class="row" action="/admin/post-add.php" method="post" enctype="multipart/form-data">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" name="title" type="text" value="<?php echo isset($_POST['title']) ? $_POST['title'] : ''; ?>" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">标题</label>
            <textarea id="content" class="form-control input-lg" name="content" cols="30" rows="10" placeholder="内容"><?php echo isset($_POST['content']) ? $_POST['content'] : ''; ?></textarea>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" name="slug" type="text" value="<?php echo isset($_POST['slug']) ? $_POST['slug'] : ''; ?>" placeholder="slug">
            <p class="help-block">https://wlison.me/post/<strong><?php echo isset($_POST['slug']) ? $_POST['slug'] : 'slug'; ?></strong></p>
          </div>
          <div class="form-group">
            <label for="feature">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display: none">
            <input id="feature" class="form-control" name="feature" type="file" accept="image/*">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach ($categories as $item) { ?>
              <option value="<?php echo $item['id']; ?>"<?php echo isset($_POST['category']) && $_POST['category'] == $item['id'] ? ' selected' : ''; ?>><?php echo $item['name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local" value="<?php echo isset($_POST['created']) ? $_POST['created'] : ''; ?>">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted"<?php echo isset($_POST['status']) && $_POST['status'] == 'draft' ? ' selected' : ''; ?>>草稿</option>
              <option value="published"<?php echo isset($_POST['status']) && $_POST['status'] == 'published' ? ' selected' : ''; ?>>已发布</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $current_page = 'post-add'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/simplemde/simplemde.min.js"></script>
  <script src="/static/assets/vendors/moment/moment.js"></script>
  <script>
    $(function () {
      // 当文件域文件选择发生改变过后，本地预览选择的图片
      $('#feature').on('change', function () {
        var file = $(this).prop('files')[0]
        // 为这个文件对象创建一个 Object URL
        var url = URL.createObjectURL(file)
        // 将图片元素显示到界面上（预览）
        $(this).siblings('.thumbnail').attr('src', url).fadeIn()
      })

      // slug 预览
      $('#slug').on('input', function () {
        $(this).next().children().text($(this).val())
      })

      $('#created').val(moment().format('YYYY-MM-DDTHH:mm'))
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
