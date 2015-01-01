<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>


<!-- LE CODE COMMENCE ICI -->

<?php include('./include/log.php5'); ?>

<?php
// D O W N L O A D S   P A R T
// echo '<a href="./download.php5?file=Newsletter-201411.pdf" target="_blank" style="margin-left: 30px; font-weight: bold; color: #3C71B2; padding: 10px 10px; border: solid 1px #e0e0e0; background-color: #f5f5f5;"><img src="./img/logo_pdf.png" width="25" style="position: relative; top: 8px; margin-right: 10px;"/> November newsletter</a>';
// echo '<br/>';
// echo '<br/>';
// echo '<br/>';

// F L I G H T P L A N    F O R M    V I S I B I L I T Y
// If the user is connected
if ($_SESSION['mode'] == 'connected' AND isset($_SESSION['id']))
{
    // The user has just changed its preferences
    if (isset($_POST['FPForm_visibility']) AND $_POST['FPForm_visibility'] != NULL)
    {
            $FPForm_visibility = $_POST['FPForm_visibility'];
            $parameters = ['FPForm_visibility' => $FPForm_visibility];
            $User->changeParameters($parameters);
            ?>
            <div class="information">
                    Thanks ! You can change your settings in your dashboard
            </div>
            <?php
    }
    // If the user has not determined yet if he wants to see the FP form area
    else if (!isset($User->parameters['FPForm_visibility']))
    {
    ?>
    <div class="information">
            As an ATC, do you care about the Flightplan filling area ?
            <br/>
            <form action="./index.php5" method="post">
                    <input type="radio" name="FPForm_visibility" value="visible"/> Yes
                    <br/>
                    <input type="radio" name="FPForm_visibility" value="hidden"/> No
                    <br/>
                    <input type="submit" value="Apply"/>
            </form>
    </div>
<?php
    }
}

// I am first looking for every eventId
$events = returnEvents();

// If the user is not connected or wants to see the FP area
if (!isset($User->parameters['FPForm_visibility']) OR (isset($User->parameters['FPForm_visibility']) AND $User->parameters['FPForm_visibility'] == "visible"))
{
    include('./include/file_flightplan_v3.php5');
}
// If the user is connected and does not want to see the FP area, we include the date picker that is inside file_flightplan_v3.php5 in normal conditions
// *Warning : this is a workaround... Needs to be improved.
elseif (isset($User->parameters['FPForm_visibility']) AND $User->parameters['FPForm_visibility'] == "hidden")
{
    echo "<script type='text/javascript' language='javascript'>";
    echo "$(document).ready(function()";
    echo "{";
    echo "$('#dateTimePicker').datepicker({ dateFormat:'yy-mm-dd', showOn: 'button', buttonImage: './img/scheme_date.png', buttonImageOnly: true });";
    echo "})";
    echo "</script>";
}


// New specialEvents feature
include('./include/specialEvents.php5');

// C A L E N D A R
include('./include/calendar.php5');
echo '<br style="clear:both;"/>';

// E V E N T S
include('./include/viewEvents.php5');

?>

<br/>
<br/>
<?php include('./include/footer.php5'); ?>
