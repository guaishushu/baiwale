<?php
require dirname(__FILE__).'/../lib/adminAction.php';

class Action extends adminAction
{
    function index()
    {
        // $ip = $_SERVER['SERVER_ADDR'];
        // $info = $_SERVER['SERVER_SIGNATURE'];
        //get_defined_vars()得到作用域所有变量
        $data = _model('config')->read(array('name'=>'index'));
        $this->display('default.html',array('data'=>$data));
    }

    public function data() {
        $index = _POST('index', 0);
        if ( is_int($index) ) {
            _model('config')->update(array('name' => 'index'), array('data' => $index));
        } else {
            $this->msg("是数字啊 哥哥");
        }
        $this->redirect("admin.html");
    }
    
    public function upload($value) {
        if($value['error'] > 0){
            return false;
        }

        $path = ADMIN_PATH . "/upload/photo/";
        $fix = array_pop((explode(".",$value['name'])));
        $file = $path . time() . rand(0,10) . '.' . $fix;
        $name = '/' . str_replace($_SERVER['DOCUMENT_ROOT'], "", $file);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if (move_uploaded_file( $value['tmp_name'], $file)) {
            return $name;
        } else {
            $this->msg('上传失败');
        }
    }
}