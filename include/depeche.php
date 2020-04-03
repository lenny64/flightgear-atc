
<?php


/* LISTE DEPECHES
 *
 * A new depeche will appear each day, refreshed the first time a user visits the website
 *
 * FLIGHTPLANS
 * - Local flight
 * - From/To flight with defined from and to airport (if hours are not exactly the same)
 * - From/To flight without defined from and to airport (if hours are the same)
 *
 * NEWS
 * - Security news
 * - Feature news
 * - Flightgear news
 *
 * DATABASE STRUCTURE
 * Table __depecheList__
 * depecheId
 * title
 * content
 * abstractImg (picture)
 * type (fpLocal, fpNormal, newsSecurity, ...)
 * importance (between 1 and 10)
 * maxOccurences (max occurences per week)
 * validFrom (date, for short term depeches)
 * validTo (date, for short term depeches)
 * nbControlledAirports (number of controlled airports to display the depeche)
 * conditions (php code, like "EVENT_DATE == '2015-05-01'")
 *
 * Table __depecheSelection__
 * depecheSelectionId
 * depecheId
 * type
 * dateSelected (date)
 * limitDateValidity (date, most of the time 1 day after dateSelected)
 * occurences (number of occurences this week : an incremental index)
 *
 */

$Depeche = new Depeche();

// We list the validated depeche of the day
$Depeche->listValidatedDepeche();
// If there are no depeche validated
if (!isset($Depeche->validatedDepechesList) OR empty($Depeche->validatedDepechesList))
{
    $Depeche->validateDepeche();
}

include_once('./include/poll.php');

$Poll = new Poll();
$polls_list = $Poll->getPolls();
// By default we do not show poll
$showPoll = FALSE;
if ($polls_list > 0) { // If there is a poll
    // We check if the user already answered
    $Poll->checkAnswer();
    if ($Poll->okToVote !== FALSE) {
        $showPoll = TRUE;
    }
}

if ($showPoll == FALSE) { // If there is no poll we display the depeche
    $title = $Depeche->displayDepeche($Depeche->title);
    $content = $Depeche->displayDepeche($Depeche->content);
}
else { // If there is poll
    $title = "A question for you!";
    $content = $Poll->content;
    $content .= "<br/>";
    $content .= "<form class='form' action='./' method='post'>";
    $content .= "<input type='hidden' name='poll_id' value='".$Poll->id."'/>";
    foreach ($Poll->choices as $choice) {
        $content .= "<input type='submit' class='btn btn-light btn-sm' name='poll_answer' value='".$choice."'/> ";
    }
    // $content .= "<input type='submit' value='Submit!' class='btn btn-sm btn-success'>";
    $content .= "</form>";
}

?>

<!-- Image size 900 x 190 -->

<div class="jumbotron" id="jumbotron_mainPage" style="background: #f0f0f0 url('./img/a<?php echo $Depeche->abstractImg;?>') no-repeat center center;">
    <div id='bg-overlay' class="mb-3">
        <div class="row">
            <div class="col-md-12">
                <h1 class="display-4"><?php echo $title; ?></h1>
                <p class="lead" >
                    <?php echo $content; ?>
                </p>
            </div>
            <!--<div class="col-md-4 col-sm-12">
                <?php include('./include/liveATC.php'); ?>
            </div>-->
        </div>
    </div>
</div>
