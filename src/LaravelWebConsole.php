<?php

namespace Alkhachatryan\LaravelWebConsole;

use Illuminate\Http\Request;

class LaravelWebConsole
{
    public static function show()
    {
        return view('webconsole::window');
    }

    public function requestHandler(Request $request)
    {
        $rpc_server = new WebConsoleRPCServer();
        $rpc_server->Execute();
    }
}
