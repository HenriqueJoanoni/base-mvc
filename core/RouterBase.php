<?php

namespace core;

use \src\Config;

class RouterBase
{
    private const URL_PATTERN = '(\{[a-z0-9]+})';

    public function run($routes)
    {
        $method = Request::getMethod();
        $url = Request::getUrl();

        /**
         * Default Items
         */
        $controller = Config::ERROR_CONTROLLER;
        $action = Config::DEFAULT_ACTION;
        $args = [];
        $resultActions = [];

        if (isset($routes[$method])) {
            $resultActions = $this->setRoutesCallbacks($routes, $method, $url);
        }

        $controller = "\src\controllers\\" . $resultActions['controller'] ?? $controller;
        $definedController = new $controller();

        if ($resultActions['action']) {
            $action = $resultActions['action'];
            $args = $resultActions['args'];
        }
        $definedController->$action($args);
    }

    private function setRoutesCallbacks(array $routes, string $method, string $url): array
    {
        foreach ($routes[$method] as $route => $callback) {
            /**
             * Identify the arguments and replace them with a regex
             */
            $pattern = preg_replace(self::URL_PATTERN, self::URL_PATTERN, $route);

            /**
             * Matches the URL
             */
            if (preg_match('#^(' . $pattern . ')*$#i', $url, $matches) === 1) {
                array_shift($matches);
                array_shift($matches);

                /**
                 * Associate the arguments
                 */
                $items = array();
                if (preg_match_all(self::URL_PATTERN, $route, $m)) {
                    $items = preg_replace('([{}])', '', $m[0]);
                }

                /*
                 * Perform the association
                 */
                $args = array();
                foreach ($matches as $key => $match) {
                    $args[$items[$key]] = $match;
                }

                /**
                 * Set controller/action
                 */
                $callbackSplit = explode('@', $callback);
                $controller = $callbackSplit[0];
                if (isset($callbackSplit[1])) {
                    $action = $callbackSplit[1];
                }
                break;
            }
        }

        return ["controller" => $controller ?? "", "action" => $action ?? "", "args" => $args ?? ""];
    }
}