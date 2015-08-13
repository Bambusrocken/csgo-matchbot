<?php

/*
 * This file is part of PHP-WebSockets.
 * Copyright (c) 2012, Adam Alexander
 * All rights reserved.
 */

namespace Bot\Console\WebSocket\Base;

class WebSocketUser {

    public $socket;
    public $id;
    public $headers = array();
    public $handshake = false;

    public $handlingPartialPacket = false;
    public $partialBuffer = "";

    public $sendingContinuous = false;
    public $partialMessage = "";

    public $hasSentClose = false;

    function __construct($id, $socket) {
        $this->id = $id;
        $this->socket = $socket;
    }
}