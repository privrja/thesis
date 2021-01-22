<?php

namespace App\Structure;

class AtomCount {

    /** @var string $atom */
    private $atom;

    /** @var int $count */
    private $count;

    /**
     * AtomCount constructor.
     * @param string $atom
     * @param int $count
     */
    public function __construct(string $atom, int $count) {
        $this->atom = $atom;
        $this->count = $count;
    }

    /**
     * @return string
     */
    public function getAtom(): string {
        return $this->atom;
    }

    /**
     * @param string $atom
     */
    public function setAtom(string $atom): void {
        $this->atom = $atom;
    }

    /**
     * @return int
     */
    public function getCount(): int {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void {
        $this->count = $count;
    }

}
