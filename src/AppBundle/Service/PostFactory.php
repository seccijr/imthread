<?php

namespace AppBundle\Service;

use AppBundle\Entity\Post;

class PostFactory
{
    /**
     * Factory method for Post entity
     * 
     * @return Post
     */
    public function create()
    {
       return new Post(); 
    }
}
