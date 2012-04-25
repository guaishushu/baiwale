<?php
abstract class SiteAction
{
    protected $controller = null;

    public function __construct($controller)
    {
        session_start();
        $this->controller = $controller;
    }

    public function redirect($url = '') {
        $site = SITE_URL;
        if (!$url) {
            $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $site;
        }

        if (substr($url, 0, 4) != 'http') {
            if ($url{0} != '/') {
                $url = '/'.$url;
            }
            $url = $site.$url;
        }
        header('Location: ' . $url);
        exit;
    }

    public function __get($name)
    {
        return Site::getInstance()->$name;
    }

    protected function display($file, $param = array())
    {
        $this->smarty->assign('this', $this);
        foreach ($param as $key => $val) {
            $this->smarty->assign($key, $val);
        }
        $this->smarty->display($file);
    }
	
	function trim_right($str)
    {
        $len = strlen($str);

        if ($len == 0 || ord($str{$len - 1}) < 127) {
            return $str;
        }

        if (ord($str{$len - 1}) >= 192) {
           return substr($str, 0, $len - 1);
        }

        $r_len = strlen(rtrim($str, "\x80..\xBF"));
        if ($r_len == 0 || ord($str{$r_len - 1}) < 127) {
            return sub_str($str, 0, $r_len);
        }

        $as_num = ord( ~ $str{$r_len - 1});
        if ($as_num > (1 << (6 + $r_len - $len))) {
            return $str;
        } else {
            return substr($str, 0, $r_len - 1);
        }
    }

    function substr_fix($string, $len = 4) {
        $old = $string;
        $len *= 2;
        if (strlen($string) <= $len) {
            return $string;
        }
        $chinese = "(?:[".chr(228)."-".chr(233)."][".chr(128)."-".chr(191)."][".chr(128)."-".chr(191)."])";
        preg_match_all("/$chinese|\S|\s/", $string, $out);
        $string = '';
        foreach ($out[0] as $key => $val) {
            $len = strlen($val) == 1 ? $len - 1 : $len - 2;
            if ($len < 0) {
                break;
            }
            $string .= $val;
        }
        $string = self::trim_right($string);
        
        if ($string == $old) {
            return $string;
        }
        return $string.'..';
    }

    public function islogin() {
        if (!isset($_SESSION['user'])) {
            $this->redirect('account/sing_in');
        }
    }

    public function puts($value='') {
        echo $value . "<br />";
    }

    public function pvar($value='') {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }

    public function msg($msg, $jumpurl='', $lev = 'notice', $t = 3)
    {
        $param = array(
            'msg' => $msg
        );
       if ($jumpurl) {
            $jumpurl = htmlspecialchars($jumpurl);
            if (substr($jumpurl, 0, 4) != 'http') {
                if ($jumpurl{0} != '/') {
                    $jumpurl = '/'.$jumpurl;
                }
                $jumpurl = SITE_URL.$jumpurl;
            }
            $ifjump = "<META HTTP-EQUIV='Refresh' CONTENT='$t; URL=$jumpurl'>";
            $param['jumpurl'] = $jumpurl;
            $param['lev'] = $lev;
            $param['ifjump'] = $ifjump;
        }
        $this->display('message.html', $param);
        exit;
    }

    public function upload($value) {
        if($value['error'] > 0){
            return false;
        }

        if ( $value['size'] >=  2097152 ) {
            return false;
        }

        $path = "upload/photo/";
        $fix = array_pop((explode(".",$value['name'])));
        $isfix = array('jpg', 'png', 'gif');
        if ( !in_array($fix, $isfix) ) {
            return false;
        }
        $file = $path . time() . rand(0,10) . '.' . $fix;
        //$name = str_replace($_SERVER['DOCUMENT_ROOT'], "", $file);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if (move_uploaded_file( $value['tmp_name'], $file)) {
            return $file;
        } else {
            $this->msg('上传失败');
        }
    }
}

?>