<?php

namespace App\Controller;

use App\Base\Message;
use App\Base\RequestHelper;
use App\Base\ResponseHelper;
use App\Entity\Condition;
use App\Repository\ConditionRepository;
use App\Repository\SetupRepository;
use App\Repository\UserRepository;
use App\Structure\ConditionStructure;
use App\Structure\ConditionTransformed;
use App\Structure\SetupSimilarityStructure;
use App\Structure\SetupSimilarityTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as SWG;

class SetupController extends AbstractController {

    /**
     * Get similarity option
     * @Route("/rest/setup/similarity", name="similarity", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param SetupRepository $setupRepository
     * @return JsonResponse
     *
     * @SWG\Get(
     *  tags={"Setup"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="200", description="Similarity."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when you don't have right for operation.")
     * )
     *
     */
    public function getSimilarity(SetupRepository $setupRepository) {
        return new JsonResponse(['similarity' => $setupRepository->find(1)->getSimilarity()]);
    }

    /**
     * Setup similarity
     * @Route("/rest/setup/similarity", name="setup_similarity", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param SetupRepository $setupRepository
     * @return JsonResponse
     *
     * @SWG\Post(
     *  tags={"Setup"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\RequestBody(
     *          required=true,
     *          description="Setup similarity method computing for application, values: name or tanimoto",
     *          @SWG\MediaType(mediaType="application/json",
     *              @SWG\Schema(type="object",
     *                  @SWG\Property(property="similarity", type="string"),
     *                  example="{""similarity"":""tanimoto""}")
     *              ),
     *          ),
     *      ),
     *     @SWG\Response(response="204", description="Similarity method set."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when you don't have right for operation.")
     * )
     */
    public function setupSimilarity(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger, SetupRepository $setupRepository) {
        /** @var SetupSimilarityTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new SetupSimilarityStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $setup = $setupRepository->find(1);
        $setup->setSimilarity($trans->similarity);
        $entityManager->persist($setup);
        $entityManager->flush();
        return ResponseHelper::jsonResponse(Message::createNoContent());
    }

    /**
     * Reset conditions
     * @Route("/rest/setup/condition", name="setup_condition_reset", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return JsonResponse
     *
     * @SWG\Post(
     *  tags={"Setup"},
     *     security={
     *         {"ApiKeyAuth":{}}
     *     },
     *     @SWG\Response(response="204", description="Conditions reset."),
     *     @SWG\Response(response="401", description="Return when user is not logged in."),
     *     @SWG\Response(response="403", description="Return when you don't have right for operation.")
     * )
     */
    public function resetConditions(Request $request, LoggerInterface $logger, EntityManagerInterface $entityManager, UserRepository $userRepository) {
        /** @var ConditionTransformed $trans */
        $trans = RequestHelper::evaluateRequest($request, new ConditionStructure(), $logger);
        if ($trans instanceof JsonResponse) {
            return $trans;
        }
        $condition = new Condition();
        $condition->setText($trans->text);
        $entityManager->beginTransaction();
        $entityManager->persist($condition);
        $entityManager->flush();
        $res = $userRepository->resetConditions();
        $entityManager->commit();
        if (!$res) {
            return ResponseHelper::jsonResponse(new Message('Failure', Response::HTTP_INTERNAL_SERVER_ERROR));
        }
        return ResponseHelper::jsonResponse(Message::createNoContent());
    }


    /**
     * Get active conditions
     * @Route("/rest/setup/condition", name="setup_condition", methods={"GET"})
     * @param ConditionRepository $conditionRepository
     * @return JsonResponse
     *
     * @SWG\Get(
     *  tags={"Setup"},
     *  @SWG\Response(response="204", description="Conditions reset."),
     * )
     */
    public function getLastConditions(ConditionRepository $conditionRepository) {
        $data = $conditionRepository->findActiveCondition();
        if (isset($data) && sizeof($data) > 0) {
            return new JsonResponse($data[0]);
        }
        return new JsonResponse(['text' => '']);
    }

}
