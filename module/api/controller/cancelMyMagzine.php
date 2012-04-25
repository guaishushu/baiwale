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
	/**
	 * api接口，取消一部可取消刊物的订阅
     * @param(GET) $username, $password, $id
     * @return json_encode array('result'=>'ok')
     *         json_encode array('result'=>'error')
	 */
    function index()
    {
        if (!$id = _GET('id', 0)) {
            $this->module->outResult(false);
        }
        $username = _GET('username');
        $password = _GET('password');
        if (empty($username) || empty($password)) {
            $this->module->outResult(false);
        }

        //判断 该用户是否存在
        if (!$info = $this->module->checkUser(_GET('username'), _GET('password'))) {
            $this->module->outResult(false);
        }

        //获取 该用户个人杂志id
        if (empty($info['magazine_class_id'])) {
            $this->module->outResult(false);
        }
        $single_magazine_id = explode(',', $info['magazine_class_id']);
        
        if (($key = array_search($id, $single_magazine_id)) !== false) {
            unset($single_magazine_id[$key]);
        }
        _model('member')->update(array('id'=>$info['id']), array('magazine_class_id'=>implode(',', $single_magazine_id)));
        
        $this->module->outResultTrue();
    }
}

?>