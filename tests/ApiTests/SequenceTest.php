<?php

namespace App\Tests\ApiTests;

class SequenceTest extends LoginTest {

    public function testCreateSequence() {
        $client = self::loginClient();
        $client->request('POST', '/rest/container/1/sequence', [], [], [], json_encode([
            'sequenceName' => 'roseotoxin A',
            'formula' => 'C30H49N5O7',
            'mass' => 591.363199,
            'smiles' => 'CCC(C)C1C(=O)N(C(C(=O)N(C(C(=O)NCCC(=O)OC(C(=O)N2CCC(C2C(=O)N1)C)CC=C)C)C)C(C)C)C',
            'source' => 0,
            'identifier' => '24039283',
            'sequence' => '[Ile]-[3-m]-[2-h]-[Bet]-[Met1]-[Met2]',
            'sequenceOriginal' => '[0]-[5]-[4]-[3]-[2]-[1]',
            'decays' => '[6,10,14,19,23,30]',
            'sequenceType' => 'cyclic',
            'blocks' => [
                0 => [
                    'databaseId' => 15,
                    'originalId' => 0,
                ],
                1 => [
                    'originalId' => 1,
                    'acronym' => 'Met2',
                    'blockName' => 'N-Methylvaline',
                    'smiles' => 'CNC(C(C)C)C(O)=O',
                    'formula' => 'C6H11NO',
                    'mass' => 113.084064,
                    'source' => 0,
                    'identifier' => '4378',
                ],
                2 => [
                    'originalId' => 2,
                    'acronym' => 'Met1',
                    'blockName' => 'N-Methyl-DL-alanine',
                    'smiles' => 'CNC(C)C(O)=O',
                    'formula' => 'C4H7NO',
                    'source' => 0,
                    'identifier' => '4377'
                ],
                3 => [
                    'originalId' => 3,
                    'acronym' => 'Bet',
                    'blockName' => 'beta-alanine',
                    'smiles' => 'NCCC(O)=O',
                    'formula' => 'C3H5NO',
                    'mass' => 71.0371133137,
                    'source' => 0,
                    'identifier' => '237'
                ],
                4 => [
                    'originalId' => 4,
                    'acronym' => '2-h',
                    'blockName' => '2-hydroxypent-4-enoic acid',
                    'smiles' => 'OC(CC=C)C(O)=O',
                    'formula' => 'C5H6O2',
                    'mass' => 98.0367793137,
                    'source' => 0,
                    'identifier' => '172026'
                ],
                5 => [
                    'originalId' => 5,
                    'acronym' => '3-m',
                    'blockName' => '3-methylpyrrolidine-2-carboxylic acid',
                    'smiles' => 'CC1CCNC1(O)=O',
                    'formula' => 'C6H9NO',
                    'mass' => 111.0684143137,
                    'source' => 0,
                    'identifier' => '14185042'
                ]
            ]
        ]));
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testPutSequenceFamily() {
        $client = self::loginClient();
        $client->request('PATCH', '/rest/container/1/sequence/1', [], [], [], json_encode(['family' => ['Kokos', 2]]));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testPutSequenceEmpty() {
        $client = self::loginClient();
        $client->request('PATCH', '/rest/container/1/sequence/1');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

}