<?php
namespace RescueTimeStatusboard;

use \Silex\Application as Application;
use \Symfony\Component\HttpFoundation\Request as Request;
use \Symfony\Component\HttpFoundation\Response as Response;

class RescueTimeBoards
{

    public $Client;
    private $RescueTimeHelper;

    public function __construct($apiKey)
    {
        $this->Client = new \RescueTime\Client($apiKey);
        $this->RescueTimeHelper = new RescueTimeHelper();
    }

    public function summary(Request $request, Application $app)
    {
        $activities = $this->Client->getActivities(
            "interval",
            "day",
            null,
            null,
            new \DateTime("-6 day"),
            new \DateTime("today")
        );
        $totalTime = $this->RescueTimeHelper->calculateTotalTime($activities);
        $productivity = $this->RescueTimeHelper->calculateProductivity($activities);
        $dailyAverage = $this->RescueTimeHelper->calculateDailyHoursAverage($activities, 7);
        $productivityByDay = $this->RescueTimeHelper->calculateProductivityByDay($activities);
        $maxs = array_keys($productivityByDay, max($productivityByDay));
        $summaryData = array(
            array("RescueTime Summary", $this->RescueTimeHelper->getStartDate($activities)->format("M d") . " - " . $this->RescueTimeHelper->getEndDate($activities)->format("M d, Y")),
            array("Total Time", $totalTime),
            array("Avg / Day", $dailyAverage),
            array("Productivity", "{$productivity}%"),
            array("Most Productive", $maxs[0])
        );
        $csvOutput = $this->RescueTimeHelper->arrayToCsv($summaryData);
        return new Response(
            $csvOutput,
            200,
            array(
                'Content-Encoding' => 'UTF-8',
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename=overview.csv',
                'Pragma' => 'no-cache',
                'Cache-Control' => '',
                'Expires' => '0'
            )
        );
    }

    public function productivity_by_activity(Request $request, Application $app)
    {
        $activities = $this->Client->getActivities(
            "interval",
            "day",
            null,
            null,
            new \DateTime("-6 day"),
            new \DateTime("today"),
            "activity"
        );

        $timeByActivity = $this->RescueTimeHelper->calculateTimeByActivity($activities, 6);
        $datapoints = array();

        foreach ($timeByActivity as $activity => $seconds) {
            $datapoints[] = array(
                'title' => substr($activity, 0, 14),
                'value' => round($seconds / 60 / 60, 2)
            );
        }
        $graph = array(
            'graph' => array(
                'title' => 'Top Activities',
                'total' => true,
                'type' => 'bar',
                'yAxis' => array(
                    'units' => array('suffix' => 'h')
                ),
                'xAxis' => array('showEveryLabel' => true),
                'datasequences' => array(
                    array(
                        'title' => 'RescueTime',
                        'datapoints' => $datapoints
                    )
                )
            )
        );
        return $app->json($graph);
    }

    public function productivity_by_category(Request $request, Application $app)
    {
        $activities = $this->Client->getActivities(
            "interval",
            "day",
            null,
            null,
            new \DateTime("-6 day"),
            new \DateTime("today"),
            "category"
        );

        $timeByCategory = $this->RescueTimeHelper->calculateTimeByCategory($activities);

        $datapoints = array();
        foreach ($timeByCategory as $category => $seconds) {
            $datapoints[] = array(
                'title' => substr($category, 0, 14),
                'value' => round($seconds / 60 / 60, 2)
            );
        }
        $graph = array(
            'graph' => array(
                'title' => 'Top Categories',
                'total' => true,
                'type' => 'bar',
                'yAxis' => array(
                    'units' => array('suffix' => 'h')
                ),
                'xAxis' => array('showEveryLabel' => true),
                'datasequences' => array(
                    array(
                        'title' => 'RescueTime',
                        'datapoints' => $datapoints
                    )
                )
            )
        );
        return $app->json($graph);
    }

    public function productivity_by_day(Request $request, Application $app)
    {
        $activities = $this->Client->getActivities(
            "interval",
            "day",
            null,
            null,
            new \DateTime("-6 day"),
            new \DateTime("today")
        );

        $timeByDay = $this->RescueTimeHelper->calculateTimeByProductivityCategoryByDay($activities);

        $datasequences = array();
        foreach ($timeByDay as $category => $dates) {
            $datapoints = array();
            foreach ($dates as $date => $timeSpentInSeconds) {
                $datapoints[] = array(
                    'title' => $date,
                    'value' => round($timeSpentInSeconds / 60 / 60, 2)
                );
            }
            $datasequences[] = array(
                'title' => ucwords($category),
                'datapoints' => $datapoints
            );
        }
        $graph = array(
            'graph' => array(
                'title' => 'RescueTime Productivity By Day',
                'total' => true,
                'type' => 'line',
                'yAxis' => array(
                    'units' => array('suffix' => 'h')
                ),
                'xAxis' => array('showEveryLabel' => true),
                'datasequences' => $datasequences
            )
        );
        return $app->json($graph);
    }
}
