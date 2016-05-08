<?php

namespace AppBundle\Service;

use AppBundle\Entity\Param;

class ParamFactory
{
    /**
     * @return Param
     */
    public function create()
    {
       return new Param(); 
    }
}
