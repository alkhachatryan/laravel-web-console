<?php

namespace Alkhachatryan\LaravelWebConsole;

use Illuminate\Http\Request;

class LaravelWebConsole
{
    /**
     * The flag which indicates if the request has errors.
     *
     * @var bool
     */
    private $has_errors = false;

    /**
     * The output response of the request.
     *
     * @var string
     */
    private $output;

    /**
     * Show the web terminal.
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public static function show()
    {
        return view('webconsole::window');
    }

    /**
     * Verify and execute the request.
     *
     * @param Request $request
     * @return void
     */
    public function requestHandler(Request $request)
    {
        $this->verifyRequest($request);

        if ($this->has_errors) {
            echo json_encode(['result' => ['output' => $this->output]]);
            exit(403);
        }

        $rpc_server = new WebConsoleRPCServer();
        $rpc_server->Execute();
    }

    /**
     * Verify the request.
     *
     * @param Request $request
     * @return void
     */
    private function verifyRequest(Request $request)
    {
        $in = $request->input('params');

        if (! isset($in[2])) {
            return;
        }

        $command = explode(' ', $in[2])[0];

        $forbidden_commands = config('laravelwebconsole.forbidden_commands');

        // Check if the command is in the forbidden commands list
        if ($forbidden_commands && in_array($command, $forbidden_commands)) {
            $this->has_errors = true;
            $this->output = "Denied to execute '$command' command";
        }
    }
}
