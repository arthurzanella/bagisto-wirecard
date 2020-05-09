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
     * get Reference from Order Id
     *
     * @param int $id
     * @return array
     */
    function getReference($id)
    {
        return $this->model->where('order_id', $id)->groupBy('reference')->first()->reference;
    }

    /**
     * get Order Id from Order Reference
     *
     * @param int $id
     * @return array
     */
    function getOrderId($reference)
    {
        return $this->model->where('reference', $reference)->groupBy('order_id')->first()->order_id;
    }

    /**
     * @param  int  $order_id
     * @param  string  $status
     * @param  string  $reference
     * @return Wirecard
     */
    public function createStatus($order_id, $status, $reference, $event = null)
    {
        $data = new $this->model;
        $data->order_id = $order_id;
        $data->status = $status;
        $data->reference = $reference;
        $data->event = $event;
        $data->save();
        return $data;
    }

}