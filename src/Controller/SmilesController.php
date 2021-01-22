<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Exception\IllegalStateException;
use App\Smiles\SmilesHelper;
use App\Structure\UniqueSmilesStructure;
use App\Structure\UniqueSmilesTransformed;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * Class SmilesController
 * @package App\Controller
 */
class SmilesController extends AbstractController {

    /**
     * @Route("/rest/smiles/unique", name="smiles_unique", methods={"GET"})
     * @param Request $request
     * @param LoggerInterface $logger
     * @return UniqueSmilesTransformed|JsonResponse
     *
     * @SWG\Get(
     *     tags={"SMILES"},
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="One param: smiles. Shouldn't be empty. SMILES is special format of molecular structures like: CCC(C)C1C(=O)NC(C(=O)NCCCC(C(=O)NC(C(=O)N2CCCC2C(=O)N1)CC3=CC=CC=C3)NC(=O)C(C(C)CC)NC(=O)C)C(C)CC",
     *          @SWG\Schema(type="string",
     *              example="{""smiles"":""OC(C(C(CC)C)N)=O""}"),
     *      ),
     *     @SWG\Response(response="200", description="Return Unique SMILES."),
     *     @SWG\Response(response="401", description="Return when input is wrong."),
     *)
     */
    public function uniqueSmiles(Request $request, LoggerInterface $logger) {
        /** @var UniqueSmilesTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new UniqueSmilesStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }

        try {
            return ResponseHelper::jsonResponse(new Message(SmilesHelper::getUniqueSmiles($trans->getSmiles())));
        } catch (IllegalStateException $e) {
            return ResponseHelper::jsonResponse(new Message($e->getMessage()));
        }
    }

}
