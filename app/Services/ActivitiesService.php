<?php

namespace App\Services;

use App\Models\Activity;
use App\Services\BaseServices;

class ActivitiesService extends BaseServices
{

    public function __construct() {
        $this->model = new Activity();
    }
}
