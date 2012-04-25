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
	 * api接口，返回该用户的杂志内容
     * @param(GET) $username, $password, $id
     * @return (rss)
	 */
    function index()
    {
        if (!$id = _GET('id', 0)) {
            $this->module->outResult(false);
        }

        if (!$info = $this->module->checkUser(_GET('username'), _GET('password'))) {
            $this->module->outResult(false);
        }

        $m = _model('magazine')->getList(array('magazine_class_id'=>$id));
        $ids = array();
        foreach ($m as $key => $val) {
            $ids[] = $val['id'];
        }
        $out = array();
        if ($ids) {
            $result = _model('feed')->getList("WHERE member_id = {$info['id']} AND magazine_id IN('".implode("', '", $ids)."') ORDER BY is_read DESC, add_time DESC");
            
            foreach ($result as $key => $val) {
                
                $data = _model('item')->getList(array('magazine_id'=>$val['magazine_id']), 'ORDER BY position');
                foreach ($data as $k => $v) {
                    if ($v['condition1']) {
                        list($kk, $vv) = explode("|", $v['condition1']);
                        if ($info[$kk] != $vv) {
                            continue;
                        }
                    }
                    if ($v['condition2']) {
                        list($kk, $vv) = explode("|", $v['condition2']);
                        if ($info[$kk] != $vv) {
                            continue;
                        }
                    }
                    if ($v['condition3']) {
                        list($kk, $vv) = explode("|", $v['condition3']);
                        if ($info[$kk] != $vv) {
                            continue;
                        }
                    }
                    $content = '';
                    $url = '';
                    if ($v['type'] == 0) {
                        $content = $v['content'];
                    } elseif ($v['type'] == 1 ) {
                        $url = SITE_URL."/Services/images/300/{$v["content"]}";
                    } elseif ($v['type'] == 2) {
                        //投票
                        $tt = _model('vote')->read(array('id'=>$v['content']));
                        if (empty($tt)) {
                            continue;
                        }
                        $content = $tt['title'];
                        $url = SITE_URL.'/vote?id='.Cipher::encrypt($v['content'].'|'.$info['id']);
                    } elseif ($v['type'] == 3) {
                        // 视频
                        $content = @file_get_contents($v['content']);
                        if (!preg_match('/http\:.+\.mp4/', $content, $tt)) {
                           continue;
                        }
                        $url = $tt[0];
                        if (!preg_match('/http\:.+w120\.jpg/', $content, $tt)) {
                           continue;
                        }
                        
                        $content = $tt[0];
                    }

                    $out[] = array(
                        'type' => $v['type'],
                        'content' => $content,
                        'url' => $url
                    );

                }
            }
        }
        
        $this->module->outResult($out);
        
    }
}

?>