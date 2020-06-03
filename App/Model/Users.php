<?php


namespace App\Model;


use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\Utility\Schema\Table;

class Users extends AbstractModel
{

    /**
     * @param int $page
     * @param string|null $keyword
     * @param int $pageSize
     * @return array
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    public function getAll(int $page = 1, string $keyword = null, int $pageSize = 10): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['userAccount'] = ['%' . $keyword . '%','like'];
        }
        $list = $this->limit($pageSize * ($page - 1), $pageSize)->withTotalCount()->all($where);
        $total = $this->lastQueryResult()->getTotalCount();
        return ['total' => $total, 'list' => $list];
    }
}