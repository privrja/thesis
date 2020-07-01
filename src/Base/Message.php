<?php

namespace App\Base;

use JsonSerializable;

/**
 * Class Message
 * This class hold if status of some operation is successful or not. And have a message with some text.
 * @package App\Base
 */
class Message implements JsonSerializable {

    /** @var bool result of operation */
    public $result;

    /** @var string text with message */
    public $messageText;

    /**
     * Message constructor.
     * @param string $messageText
     * @param bool $result
     */
    public function __construct(string $messageText = null, bool $result = false) {
        $this->messageText = $messageText;
        $this->result = $result;
    }

    /**
     * Helps to create OK message: Result successful and message OK.
     * @return Message
     */
    public static function createOkMessage(): Message {
        return new Message('OK', true);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        return ['message' => $this->messageText];
    }

}
