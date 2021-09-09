<?php


namespace ModStart\App\Core;


use Illuminate\Http\Request;

interface AccessGate
{
    function check(Request $request);
}
