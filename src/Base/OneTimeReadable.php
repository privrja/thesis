<?php

namespace App\Base;

use App\Exception\ReadOnlyOneTimeException;

/**
 * Class OneTimeReadable
 * One time readable storage, after first read the value is destroyed
 * @package Bbdgnc\Base
 */
class OneTimeReadable {

    /** @var mixed stored object */
    private $object;

    /** @var bool indicating times of read ('Zero' = false or 'More times' = true) */
    private $read = false;

    /**
     * OneTimeReadable constructor.
     * @param mixed $object
     */
    public function __construct($object) {
        $this->object = $object;
    }

    /**
     * Get Stored Object
     * @return mixed stored object
     * @throws ReadOnlyOneTimeException when object has been already read
     */
    public function getObject() {
        if ($this->read) {
            throw new ReadOnlyOneTimeException();
        }
        $this->read = true;
        return $this->swap();
    }

    /**
     * Destroy object in this class
     * @return mixed
     */
    private function swap() {
        $tmpObject = $this->object;
        $this->object = null;
        return $tmpObject;
    }

    /**
     * @see OneTimeReadable::$read
     * @return bool
     */
    public function isRead() {
        return $this->read;
    }

}
