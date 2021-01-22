<?php

namespace App\Smiles\Parser;

use App\Exception\ReadOnlyOneTimeException;
use App\Smiles\Digit;
use App\Smiles\Enum\BondTypeEnum;
use App\Smiles\Exception\RejectException;
use App\Smiles\Graph;
use Bbdgnc\Base\OneTimeReadable;

class SmilesParser implements IParser {

    /** @var Graph $graph */
    private $graph;

    /** @var string $strSmiles control variable for while cycle */
    private $strSmiles;

    /** @var int $intNodeIndex actual node index */
    private $intNodeIndex;

    /** @var string $lastBond last parsed bond */
    private $lastBond;

    /** @var int $intLeftBraces count of '(' */
    private $intLeftBraces;

    /** @var int $intRightBraces count of ')' */
    private $intRightBraces;

    /** @var int $intReading count of numbers read */
    private $intReading;

    /** @var int $intWriting count of numbers stored */
    private $intWriting;

    private $isLastParsedBond = false;

    /** @var int[] $arNodesBeforeBracket stack for nodes before '(' */
    private $arNodesBeforeBracket;

    /** @var OneTimeReadable[] $arNumberBonds */
    private $arNumberBonds;

    /** @var BondParser $bondParser */
    private $bondParser;

    /** @var ElementParser $elementParser */
    private $elementParser;

    /** @var LeftBracketParser $leftBracketParser */
    private $leftBracketParser;

    /** @var RightBracketParser $rightBracketParser */
    private $rightBracketParser;

    /** @var SmilesNumberParser $smilesNumberParser */
    private $smilesNumberParser;

    /** @var string name of methods for callbacks */
    const TRY_BOND = 'tryBond';
    const TRY_NUMBER = 'tryNumberOk';
    const NEXT = 'next';
    const KO = 'ko';
    const BOND_AFTER_BRACKET = 'bondAfterBracketOk';
    const BOND_OK = 'bondOk';
    const LEFT_BRACKET_OK = 'leftBracketOk';
    const RIGHT_BRACKET_OK = 'rightBracketOk';
    const ORG_AFTER_BRACKET_OK = 'orgAfterBracketOk';
    const BOND_AFTER_RIGHT_BRACKET_OK = 'bondAfterRightBracketOk';
    const FIRST_ORG_OK = 'firstOrgOk';
    const BACK_FROM_BRACKET = 'backFromBracket';

    /**
     * SmilesParser constructor.
     * Initialize needed parsers and setup graph
     * @param Graph $graph
     */

    public function __construct(Graph $graph) {
        $this->graph = $graph;
        $this->bondParser = new BondParser();
        $this->elementParser = new ElementParser();
        $this->leftBracketParser = new LeftBracketParser();
        $this->rightBracketParser = new RightBracketParser();
        $this->smilesNumberParser = new BondAndNumberParser();
    }

    /**
     * Initialize variables for new parsing
     * @param string $strText
     */
    public function initialize($strText) {
        $this->strSmiles = $strText;
        $this->intNodeIndex = $this->intLeftBraces = $this->intRightBraces = $this->intReading = $this->intWriting = 0;
        $this->arNodesBeforeBracket = $this->arNumberBonds = [];
        $this->lastBond = "";
    }

    /**
     * Parse text
     * - remove all whitespace from text
     * - split by dots
     * - parse spliced strings to graph
     * @param string $strText
     * @return Accept|Reject
     */
    public function parse($strText) {
        $this->initialize($strText);
        try {
            while ($this->strSmiles !== '') {
                $this->parseAndCallBack(self::accept(), $this->elementParser, self::FIRST_ORG_OK, self::BACK_FROM_BRACKET);
            }
        } catch (RejectException | ReadOnlyOneTimeException $exception) {
            return self::reject();
        }
        return $this->intWriting == $this->intReading && $this->intLeftBraces == $this->intRightBraces && !$this->isLastParsedBond
            ? new Accept($this->graph, '') : self::reject();
    }

