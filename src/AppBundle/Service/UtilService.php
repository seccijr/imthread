<?php

namespace AppBundle\Service;


use AppBundle\Entity\Post;

class UtilService
{
    public function realpath($pat)
    {
        return realpath($pat);
    }
    
    public function generateFileName(Post $post)
    {
        return md5($post->getTitle() . uniqid()) . '.' . $post->getFile()->guessExtension();
    }
}