<?php

namespace App\Listeners;

use Illuminate\Support\Arr;

abstract class Listener
{
    protected $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function preprocess()
    {

    }

    public function postprocess()
    {

    }

    public function getHandler()
    {
        return Arr::get($this->event->context, 'handler');
    }
}
