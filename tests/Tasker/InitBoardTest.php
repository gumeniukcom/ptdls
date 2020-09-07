<?php 
class InitBoardTest extends \Codeception\Test\Unit
{
    /**
     * @var \TaskerTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testSomeFeature()
    {
        $this->tester->am("System");
        $this->tester->amGoingTo("init Board instance");

        $testID = 1;
        $testTitle = "Foobar";
        $board = new \Gumeniukcom\Tasker\Objects\Board($testID, $testTitle);

        $this->tester->expect("Board id and title equals init value");

        $this->assertEquals($testID, $board->getId(), "id equals");
        $this->assertEquals($testTitle, $board->getTitle(), "title equals");

        $this->tester->comment("all ok");
    }
}