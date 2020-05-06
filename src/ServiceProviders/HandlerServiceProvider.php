<?php
namespace agoalofalife\bpm\ServiceProviders;


use agoalofalife\bpm\Contracts\ServiceProvider;
use agoalofalife\bpm\Handlers\JsonHandler;
use agoalofalife\bpm\Handlers\XmlHandler;


class HandlerServiceProvider implements ServiceProvider
{

    public function register()
    {
        app()->bind(JsonHandler::class, JsonHandler::class);
        app()->bind(XmlHandler::class, XmlHandler::class);
    }
}