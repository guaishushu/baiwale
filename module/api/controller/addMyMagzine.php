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
	 * api接口，添加一个该用户的个人刊物
     * @param(GET) $username, $password, $id
     * @return json_encode array('result'=>'ok')
               json_encode array('result'=>'error')
	 */
    function index()
    {
        $username = _GET('username');
        $password = _GET('password');
        $id = _GET('id', 0);
        if (empty($username) || empty($password) || empty($id)) {
            $this->module->outResult(false);
        }

        //判断 此用户是否存在
        if (!$this->module->checkUser($username, $password)) {
            $this->module->outResult(false);
        }

        //获取 该用户的杂志用户组id
        $user_info = _model('member')->read(array('username'=>$username));
        if (!empty($user_info['group_member_id'])) {
            $group_member_id = $this->module->StrToArray($user_info['group_member_id']);
        }

        if (!empty($group_member_id)) {

            if (count($group_member_id) == 1) {
                $magazine_id = _model('group_member')->read(array('id'=>$group_member_id[0]));
                $magazine = $this->module->StrToArray($magazine_id['magazine_class_id']);
                
            } elseif (count($group_member_id) > 1) {

                foreach ($group_member_id as $key => $val) {
                    $magazine_id[$key] = _model('group_member')->read(array('id'=>$val));

                    if (!empty($magazine_id[$key]['magazine_class_id'])) {
                        $magazine_class_id .= ','.$magazine_id[$key]['magazine_class_id'];
                    }
                }
                $magazine_class_id = explode(',', $magazine_class_id);
                unset($magazine_class_id[0]);
                sort($magazine_class_id);
                $magazine = $magazine_class_id;
            
            } else {
                $magazine = null;
            }

            //去除重复用户组杂志id (array)
            if (!empty($magazine)) {
                $magazine = array_unique($magazine); 
            }

            //判断 存在该杂志 结束
            if (is_array($magazine) && !empty($magazine)) {
                if (in_array($id, $magazine)) {
                    $this->module->outResultTrue();
                    //$this->module->outResult(false);
                }
            }
        }

        //获取用户的个人杂志id
        //判断 存在该杂志 结束
        if (!empty($user_info['magazine_class_id'])) {
            if (stristr($user_info['magazine_class_id'], ',')) {
                $single_magazine_id = explode(',', $user_info['magazine_class_id']);
            } else {
                $single_magazine_id = array($user_info['magazine_class_id']);
            }
            if (in_array($id, $single_magazine_id)) {
                $this->module->outResultTrue();
                
                //$this->module->outResult(false);
            }
        }
        
        //添加该id,逗号分隔,更新,结束
        if (!empty($single_magazine_id)) {
            if (array_unshift($single_magazine_id, $id)) {
                $user_info['magazine_class_id'] = implode(',', $single_magazine_id);
            }
            if (_model('member')->update(array('username'=>$username), $user_info)) {
                $this->module->outResultTrue();
            }
        } else {
            $user_info['magazine_class_id'] = $id;
            if (_model('member')->update(array('username'=>$username), $user_info)) {
                $this->module->outResultTrue();
            }
        }

    }
}

?>