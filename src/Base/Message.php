<?php

namespace App\Base;

use Symfony\Component\HttpFoundation\Response;
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

    /** @var int HTTP status code for response */
    public $status;

    public $id;

    /**
     * Message constructor.
     * @param string $messageText
     * @param bool $result
     * @param int $status
     */
    public function __construct(string $messageText = null, int $status = Response::HTTP_BAD_REQUEST, bool $result = false, $id = null) {
        $this->messageText = $messageText;
        $this->status = $status;
        $this->result = $result;
        $this->id = $id;
    }

    /**
     * Helps to create OK message: Result successful and message OK.
     * @return Message
     */
    public static function createOkMessage(): Message {
        return new Message('OK', Response::HTTP_OK, true);
    }

    public static function createNoContent(): Message {
        return new Message(null, Response::HTTP_NO_CONTENT, true);
    }

    public static function createCreated($containerId = null): Message {
        return new Message(null, Response::HTTP_CREATED, true, $containerId);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize() {
        if ($this->messageText === null) {
            return null;
        } else {
            return ['message' => $this->messageText];
        }
    }

}
