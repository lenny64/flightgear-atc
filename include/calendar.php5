<?php

// We open the calendar DIV, master
echo "<a name='calendar_anchor'> </a>";

for ($monthNumber = 0; $monthNumber < 2; $monthNumber++)
{
    $today = date('Y-m-d');
    $dayCursor = date('Y-m-d',strtotime('+'.$monthNumber.' month'));
    $firstDayCurrentMonth = date('Y-m-01',strtotime($dayCursor));
    $lastDayCurrentMonth = date('Y-m-t',strtotime($dayCursor));
    $totalDaysCurrentMonth = date('t',strtotime($dayCursor));

    ?>

    <table class='calendar'>
        <tr class="calendarHeader">
            <td colspan="7"><?php echo date('F',strtotime($dayCursor)); ?></td>
        </tr>
        <tr class="calendarHeader">
            <td>Mon</td>
            <td>Tue</td>
            <td>Wed</td>
            <td>Thu</td>
            <td>Fri</td>
            <td>Sat</td>
            <td>Sun</td>
        </tr>

    <?php

    // We initialize the day index at 0
    $dayCounter = $firstDayCurrentMonth;
    $firstWeek = TRUE;

    // We go through all the days in the month
    while ($dayCounter <= $lastDayCurrentMonth)
    {
        // We open the week on monday ?
        if (date('w',strtotime($dayCounter)) == 1) echo "<tr>";
        // If it's the first week
        if ($firstWeek == TRUE)
        {
            // We create as much TD as required
            for ($i = 1; $i < date('N',strtotime($dayCounter)); $i++)
            {
                echo "<td></td>";
            }
            // And of course we are not in the first week anymore
            $firstWeek = FALSE;
        }
        
        // We check if the date is selected. We will apply a different style.
        $isSelected = '';
        if (isset($_GET['date']) AND $_GET['date'] == $dayCounter)
        {
            $isSelected = 'isSelected';
        }
        
        // CELL DAY GENERATION
        // Is this date in the past ?
        if ($dayCounter < $today) { echo "<td class='pastDate'>"; }
        // Is this date today ?
        elseif ($dayCounter == $today) { echo "<td class='today'><a href='index.php5?viewEvents&date=".$dayCounter."'>"; }
        
        // Otherwise it's in the future
        else { echo "<td class='".$isSelected."'><a href='index.php5?viewEvents&date=".$dayCounter."'>"; }
        
        // CELL CONTENT GENERATION
        // Inside the cell we print the day number
        echo date('d',strtotime($dayCounter));
        // We get all the events for this particular day
        $events[$dayCounter] = returnEvents($dayCounter);
        // If there are no events for this day we don't show anything
        if (sizeof($events[$dayCounter]) < 1) { echo "<br/>&nbsp;"; }
        // Otherwise we show the number of events occuring this day
        else {
            
            // Number of events
            echo "<br/><span class='number_events'>".sizeof($events[$dayCounter])." ";
            // Several events ?
            if (sizeof($events[$dayCounter]) > 1) { echo "events"; }
            // Single event ?
            else { echo "event"; }
            echo "</span>";
            
        }
        
        // Was there a link because it is today or a future date ?
        if ($dayCounter >= $today) { echo "</a>"; }
        // WE CLOSE THE CELL DAY
        echo "</td>";

        // We close the week on sunday
        if (date('w',strtotime($dayCounter)) == 0) echo "</tr>";
        
        // We increment the day counter by one day
        $dayCounter = date('Y-m-d',strtotime($dayCounter." +1 day"));
    }

// We close the calendar display
    echo "</table>";
}

?>
