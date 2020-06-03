<?php


namespace App\HttpController;

use App\Model\Users as User;

/**
 * Class Users
 * @package App\HttpController
 */
class Users extends Base
{
    /**
     * @var int 页数
     */
    private $page;

    /**
     * @var int 每一页分多少
     */
    private $limit = 3;

    /**
     * @return bool
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function index()
    {
        $page = $this->request()->getRequestParam('page') ?: 1;;          // 当前页码
        $limit = $this->request()->getRequestParam('limit') ?: 5;        // 每页多少条数据e;
        $model = \App\Model\Area::create()->limit($limit * ($page - 1), $limit * $page + 1)->withTotalCount();
        // 列表数据
        $list = $model->all(null, true);
        $result = $model->lastQueryResult();
        // 总条数
        $total = $result->getTotalCount();
        $data = [
            'page-list' => $list,
            'total' => $total,
            'last' => $result,
        ];
        return $this->writeJson(1,$data,'获取成功');
    }

}