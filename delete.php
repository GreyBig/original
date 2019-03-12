<?php

include_once './lib/fun.php';
if(!checkLogin())
{
    msg(2, '请登陆', 'login.php');
}

$goodsId = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : '';

// 如果id不存在，跳转到商品列表
if(!$goodsId)
{
    msg(2, '参数非法', 'index.php'); 
}

// 根据画品id查询商品信息
$con = mysqlInit(); // 连接数据库
$sql = "SELECT * FROM `im_goods` where `id` = {$goodsId}";
$obj = $con->query($sql);
$goods = $obj->fetch();
//var_dump($result);die;
// 当根据id查询商品信息为空 跳转商品到列表页
if(!$goods)
{
    msg(2, '画品不存在', 'index.php');
}
// 删除处理
$sql = "UPDATE `im_goods` SET `status` = '-1' WHERE `id` = {$goodsId} LIMIT 1 ";
$obj = $con->prepare($sql);
$obj->execute();
if($obj)
{
    msg(1,'删除成功','index.php');
}else{
    msg(2,'操作失败','index.php');
}

// 注意项
// 1.项目中 不会真正删除商品 而是更新商品表中的status字段 1：正常操作 -1：删除操作