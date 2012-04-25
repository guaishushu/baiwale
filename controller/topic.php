<?php  
class Action extends SiteAction {
    public function index() {
        $info = _model('')->getList('SELECT user.username, tag.id, tag.tag, topic.title, topic.tid, topic.retime FROM topic, user, tag WHERE topic.uid = user.uid AND topic.tag = tag.id');
        //$this->pvar($info);die;
        foreach ($info as $key => $value) {
            $replay = _model('replay')->read('WHERE tid = ' . $value['tid'] . ' ORDER BY addtime DESC');
            if ( $replay ) {
                if ( $replay['uid'] == 0 ) {
                    $info[$key]['rusername'] = '青岛银';
                } else {
                    $ruser = _model()->read('SELECT uid, username FROM user WHERE uid = ' . $replay['uid']);
                    $info[$key]['ruid'] = $ruser['uid'];
                    $info[$key]['rusername'] = $ruser['username'];
                }
                $info[$key]['rtime'] = date("m-d H:i", $replay['addtime']);
            }else {
                $info[$key]['rtime'] = '无回复';
            }
        }

        $tag = _model('tag')->getList();
        //$this->pvar($info);die;
        $this->display("topic/list.html", array(
			'active' => 'topic',
            'info' => $info,
            'tag' => $tag,
		));
    }

    public function view($tid='') {
    	$topic = _model('topic')->read(array('tid'=>$tid));
        $tag = _model('tag')->read(array('id'=>$topic['tag']));
        $replay = _model('replay')->getList("WHERE tid = $tid ORDER BY addtime ASC");
        foreach ($replay as $key => $value) {
            if ( $value['uid'] == 0) {
                $replay[$key]['username'] = '青岛银';
                $replay[$key]['avatar'] = '';
            } else {
                $user = _model('user')->read(array('uid'=>$value['uid']));
                $replay[$key]['uid'] = $user['uid'];
                $replay[$key]['username'] = $user['username'];
                $replay[$key]['avatar'] = $user['avatar'];
            }
            $replay[$key]['addtime'] = date('m-d H:i', $replay[$key]['addtime']);
        }
    	$this->display("topic/view.html", array(
			'active' => 'topic',
            'topic' => $topic,
            'tag' => $tag,
            'replay' => $replay,
		));
    }

    public function tag() {
    	$this->display("topic/list.html", array(
    		'active' => 'topic',
    	));
    }

    public function create() {
        $this->islogin();
    	if ( $_SERVER['REQUEST_METHOD'] === "POST" ) {
            $info = _POST('info', array());
    		if ( in_array('', $info) ) {
                $this->msg('请填写内容');
            }
            $info['text'] = trim($info['text']);
            $info['uid'] = $_SESSION['user']['uid'];
            $info['addtime'] = time();
            $id = _model('topic')->create($info);
            if ( !$id ) {
                $this->msg('发布失败，请重试');
            }
            $this->redirect("topic/view/$id.html");
    	} else {
            $tag = _model('tag')->getList();
    		$this->display("topic/create.html", array(
    			'active' => 'create',
                'tag' => $tag,
    		));
    	}
    }

    public function edit($tid) {
        $this->islogin();
        if ( $_SERVER['REQUEST_METHOD'] === "POST" ) {
            $info = _POST('info', array());
            if ( in_array('', $info) ) {
                $this->msg('请填写内容');
            }
            $info['text'] = trim($info['text']);
            _model('topic')->update(array('tid'=>$tid), $info);
            $this->redirect("topic/view/$tid.html");
        } else {
            $info = _model('topic')->read(array('tid'=>$tid));
            if ( !$info ) {
                $this->msg('没有这个文章哎');
            }
            $tag = _model('tag')->getList();
            $this->display("topic/edit.html", array(
                'active' => 'topic',
                'info' => $info,
                'tag'=> $tag,
            ));
        }
    }

    public function delete($tid) {
        $is = _model('topic')->delete(array('tid'=>$tid));
        $this->redirect("topic.html");
    }

    public function replay($tid) {
        $info = _POST('info', array());
        if ( !$info['text'] ) {
            $this->msg('请输入恢复内容');
        }
        if ( empty($_SESSION['user']['uid']) ) {
            $info['uid'] = 0;
        } else {
            $info['uid'] = $_SESSION['user']['uid'];
        }
        $info['tid'] = $tid;
        $info['addtime'] = time();
        $id = _model('replay')->create($info);
        $this->redirect("topic/view/$tid.html");
    }
}