<?php

namespace App\Structure;

class Sort {

    /** @var string */
    public $sort;

    /** @var string */
    public $order;

    /**
     * Sort constructor.
     * @param string $sort
     * @param string $order
     */
    public function __construct(string $sort, string $order) {
        $this->sort = $sort;
        $this->order = $order;
        if ($order !== "asc" && $order !== "desc") {
            $this->order = 'asc';
        }
    }

    public function asArray() {
        return [$this->sort => $this->order];
    }

}
