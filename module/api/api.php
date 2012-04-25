<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */


class apiModule extends Module
{
    function outResult($array)
    {
        if (!is_array($array)) {
            $array = array('result'=>'error');
        }
        echo json_encode($array);
        exit;
    }

    function outResultTrue()
    {
        echo json_encode(array('result'=>'ok'));
        exit;
    }

    function checkUser($username, $password)
    {
        if (empty($username) || empty($password)) {
            return false;
        }

        $info = _model('member')->read(array('username'=>$username));
        if (!$info) {
            return false;
        }
        $url = 'http://passport.guoshi.com/mp/login';
        $data = array(
            'type' => 0,
            'username' => $username,
            'password' => md5($password),
            'noscode' => 'no',
            'daemon' => 'yes',
            'fromSys' => '30'
        );

        $data = http_build_query($data);
        $opts = array (
            'http' => array (
                'method' => 'POST',
                'header'=> "Content-type: application/x-www-form-urlencoded\r\n" .
                           "Content-Length: " . strlen($data) . "\r\n",
                'content' => $data
            )
        );

        $context = stream_context_create($opts);
        $return = @file_get_contents($url, false, $context);
        return strlen($return) > 200 ? $info : false;
    }

    function StrToArray($check = '')
    {
        if (empty($check)) {
            return array();
        }

        if (stristr($check, ',')) {
            return explode(',', $check);
        } else {
            return array($check);
        }
    }

    function ArrayToStr($check = array())
    {
        if (empty($check)) {
            return null;
        }

        if (count($check) == 1) {
            sort($check);
            return $check[0];
        } else {
            return implode(',', $check);
        }
    }
}

?>