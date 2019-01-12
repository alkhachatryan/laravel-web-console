<?php

namespace Alkhachatryan\LaravelWebConsole;

class WebConsoleRPCServer extends BaseJsonRpcServer
{
    /**
     * Default home dir.
     * @var
     */
    protected $home_directory = '';

    /**
     * Registered accounts array.
     * @var
     */
    protected $accounts;

    /**
     * Selected password hashing algorithm.
     * @var
     */
    protected $password_hash_algorithm;

    /**
     * Login enabled/disabled boolean.
     * @var
     */
    protected $no_login;

    /**
     * Home dir config from settings.
     * @var
     */
    protected $home_dir_conf;

    /**
     * Is webconsole configured?
     * @var
     */
    protected $is_configured;

    public function __construct($instance = null)
    {
        parent::__construct($instance);

        $this->no_login = config('laravelwebconsole.no_login');
        $this->home_dir_conf = config('laravelwebconsole.home_dir');
        $this->accounts = config('laravelwebconsole.accounts');
        $this->password_hash_algorithm = config('laravelwebconsole.password_hash_algorithm');

        // Initializing

        if (config('laravelwebconsole.user.name') && config('laravelwebconsole.user.password')) {
            $this->accounts[config('laravelwebconsole.user.name')] = config('laravelwebconsole.user.password');
        }

        $this->is_configured = ($this->no_login || count($this->accounts) >= 1) ? true : false;

        if (! $this->is_configured) {
            throw new \Exception('Webconsole not configured. Please see: /config/laravelwebconsole.php');
        }
    }

    private function error($message)
    {
        throw new \Exception($message);
    }

    // Authentication
    private function authenticate_user($user, $password)
    {
        $user = trim((string) $user);
        $password = trim((string) $password);

        if ($user && $password) {
            if (isset($this->accounts[$user]) && ! is_empty_string($this->accounts[$user])) {
                if ($this->password_hash_algorithm) {
                    $password = get_hash($this->password_hash_algorithm, $password);
                }

                if (is_equal_strings($password, $this->accounts[$user])) {
                    return $user.':'.get_hash('sha256', $password);
                }
            }
        }

        throw new \Exception('Incorrect user or password');
    }

    private function authenticate_token($token)
    {
        if ($this->no_login) {
            return true;
        }

        $token = trim((string) $token);
        $token_parts = explode(':', $token, 2);

        if (count($token_parts) == 2) {
            $user = trim((string) $token_parts[0]);
            $password_hash = trim((string) $token_parts[1]);

            if ($user && $password_hash) {
                if (isset($this->accounts[$user]) && ! is_empty_string($this->accounts[$user])) {
                    $real_password_hash = get_hash('sha256', $this->accounts[$user]);
                    if (is_equal_strings($password_hash, $real_password_hash)) {
                        return $user;
                    }
                }
            }
        }

        throw new \Exception('Incorrect user or password');
    }

    private function get_home_directory($user)
    {
        if (is_string($this->home_dir_conf)) {
            if (! is_empty_string($this->home_dir_conf)) {
                return $this->home_dir_conf;
            }
        } elseif (is_string($user) && ! is_empty_string($user) && isset($this->home_dir_conf[$user]) && ! is_empty_string($this->home_dir_conf[$user])) {
            return $this->home_dir_conf[$user];
        }

        return getcwd();
    }

    // Environment
    private function get_environment()
    {
        $hostname = function_exists('gethostname') ? gethostname() : null;

        return ['path' => getcwd(), 'hostname' => $hostname];
    }

    private function set_environment($environment)
    {
        $environment = ! empty($environment) ? (array) $environment : [];
        $path = (isset($environment['path']) && ! is_empty_string($environment['path'])) ? $environment['path'] : $this->home_directory;

        if (! is_empty_string($path)) {
            if (is_dir($path)) {
                if (! @chdir($path)) {
                    return ['output' => 'Unable to change directory to current working directory, updating current directory',
                    'environment' => $this->get_environment(), ];
                }
            } else {
                return ['output' => 'Current working directory not found, updating current directory',
                'environment' => $this->get_environment(), ];
            }
        }
    }

    // Initialization
    private function initialize($token, $environment)
    {
        $user = $this->authenticate_token($token);
        $this->home_directory = $this->get_home_directory($user);
        $result = $this->set_environment($environment);

        if ($result) {
            return $result;
        }
    }

    // Methods
    public function login($user, $password)
    {
        $result = ['token' => $this->authenticate_user($user, $password),
            'environment' => $this->get_environment(), ];

        $home_directory = $this->get_home_directory($user);
        if (! is_empty_string($home_directory)) {
            if (is_dir($home_directory)) {
                $result['environment']['path'] = $home_directory;
            } else {
                $result['output'] = 'Home directory not found: '.$home_directory;
            }
        }

        return $result;
    }

    public function cd($token, $environment, $path)
    {
        $result = $this->initialize($token, $environment);
        if ($result) {
            return $result;
        }

        $path = trim((string) $path);
        if (is_empty_string($path)) {
            $path = $this->home_directory;
        }

        if (! is_empty_string($path)) {
            if (is_dir($path)) {
                if (! @chdir($path)) {
                    return ['output' => 'cd: '.$path.': Unable to change directory'];
                }
            } else {
                return ['output' => 'cd: '.$path.': No such directory'];
            }
        }

        return ['environment' => $this->get_environment()];
    }

    public function completion($token, $environment, $pattern, $command)
    {
        $result = $this->initialize($token, $environment);
        if ($result) {
            return $result;
        }

        $scan_path = '';
        $completion_prefix = '';
        $completion = [];

        if (! empty($pattern)) {
            if (! is_dir($pattern)) {
                $pattern = dirname($pattern);
                if ($pattern == '.') {
                    $pattern = '';
                }
            }

            if (! empty($pattern)) {
                if (is_dir($pattern)) {
                    $scan_path = $completion_prefix = $pattern;
                    if (substr($completion_prefix, -1) != '/') {
                        $completion_prefix .= '/';
                    }
                }
            } else {
                $scan_path = getcwd();
            }
        } else {
            $scan_path = getcwd();
        }

        if (! empty($scan_path)) {
            // Loading directory listing
            $completion = array_values(array_diff(scandir($scan_path), ['..', '.']));
            natsort($completion);

            // Prefix
            if (! empty($completion_prefix) && ! empty($completion)) {
                foreach ($completion as &$value) {
                    $value = $completion_prefix.$value;
                }
            }

            // Pattern
            if (! empty($pattern) && ! empty($completion)) {
                // For PHP version that does not support anonymous functions (available since PHP 5.3.0)
                function filter_pattern($value)
                {
                    global $pattern;

                    return ! strncmp($pattern, $value, strlen($pattern));
                }

                $completion = array_values(array_filter($completion, 'filter_pattern'));
            }
        }

        return ['completion' => $completion];
    }

    public function run($token, $environment, $command)
    {
        $result = $this->initialize($token, $environment);
        if ($result) {
            return $result;
        }

        $output = ($command && ! is_empty_string($command)) ? execute_command($command) : '';
        if ($output && substr($output, -1) == "\n") {
            $output = substr($output, 0, -1);
        }

        return ['output' => $output];
    }
}
