<?php

use Psr\Log\NullLogger;
use \Psr\Log\LoggerInterface;

class TaskerTest extends \Codeception\Test\Unit
{
    /**
     * @var \TaskerTester
     */
    protected $tester;

    protected LoggerInterface $logger;
    
    protected function _before()
    {
        $this->logger = new NullLogger();
    }

    protected function _after()
    {
    }

    // tests
    public function testSomeFeature()
    {
//        $this->make('User', ['name' => 'davert']);
//        $tasker = new Gumeniukcom\Tasker\Tasker($this->logger);

    }
}