<?php
include_once './lib/fun.php';
session_start();
// 释放user
unset($_SESSION['user']);
msg(1,'登出登陆成功', 'index.php');