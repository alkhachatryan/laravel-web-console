<?php

function is_empty_string($string)
{
    return strlen($string) <= 0;
}

function is_equal_strings($string1, $string2)
{
    return strcmp($string1, $string2) == 0;
}

function get_hash($algorithm, $string)
{
    return hash($algorithm, trim((string) $string));
}

// Command execution
function execute_command($command)
{
    $descriptors = [
        0 => ['pipe', 'r'], // STDIN
        1 => ['pipe', 'w'], // STDOUT
        2 => ['pipe', 'w'],  // STDERR
    ];

    $process = proc_open($command.' 2>&1', $descriptors, $pipes);
    if (! is_resource($process)) {
        exit("Can't execute command.");
    }

    // Nothing to push to STDIN
    fclose($pipes[0]);

    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $error = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    // All pipes must be closed before "proc_close"
    $code = proc_close($process);

    return $output;
}

// Command parsing
function parse_command($command)
{
    $value = ltrim((string) $command);

    if (! is_empty_string($value)) {
        $values = explode(' ', $value);
        $values_total = count($values);

        if ($values_total > 1) {
            $value = $values[$values_total - 1];

            for ($index = $values_total - 2; $index >= 0; $index--) {
                $value_item = $values[$index];

                if (substr($value_item, -1) == '\\') {
                    $value = $value_item.' '.$value;
                } else {
                    break;
                }
            }
        }
    }

    return $value;
}