    /**
     * Smiles number parsed ok ->
     * try to load that number from map
     *  true -> load index of destination node -> can throw exception, can't load more times than one
     *  false -> store number of current node to OneTimeReadable object to map with index of read number from input
     * after that try to load another number, if there no number there should be bond
     * @param ParseResult $result
     * @param ParseResult $lastResult
     * @throws ReadOnlyOneTimeException
     * @throws RejectException
     */
    private function tryNumberOk(ParseResult $result, ParseResult $lastResult) {
        $this->isLastParsedBond = false;
        if (isset($this->arNumberBonds[$result->getResult()->getDigit()])) {
            if ($this->arNumberBonds[$result->getResult()->getDigit()]->isRead()) {
                $this->arNumberBonds[$result->getResult()->getDigit()] = new OneTimeReadable(new Digit($this->intNodeIndex - 1, false, $result->getResult()->getBondType()));
                $this->intWriting++;
            } else {
                /** @var Digit $digit */
                $digit = $this->arNumberBonds[$result->getResult()->getDigit()]->getObject();
                if ($digit->getBondType() !== $result->getResult()->getBondType()) {
                    self::ko($result, $lastResult);
                }
                $this->graph->addBidirectionalBond($digit->getDigit(), $this->intNodeIndex - 1, $digit->getBondType());
                $this->intReading++;
            }
        } else {
            $this->arNumberBonds[$result->getResult()->getDigit()] = new OneTimeReadable(new Digit($this->intNodeIndex - 1, false, $result->getResult()->getBondType()));
            $this->intWriting++;
        }
        $this->parseAndCallBack($result, $this->smilesNumberParser, self::TRY_NUMBER, self::TRY_BOND);
    }

    /**
     * Try parse bond
     *  true -> call bondOk
     *  false -> call next
     * @param ParseResult $result
     * @param ParseResult $lastResult
     */
    private function tryBond(ParseResult $result, ParseResult $lastResult) {
        $this->parseAndCallBack($lastResult, $this->bondParser, self::BOND_OK, self::NEXT);
    }

    /**
     * Bond parse ok ->
     * Set last bond as parsed bond
     * Try to parse '('
     *  true -> call leftBracketOk
     *  false -> next
     * @param ParseResult $result
     * @param ParseResult $lastResult
     */
    private function bondOk(ParseResult $result, ParseResult $lastResult) {
        $this->lastBond = $result->getResult();
        $this->isLastParsedBond = true;
        $this->parseAndCallBack($result, $this->leftBracketParser, self::LEFT_BRACKET_OK, self::NEXT);
    }

    /**
     * '(' parsed ok
     * If last bond was more than '-' -> then reject, because of wrong input
     * Try to parse right bond
     *  true -> call bondAfterBracketOk
     *  false -> call next
     * @param ParseResult $result
     * @param ParseResult $lastResult
     * @throws RejectException
     */
    private function leftBracketOk(ParseResult $result, ParseResult $lastResult) {
        $this->intLeftBraces++;
        $this->isLastParsedBond = false;
        if (BondTypeEnum::isMultipleBinding($lastResult->getResult())) {
            throw new RejectException();
        }
        $this->parseAndCallBack($result, $this->bondParser, self::BOND_AFTER_BRACKET, self::NEXT);
    }

    /**
     * Setup SMILES to last parsed remainder and than go to next cycle of while
     * @param ParseResult $result
     * @param ParseResult $lastResult
     */
    private function next(ParseResult $result, ParseResult $lastResult) {
        $this->strSmiles = $lastResult->getRemainder();
    }

    /**
     * Bond parsed ok
     * Setup last bond to parsed bond, add index of current node to stack,
     * set SMILES to last parsed remainder and go to next cycle of while
     * @param ParseResult $result
     * @param ParseResult $lastResult
     */
    private function bondAfterBracketOk(ParseResult $result, ParseResult $lastResult) {
        $this->lastBond = $result->getResult();
        $this->isLastParsedBond = true;
        $this->arNodesBeforeBracket[] = $this->intNodeIndex - 1;
        $this->strSmiles = $result->getRemainder();
    }

    /**
     * Organic subset parsed ok
     * Add node to graph
     * if isn't first literal of input then add bond to previous node
     * try to parse Smiles number
     *  true -> call tryNumberOk
     *  false -> call tryBond
     * @param ParseResult $result
     * @param ParseResult $lastResult
     */
    private function firstOrgOk(ParseResult $result, ParseResult $lastResult) {
        $this->isLastParsedBond = false;
        $this->graph->addNode($result->getResult());
        if ($this->intNodeIndex > 0) {
            $this->graph->addBidirectionalBond($this->intNodeIndex - 1, $this->intNodeIndex, $this->lastBond);
        }
        $this->intNodeIndex++;
        $this->parseAndCallBack($result, $this->smilesNumberParser, self::TRY_NUMBER, self::TRY_BOND);
    }

    /**
     * If stack isn't empty try to parse ')'
     *  true -> call rightBracketOk
     *  false -> call ko
     * elsewhere wrong input
     * @param ParseResult $result
     * @param ParseResult $lastResult
     * @throws RejectException
     */
    private function backFromBracket(ParseResult $result, ParseResult $lastResult) {
        if (!empty($this->arNodesBeforeBracket)) {
            $this->parseAndCallBack(self::accept(), $this->rightBracketParser, self::RIGHT_BRACKET_OK, self::KO);
        } else {
            throw new RejectException();
        }
    }

