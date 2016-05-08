<?php

namespace AppBundle\Service;

use AppBundle\Entity\Param;

class ParamFactory
{
    /**
     * Factory method for Param entity 
     * 
     * @return Param
     */
    public function create()
    {
       return new Param(); 
    }
}
