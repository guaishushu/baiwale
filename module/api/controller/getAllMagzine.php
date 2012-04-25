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
	 * api接口，显示所有刊物列表
     * @param(GET) $username, $password
     * @return json_encode array(
                                'id'=>,
                                'name'=>,
                                'pic'=>,
                                'desc'=>
                        )
               json_encode array('result'=>'error')
	 */
    function index()
    {
        $username = _GET('username');
        $password = _GET('password');
        if (empty($username) || empty($password)) {
            $this->module->outResult(false);
        }

        //判断 此用户是否存在
        if (!$this->module->checkUser(_GET('username'), _GET('password'))) {
            $this->module->outResult(false);
        }

        $magazine = _model('magazine_class')->getList('ORDER BY id DESC');
        foreach ($magazine as $key => $val) {
            $result[$key]['id'] = $val['id'];
            $result[$key]['name'] = $val['class_name'];
            $result[$key]['pic'] = SITE_URL.'/Services/images/cropped_110_129/'.$val['pic'];
            $result[$key]['desc'] = $val['description'];
        }
        
        $this->module->outResult($result);
    }
}

?>