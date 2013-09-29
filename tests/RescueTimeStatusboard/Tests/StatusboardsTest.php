<?php
namespace RescueTimeStatusboard\Tests;

use Symfony\Component\HttpFoundation\Request as Request;
use Silex\Application as Application;

class StatusboardsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Silex app
     * @var Silex\Application
     */
    private $app;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->app = new Application();
        $this->app->register(new \Igorw\Silex\ConfigServiceProvider(__DIR__ . "/../../../config.json"));

        $this->RescueTimeBoards = new \RescueTimeStatusboard\RescueTimeBoards($this->app['rescuetime-api-key']);

        $data = file_get_contents(__DIR__ . '/Fakes/activities.txt');
        $activities = unserialize($data);

        $RescueTimeClient = $this->getMockBuilder('\RescueTime\Client')
            ->setConstructorArgs(array('apiKey' => 'secret'))
            ->setMethods(array('getActivities'))
            ->getMock();
        $RescueTimeClient->expects($this->once())
            ->method('getActivities')
            ->will($this->returnValue($activities));

        $this->RescueTimeBoards->Client = $RescueTimeClient;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->RescueTimeBoards);
        unset($this->app);
    }

    public function testSummary()
    {
        $response = $this->RescueTimeBoards->summary(new Request(), $this->app);
        $output = $response->getContent();

        $expectedOutput = file_get_contents(__DIR__ . '/Fakes/summary.csv');

        $this->assertEquals($expectedOutput, $output);
    }

    public function testProductivityByActivity()
    {
        $response = $this->RescueTimeBoards->productivity_by_activity(new Request(), $this->app);
        $output = $response->getContent();

        $expectedOutput = file_get_contents(__DIR__ . '/Fakes/productivity_by_activity.json');

        $this->assertEquals($expectedOutput, $output);
    }

    public function testProductivityByCategory()
    {
        $response = $this->RescueTimeBoards->productivity_by_category(new Request(), $this->app);
        $output = $response->getContent();

        $expectedOutput = file_get_contents(__DIR__ . '/Fakes/productivity_by_category.json');

        $this->assertEquals($expectedOutput, $output);
    }

    public function testProductivityByDay()
    {
        $response = $this->RescueTimeBoards->productivity_by_day(new Request(), $this->app);
        $output = $response->getContent();

        $expectedOutput = file_get_contents(__DIR__ . '/Fakes/productivity_by_day.json');

        $this->assertEquals($expectedOutput, $output);
    }
}
