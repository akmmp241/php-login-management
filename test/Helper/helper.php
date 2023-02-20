<?php

namespace Akmalmp\BelajarPhpMvc\App {
    function header(string $value)
    {
        echo $value;
    }
}

namespace Akmalmp\BelajarPhpMvc\Service {
    function setcookie(string $name, string $value)
    {
        echo "$name: $value";
    }
}