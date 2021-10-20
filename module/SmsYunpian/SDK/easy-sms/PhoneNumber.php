<?php



namespace Overtrue\EasySms;


class PhoneNumber implements \Overtrue\EasySms\Contracts\PhoneNumberInterface
{
    
    protected $number;

    
    protected $IDDCode;

    
    public function __construct($numberWithoutIDDCode, $IDDCode = null)
    {
        $this->number = $numberWithoutIDDCode;
        $this->IDDCode = $IDDCode ? intval(ltrim($IDDCode, '+0')) : null;
    }

    
    public function getIDDCode()
    {
        return $this->IDDCode;
    }

    
    public function getNumber()
    {
        return $this->number;
    }

    
    public function getUniversalNumber()
    {
        return $this->getPrefixedIDDCode('+').$this->number;
    }

    
    public function getZeroPrefixedNumber()
    {
        return $this->getPrefixedIDDCode('00').$this->number;
    }

    
    public function getPrefixedIDDCode($prefix)
    {
        return $this->IDDCode ? $prefix.$this->IDDCode : null;
    }

    
    public function __toString()
    {
        return $this->getUniversalNumber();
    }

    
    public function jsonSerialize()
    {
        return $this->getUniversalNumber();
    }
}
