<?php

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class StatusInMemoryTest extends \Codeception\Test\Unit
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
        $storage = new \Gumeniukcom\ToDo\Status\StatusInMemoryStorage($this->logger);

        $this->tester->am("System");

        $title1 = 'title';
        $title2 = 'title2';

        $board = new Gumeniukcom\ToDo\Board\Board(1, 'new board');

        $status = $storage->New($title1, $board->getId());

        $this->assertEquals(1, $status->getId(), "id1");

        $this->assertEquals($title1, $status->getTitle(), "title1");


        $status22 = $storage->New($title2, $board->getId());

        $this->assertEquals(2, $status22->getId(), "id2");

        $this->assertEquals($title2, $status22->getTitle(), "title2");

        $status_load_1 = $storage->Load(1);

        $this->assertNotNull($status_load_1, 'should not be bull');

        $this->assertEquals(1, $status_load_1->getId(), "id1");

        $this->assertEquals($title1, $status_load_1->getTitle(), "title1");


        $ok = $storage->Delete($status_load_1);
        $this->assertTrue($ok, 'delete ok');

        $notok = $storage->Delete($status_load_1);
        $this->assertFalse($notok, 'delete deleted not ok');

        $status_load_after_delete_1 = $storage->Load(1);

        $this->assertNull($status_load_after_delete_1);
    }
}