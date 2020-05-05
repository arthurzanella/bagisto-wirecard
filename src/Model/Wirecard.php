<?php

namespace ArthurZanella\Wirecard\Models;

use Illuminate\Database\Eloquent\Model;
use ArthurZanella\Wirecard\Contracts\Wirecard as WirecardContract;

class Wirecard extends Model implements WirecardContract
{
    protected $table = 'wirecard';

    protected $fillable = ['order_id'];
}