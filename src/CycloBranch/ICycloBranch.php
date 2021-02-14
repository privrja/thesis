<?php

namespace App\CycloBranch;

use Symfony\Component\HttpFoundation\Response;

interface ICycloBranch {

    /**
     * Import data from file
     * @param string $filePath path to uploaded file
     */
    public function import(string $filePath);

    /**
     * Export files in CycloBranch format and download them
     */
    public function export(): Response;

}
