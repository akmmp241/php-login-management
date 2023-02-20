<?php

namespace Akmalmp\BelajarPhpMvc\Middleware;

interface Middleware
{
    function before(): void;
}