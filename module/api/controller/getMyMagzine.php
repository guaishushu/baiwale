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
     * api接口，显示此用户订阅的杂志
     * @param(GET) $username,$password
     * @return(json_encode) array(
     *                          'id'=>'',
     *                          'name'=>'',
     *                          'pic'=>'',
     *                          'desc'=>'',
     *                          'newcontent'=>'',
     *                          'cancelable'=>'',
     *                      )
     */

    function index()
    {
        $username = _GET('username');
        $password = _GET('password');

        $resultMy = $resultAll = array();

        if (empty($username) || empty($password)) {
            $this->module->outResult(false);
        }
        if (!$member = $this->module->checkUser(_GET('username'), _GET('password'))) {
            $this->module->outResult(false);
        }

        if (empty($member['group_member_id']) && empty($member['magazine_class_id'])) {
            $this->module->outResult(array());
        }

        //如果该用户有用户组
        if (!empty($member['group_member_id'])) {
            //所属用户组id (array) 可能为多个
            $group_member_id = $this->module->StrToArray($member['group_member_id']);

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

                //用户组中的杂志信息
                foreach ($magazine as $key => $val) {
                    $result4[] = _model('magazine_class')->read(array('id'=>$val));
                }

                foreach ($result4 as $key => $val) {
                    $resultAll[$key]['id'] = $val['id'];
                    $resultAll[$key]['name'] = $val['class_name'];

                    if ($val['pic']) {
                        $resultAll[$key]['pic'] = SITE_URL.'/Services/images/cropped_110_129/'.$val['pic'];
                    } else {
                        $resultAll[$key]['pic'] = SITE_URL.'/module/admin/images/model_icon.png';
                    }

                    $resultAll[$key]['desc'] = $val['description'];
                    $resultAll[$key]['newcontent'] = 0;
                    $resultAll[$key]['cancelable'] = 0;
                }
            }


        }

        //如果该用户有个人杂志
        if (!empty($member['magazine_class_id'])) {
            //自己订阅的杂志id (array)
            $magazine_id_single = $this->module->StrToArray($member['magazine_class_id']);

            //去除与用户组中重复的杂志id (array)
            if (!empty($magazine)) {
                $magazine_id_single = $magazine_id_single;
                $maga = array_diff($magazine_id_single, $magazine);
            } else {
                $maga = $magazine_id_single;
            }

        }

        //用户个人独立选择的杂志信息
        if ($maga) {
            foreach ($maga as $key => $val) {
                $result2[$key] = _model('magazine_class')->read(array('id'=>$val));
            }

            foreach ($result2 as $key => $val) {
                $resultMy[$key]['id'] = $val['id'];
                $resultMy[$key]['name'] = $val['class_name'];

                if ($val['pic']) {
                    $resultMy[$key]['pic'] = SITE_URL.'/Services/images/cropped_110_129/'.$val['pic'];
                } else {
                    $resultMy[$key]['pic'] = SITE_URL.'/module/admin/images/model_icon.png';
                }

                $resultMy[$key]['desc'] = $val['description'];
                $resultMy[$key]['newcontent'] = 0;
                $resultMy[$key]['cancelable'] = 1;
            }
        }

        $result = array_merge($resultMy, $resultAll);

        $this->module->outResult($result);
    }
}



?>