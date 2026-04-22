<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxon extends Model
{
    protected $fillable = ['name', 'rank', 'parent_name'];
}
