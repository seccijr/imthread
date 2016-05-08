<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DefaultControllerTest extends WebTestCase
{
    public function testIndexLayout()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $crawler = $client->request('GET', '/');

        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Posts: ', $crawler->filter('#posts-count')->text());
        $this->assertContains('Views: ', $crawler->filter('#views-count')->text());
        $this->assertContains('Export', $crawler->filter('#export-button')->text());
        $this->assertContains('Select an image file', $crawler->filter('#image-upload')->text());
        $this->assertEquals(1, $crawler->filter('#image-title-edit input[type=text]')->count());
        $this->assertContains('Upload Image', $crawler->filter('#upload')->text());
    }

    public function testPostCorrectImageWithTitle()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('post[upload]')->form();
        $form['post[title]'] = 'Sydney Opera House';
        $form['post[file]'] = realpath(__DIR__ . '/assets/600x450.jpg');
        $crawler = $client->submit($form);

        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Successfuly Saved Image', $crawler->filter('.alert.alert-success')->text());
        $this->assertContains('Sydney Opera House', $crawler->filter('.row.post h2')->text());
    }

    public function testPostCorrectImageWithoutTitle()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('post[upload]')->form();
        $form['post[file]'] = realpath(__DIR__ . '/assets/600x450.jpg');
        $crawler = $client->submit($form);

        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Successfuly Saved Image', $crawler->filter('.alert.alert-success')->text());
        $this->assertEmpty(trim($crawler->filter('.row.post h2')->text()));
    }

    public function testPostToBigDimensionsImage()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('post[upload]')->form();
        $form['post[file]'] = realpath(__DIR__ . '/assets/1920x1920.jpg');
        $crawler = $client->submit($form);

        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('The image height is too big', $crawler->filter('.alert.alert-danger')->text());
    }

    public function testPostToBigImage()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('post[upload]')->form();
        $form['post[file]'] = realpath(__DIR__ . '/assets/20MB.jpg');
        $crawler = $client->submit($form);

        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('The file is too large', $crawler->filter('.alert.alert-danger')->text());
    }

    public function testBadFormatImage()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('post[upload]')->form();
        $form['post[file]'] = realpath(__DIR__ . '/assets/icon.svg');
        $crawler = $client->submit($form);

        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('The mime type of the file is invalid', $crawler->filter('.alert.alert-danger')->text());
    }

    public function testJpgFormatImage()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('post[upload]')->form();
        $form['post[file]'] = realpath(__DIR__ . '/assets/hello.jpg');
        $crawler = $client->submit($form);

        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Successfuly Saved Image', $crawler->filter('.alert.alert-success')->text());
    }

    public function testPngFormatImage()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('post[upload]')->form();
        $form['post[file]'] = realpath(__DIR__ . '/assets/hello.png');
        $crawler = $client->submit($form);

        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Successfuly Saved Image', $crawler->filter('.alert.alert-success')->text());
    }

    public function testGifFormatImage()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('post[upload]')->form();
        $form['post[file]'] = realpath(__DIR__ . '/assets/hello.gif');
        $crawler = $client->submit($form);

        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Successfuly Saved Image', $crawler->filter('.alert.alert-success')->text());
    }

    public function testExportCSV()
    {
        // Arrange
        $client = static::createClient();

        // Act
        $client->request('GET', '/export');

        // Assert
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('text/csv', $client->getResponse()->headers->get('Content-Type'));
        $this->assertContains('images.csv', $client->getResponse()->headers->get('Content-Disposition'));
    }
}
