<?php

namespace App\Enum;

use App\Smiles\Element;

/**
 * Class PeriodicTableEnum
 * Singleton with Elements of Periodic table, their names shortcuts and mass
 * @package Bbdgnc\Enum
 */
class PeriodicTableSingleton {

    /** @var PeriodicTableSingleton $instance instance of this class */
    private static $instance = null;

    /** @var Element[] */
    private $arAtoms;

    /** @var string H */
    const H = 'H';

    /** @var string O */
    const O = 'O';

    /**
     * Get instance of singleton
     * @return PeriodicTableSingleton
     */
    public static function getInstance(): PeriodicTableSingleton {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return Element[]
     */
    public function getAtoms(): array {
        return $this->arAtoms;
    }

    /**
     * PeriodicTableEnum constructor.
     */
    public function __construct() {
        $this->arAtoms = array(
            'H' => new Element('H', 1, 1, 1.0078250321),
            'D' => new Element('D', 1, 1, 2.014102),
            'T' => new Element('T', 1, 1, 3.016049),
            'He' => new Element('He', 2, 1, 4.002606),
            'Li' => new Element('Li', 3, 1, 7.016004),
            'Be' => new Element('Be', 4, 1, 9.012182),
            'B' => new Element('B', 5, 3, 11.009306),
            'b' => new Element('B', 5, 2, 11.009306, true),
            'C' => new Element('C', 6, 4, 12.0),
            'c' => new Element('C', 6, 3, 12.0, true),
            'N' => new Element('N', 7, 3, 14.0030740052),
            'n' => new Element('N', 7, 2, 14.0030740052, true),
            'O' => new Element('O', 8, 2, 15.9949146221),
            'o' => new Element('O', 8, 1, 15.9949146221, true),
            'F' => new Element('F', 9, 1, 18.998404),
            'f' => new Element('F', 9, 0, 18.998404, true),
            'Ne' => new Element('Ne', 10, 1, 19.992439),
            'Na' => new Element('Na', 11, 1, 22.989771),
            'Mg' => new Element('Mg', 12, 1, 23.985043),
            'Al' => new Element('Al', 13, 1, 26.981539),
            'Si' => new Element('Si', 14, 1, 27.976927),
            'P' => new Element('P', 15, 3, 30.97376151),
            'p' => new Element('P', 15, 2, 30.97376151, true),
            'S' => new Element('S', 16, 2, 31.97207069),
            's' => new Element('S', 16, 1, 31.97207069, true),
            'Cl' => new Element('Cl', 17, 1, 34.968853),
            'Ar' => new Element('Ar', 18, 1, 39.962383),
            'K' => new Element('K', 19, 1, 38.963707),
            'Ca' => new Element('Ca', 20, 1, 39.962589),
            'Sc' => new Element('Sc', 21, 1, 44.95592),
            'Ti' => new Element('Ti', 22, 1, 47.947948),
            'V' => new Element('V', 23, 1, 50.943962),
            'Cr' => new Element('Cr', 24, 1, 51.940514),
            'Mn' => new Element('Mn', 25, 1, 54.938049),
            'Fe' => new Element('Fe', 26, 1, 55.93494),
            'Co' => new Element('Co', 27, 1, 58.933201),
            'Ni' => new Element('Ni', 28, 1, 57.935349),
            'Cu' => new Element('Cu', 29, 1, 62.9296),
            'Zn' => new Element('Zn', 30, 1, 63.929146),
            'Ga' => new Element('Ga', 31, 1, 68.925583),
            'Ge' => new Element('Ge', 32, 1, 73.921181),
            'As' => new Element('As', 33, 1, 74.921600),
            'Se' => new Element('Se', 34, 1, 79.916519),
            'Br' => new Element('Br', 35, 1, 78.918327),
            'Kr' => new Element('Kr', 36, 1, 83.911507),
            'Rb' => new Element('Rb', 37, 1, 84.911789),
            'Sr' => new Element('Sr', 38, 1, 87.905617),
            'Y' => new Element('Y', 39, 1, 88.905846),
            'Zr' => new Element('Zr', 40, 1, 89.904701),
            'Nb' => new Element('Nb', 41, 1, 92.906403),
            'Mo' => new Element('Mo', 42, 1, 97.905411),
            'Tc' => new Element('Tc', 43, 1, 97.907219),
            'Ru' => new Element('Ru', 44, 1, 101.904388),
            'Rh' => new Element('Rh', 45, 1, 102.905502),
            'Pd' => new Element('Pd', 46, 1, 105.903481),
            'Ag' => new Element('Ag', 47, 1, 106.905090),
            'Cd' => new Element('Cd', 48, 1, 113.903358),
            'In' => new Element('In', 49, 1, 114.903877),
            'Sn' => new Element('Sn', 50, 1, 119.902199),
            'Sb' => new Element('Sb', 51, 1, 120.903824),
            'Te' => new Element('Te', 52, 1, 129.906219),
            'I' => new Element('I', 53, 1, 126.904457),
            'i' => new Element('I', 53, 0, 126.904457, true),
            'Xe' => new Element('Xe', 54, 1, 131.904160),
            'Cs' => new Element('Cs', 55, 1, 132.905441),
            'Ba' => new Element('Ba', 56, 1, 137.905243),
            'La' => new Element('La', 57, 1, 138.906342),
            'Ce' => new Element('Ce', 58, 1, 139.905441),
            'Pr' => new Element('Pr', 59, 1, 140.907593),
            'Nd' => new Element('Nd', 60, 1, 141.907700),
            'Pm' => new Element('Pm', 61, 1, 144.912750),
            'Sm' => new Element('Sm', 62, 1, 151.919693),
            'Eu' => new Element('Eu', 63, 1, 152.921204),
            'Gd' => new Element('Gd', 64, 1, 157.924103),
            'Tb' => new Element('Tb', 65, 1, 158.925293),
            'Dy' => new Element('Dy', 66, 1, 163.929199),
            'Ho' => new Element('Ho', 67, 1, 164.930298),
            'Er' => new Element('Er', 68, 1, 165.930298),
            'Tm' => new Element('Tm', 69, 1, 168.934204),
            'Yb' => new Element('Yb', 70, 1, 173.938904),
            'Lu' => new Element('Lu', 71, 1, 174.940796),
            'Hf' => new Element('Hf', 72, 1, 179.946503),
            'Ta' => new Element('Ta', 73, 1, 180.947998),
            'W' => new Element('W', 74, 1, 183.950897),
            'Re' => new Element('Re', 75, 1, 186.955597),
            'Os' => new Element('Os', 76, 1, 191.961502),
            'Ir' => new Element('Ir', 77, 1, 192.962906),
            'Pt' => new Element('Pt', 78, 1, 194.964798),
            'Au' => new Element('Au', 79, 1, 196.966507),
            'Hg' => new Element('Hg', 80, 1, 201.970596),
            'Tl' => new Element('Tl', 81, 1, 204.974396),
            'Pb' => new Element('Pb', 82, 1, 207.976593),
            'Bi' => new Element('Bi', 83, 1, 208.980392),
            'Po' => new Element('Po', 84, 1, 208.982422),
            'At' => new Element('At', 85, 1, 209.987137),
            'Rn' => new Element('Rn', 86, 1, 222.017563),
            'Fr' => new Element('Fr', 87, 1, 209.996399),
            'Ra' => new Element('Ra', 88, 1, 226.025406),
            'Ac' => new Element('Ac', 89, 1, 227.027740),
            'Th' => new Element('Th', 90, 1, 232.038055),
            'Pa' => new Element('Pa', 91, 1, 231.035904),
            'U' => new Element('U', 92, 1, 238.050781),
            'Np' => new Element('Np', 93, 1, 237.048172),
            'Pu' => new Element('Pu', 94, 1, 242.058746),
            'Am' => new Element('Am', 95, 1, 243.061371),
            'Cm' => new Element('Cm', 96, 1, 247.070343),
            'Bk' => new Element('Bk', 97, 1, 247.070297),
            'Cf' => new Element('Cf', 98, 1, 251.079575),
            'Es' => new Element('Es', 99, 1, 252.082977),
            'Fm' => new Element('Fm', 100, 1, 257.095093),
            'Md' => new Element('Md', 101, 1, 258.098419),
            'No' => new Element('No', 102, 1, 259.101044),
            'Lr' => new Element('Lr', 103, 1, 262.109802)
        );
    }

}
