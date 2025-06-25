<?php

namespace Modules\Communication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketCategory extends Model
{
    use SoftDeletes;

    protected $table = 'ticket_categories';

    protected $fillable = ['name'];

    /** @var array<int, string> */
    protected $dates = ['deleted_at'];
}
