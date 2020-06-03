<?php


namespace App\HttpController;

use App\Model\tempBmwCatchTest;

/**
 * Class Index
 * @package App\HttpController
 */
class Index extends Base
{
    /**
     * @return bool
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function index()
    {
        $result = tempBmwCatchTest::create()->get();
        return $this->writeJson(1, $result, '获取成功');
    }

    /**
     * @param string|null $action
     * @return bool|void
     */
    protected function actionNotFound(?string $action)
    {
        return $this->writeJson(0, [], '不存在该接口');
    }

    /**
     * 获取令牌
     * @return bool token
     */
    public function getJwt()
    {
        $obj = \EasySwoole\Jwt\Jwt::getInstance()->algMethod('AES')->setSecretKey('测试呀')->publish();
        $data = '测试用例';
        //设置过期时间 默认为当前时间加2小时
        $obj->setExp(time() + 30);
        //设置签发时间,默认time()
        $obj->setIat(time());
        //设置签发者.默认为EasySwoole
        $obj->setIss('测试');
        $token = $obj->__toString();
        return $this->writeJson(1, ['token' => $token], '获取成功');
    }

    /**
     * 验证令牌是否有效
     * @return bool
     * @throws \Throwable
     */
    public function verification()
    {
        $jwt = \EasySwoole\Jwt\Jwt::getInstance();
        try {
            //验证token,解密并验证签名验证过期时间
            /** @var \EasySwoole\Jwt\JwtObject $result */
            $result = $jwt->decode($this->request()->getRequestParam('token'));

            $user = User::create()->get(1);
            echo $result->getStatus();
            switch ($result->getStatus()) {
                case  1:
                    return $this->writeJson(1, $user, '验证通过');
                    break;
                case  -2:
                    return $this->writeJson(0, [], '验证失败');
                    break;
                case  -1:
                    return $this->writeJson(0, [], '验证过期');
                    break;
            }
            //根据解密之后的结果完善业务逻辑
        } catch (\Exception $e) {
            return $this->writeJson(1, $e->getMessage(), '抛异常');
        }

    }
}
