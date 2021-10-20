<?php



namespace Overtrue\EasySms\Contracts;


interface PhoneNumberInterface extends \JsonSerializable
{
    
    public function getIDDCode();

    
    public function getNumber();

    
    public function getUniversalNumber();

    
    public function getZeroPrefixedNumber();

    
    public function __toString();
}
