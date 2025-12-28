<?php

define('APP_NAME', 'Events');
define('PAGE_TITLE', 'Calendar');
define('PAGE_SELECTED_SECTION', '');
define('PAGE_SELECTED_SUB_PAGE', '');

include('../templates/html_header.php');
include('../templates/nav_header.php');
include('../templates/nav_sidebar.php');
include('../templates/main_header.php');

include('../templates/message.php');

// Get month and year from URL or default to current
$month = isset($_GET['month']) && is_numeric($_GET['month']) ? intval($_GET['month']) : date('n');
$year = isset($_GET['year']) && is_numeric($_GET['year']) ? intval($_GET['year']) : date('Y');

// Validate month and year
if ($month < 1 || $month > 12) $month = date('n');
if ($year < 2000 || $year > 2100) $year = date('Y');

// Calculate first and last day of month
$first_day = mktime(0, 0, 0, $month, 1, $year);
$last_day = mktime(0, 0, 0, $month + 1, -0, $year);
$days_in_month = date('t', $first_day);
$day_of_week = date('w', $first_day); // 0 (Sunday) to 6 (Saturday)

// Get first and last event dates
$quey = 'SELECT MIN(starts_at) AS starts_at
    FROM events';
$result = mysqli_query($connect, $quey);
$record = mysqli_fetch_array($result);

$first_event = strtotime($record['starts_at']);

$quey = 'SELECT MAX(starts_at) AS starts_at
    FROM events';
$result = mysqli_query($connect, $quey);
$record = mysqli_fetch_array($result);

$last_event = strtotime($record['starts_at']);

// Calculate previous and next month
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}

// Get events for this month
$month_start = date('Y-m-01 00:00:00', $first_day);
$month_end = date('Y-m-t 23:59:59', $first_day);

$query = 'SELECT id, name, starts_at, ends_at, location, thumbnail
    FROM events
    WHERE (starts_at BETWEEN "'.$month_start.'" AND "'.$month_end.'")
       OR (ends_at BETWEEN "'.$month_start.'" AND "'.$month_end.'")
       OR (starts_at <= "'.$month_start.'" AND ends_at >= "'.$month_end.'")
    ORDER BY starts_at ASC';
$result = mysqli_query($connect, $query);

// Organize events by day
$events_by_day = array();
while ($record = mysqli_fetch_assoc($result)) 
{

    $start_day = date('j', strtotime($record['starts_at']));
    $end_day = date('j', strtotime($record['ends_at']));
    $start_month = date('n', strtotime($record['starts_at']));
    $end_month = date('n', strtotime($record['ends_at']));
    
    // Add event to each day it spans in this month
    for ($d = 1; $d <= $days_in_month; $d++) 
    {
        $current_date = date('Y-m-d', mktime(0, 0, 0, $month, $d, $year));
        $event_start = date('Y-m-d', strtotime($record['starts_at']));
        $event_end = date('Y-m-d', strtotime($record['ends_at']));
        
        if ($current_date >= $event_start && $current_date <= $event_end) 
        {
            if (!isset($events_by_day[$d])) $events_by_day[$d] = array();
            $events_by_day[$d][] = $record;
        }
    }
}

$month_name = date('F Y', $first_day);

?>

<main>
    
    <div class="w3-center">
        <h1>Event Calendar</h1>
        <a href="<?=ENV_DOMAIN?>/list">Upcoming Events</a> | <a href="<?=ENV_DOMAIN?>/calendar">Calendar View</a>
    </div>

    <hr>

    <!-- Calendar Navigation -->
    <div class="w3-bar w3-margin-bottom" style="display: flex; align-items: center;">
        <?php if($first_event < $first_day): ?>
            <a href="<?=ENV_DOMAIN?>/calendar/month/<?=$prev_month?>/year/<?=$prev_year?>" class="w3-button w3-white w3-border">
                <i class="fa-solid fa-chevron-left"></i> Previous
            </a>
        <?php endif; ?>
        
        <div style="flex: 1; text-align: center;">
            <h2 style="margin: 0;"><?=$month_name?></h2>
        </div>
        
        <?php if($last_event > $last_day): ?>
            <a href="<?=ENV_DOMAIN?>/calendar/month/<?=$next_month?>/year/<?=$next_year?>" class="w3-button w3-white w3-border">
                Next <i class="fa-solid fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>

    <!-- Calendar Grid -->
    <div class="w3-card w3-white">
        <header class="w3-container w3-purple">
            <h2><?=$month_name?></h2>
        </header>
        
        <div style="overflow-x: auto;">
            <table class="w3-table" style="table-layout: fixed; min-width: 800px;">
                <thead>
                    <tr class="w3-light-grey">
                        <th style="width: 14.28%; text-align: center;">Sunday</th>
                        <th style="width: 14.28%; text-align: center;">Monday</th>
                        <th style="width: 14.28%; text-align: center;">Tuesday</th>
                        <th style="width: 14.28%; text-align: center;">Wednesday</th>
                        <th style="width: 14.28%; text-align: center;">Thursday</th>
                        <th style="width: 14.28%; text-align: center;">Friday</th>
                        <th style="width: 14.28%; text-align: center;">Saturday</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $day_counter = 1;
                    $calendar_started = false;
                    
                    // Calendar rows (max 6 weeks)
                    for ($week = 0; $week < 6; $week++):
                        if ($day_counter > $days_in_month) break;
                    ?>
                        <tr>
                            <?php for ($dow = 0; $dow < 7; $dow++): ?>
                                <?php
                                $is_today = ($day_counter == date('j') && $month == date('n') && $year == date('Y'));
                                $cell_class = $is_today ? 'w3-light-grey' : '';
                                ?>
                                <td class="<?=$cell_class?>" style="vertical-align: top !important; height: 100px; padding: 5px; border: 1px solid #ddd;">
                                    <?php
                                    // Check if we should start printing days
                                    if ($week == 0 && $dow < $day_of_week) {
                                        // Empty cell before month starts
                                    } elseif ($day_counter <= $days_in_month) {
                                        // Print day number
                                        echo '<div style="font-weight: bold; margin-bottom: 3px;">'.$day_counter.'</div>';

                                        // Print events for this day
                                        if (isset($events_by_day[$day_counter])) {
                                            foreach ($events_by_day[$day_counter] as $event) {
                                                $thumb_url = $event['thumbnail'] ? $event['thumbnail'] : 'https://cdn.brickmmo.com/images@1.0.0/no-calendar.png';
                                                echo '<div style="font-size: 11px; margin: 2px 0; padding: 2px; background: #f8f8f8; display: flex; align-items: center; gap: 4px;">';
                                                echo '<img src="'.$thumb_url.'" style="width: 20px; height: 20px; object-fit: cover; flex-shrink: 0;">';
                                                echo '<a href="'.ENV_DOMAIN.'/details/'.$event['id'].'" style="text-decoration: none; color: #333; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">';
                                                echo htmlspecialchars(strlen($event['name']) > 18 ? substr($event['name'], 0, 18).'...' : $event['name']);
                                                echo '</a>';
                                                echo '</div>';
                                            }
                                        }

                                        $day_counter++;
                                    } else {
                                        // Empty cell after month ends
                                        echo '&nbsp;';
                                    }
                                    ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<?php

include('../templates/main_footer.php');
include('../templates/debug.php');
include('../templates/html_footer.php');