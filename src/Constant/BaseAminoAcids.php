<?php

namespace App\Constant;

use App\Entity\Block;
use App\Entity\Container;
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
        $tryptophan->setName("Tryptophan");
        $tryptophan->setAcronym("Trp");
        $tryptophan->setResidue("C11H10N20");
        $tryptophan->setMass(186.07931300000001328);
        $tryptophan->setSmiles("C1=CC=C2C(=C1)C(=CN2)CC(C(=O)O)N");
        $tryptophan->setUsmiles("NC(CC1=CNC2=CC=CC=C12)C(O)=O");
        $tryptophan->setSource(SourceServer::PUBCHEM);
        $tryptophan->setIdentifier("6305");
        $tryptophan->setContainer($container);
        $this->list->add($tryptophan);

        $glycine = new Block();
        $glycine->setName("Glycine");
        $glycine->setAcronym("Gly");
        $glycine->setResidue("C2H3NO");
        $glycine->setMass(57.021464);
        $glycine->setSmiles("C(C(=O)O)N");
        $glycine->setUsmiles("NCC(O)=O");
        $glycine->setSource(SourceServer::PUBCHEM);
        $glycine->setIdentifier("750");
        $glycine->setContainer($container);
        $this->list->add($glycine);

        $alanine = new Block();
        $alanine->setName("Alanine");
        $alanine->setAcronym("Ala");
        $alanine->setResidue("C3H5NO");
        $alanine->setMass(71.037114);
        $alanine->setSmiles("CC(C(=O)O)N");
        $alanine->setUsmiles("CC(N)C(O)=O");
        $alanine->setSource(SourceServer::PUBCHEM);
        $alanine->setIdentifier("5950");
        $alanine->setContainer($container);
        $this->list->add($alanine);

        $serine = new Block();
        $serine->setName("Serine");
        $serine->setAcronym("Ser");
        $serine->setResidue("C3H5NO2");
        $serine->setMass(87.032028);
        $serine->setSmiles("C(C(C(=O)O)N)O");
        $serine->setUsmiles("NC(CO)C(O)=O");
        $serine->setSource(SourceServer::PUBCHEM);
        $serine->setIdentifier("5951");
        $serine->setContainer($container);
        $this->list->add($serine);

        $cysteine = new Block();
        $cysteine->setName("Cysteine");
        $cysteine->setAcronym("Cys");
        $cysteine->setResidue("C3H5NOS");
        $cysteine->setMass(103.009184);
        $cysteine->setSmiles("C(C(C(=O)O)N)S");
        $cysteine->setUsmiles("NC(CS)C(O)=O");
        $cysteine->setSource(SourceServer::PUBCHEM);
        $cysteine->setIdentifier("5862");
        $cysteine->setContainer($container);
        $this->list->add($cysteine);

        $asparatic = new Block();
        $asparatic->setName("Aspartic acid");
        $asparatic->setAcronym("Asp");
        $asparatic->setResidue("C4H5NO3");
        $asparatic->setMass(115.026943);
        $asparatic->setSmiles("C(C(C(=O)O)N)C(=O)O");
        $asparatic->setUsmiles("NC(CC(O)=O)C(O)=O");
        $asparatic->setSource(SourceServer::PUBCHEM);
        $asparatic->setIdentifier("5960");
        $asparatic->setContainer($container);
        $this->list->add($asparatic);

        $asparagine = new Block();
        $asparagine->setName("Asparagine");
        $asparagine->setAcronym("Asn");
        $asparagine->setResidue("C4H6N2O2");
        $asparagine->setMass(114.042927);
        $asparagine->setSmiles("C(C(C(=O)O)N)C(=O)N");
        $asparagine->setUsmiles("NC(CC(N)=O)C(O)=O");
        $asparagine->setSource(SourceServer::PUBCHEM);
        $asparagine->setIdentifier("6267");
        $asparagine->setContainer($container);
        $this->list->add($asparagine);

        $threonine = new Block();
        $threonine->setName("Threonine");
        $threonine->setAcronym("Thr");
        $threonine->setResidue("C4H7NO2");
        $threonine->setMass(101.047678);
        $threonine->setSmiles("CC(C(C(=O)O)N)O");
        $threonine->setUsmiles("CC(O)C(N)C(O)=O");
        $threonine->setSource(SourceServer::PUBCHEM);
        $threonine->setIdentifier("6288");
        $threonine->setContainer($container);
        $this->list->add($threonine);

        $proline = new Block();
        $proline->setName("Proline");
        $proline->setAcronym("Pro");
        $proline->setResidue("C5H7NO");
        $proline->setMass(97.052764);
        $proline->setSmiles("C1CC(NC1)C(=O)O");
        $proline->setUsmiles("OC(=O)C1CCCN1");
        $proline->setSource(SourceServer::PUBCHEM);
        $proline->setIdentifier("145742");
        $proline->setContainer($container);
        $this->list->add($proline);

        $glutamic = new Block();
        $glutamic->setName("Glutamic acid");
        $glutamic->setAcronym("Glu");
        $glutamic->setResidue("C5H7NO3");
        $glutamic->setMass(129.042593);
        $glutamic->setSmiles("C(CC(=O)O)C(C(=O)O)N");
        $glutamic->setUsmiles("NC(CCC(O)=O)C(O)=O");
        $glutamic->setSource(SourceServer::PUBCHEM);
        $glutamic->setIdentifier("33032");
        $glutamic->setContainer($container);
        $this->list->add($glutamic);

        $glutamine = new Block();
        $glutamine->setName("Glutamine");
        $glutamine->setAcronym("Gln");
        $glutamine->setResidue("C5H8N2O2");
        $glutamine->setMass(128.058578);
        $glutamine->setSmiles("C(CC(=O)N)C(C(=O)O)N");
        $glutamine->setUsmiles("NC(CCC(N)=O)C(O)=O");
        $glutamine->setSource(SourceServer::PUBCHEM);
        $glutamine->setIdentifier("5961");
        $glutamine->setContainer($container);
        $this->list->add($glutamine);

        $valine = new Block();
        $valine->setName("Valine");
        $valine->setAcronym("Val");
        $valine->setResidue("C5H9NO");
        $valine->setMass(99.068414);
        $valine->setSmiles("CC(C)C(C(=O)O)N");
        $valine->setUsmiles("CC(C)C(N)C(O)=O");
        $valine->setSource(SourceServer::PUBCHEM);
        $valine->setIdentifier("6287");
        $valine->setContainer($container);
        $this->list->add($valine);

        $methionine = new Block();
        $methionine->setName("Methionine");
        $methionine->setAcronym("Met");
        $methionine->setResidue("C5H9NOS");
        $methionine->setMass(131.040485);
        $methionine->setSmiles("CSCCC(C(=O)O)N");
        $methionine->setUsmiles("CSCCC(N)C(O)=O");
        $methionine->setSource(SourceServer::PUBCHEM);
        $methionine->setIdentifier("6137");
        $methionine->setContainer($container);
        $this->list->add($methionine);

        $leucine = new Block();
        $leucine->setName("Leucine");
        $leucine->setAcronym("Leu");
        $leucine->setResidue("C6H11NO");
        $leucine->setMass(113.084064);
        $leucine->setSmiles("CC(C)CC(C(=O)O)N");
        $leucine->setUsmiles("CC(C)CC(N)C(O)=O");
        $leucine->setSource(SourceServer::PUBCHEM);
        $leucine->setIdentifier("6106");
        $leucine->setContainer($container);
        $this->list->add($leucine);

        $isoLeucine = new Block();
        $isoLeucine->setName("Isoleucine");
        $isoLeucine->setAcronym("Ile");
        $isoLeucine->setResidue("C6H11NO");
        $isoLeucine->setMass(113.084064);
        $isoLeucine->setSmiles("CCC(C)C(C(=O)O)N");
        $isoLeucine->setUsmiles("CCC(C)C(N)C(O)=O");
        $isoLeucine->setSource(SourceServer::PUBCHEM);
        $isoLeucine->setIdentifier("6306");
        $isoLeucine->setContainer($container);
        $this->list->add($isoLeucine);

        $lysine = new Block();
        $lysine->setName("Lysine");
        $lysine->setAcronym("Lys");
        $lysine->setResidue("C6H12N2O");
        $lysine->setMass(128.094963);
        $lysine->setSmiles("C(CCN)CC(C(=O)O)N");
        $lysine->setUsmiles("NCCCCC(N)C(O)=O");
        $lysine->setSource(SourceServer::PUBCHEM);
        $lysine->setIdentifier("5962");
        $lysine->setContainer($container);
        $this->list->add($lysine);

        $arginine = new Block();
        $arginine->setName("Arginine");
        $arginine->setAcronym("Arg");
        $arginine->setResidue("C6H12N4O");
        $arginine->setMass(156.101111);
        $arginine->setSmiles("C(CC(C(=O)O)N)CN=C(N)N");
        $arginine->setUsmiles("NC(CCCN=C(N)N)C(O)=O");
        $arginine->setSource(SourceServer::PUBCHEM);
        $arginine->setIdentifier("6322");
        $arginine->setContainer($container);
        $this->list->add($arginine);

        $histidine = new Block();
        $histidine->setName("Histidine");
        $histidine->setAcronym("His");
        $histidine->setResidue("C6H7N3O");
        $histidine->setMass(137.058912);
        $histidine->setSmiles("C1=C(NC=N1)CC(C(=O)O)N");
        $histidine->setUsmiles("NC(CC1=CN=CN1)C(O)=O");
        $histidine->setSource(SourceServer::PUBCHEM);
        $histidine->setIdentifier("6274");
        $histidine->setContainer($container);
        $this->list->add($histidine);

        $phenylAlanine = new Block();
        $phenylAlanine->setName("Phenylalanine");
        $phenylAlanine->setAcronym("Phe");
        $phenylAlanine->setResidue("C9H9NO");
        $phenylAlanine->setMass(147.068414);
        $phenylAlanine->setSmiles("C1=CC=C(C=C1)CC(C(=O)O)N");
        $phenylAlanine->setUsmiles("NC(CC1=CC=CC=C1)C(O)=O");
        $phenylAlanine->setSource(SourceServer::PUBCHEM);
        $phenylAlanine->setIdentifier("6140");
        $phenylAlanine->setContainer($container);
        $this->list->add($phenylAlanine);

        $tyrosine = new Block();
        $tyrosine->setName("Tyrosine");
        $tyrosine->setAcronym("Tyr");
        $tyrosine->setResidue("C9H9NO2");
        $tyrosine->setMass(163.063329);
        $tyrosine->setSmiles("C1=CC(=CC=C1CC(C(=O)O)N)O");
        $tyrosine->setUsmiles("NC(CC1=CC=C(O)C=C1)C(O)=O");
        $tyrosine->setSource(SourceServer::PUBCHEM);
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
