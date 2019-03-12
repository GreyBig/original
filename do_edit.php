<?php
include_once './lib/fun.php';

if(!checkLogin())
{
    msg(2, '请登录', 'login.php');
}

if (!empty($_POST['name']))
{   
    $con = mysqlInit(); // 连接数据库

    if(!$goodsId = intval($_POST['id']))
    {
        msg(2,'参数非法', 'index.php');
    }
    // 根据画品ID校验商品信息
    $sql = "SELECT * FROM `im_goods` where `id` = {$goodsId}";
    $obj = $con->query($sql);
    $goods = $obj->fetch();

    // 当根据id查询商品信息为空 跳转商品到列表页
    if(!$goods)
    {
        msg(2, '画品不存在', 'index.php');
    }

    $name = trim($_POST['name']);
    $price = intval($_POST['price']);
    $des = trim($_POST['des']);
    $content = trim($_POST['content']);

    // 商品名称、价格、简介、详情的一些处理
    $nameLength = mb_strlen($name, 'utf-8');
    if($nameLength<=0||$nameLength>30)
    {
        msg(2, '画品名应在1-30字符之内');
    }
    if($price<=0||$price>99999999)
    {
        msg(2, '画品价格应小于99999999');
    }
    $desLength = mb_strlen($des, 'utf-8');
    if($desLength<=0||$desLength>100)
    {
        msg(2, '画品简介应在1-100字符之内');
    }
    if(empty($content))
    {
        msg(2, '画品详情不能为空');
    }

    // 更新数组
    $update = array(
        'name' => $name,
        'price' => $price,
        'des' => $des,
        'content' => $content,
        'update_time' => time()
    );

    // 仅当用户选择上传图片 才进行图片上传处理
    if($_FILES['file']['size'] > 0)
    {
        $pic = imgUpload($_FILES['file']);
        $update['pic'] = $pic;
    }

    // 只更新被用户更改的信息 对比数据库数据跟用户表单数据
    foreach($update as $k=>$v)
    {
        if($goods[$k] == $v)  // 对应的key相等 就删除
        {
            unset($update[$k]);
        }
    }

    // 没有更新就跳回去
    if(empty($update))
    {
        msg(1, '操作成功', 'edit.php?id=' . $goodsId);
    }

    // 更新sql处理
    $updateSql = '';
    foreach($update as $k=>$v)
    {
        $updateSql .= "`{$k}` = '{$v}' ,";
    }
    $updateSql = rtrim($updateSql, ',');
    //var_dump($updateSql);die;

    unset($sql, $obj, $result);
    $sql = "UPDATE `im_goods` SET {$updateSql} WHERE `id` = {$goodsId}";
    $result = $con->prepare($sql);
    $result->execute();
    //var_dump($result);die;
    //当更新成功
    if($result){
        //$row = $result->rowCount(); // 影响行数
        msg(1, ' 操作成功', 'edit.php?id=' . $goodsId);
    }else{
        msg(2, '操作失败', 'edit.phpid' . $goodsId);
    }

}
else
{
    msg(2,'路由非法', 'index.php');
}