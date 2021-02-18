<?php

namespace App\CycloBranch;

use App\Smiles\Parser\IParser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractCycloBranch
 * Abstract class for import/export data from CycloBranch
 */
abstract class AbstractCycloBranch implements ICycloBranch, IParser {

    public const TABULATOR = "\t";

    /** @var ServiceEntityRepository */
    protected $repository;

    /** @var int */
    protected $containerId;

    /** @var string */
    protected $data = '';

    /**
     * AbstractCycloBranch constructor.
     * @param ServiceEntityRepository $repository
     * @param int $containerId
     */
    public function __construct(ServiceEntityRepository $repository, int $containerId) {
        $this->repository = $repository;
        $this->containerId = $containerId;
    }

    /**
     * @param string $filePath
     * @see ICycloBranch::import()
     */
    public final function import(string $filePath) {
//        ini_set('max_execution_time', 120);
//
//        $handle = fopen($filePath, 'r');
//        if (!$handle) {
//            return;
//        }
//        while (($line = fgets($handle)) !== false) {
//            $arBlocksResult = $this->parse($line);
//            if ($arBlocksResult->isAccepted()) {
//                $this->save($arBlocksResult->getResult());
//            } else {
//                Logger::log(LoggerEnum::WARNING, "Line not parsed correctly" . PHP_EOL . $line . $arBlocksResult->getErrorMessage());
//            }
//        }
//        fclose($handle);
//        unlink($filePath);
//        ini_set('max_execution_time', 30);
    }

    /**
     * Parse one line of an uploaded file
     * @param string $strText line of file
     * @see IParser::parse()
     */
    public abstract function parse(string $strText);

    /**
     * @see IParser::reject()
     */
    public abstract static function reject();

    /**
     * Exporting data to a file
     */
    public abstract function download(): string;

    /**
     * @see ICycloBranch::export()
     */
    public final function export(): Response {
        $this->download();
        $response = new Response($this->data, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/plain');
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'data.txt'
        );
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    /**
     * Save data to database
     * @param array $arTos
     */
    protected function save(array $arTos) {
//        $this->database->startTransaction();
//        $this->database->insertMore($arTos);
//        $this->database->endTransaction();
    }

    protected function validateLine($line, $allSet = true) {
        $arItems = preg_split('/\t/', $line);
        if (empty($arItems) || sizeof($arItems) !== $this->getLineLength()) {
            return false;
        }

        if ($allSet) {
            for ($index = 0; $index < $this->getLineLength(); ++$index) {
                if ($arItems[$index] === "") {
                    return false;
                }
            }
        }
        return $arItems;
    }

}
