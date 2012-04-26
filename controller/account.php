<?php  
class Action extends SiteAction {
    public function index() {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $info = _POST('info', array());

            //检查昵称是否存在
            if ( $temp = _model('user')->read(array('username'=>$info['username'])) ) {
                if ( $temp['uid'] != $_SESSION['user']['uid'] ) {
                    $this->msg('昵称已占用');
                }
            }

            //上传图片
            if ( !$_FILES['photo']['error'] == 4 ) {
                if ( !$avatar = $this->upload($_FILES['photo']) ) {
                    $this->msg('请检查图片类型是否正确 或者图片大小超过2M');
                }
                $info['avatar'] = $avatar;
            }

            //更新资料
            _model('user')->update(array('uid'=>$_SESSION['user']['uid']), $info);
            $this->redirect('account.html');
        } else {
            $info = _model('user')->read(array('uid'=>$_SESSION['user']['uid']));
            if ( !$info ) {
                $this->msg('搞错了吧...');
            }
            $this->display('account/account.html', array(
                'active' => 'user',
                'info' => $info,
            ));
        }
    }

    public function view($uid) {
        //判断uid传递是否诚g嗯
        if ( empty($uid) ) {
            $this->msg('您看谁啊???');
        }
        //获得user数据
        $user = _model('')->read("SELECT uid as '入伙排名', username as '昵称', gender as '性别', avatar, city as '城市', tagline as '自我介绍', jointime as '入伙时间' FROM user WHERE uid = $uid");
        $avatar = $user['avatar'];
        $user['入伙时间'] = date('Y-m-d H:i:s', $user['入伙时间']);
        $nickname = $user['昵称'];
        unset($user['avatar']);
        if ( !$user ) {
            $this->msg('搞错了吧...');
        }
        //获得user发帖
        $topic = _model('topic')->read("WHERE uid = $uid ORDER BY addtime DESC LIMIT 0,5");
        $this->display('account/view.html',array(
            'active' => 'user',
            'user' => $user, 
            'avatar' => $avatar,
            'nickname' => $nickname,
            'topic' => $topic,
        ));
    }

    public function password() {
        $info = _POST('info', array());
        if ( in_array('', $info) ) {
            $this->msg('请填写完整');
        }
        $user = _model('user')->read(array('uid'=>$_SESSION['user']['uid']));
        if ( md5($info['oldpass']) == $user['password'] && $info['password'] == $info['repassword']) {
            _model('user')->update(array('uid'=>$user['uid']), array('password'=>md5($info['password'])));
            $this->redirect('account.html');
        } else {
            $this->msg('错了');
        }
    }

    public function sing_in() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $info = _POST('info', array());
            if ( in_array('', $info) ) {
                $this->msg('请填写完整选项');
            }
            $info['password'] = md5($info['password']); 
            $user = _model('user')->read($info);
            if ( !$user ) {
                $this->msg('请检查用户名和密码');
            }

            $_SESSION['user']['uid'] = $user['uid'];
            $_SESSION['user']['name'] = $user['username'];
            $_SESSION['user']['power'] = $user['power'];
            $this->redirect('topic.html');
        } else {
            $this->display('account/sing.html', array(
                'active' => 'sing_in',
            ));
        }
    }

    public function sing_up() {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $info = _POST('info', array());
            if ( in_array('', $info) ) {
                $this->msg('请填写完整选项');
            }

            if ( $info['password'] != $info['re_password'] ) {
                $this->msg('两次密码填写不一致');
            }

            //检查邮箱 用户名是否已占用
            if ( _model('user')->read(array('email'=>$info['email'])) ) {
                $this->msg('此邮箱已注册');
            }
            if ( _model('user')->read(array('username'=>$info['username'])) ) {
                $this->msg('此昵称已注册');
            }

            //md5加密 移出重复密码 添加注册时间
            unset($info['re_password']);
            $info['password'] = md5($info['password']);
            $info['jointime'] = time();

            if ( !$uid = _model('user')->create($info) ) {
                $this->msg('请重注册');
            }

            $_SESSION['user']['uid'] = $uid;
            $_SESSION['user']['name'] = $info['username'];
            $_SESSION['user']['power'] = 0;
            $this->redirect('topic.html');
        } else {
        	$this->display('account/sing.html', array(
            	'active' => 'sing_up',
            ));
        }
    }

    public function sing_out() {
        $_SESSION = array();
        $this->redirect('index.html');
    }
}