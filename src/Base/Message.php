<?php

namespace App\Base;

class Message {

    /** @var bool result of operation */
    public $result = true;
    /** @var string text with message */
    public $messageText;

    /**
     * Message constructor.
     * @param string $messageText
     * @param bool $result
     */
    public function __construct(bool $result, string $messageText = null)
    {
        $this->messageText = $messageText;
        $this->result = $result;
    }


}