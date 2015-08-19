
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

?>

<!-- Image size 900 x 190 -->
<img id="abstract" src="./img/<?php echo $Depeche->abstractImg;?>"/>

<div class="depeche">
    <h1><?php echo $Depeche->displayDepeche($Depeche->title); ?></h1>
    <div class="depecheContent">
        <?php echo $Depeche->displayDepeche($Depeche->content); ?>
    </div>
</div>