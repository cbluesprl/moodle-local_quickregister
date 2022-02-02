<?php

$observers = [
    [
      'eventname' => '\core\event\user_created',
      'callback' => '\local_quickregister\observer::user_created'
    ]
];