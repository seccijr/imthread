<?php

namespace AppBundle\Service;

use AppBundle\Entity\Post;
use Doctrine\Common\Persistence\ObjectManager;

class ForumService
{
    private $om;
    private $paramfactory;
    private $util;
    private $uploaddir;
    
    public function __construct(
        ObjectManager $om, 
        ParamFactory $paramfactory, 
        UtilService $util,
        $uploaddir
    )
    {
        $this->om = $om;
        $this->paramfactory = $paramfactory;
        $this->util = $util;
        $this->uploaddir = $this->util->realpath($uploaddir);
    }
    
    public function uploadPost(Post $post)
    {
        if (null === $post->getFile()) {
            return;
        }
        $name =  $this->util->generateFileName($post);
        $post->setName($name);
        $this->om->persist($post);
        $this->om->flush();
        $post->getFile()->move($this->uploaddir, $name);
    }

    public function getAllPosts()
    {
        return $this->om->getRepository('AppBundle:Post')->getAllOrdered();
    }

    public function getStats()
    {
        $postnumber = $this->om->getRepository('AppBundle:Post')->getTotalCount();
        $viewsnumber = $this->om->getRepository('AppBundle:Param')->findOneBy([
            'name' => 'views'
        ])->getValue();

        return ['postsNumber' => $postnumber, 'viewsNumber' => $viewsnumber];
    }
    
    public function increaseViews()
    {
        $viewsnumber = $this->om->getRepository('AppBundle:Param')->findOneBy([
            'name' => 'views'
        ]);
        
        if ($viewsnumber === null) {
            $viewsnumber = $this->paramfactory->create();
            $viewsnumber->setName('views');
            $viewsnumber->setValue(0);
        }
        
        $incremented = $viewsnumber->getValue() + 1;
        $viewsnumber->setValue($incremented);
        $this->om->persist($viewsnumber);
        $this->om->flush();
    }
}
