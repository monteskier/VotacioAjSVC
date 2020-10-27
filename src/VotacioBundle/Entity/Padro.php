<?php

namespace VotacioBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Padro
 * @ORM\Table("Padro")
 * @ORM\Entity()
 */
class Padro
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
   
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $dni;

    /**
     * @ORM\Column(type="string", length=20,  nullable=true)
     */
    private $dataNeix;
    
    /**
     * @ORM\Column(type="integer",  nullable=true)
     */
    private $questionaris;
    
    /**
     * @ORM\Column(type="integer",  nullable=true)
     */
    private $codi;
    
    /**
     * @ORM\Column(type="integer",  nullable=true)
     */
    private $mobil;
    
    /**
     * @ORM\Column(type="integer",  nullable=true)
     */
    private $intents;

    /**
     * Get id
     *
     * @return int
     */
    
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dni
     *
     * @param string $dni
     *
     * @return Padro
     */
    public function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }
    /**
     * Set Codi
     *
     * @param integer $codi
     *
     * @return Padro
     */
        public function setCodi($codi){
        $this->codi = $codi;
    }
    
    /**
     * Set Mobil
     *
     * @param integer $mobil
     *
     * @return Padro
     */
    
    public function setMobil($mobil){
        $this->mobil = $mobil;
    }
    
    public function setIntents($intent){
        $this->intents = $intent;
    }

    /**
     * Get dni
     *
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set dataNeix
     *
     * @param \DateTime $dataNeix
     *
     * @return Padro
     */
    public function setDataNeix($dataNeix)
    {
        $this->dataNeix = $dataNeix;

        return $this;
    }

    /**
     * Get dataNeix
     *
     * @return Date
     */
    public function getDataNeix()
    {
        return $this->dataNeix;
    }
    /**
     * 
     * @return Padro
     */
    public function setQuestionaris($string){
     $this->questionaris = $string;
     return $this;
    }
    
   /**
    * @return string
    */
    public function getQuestionaris(){
     return $this->questionaris;
    }
    
    /**
     * @return integer
     */
    public function getCodi(){
        return $this->codi;
    }
    /**
     * @return integer
     */
    public function getMobil(){
        return $this->mobil;
    }
    /**
     * @return integer
     */
    public function getIntents(){
        return $this->intents;
    }

}
