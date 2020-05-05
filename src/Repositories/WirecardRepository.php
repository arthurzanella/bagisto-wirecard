<?php

namespace ArthurZanella\Wirecard\Repositories;

use Webkul\Core\Eloquent\Repository;

class WirecardRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'ArthurZanella\Wirecard\Contracts\Wirecard';
    }

    /**
     * get Order Status from Order Id
     *
     * @param int $id
     * @return array
     */
    function getStatus($id)
    {
        return $this->model
            ->whereIn('order_id', [$id, 0])
            ->where('status', '1')
            ->get();
    }
}