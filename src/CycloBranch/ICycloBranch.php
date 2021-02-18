<?php

namespace App\CycloBranch;

use Symfony\Component\HttpFoundation\Response;

interface ICycloBranch {

    /**
     * Import data from file
     */
    public function import();

    /**
     * Export files in CycloBranch format and download them
     */
    public function export(): Response;

}
