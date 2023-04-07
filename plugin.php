<?php

/*
 * Plugin Name: Prompts Lab
 */

defined( 'ABSPATH' ) || exit;

include_once 'includes/class-promptslab.php';

PromptsLab::initialize(__FILE__);

function promptslab() {
    return PromptsLab::instance();
}

promptslab();