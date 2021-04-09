<?php

namespace App\Base;

class Cap {

    /**
     * @var array ACIDS, this array is static and READONLY! Never change it in other place in code!
     */
    private static $ACIDS = [
        'Ala' => 'Alanine',
        'Arg' => 'Arginine',
        'Asn' => 'Asparagine',
        'Asp' => 'Aspartic acid',
        'Cys' => 'Cysteine',
        'Gln' => 'Glutamine',
        'Glu' => 'Glutamic acid',
        'Gly' => 'Glycine',
        'His' => 'Histidine',
        'Ile' => 'Isoleucine',
        'Leu' => 'Leucine',
        'Lys' => 'Lysine',
        'Met' => 'Methionine',
        'Phe' => 'Phenylalanine',
        'Pro' => 'Proline',
        'Ser' => 'Serine',
        'Thr' => 'Threonine',
        'Trp' => 'Tryptophan',
        'Tyr' => 'Tyrosine',
        'Val' => 'Valine'
    ];

    public static function getRandomAcid() {
        return self::$ACIDS[array_rand(self::$ACIDS, 1)];
    }

    public static function verify(string $question, string $answer) {
        return isset(self::$ACIDS[$answer]) && self::$ACIDS[$answer] === $question;
    }

}
