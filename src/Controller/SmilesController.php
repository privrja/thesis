<?php

namespace App\Controller;

use App\Base\FormulaHelper;
use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Constant\ErrorConstants;
use App\Entity\Container;
use App\Enum\ContainerVisibilityEnum;
use App\Exception\IllegalStateException;
use App\Model\ContainerModel;
use App\Repository\SequenceFamilyRepository;
use App\Repository\SequenceRepository;
use App\Repository\SetupRepository;
use App\Smiles\Enum\LossesEnum;
use App\Smiles\SmilesHelper;
use App\Structure\FormulaMass;
use App\Structure\SimilarityStructure;
use App\Structure\SimilarityTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Symfony\Component\Security\Core\Security;

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
            // TODO better check for polyketide, then form mass with losses and maybe seting to not removing any losses?
            $polyketide = str_contains(strtoupper($smiles->smiles), 'O');
            $resObject = new FormulaMass();
            $resObject->smiles = $smiles->smiles;
            $resObject->formula = FormulaHelper::formulaFromSmiles($smiles->smiles, $polyketide ? LossesEnum::H2 : LossesEnum::H2O);
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
     * @Route("/rest/container/{containerId}/sim", name="similarity_container", methods={"POST"})
     * @Entity("container", expr="repository.find(containerId)")
     * @param Container $container
     * @param Request $request
     * @param LoggerInterface $logger
     * @param SetupRepository $setupRepository
     * @param SequenceFamilyRepository $sequenceFamilyRepository
     *
     * @param SequenceRepository $sequenceRepository
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @return JsonResponse
     * @SWG\Post(
     *     tags={"SMILES"},
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          type="string",
     *          required=true,
     *          description="This return best match for sequence family. SequenceName or rest must be filled based on similarity method used by application - name or tanimoto, when name is used is required too fill sequenceName, when tanimoto is used you need too fill blockLengthUnique, blockLength and blocks, where blocks is array of blocks id from database from sequence, and difference between length is only when some blocks are used in sequence more times.",
     *          @SWG\Schema(type="string",
     *              example="{""sequenceName"":""pseudacyclin a"",""blockLengthUnique"":5,""blockLength"":6,""blocks"":[15,39,19,9,26]}"),
     *      ),
     *     @SWG\Response(response="200", description="Return Unique SMILES."),
     *     @SWG\Response(response="400", description="Return when input is wrong."),
     *)
     */
    public function similarityContainer(Container $container, Request $request, LoggerInterface $logger, SetupRepository$setupRepository, SequenceFamilyRepository $sequenceFamilyRepository, SequenceRepository $sequenceRepository, EntityManagerInterface $entityManager, Security $security) {
        $model = new ContainerModel($entityManager, $this->getDoctrine(), $security->getUser(), $logger);
        if ($container->getVisibility() === ContainerVisibilityEnum::PUBLIC || ($this->isGranted("ROLE_USER") && $model->hasContainer($container->getId()))) {
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
                return new JsonResponse($sequenceRepository->similarity($container->getId(), $trans->blocks, $trans->blockLengthUnique, $trans->blockLength));
            }
        } else {
            return new JsonResponse(new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN));
        }
    }

}
