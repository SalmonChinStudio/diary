<?php
/**
 * 用户的相关操作方法
 * Created by PhpStorm.
 * User: Kyle
 * Date: 2019-03-13
 * Time: 19:24
 */

require "class/Response.php";
require "class/MSql.php";


define('INVITATION','kylebingooOO');


switch ($_POST['type']){
    case 'insert':
        if (isset($_POST['invitation']) && $_POST['invitation'] == INVITATION){
            addNewUser($_POST['email'],$_POST['password']);
        } else {
            $response = new ResponseError('邀请码不正确');
            echo $response->toJson();
        }
        break;

    case 'update':
        updatePassword($_POST['email'],$_POST['oldPassword'],$_POST['newPassword']);
        break;
    default:
        $response = new ResponseError('请求参数错误');
        echo $response->toJson();
}



// 查询注册用户是否存在
function addNewUser($email, $password){
    $con = new dsqli();
    $result = $con->query(MSql::QueryEmailExitance($email));
    $response = '';

    if ($result){
        if ($result->num_rows === 0){ // 如果用户不存在，新建用户
            $sql = MSql::InsetNewUser($email,$password);
        $result = $con->query(MSql::InsetNewUser($email,password_hash($password,PASSWORD_DEFAULT)));

            if ($result) {
                $response = new ResponseSuccess('注册成功');
            } else {
                $response = new ResponseError('注册失败');
            }
        } else { // 如果用户已存在，返回结果
            $response =  new ResponseError('用户已存在');
        }
    } else {
        $response = new ResponseError('查询失败');
    }
    echo $response->toJson();
    $con->close();
}

// 更新密码
function updatePassword($email, $oldPassword, $newPassword){
    $con = new dsqli();
    $result = $con->query(MSql::QueryUserPassword($email));
    $response = '';

    if ($result){
        $response = '';
        if ($result->num_rows !== 0){ // 存在用户
            $row = $result->fetch_array();
            if (password_verify($oldPassword, $row['password'])){
                if ($con->query(MSql::UpdateUserPassword($email,password_hash($newPassword,PASSWORD_DEFAULT))) === true){
                    $response = new ResponseSuccess('密码修改成功');
                } else {
                    $response = new ResponseError('修改密码失败');
                }
            } else {
                $response = new ResponseError('原密码不正确');
            }
        } else { // 查无此用户 查询失败
            $response =  new ResponseError('用户不存在');
        }
    } else {
        $response = new ResponseError('查询失败');
    }
    echo $response->toJson();
    $con->close();
}


