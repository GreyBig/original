<?php

// 连接数据库
function mysqlInit()
{
    try {
        $con = new PDO('mysql:host=localhost;dbname=original', 'root', '1q2w3e4r'); //初始化一个PDO对象
        // 设置 PDO 错误模式，用于抛出异常
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die ("Error!: " . $e->getMessage() . "<br/>");
    }
    return $con;
}

// 密码加密
function createPassword($password)
{
    if(!$password)
    {
        return false;
    }

    return md5(md5($password).'IMOOC');
}

/**
 * 消息提示
 * 1成功 2失败
 */
function msg($type, $msg=null, $url=null)
{
    $toUrl = "Location:msg.php?type={$type}";
    // 当msg为空时 url不写入
    $toUrl.=$msg?"&msg={$msg}":'';
    // 当url为空时 toUrl不写入
    $toUrl.=$url?"&url={$url}":'';
    header($toUrl);
    exit;
}

/**
 * 上传图片
 */
function imgUpload($file)
{
    // 检查上传文件是否合法 is_uploaded_file() 函数判断指定的文件是否是通过 HTTP POST 上传的。
    if(!is_uploaded_file($file['tmp_name']))
    {
        msg(2, '请上传符合规范的图像');
    }
    
    // 图像类型验证
    $type = $file['type'];
    if(!in_array($type, array("image/png","image/gif","image/jpeg")))
    {
        msg(2, '请上传png,gif,jpg的图像');
    }

    // 上传目录
    $uploadPath = './static/file/';
    // 上传目录访问url
    $uploadUrl = '/static/file/';
    // 上传文件夹
    $fileDir = date('Y/md/', $_SERVER['REQUEST_TIME']);  // 打印出来为 '2019/0204/'
    // 如果没有目录就创建存放图片的目录
    if(!is_dir($uploadPath . $fileDir))
    {
        mkdir($uploadPath . $fileDir, 0755, true); // true为递归创建目录
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)); // 拿到文件扩展名
    
    // 上传图像名称
    $img = uniqid().mt_rand(1000, 9999).'.'.$ext;

    $imgPath = $uploadPath.$fileDir.$img; // 物理地址
    $imgUrl = 'http://original.cc'.$uploadUrl.$fileDir.$img; // url地址  
    // 正常情况是用$_SERVER获取域名

    // 操作失败 查看上传目录的权限
    if(!move_uploaded_file($file['tmp_name'],$imgPath))
    {
        msg(2, '服务器繁忙，请稍后再试');
    }

    return $imgUrl;

}

/**
 * 检查用户是否登陆
 */
function checkLogin()
{
    // 开启session
    session_start();
    if(!isset($_SESSION['user']) || empty($_SESSION['user']))
    {
        return false;
    }

    return true;
}


/**
 * 分页显示
 * @param int $total 数据总数
 * @param int $currentPage 当前页
 * @param int $pageSize 每页显示条数
 * @param int $show 显示按按钮数
 * @return string
 */
function pages($total, $currentPage, $pageSize, $show=6)
{
    $pageStr = '';
    // 仅当总数大于每页显示条数
    if($total>$pageSize)
    {
        // 总页数
        $totalPage = ceil($total/$pageSize); // 向上取整 获取总页数

        // 对当前页进行处理
        $currentPage = $currentPage>$totalPage?$totalPage:$currentPage;
        // 分页起始页
        $from = max(1, $currentPage - intval($show/2));
        // 分页结束页
        $to = $from+$show-1;

        $pageStr .='<div class="page-nav">';
        $pageStr .= '<ul>';
        // 仅当 当前页大于1的时候 存在 首页和上一页按钮
        if($currentPage>1)
        {
            $pageStr .="<li><a href='1'>首页</a></li>";
            $pageStr .="<li><a href='".($currentPage-1)."'>上一页</a></li>";
        }

        // 当结束页大于总页
        if($to>$totalPage)
        {
            $to = $totalPage;
            $from = max(1, $to-$show+1);
        }

        if($from>1)
        {
            $pageStr .='<li>...</li>';
        }

        for($i=$from;$i<=$to;$i++)
        {
            if($i!=$currentPage){
                $pageStr .="<li><a href='".$i."'>{$i}</a></li>";
            }else{
                $pageStr .="<li><span class='curr-page'>{$i}</span></li>";
            }
        }

        if($to<$totalPage)
        {
            $pageStr .='<li>...</li>';
        }

        if($currentPage>$totalPage) 
        {
            $pageStr .="<li><a href='".($currentPage+1)."'>下一页</a></li>";
            $pageStr .="<li><a href='1'>尾页</a></li>";
        }
        $pageStr .= '</ul>';
        $pageStr .= '</div>';
    }

    return $pageStr;
}