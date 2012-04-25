<?php

/**
 * This file is part of the Geek-Zoo Projects.
 *
 * @copyright (c) 2010 Geek-Zoo Projects More info http://www.geek-zoo.com
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License
 * @author jiangyi <jiangyi@geek-zoo.com>
 *
 */


class Action extends ModuleAction
{
    function index()
    {
        if ($this->module->checkUser(_GET('username'), _GET('password'))) {
        	$result = array('result'=>'ok');
        } else {
        	$result = array('result'=>'error');
        }
        echo json_encode($result);
        exit;
    }
}

?>