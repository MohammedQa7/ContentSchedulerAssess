<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlatformResource;
use App\Models\Platform;
use App\Services\PlatformService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlatfromController extends Controller
{
    function index(PlatformService $platform_service)
    {

        return Inertia::render('PlatformManager', [
            'platforms' => PlatformResource::collection($platform_service->getPlatfroms()),
            'enabledPlatforms' => PlatformResource::collection($platform_service->getEnabledPlatforms())
        ]);
    }
}