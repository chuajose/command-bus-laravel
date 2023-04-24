<?php

namespace Chuajose\CommandBus;

class CommandBus
{
    public function justDoIt() {
        $response = json_decode(file_get_contents('https://dummyjson.com/products/1'), true);

        return $response['title'] . ' -' . $response['description'];
    }
}
