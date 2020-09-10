<?php

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class BoardInMemoryTest extends \Codeception\Test\Unit
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
        $storage = new \Gumeniukcom\ToDo\Board\BoardInMemoryStorage($this->logger);

        $this->tester->am("System");

        $title1 = 'title';
        $title2 = 'title2';

        $board1 = $storage->new($title1);

        $this->assertEquals(1, $board1->getId(), "id1");

        $this->assertEquals($title1, $board1->getTitle(), "title1");


        $board2 = $storage->new($title2);

        $this->assertEquals(2, $board2->getId(), "id2");

        $this->assertEquals($title2, $board2->getTitle(), "title2");

        $board_load_1 = $storage->load(1);

        $this->assertNotNull($board_load_1, 'should not be bull');

        $this->assertEquals(1, $board_load_1->getId(), "id1");

        $this->assertEquals($title1, $board_load_1->getTitle(), "title1");


        $ok = $storage->delete($board_load_1);
        $this->assertTrue($ok, 'delete ok');

        $notok = $storage->delete($board_load_1);
        $this->assertFalse($notok, 'delete deleted not ok');

        $board_load_after_delete_1 = $storage->load(1);

        $this->assertNull($board_load_after_delete_1);



    }
}