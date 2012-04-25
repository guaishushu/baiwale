<?php
require dirname(__FILE__).'/../lib/baseAction.php';

class Action extends baseAction
{   
    function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_info = _POST('user_info', array());
            $password  = $user_info['password'];
            $username      = $user_info['username'];
            if ($username == 'admin' && $password == 'iamtrump123') {
                $_SESSION['admin_user'] = $username;
                $this->redirect('admin');
            } else {
                $this->msg('你的帐号或密码有误请重新填写');
            }
        } else {
            $this->display('login.html');
        }
    }

    function logout()
    {
        $_SESSION = array();
        $this->redirect('admin/user/login');
    }

    function clean_history()
    {
        $_SESSION['admin_user']['history'] = array();
        $this->redirect();
    }
}
