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

    /**
     * Using the uploadPost method each post image
     * is persisted in disk at the upload defined 
     * directory using a random name composed by its title
     * hash and an unique id.
     * 
     * @param Post $post
     */
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

    /**
     * Proxy method to retrieve all ordered posts
     * hosted at the data source.
     * 
     * @return array
     */
    public function getAllPosts()
    {
        return $this->om->getRepository('AppBundle:Post')->getAllOrdered();
    }

    /**
     * Retrieves information about the number of posts hosted
     * in the forum and the number of views the image thread
     * has received.
     * 
     * @return array
     */
    public function getStats()
    {
        $postnumber = $this->om->getRepository('AppBundle:Post')->getTotalCount();
        $viewsnumber = $this->om->getRepository('AppBundle:Param')->findOneBy([
            'name' => 'views'
        ])->getValue();

        return ['postsNumber' => $postnumber, 'viewsNumber' => $viewsnumber];
    }

    /**
     * Increases the number of views for the forum thread. If there is
     * no current count of the number of views it sets the variable
     * to zero and starts adding visits.
     */
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
