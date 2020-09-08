<?php 
class InitStatusTest extends \Codeception\Test\Unit
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
        $this->tester->amGoingTo("init Status instance");

        $statusID = 1;
        $testTitle = "Foobar";
        $status = new \Gumeniukcom\ToDo\Status\Status($statusID, $testTitle);

        $this->tester->expect("Status id and title equals init value");

        $this->assertEquals($statusID, $status->getId(), "id equals");
        $this->assertEquals($testTitle, $status->getTitle(), "title equals");

        $this->tester->comment("all ok");
    }
}