<?php

namespace App\Enums;

use App\Actions\MonitorStrategies\HttpMonitorStrategy;
use App\Actions\MonitorStrategies\MonitorStrategy;

enum HttpMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';

}
