<?php

function pl_option($key) {
    $options = get_option( PL_OPTIONS_KEY, [] );
    return isset($options[$key]) ? $options[$key] : null;
}