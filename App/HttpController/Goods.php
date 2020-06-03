<?php


namespace App\HttpController;

use App\Model\Users;
use EasySwoole\Http\Message\Status;

/**
 * 商品
 * Class Goods
 * @package App\HttpController
 */
class Goods extends Base
{
    /**
     * 第几页
     * @var int
     */
    private $page = 1;

    /**
     * 每一页分多少条
     * @var int
     */
    private $limit = 15;

    /**
     * 测试分页
     * @return bool|void
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function index()
    {
        $userModel = new Users();
        $data = $userModel->getAll($this->getPage(), '', $this->getlimit());
        return $this->writeJson(Status::CODE_OK, $data, 'success');
    }

    /**
     * @return int
     */
    private function getPage()
    {
        if($this->request()->getRequestParam('page')){
            $this->page = $this->request()->getRequestParam('page');
        }
        return (int) $this->page;
    }

    /**
     * @return int
     */
    private function getLimit()
    {
        if($this->request()->getRequestParam('limit')){
            $this->limit = $this->request()->getRequestParam('limit');
        }
        return (int) $this->limit;
    }
}
