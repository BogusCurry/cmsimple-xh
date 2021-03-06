<?php

spl_autoload_register(function ($className) {
    $className = str_replace('_', '\\', $className);
    // set $package, $subpackages and $class
    $subpackages = explode('\\', $className);
    $packages = array_splice($subpackages, 0, 1);
    $package = $packages[0];
    $classes = array_splice($subpackages, -1);
    $class = $classes[0];

    // construct $filename
    if ($package == 'XH') {
        $folder = './cmsimple/classes/';
    } else {
        $folder = './plugins/' . strtolower($package) . '/classes/';
    }
    foreach ($subpackages as $subpackage) {
        $folder .= strtolower($subpackage) . '/';
    }
    $filename = $folder . $class . '.php';

    if (file_exists($filename)) {
        include_once $filename;
    }
});

?>
