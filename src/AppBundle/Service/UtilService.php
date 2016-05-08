<?php

namespace AppBundle\Service;


use AppBundle\Entity\Post;

class UtilService
{
    /**
     * Wrapper method for built-in php function realpath
     * 
     * @param $path
     * @return mixed
     */
    public function realpath($path)
    {
        return realpath($path);
    }

    /**
     * Aux method to create a random name using the title
     * value of a post entity, its extension and the built-in
     * php functions md5 and uniqid
     *
     * @param Post $post
     * @return mixed
     */
    public function generateFileName(Post $post)
    {
        return md5($post->getTitle() . uniqid()) . '.' . $post->getFile()->guessExtension();
    }
}