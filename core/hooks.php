<?php

$hooks = [];

function add_action(string $hook_name, callable $callback): void {
    global $hooks;
    $hooks[$hook_name][] = $callback;
}

function do_action(string $hook_name, ...$args)
{
    global $hooks;
    $output = '';

    if (isset($hooks[$hook_name])) {
        foreach ($hooks[$hook_name] as $callback) {
            $result = call_user_func_array($callback, $args);
            if (is_string($result)) {
                $output .= $result;
            }
        }
    }

    return $output;
}