<?php

namespace Modules\Communication\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ticket_categories';

    protected $fillable = ['name'];

    protected $dates = ['deleted_at'];
}
