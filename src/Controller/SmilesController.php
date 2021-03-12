<?php

namespace App\Controller;

use App\Base\FormulaHelper;
use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\ErrorConstants;
use App\Exception\IllegalStateException;
use App\Repository\SequenceFamilyRepository;
use App\Repository\SequenceRepository;
use App\Repository\SetupRepository;
use App\Smiles\SmilesHelper;
use App\Structure\FormulaMass;
use App\Structure\SimilarityStructure;
use App\Structure\SimilarityTransformed;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * Class SmilesController
 * @package App\Controller
 */
class SmilesController extends AbstractController {

    /**
     * @Route("/rest/smiles/unique", name="smiles_unique", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"SMILES"},
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Array of smiles objects: Shouldn't be empty. SMILES is special format of molecular structures like: CCC(C)C1C(=O)NC(C(=O)NCCCC(C(=O)NC(C(=O)N2CCCC2C(=O)N1)CC3=CC=CC=C3)NC(=O)C(C(C)CC)NC(=O)C)C(C)CC",
     *          @SWG\Schema(type="string",
     *              example="[{""smiles"": ""CC(=O)CC""}]"),
     *      ),
     *     @SWG\Response(response="200", description="Return Unique SMILES."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *)
     */
    public function uniqueSmiles(Request $request) {
        $smilesInput = SmilesHelper::checkInputJson($request);
        if ($smilesInput instanceof JsonResponse) {
            return $smilesInput;
        }
        $length = count($smilesInput);
        $nextCheck = SmilesHelper::checkNext($smilesInput, $length);
        if ($nextCheck instanceof JsonResponse) {
            return $nextCheck;
        }
        return new JsonResponse(SmilesHelper::unique($smilesInput, $length));
    }

    /**
     * @Route("/rest/smiles/formula", name="smiles_formula", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     *
     * @SWG\Post(
     *     tags={"SMILES"},
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Array of smiles objects: Shouldn't be empty. SMILES is special format of molecular structures like: CCC(C)C1C(=O)NC(C(=O)NCCCC(C(=O)NC(C(=O)N2CCCC2C(=O)N1)CC3=CC=CC=C3)NC(=O)C(C(C)CC)NC(=O)C)C(C)CC",
     *          @SWG\Schema(type="string",
     *              example="[{""smiles"": ""CC(=O)CC""}]"),
     *      ),
     *     @SWG\Response(response="200", description="Return Unique SMILES."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *)
     */
    public function computeFormulaMass(Request $request) {
        $smilesInput = SmilesHelper::checkInputJson($request);
        if ($smilesInput instanceof JsonResponse) {
            return $smilesInput;
        }
        $length = count($smilesInput);
        $nextCheck = SmilesHelper::checkNext($smilesInput, $length);
        if ($nextCheck instanceof JsonResponse) {
            return $nextCheck;
        }
        $res = [];
        foreach ($smilesInput as $smiles) {
            $resObject = new FormulaMass();
            $resObject->smiles = $smiles->smiles;
            $resObject->formula = FormulaHelper::formulaFromSmiles($smiles->smiles);
            try {
                $resObject->mass = FormulaHelper::computeMass($resObject->formula);
            } catch (IllegalStateException $e) {
                /** Empty on purpose */
            }
            array_push($res, $resObject);
        }
        return new JsonResponse($res);
    }

    /**
     * @Route("/rest/smiles/similarity", name="similarity", methods={"POST"})
     * @param Request $request
     * @param LoggerInterface $logger
     * @param SetupRepository $setupRepository
     * @param SequenceFamilyRepository $sequenceFamilyRepository
     *
     * @param SequenceRepository $sequenceRepository
     * @return JsonResponse
     * @SWG\Post(
     *     tags={"SMILES"},
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="Array of smiles objects: Shouldn't be empty. SMILES is special format of molecular structures like: CCC(C)C1C(=O)NC(C(=O)NCCCC(C(=O)NC(C(=O)N2CCCC2C(=O)N1)CC3=CC=CC=C3)NC(=O)C(C(C)CC)NC(=O)C)C(C)CC",
     *          @SWG\Schema(type="string",
     *              example="[{}]"),
     *      ),
     *     @SWG\Response(response="200", description="Return Unique SMILES."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *)
     */
    public function similarity(Request $request, LoggerInterface $logger, SetupRepository$setupRepository, SequenceFamilyRepository $sequenceFamilyRepository, SequenceRepository $sequenceRepository) {
        /** @var SimilarityTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new SimilarityStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $setup = $setupRepository->findOneBy(['id' => 1]);
        if ($setup->getSimilarity() === 'name') {
            return new JsonResponse($sequenceFamilyRepository->similarity(1, $trans->sequenceName));
        } else {
            if ($trans->blockLengthUnique === 0) {
                return ResponseHelper::jsonResponse(new Message(ErrorConstants::ERROR_EMPTY_PARAMS, Response::HTTP_BAD_REQUEST));
            }
            return new JsonResponse($sequenceRepository->similarity(1, $trans->blocks, $trans->blockLengthUnique, $trans->blockLength));
        }
    }

}
