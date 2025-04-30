<?php

namespace Modules\CarInfo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Modules\CarInfo\Database\Factories\TransmissionFactory;

class Transmission extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = "transmissions";

    protected $fillable = ["name", "status", "language_id"];

    // protected static function newFactory(): TransmissionFactory
    // {
    //     // return TransmissionFactory::new();
    // }
}
