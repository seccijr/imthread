# Image Thread Demo

Image Thread Demo is an Instagram like application that allows you to submit image posts and checkout them later on. Only one thread is visible and each post must have an image. Some characteristics for this application are thw following:

  - For each image posted, the image lists grouws downwards, sticking the most recent image ate the top of the thread list
  - Image format support are JPG, PNG and GIF
  - Image dimensions should be up to 1920 pixels width and 1080 hight
  - Image size must be up to 20 MB
  - It is allowed to submit an image post with an empty title
  - CSV report is gathered cliking on the "Export" button.

This application is currently deployed in a [Heroku application] called imthread. It will be available each day in a 18 hours period of time starting at 6.00 GMT +1 to 00.00 GMT +1.

### Version
0.1.0

### Installation

Image Thread requires [Composer](https://getcomposer.org/) and [Bower](http://bower.io/) to run.

```sh
$ git clone [git-repo-url] imthread
$ cd imthread
$ bower install
$ export SYMFONY_ENV=prod && composer install
$ php bin/console doctrine:database:create
$ php bin/console server:start
```

License
----

MIT

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen. Thanks SO - http://stackoverflow.com/questions/4823468/store-comments-in-markdown-syntax)


   [Heroku application]: <https://imthread.herokuapp.com/>
   [git-repo-url]: <https://github.com/seccijr/imthread.git>
