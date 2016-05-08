<?php

namespace AppBundle\Service;

use AppBundle\Entity\Post;

class PostFactory
{
    public function create()
    {
       return new Post(); 
    }
}
