<?php

namespace App\Constant;

use App\Entity\Block;
use App\Entity\Container;
use App\Enum\ServerEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class BaseAminoAcids {

    private $list;

    /**
     * BaseAminoAcids constructor - prepare base amino acids as blocks
     * @param Container $container
     */
    public function __construct(Container $container) {
        $this->list = new ArrayCollection();

        $tryptophan = new Block();
        $tryptophan->setBlockName("Tryptophan");
        $tryptophan->setAcronym("Trp");
        $tryptophan->setResidue("C11H10N20");
        $tryptophan->setBlockMass(186.07931300000001328);
        $tryptophan->setBlockSmiles("C1=CC=C2C(=C1)C(=CN2)CC(C(=O)O)N");
        $tryptophan->setUsmiles("NC(CC1=CNC2=CC=CC=C12)C(O)=O");
        $tryptophan->setSource(ServerEnum::PUBCHEM);
        $tryptophan->setIdentifier("6305");
        $tryptophan->setContainer($container);
        $this->list->add($tryptophan);

        $glycine = new Block();
        $glycine->setBlockName("Glycine");
        $glycine->setAcronym("Gly");
        $glycine->setResidue("C2H3NO");
        $glycine->setBlockMass(57.021464);
        $glycine->setBlockSmiles("C(C(=O)O)N");
        $glycine->setUsmiles("NCC(O)=O");
        $glycine->setSource(ServerEnum::PUBCHEM);
        $glycine->setIdentifier("750");
        $glycine->setContainer($container);
        $this->list->add($glycine);

        $alanine = new Block();
        $alanine->setBlockName("Alanine");
        $alanine->setAcronym("Ala");
        $alanine->setResidue("C3H5NO");
        $alanine->setBlockMass(71.037114);
        $alanine->setBlockSmiles("CC(C(=O)O)N");
        $alanine->setUsmiles("CC(N)C(O)=O");
        $alanine->setSource(ServerEnum::PUBCHEM);
        $alanine->setIdentifier("5950");
        $alanine->setContainer($container);
        $this->list->add($alanine);

        $serine = new Block();
        $serine->setBlockName("Serine");
        $serine->setAcronym("Ser");
        $serine->setResidue("C3H5NO2");
        $serine->setBlockMass(87.032028);
        $serine->setBlockSmiles("C(C(C(=O)O)N)O");
        $serine->setUsmiles("NC(CO)C(O)=O");
        $serine->setSource(ServerEnum::PUBCHEM);
        $serine->setIdentifier("5951");
        $serine->setContainer($container);
        $this->list->add($serine);

        $cysteine = new Block();
        $cysteine->setBlockName("Cysteine");
        $cysteine->setAcronym("Cys");
        $cysteine->setResidue("C3H5NOS");
        $cysteine->setBlockMass(103.009184);
        $cysteine->setBlockSmiles("C(C(C(=O)O)N)S");
        $cysteine->setUsmiles("NC(CS)C(O)=O");
        $cysteine->setSource(ServerEnum::PUBCHEM);
        $cysteine->setIdentifier("5862");
        $cysteine->setContainer($container);
        $this->list->add($cysteine);

        $asparatic = new Block();
        $asparatic->setBlockName("Aspartic acid");
        $asparatic->setAcronym("Asp");
        $asparatic->setResidue("C4H5NO3");
        $asparatic->setBlockMass(115.026943);
        $asparatic->setBlockSmiles("C(C(C(=O)O)N)C(=O)O");
        $asparatic->setUsmiles("NC(CC(O)=O)C(O)=O");
        $asparatic->setSource(ServerEnum::PUBCHEM);
        $asparatic->setIdentifier("5960");
        $asparatic->setContainer($container);
        $this->list->add($asparatic);

        $asparagine = new Block();
        $asparagine->setBlockName("Asparagine");
        $asparagine->setAcronym("Asn");
        $asparagine->setResidue("C4H6N2O2");
        $asparagine->setBlockMass(114.042927);
        $asparagine->setBlockSmiles("C(C(C(=O)O)N)C(=O)N");
        $asparagine->setUsmiles("NC(CC(N)=O)C(O)=O");
        $asparagine->setSource(ServerEnum::PUBCHEM);
        $asparagine->setIdentifier("6267");
        $asparagine->setContainer($container);
        $this->list->add($asparagine);

        $threonine = new Block();
        $threonine->setBlockName("Threonine");
        $threonine->setAcronym("Thr");
        $threonine->setResidue("C4H7NO2");
        $threonine->setBlockMass(101.047678);
        $threonine->setBlockSmiles("CC(C(C(=O)O)N)O");
        $threonine->setUsmiles("CC(O)C(N)C(O)=O");
        $threonine->setSource(ServerEnum::PUBCHEM);
        $threonine->setIdentifier("6288");
        $threonine->setContainer($container);
        $this->list->add($threonine);

        $proline = new Block();
        $proline->setBlockName("Proline");
        $proline->setAcronym("Pro");
        $proline->setResidue("C5H7NO");
        $proline->setBlockMass(97.052764);
        $proline->setBlockSmiles("C1CC(NC1)C(=O)O");
        $proline->setUsmiles("OC(=O)C1CCCN1");
        $proline->setSource(ServerEnum::PUBCHEM);
        $proline->setIdentifier("145742");
        $proline->setContainer($container);
        $this->list->add($proline);

        $glutamic = new Block();
        $glutamic->setBlockName("Glutamic acid");
        $glutamic->setAcronym("Glu");
        $glutamic->setResidue("C5H7NO3");
        $glutamic->setBlockMass(129.042593);
        $glutamic->setBlockSmiles("C(CC(=O)O)C(C(=O)O)N");
        $glutamic->setUsmiles("NC(CCC(O)=O)C(O)=O");
        $glutamic->setSource(ServerEnum::PUBCHEM);
        $glutamic->setIdentifier("33032");
        $glutamic->setContainer($container);
        $this->list->add($glutamic);

        $glutamine = new Block();
        $glutamine->setBlockName("Glutamine");
        $glutamine->setAcronym("Gln");
        $glutamine->setResidue("C5H8N2O2");
        $glutamine->setBlockMass(128.058578);
        $glutamine->setBlockSmiles("C(CC(=O)N)C(C(=O)O)N");
        $glutamine->setUsmiles("NC(CCC(N)=O)C(O)=O");
        $glutamine->setSource(ServerEnum::PUBCHEM);
        $glutamine->setIdentifier("5961");
        $glutamine->setContainer($container);
        $this->list->add($glutamine);

        $valine = new Block();
        $valine->setBlockName("Valine");
        $valine->setAcronym("Val");
        $valine->setResidue("C5H9NO");
        $valine->setBlockMass(99.068414);
        $valine->setBlockSmiles("CC(C)C(C(=O)O)N");
        $valine->setUsmiles("CC(C)C(N)C(O)=O");
        $valine->setSource(ServerEnum::PUBCHEM);
        $valine->setIdentifier("6287");
        $valine->setContainer($container);
        $this->list->add($valine);

        $methionine = new Block();
        $methionine->setBlockName("Methionine");
        $methionine->setAcronym("Met");
        $methionine->setResidue("C5H9NOS");
        $methionine->setBlockMass(131.040485);
        $methionine->setBlockSmiles("CSCCC(C(=O)O)N");
        $methionine->setUsmiles("CSCCC(N)C(O)=O");
        $methionine->setSource(ServerEnum::PUBCHEM);
        $methionine->setIdentifier("6137");
        $methionine->setContainer($container);
        $this->list->add($methionine);

        $leucine = new Block();
        $leucine->setBlockName("Leucine");
        $leucine->setAcronym("Leu");
        $leucine->setResidue("C6H11NO");
        $leucine->setBlockMass(113.084064);
        $leucine->setBlockSmiles("CC(C)CC(C(=O)O)N");
        $leucine->setUsmiles("CC(C)CC(N)C(O)=O");
        $leucine->setSource(ServerEnum::PUBCHEM);
        $leucine->setIdentifier("6106");
        $leucine->setContainer($container);
        $this->list->add($leucine);

        $isoLeucine = new Block();
        $isoLeucine->setBlockName("Isoleucine");
        $isoLeucine->setAcronym("Ile");
        $isoLeucine->setResidue("C6H11NO");
        $isoLeucine->setBlockMass(113.084064);
        $isoLeucine->setBlockSmiles("CCC(C)C(C(=O)O)N");
        $isoLeucine->setUsmiles("CCC(C)C(N)C(O)=O");
        $isoLeucine->setSource(ServerEnum::PUBCHEM);
        $isoLeucine->setIdentifier("6306");
        $isoLeucine->setContainer($container);
        $this->list->add($isoLeucine);

        $lysine = new Block();
        $lysine->setBlockName("Lysine");
        $lysine->setAcronym("Lys");
        $lysine->setResidue("C6H12N2O");
        $lysine->setBlockMass(128.094963);
        $lysine->setBlockSmiles("C(CCN)CC(C(=O)O)N");
        $lysine->setUsmiles("NCCCCC(N)C(O)=O");
        $lysine->setSource(ServerEnum::PUBCHEM);
        $lysine->setIdentifier("5962");
        $lysine->setContainer($container);
        $this->list->add($lysine);

        $arginine = new Block();
        $arginine->setBlockName("Arginine");
        $arginine->setAcronym("Arg");
        $arginine->setResidue("C6H12N4O");
        $arginine->setBlockMass(156.101111);
        $arginine->setBlockSmiles("C(CC(C(=O)O)N)CN=C(N)N");
        $arginine->setUsmiles("NC(CCCN=C(N)N)C(O)=O");
        $arginine->setSource(ServerEnum::PUBCHEM);
        $arginine->setIdentifier("6322");
        $arginine->setContainer($container);
        $this->list->add($arginine);

        $histidine = new Block();
        $histidine->setBlockName("Histidine");
        $histidine->setAcronym("His");
        $histidine->setResidue("C6H7N3O");
        $histidine->setBlockMass(137.058912);
        $histidine->setBlockSmiles("C1=C(NC=N1)CC(C(=O)O)N");
        $histidine->setUsmiles("NC(CC1=CN=CN1)C(O)=O");
        $histidine->setSource(ServerEnum::PUBCHEM);
        $histidine->setIdentifier("6274");
        $histidine->setContainer($container);
        $this->list->add($histidine);

        $phenylAlanine = new Block();
        $phenylAlanine->setBlockName("Phenylalanine");
        $phenylAlanine->setAcronym("Phe");
        $phenylAlanine->setResidue("C9H9NO");
        $phenylAlanine->setBlockMass(147.068414);
        $phenylAlanine->setBlockSmiles("C1=CC=C(C=C1)CC(C(=O)O)N");
        $phenylAlanine->setUsmiles("NC(CC1=CC=CC=C1)C(O)=O");
        $phenylAlanine->setSource(ServerEnum::PUBCHEM);
        $phenylAlanine->setIdentifier("6140");
        $phenylAlanine->setContainer($container);
        $this->list->add($phenylAlanine);

        $tyrosine = new Block();
        $tyrosine->setBlockName("Tyrosine");
        $tyrosine->setAcronym("Tyr");
        $tyrosine->setResidue("C9H9NO2");
        $tyrosine->setBlockMass(163.063329);
        $tyrosine->setBlockSmiles("C1=CC(=CC=C1CC(C(=O)O)N)O");
        $tyrosine->setUsmiles("NC(CC1=CC=C(O)C=C1)C(O)=O");
        $tyrosine->setSource(ServerEnum::PUBCHEM);
        $tyrosine->setIdentifier("6057");
        $tyrosine->setContainer($container);
        $this->list->add($tyrosine);

    }

    /***
     * Get list of base 20 amino acids blocks
     * @return Collection|Block[]
     */
    public function getList(): Collection {
        return $this->list;
    }

}
