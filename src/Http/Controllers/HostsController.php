<?php

namespace Commando1251\LogViewer\Http\Controllers;

use Commando1251\LogViewer\Facades\LogViewer;
use Commando1251\LogViewer\Http\Resources\LogViewerHostResource;

class HostsController
{
    public function index()
    {
        return LogViewerHostResource::collection(
            LogViewer::getHosts()
        );
    }
}
