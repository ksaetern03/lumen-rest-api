<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\$nameUpperInterface;
use App\Models\$nameUpper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use DB;

class $nameUpperRepository extends AbstractRepository implements $nameUpperInterface
{
    protected $modelName = $nameUpper::class;
}