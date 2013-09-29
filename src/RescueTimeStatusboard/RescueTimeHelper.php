<?php
namespace RescueTimeStatusboard;

class RescueTimeHelper
{

    public function calculateTotalTime($activities)
    {
        $totalTime = 0;
        foreach ($activities as $activity) {
            $totalTime += $activity->getTimeSpentSeconds();
        }
        return self::formatTime($totalTime);
    }

    public function calculateProductivity($activities)
    {
        $productiveTime = 0;
        $distractingTime = 0;
        $totalTime = 0;

        foreach ($activities as $activity) {
            $timeOnActivity = $activity->getTimeSpentSeconds();
            $totalTime += $timeOnActivity;

            if ($activity->getProductivityCode() > 0) {
                $productiveTime += $timeOnActivity;
            } elseif ($activity->getProductivityCode() < 0) {
                $distractingTime += $timeOnActivity;
            }
        }


        $timeScore = $productiveTime - $distractingTime + $totalTime;
        $productivity = intval($timeScore / $totalTime * 50);

        return $productivity;
    }

    public function calculateProductivityByDay($activities)
    {
        $activitiesByDay = [];
        foreach ($activities as $activity) {
            $date = $activity->getDate();
            $monthDay = date("m/d", strtotime($date));
            if (!array_key_exists($monthDay, $activitiesByDay)) {
                $activitiesByDay[$monthDay] = [];
            }
            $activitiesByDay[$monthDay][] = $activity;
        }

        $productivityByDay = [];
        foreach ($activitiesByDay as $dayMonth => $activities) {
            $productivityByDay[$dayMonth] = self::calculateProductivity($activities);
        }
        return $productivityByDay;
    }

    public function calculateDailyHoursAverage($activities, $days)
    {
        $totalTime = 0;
        foreach ($activities as $activity) {
            $totalTime += $activity->getTimeSpentSeconds();
        }
        $averageTime = $totalTime / $days;
        return self::formatTime($averageTime);
    }

    public function formatTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        return "{$hours}h {$minutes}m";
    }

    public function calculateTimeByActivity($activities, $top = 6)
    {
        $timeByActivity = [];
        foreach ($activities as $activity) {
            $activityName = $activity->getActivityName();
            if (!array_key_exists($activityName, $timeByActivity)) {
                $timeByActivity[$activityName] = 0;
            }
            $timeByActivity[$activityName] += $activity->getTimeSpentSeconds();
        }
        arsort($timeByActivity);
        if (!$top) {
            return $timeByActivity;
        }
        return array_slice($timeByActivity, 0, $top);
    }

    public function calculateTimeByCategory($activities, $top = 6)
    {
        $timeByCategory = [];
        foreach ($activities as $activity) {
            $category = $activity->getActivityCategory();
            if (!array_key_exists($category, $timeByCategory)) {
                $timeByCategory[$category] = 0;
            }
            $timeByCategory[$category] += $activity->getTimeSpentSeconds();
        }
        arsort($timeByCategory);
        if (!$top) {
            return $timeByCategory;
        }
        return array_slice($timeByCategory, 0, $top);
    }

    public function calculateTimeByProductivityCategoryByDay($activities)
    {
        $productivityArray = [];

        if (empty($activities)) {
            return $productivityArray;
        }


        $current = $this->getStartDate($activities)->getTimestamp();
        $endDate = $this->getEndDate($activities)->getTimestamp();
        while ($current <= $endDate) {
            $productivityArray[date('m/d', $current)] = 0;
            $current = strtotime('+1 day', $current);
        }

        $timeSpentByDay = [
            'very productive' => $productivityArray,
            'productive' => $productivityArray,
            'neutral' => $productivityArray,
            'distracting' => $productivityArray,
            'very distracting' => $productivityArray
        ];
        foreach ($activities as $activity) {
            $date = $activity->getDate();
            $produtiviy = $activity->getProductivity();
            $monthDay = date("m/d", strtotime($date));
            $timeSpentByDay[$produtiviy][$monthDay] += $activity->getTimeSpentSeconds();
        }
        return $timeSpentByDay;
    }

    public function getStartDate($activities)
    {
        $startDate = null;
        foreach ($activities as $activity) {
            $date = new \DateTime($activity->getDate());
            if ($startDate === null) {
                $startDate = $date;
            } elseif ($date < $startDate) {
                $startDate = $date;
            }
        }
        return $startDate;
    }

    public function getEndDate($activities)
    {
        $endDate = null;
        foreach ($activities as $activity) {
            $date = new \DateTime($activity->getDate());
            if ($endDate === null) {
                $endDate = $date;
            } elseif ($date > $endDate) {
                $endDate = $date;
            }
        }
        return $endDate;
    }

    public function arrayToCsv($data)
    {
        $stream = fopen('php://memory', 'r+');
        foreach ($data as $row) {
            fputcsv($stream, $row, ',');
        }
        rewind($stream);
        $output = stream_get_contents($stream);
        fclose($stream);
        return $output;
    }
}
