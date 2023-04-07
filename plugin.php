<?php

/*
 * Plugin Name: Prompts Lab
 */

defined( 'ABSPATH' ) || exit;

// load vendor libraries
require_once 'vendor/autoload.php';
// load main class
include_once 'includes/class-promptslab.php';

PromptsLab::initialize(__FILE__);

function promptslab() {
    return PromptsLab::instance();
}

promptslab();