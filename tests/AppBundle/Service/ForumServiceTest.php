<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Param;
use AppBundle\Entity\Post;
use AppBundle\Repository\ParamRepository;
use AppBundle\Repository\PostRepository;
use AppBundle\Service\FileNameFactory;
use AppBundle\Service\ForumService;
use AppBundle\Service\ParamFactory;
use AppBundle\Service\UtilService;
use Doctrine\Common\Persistence\ObjectManager;

class CalculatorTest extends \PHPUnit_Framework_TestCase
{
    public function testUploadPost()
    {
        // Arrange
        $uploaddir = 'UPLOADDIR';
        $name = 'NAME.EXTENSION';
        $file = $this->getMockBuilder('stdClass')
            ->setMethods(['move'])->getMock();
        $file->expects($this->once())
            ->method('move')
            ->with(
                $this->equalTo($uploaddir),
                $this->equalTo($name)
            );
        $post = $this->getMock(Post::class);
        $post->expects($this->exactly(2))
            ->method('getFile')
            ->will($this->returnValue($file));
        $post->expects($this->once())
            ->method('setName')
            ->with($this->equalTo($name));
        $util = $this->getMock(UtilService::class);
        $util->expects($this->once())
            ->method('generateFileName')
            ->with($this->equalTo($post))
            ->will($this->returnValue($name));
        $util->expects($this->once())
            ->method('realpath')
            ->will($this->returnArgument(0));
        $entitymanager = $this->getMock(ObjectManager::class);
        $entitymanager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($post));
        $entitymanager->expects($this->once())
            ->method('flush');
        $paramfactory = $this->getMock(ParamFactory::class);
        $forumservice = new ForumService(
            $entitymanager,
            $paramfactory,
            $util,
            $uploaddir
        );

        // Act
        // Assert
        $forumservice->uploadPost($post);
    }

    public function testGetAllPosts()
    {
        // Arrange

        $dbresult = ['1' => 'post1', '2' => 'post2'];
        $uploaddir = 'UPLOADDIR';
        $postrepository = $this
            ->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $postrepository->expects($this->once())
            ->method('getAllOrdered')
            ->will($this->returnValue($dbresult));
        $util = $this->getMock(UtilService::class);
        $util->expects($this->once())
            ->method('realpath')
            ->will($this->returnArgument(0));
        $entitymanager = $this->getMock(ObjectManager::class);
        $entitymanager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AppBundle:Post'))
            ->will($this->returnValue($postrepository));
        $paramfactory = $this->getMock(ParamFactory::class);
        $forumservice = new ForumService(
            $entitymanager,
            $paramfactory,
            $util,
            $uploaddir
        );

        // Act
        $result = $forumservice->getAllPosts();

        // Assert
        $this->assertEquals($dbresult, $result);
    }

    public function testGetStats()
    {
        // Arrange

        $postnumber = 100;
        $viewsnumber = 300;
        $uploaddir = 'UPLOADDIR';
        $param = $this->getMock(Param::class);
        $param->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($viewsnumber));
        $postrepository = $this
            ->getMockBuilder(PostRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $postrepository->expects($this->once())
            ->method('getTotalCount')
            ->will($this->returnValue($postnumber));
        $paramrepository = $this
            ->getMockBuilder(ParamRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $paramrepository->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($param));
        $util = $this->getMock(UtilService::class);
        $util->expects($this->once())
            ->method('realpath')
            ->will($this->returnArgument(0));
        $entitymanager = $this->getMock(ObjectManager::class);
        $entitymanager->expects($this->at(0))
            ->method('getRepository')
            ->with($this->equalTo('AppBundle:Post'))
            ->will($this->returnValue($postrepository));
        $entitymanager->expects($this->at(1))
            ->method('getRepository')
            ->with($this->equalTo('AppBundle:Param'))
            ->will($this->returnValue($paramrepository));
        $paramfactory = $this->getMock(ParamFactory::class);
        $forumservice = new ForumService(
            $entitymanager,
            $paramfactory,
            $util,
            $uploaddir
        );

        // Act
        $result = $forumservice->getStats();

        // Assert
        $this->assertEquals([
            'postsNumber' => $postnumber,
            'viewsNumber' => $viewsnumber
        ], $result);
    }

    public function testIncreaseViewsWithPopulatedDb()
    {
        // Arrange

        $viewsnumber = 300;
        $uploaddir = 'UPLOADDIR';
        $param = $this->getMock(Param::class);
        $param->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($viewsnumber));
        $param->expects($this->once())
            ->method('setValue')
            ->with($this->equalTo($viewsnumber + 1));
        $paramrepository = $this
            ->getMockBuilder(ParamRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $paramrepository->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue($param));
        $util = $this->getMock(UtilService::class);
        $util->expects($this->once())
            ->method('realpath')
            ->will($this->returnArgument(0));
        $entitymanager = $this->getMock(ObjectManager::class);
        $entitymanager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AppBundle:Param'))
            ->will($this->returnValue($paramrepository));
        $entitymanager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($param));
        $entitymanager->expects($this->once())
            ->method('flush');
        $paramfactory = $this->getMock(ParamFactory::class);
        $forumservice = new ForumService(
            $entitymanager,
            $paramfactory,
            $util,
            $uploaddir
        );

        // Act
        // Assert
        $forumservice->increaseViews();
    }

    public function testIncreaseViewsWithEmptyDb()
    {
        // Arrange

        $uploaddir = 'UPLOADDIR';
        $paramrepository = $this
            ->getMockBuilder(ParamRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $paramrepository->expects($this->once())
            ->method('findOneBy')
            ->will($this->returnValue(null));

        $param = $this->getMock(Param::class);
        $param->expects($this->once())
            ->method('setName')
            ->with($this->equalTo('views'));
        $param->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(0));
        $param->expects($this->exactly(2))
            ->method('setValue')
            ->withConsecutive(
                $this->equalTo(0),
                $this->equalTo(1)
            );

        $paramfactory = $this->getMock(ParamFactory::class);
        $paramfactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($param));

        $util = $this->getMock(UtilService::class);
        $util->expects($this->once())
            ->method('realpath')
            ->will($this->returnArgument(0));
        $entitymanager = $this->getMock(ObjectManager::class);
        $entitymanager->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('AppBundle:Param'))
            ->will($this->returnValue($paramrepository));
        $entitymanager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($param));
        $entitymanager->expects($this->once())
            ->method('flush');
        $forumservice = new ForumService(
            $entitymanager, 
            $paramfactory, 
            $util, 
            $uploaddir
        );

        // Act
        // Assert
        $forumservice->increaseViews();
    }
}