    /**
     * Organic subset after ')' parsed ok
     * Add node and bonds to bond before '('
     * Try to parse Smiles number
     *  true -> call tryNumberOk
     *  false -> call tryBond
     * @param ParseResult $result
     * @param ParseResult $lastResult
     */
    private function orgAfterBracketOk(ParseResult $result, ParseResult $lastResult) {
        $this->isLastParsedBond = false;
        $intTargetNodeIndex = array_pop($this->arNodesBeforeBracket);
        $this->graph->addNode($result->getResult());
        $this->graph->addBidirectionalBond($intTargetNodeIndex, $this->intNodeIndex, $this->lastBond);
        $this->intNodeIndex++;
        $this->parseAndCallBack($result, $this->smilesNumberParser, self::TRY_NUMBER, self::TRY_BOND);
    }

    /**
     * KO
     * Wrong input
     * @param ParseResult $result
     * @param ParseResult $lastResult
     * @throws RejectException
     */
    private function ko(ParseResult $result, ParseResult $lastResult) {
        throw new RejectException();
    }

    /**
     * Bond after ')' parsed ok
     * Try to parse Organic subset
     *  true -> call orgAfterBracket
     *  false -> call ko
     * @param ParseResult $result
     * @param ParseResult $lastResult
     */
    private function bondAfterRightBracketOk(ParseResult $result, ParseResult $lastResult) {
        $this->lastBond = $result->getResult();
        $this->isLastParsedBond = true;
        $this->parseAndCallBack($result, $this->elementParser, self::ORG_AFTER_BRACKET_OK, 'leftBracketAfterRight');
    }

    /**
     * @param ParseResult $result
     * @param ParseResult $lastResult
     * @throws RejectException
     */
    private function leftBracketAfterRight(ParseResult $result, ParseResult $lastResult) {
        if (BondTypeEnum::isMultipleBinding($this->lastBond)) {
            $this->ko($result, $lastResult);
        }
        $this->parseAndCallBack($lastResult, $this->leftBracketParser, 'leftWhenDifferentBrackets', self::KO);
    }

    private function leftWhenDifferentBrackets(ParseResult $result, ParseResult $lastResult) {
        $this->intLeftBraces++;
        $this->isLastParsedBond = false;
        $this->parseAndCallBack($result, $this->bondParser, 'bondWhenDifferentBracketsAfter', self::KO);
    }

    private function bondWhenDifferentBracketsAfter(ParseResult $result, ParseResult $lastResult) {
        $this->lastBond = $result->getResult();
        $this->isLastParsedBond = true;
        $this->parseAndCallBack($result, $this->elementParser, 'orgWhenDifferentBrackets', self::KO);
    }

    private function orgWhenDifferentBrackets(ParseResult $result, ParseResult $lastResult) {
        $this->isLastParsedBond = false;
        $intTargetNodeIndex = end($this->arNodesBeforeBracket);
        $this->graph->addNode($result->getResult());
        $this->graph->addBidirectionalBond($intTargetNodeIndex, $this->intNodeIndex, $this->lastBond);
        $this->intNodeIndex++;
        $this->parseAndCallBack($result, $this->smilesNumberParser, self::TRY_NUMBER, self::TRY_BOND);
    }

    /**
     * ')' parsed ok
     * Try to parse bond
     *  true -> call bondAfterRightBracketOK
     *  false -> call next
     * @param ParseResult $result
     * @param ParseResult $lastResult
     */
    private function rightBracketOk(ParseResult $result, ParseResult $lastResult) {
        $this->intRightBraces++;
        $this->isLastParsedBond = false;
        $this->parseAndCallBack($result, $this->bondParser, self::BOND_AFTER_RIGHT_BRACKET_OK, self::NEXT);
    }

    /**
     * Try to parse input
     *  parsing ok -> call $funcOk()
     *  parsing ko -> call $funcKo()
     * @param ParseResult $lastResult last result of parsed input
     * @param IParser $parser parser to try
     * @param string $funcOk callback to call if parsing success
     * @param string $funcKo callback to call if parsing go wrong
     */
    private function parseAndCallBack(ParseResult $lastResult, IParser $parser, string $funcOk, string $funcKo) {
        $result = $parser->parse($lastResult->getRemainder());
        if ($result->isAccepted()) {
            $this->$funcOk($result, $lastResult);
        } else {
            $this->$funcKo($result, $lastResult);
        }
    }

    /**
     * Get instance of Reject
     * @return Reject
     */
    public static function reject() {
        return new Reject('Not match SMILES');
    }

    /**
     * A little for starting parseAndCallback()
     * @return Accept
     */
    private function accept() {
        return new Accept('', $this->strSmiles);
    }

}
