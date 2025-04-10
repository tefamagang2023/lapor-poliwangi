<?php

namespace Modules\Admin\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Modules\Pelapor\Entities\Complaint;
use Modules\UnitPoliwangi\Entities\responses;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';
    protected $fillable = ['id', 'nama'];
    
    protected static function newFactory()
    {
        return \Modules\Admin\Database\factories\UnitFactory::new();
    }
}
