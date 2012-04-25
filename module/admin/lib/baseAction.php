<?php

/*
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author xuanyan <xuanyan@geek-zoo.com>
 *
 */

abstract class baseAction extends ModuleAction
{
    public function __construct($controller)
    {
        parent::__construct($controller);
        session_start();
        define('THEME_URL', SITE_URL.'/module/'.$this->controller->module);
        define('MODULE_URL', ROOT_PATH.'/module/'.$this->controller->module);
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

}


?>