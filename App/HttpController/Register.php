<?php


namespace App\HttpController;

use App\Model\Users;

/**
 * 注册
 * Class Register
 * @package App\HttpController
 */
class Register extends Base
{
    /**
     *  注册
     * @param username 用户名
     * @param password 密码
     * @param phone    手机号
     * @param email    邮箱
     * @return bool|void api状态
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function index()
    {
        $postData = $this->request()->getRequestParam();
        $data = [
            'username' => $postData['username'],
            'password' => md5($postData['password']),
            'phone' => $postData['phone'],
            'email' => $postData['email'],
            'state' => 1,
            'salt' => '111',
            'is_discard' => 0,
            'create_time' => time(),
            'delete_time' => time(),
        ];
        for ($i = 0 ; $i <= 100000000; $i++){
            $user = new Users();
            $user->setAttr('username',$data['username']);
            $user->setAttr('password',$data['password']);
            $user->setAttr('phone',$data['phone']);
            $user->setAttr('email',$data['email']);
            $user->setAttr('state',$data['state']);
            $user->setAttr('salt',$data['salt']);
            $user->setAttr('is_discard',$data['is_discard']);
            $user->setAttr('create_time',$data['create_time']);
            $user->setAttr('delete_time',$data['delete_time']);
            $result = $user->save();
        }
        if($result){
            return $this->writeJson(200, [], '注册成功');
        }else{
            return $this->writeJson(0, [], '注册失败');
        }

    }
}