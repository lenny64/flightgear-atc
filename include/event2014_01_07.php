<?php
// We select the event
$Event = new Event();
$Event->selectById($events[$i]['Id']);

?>


<div class="event">



<!-- LOCATION -->
<span class="event_location"
   <?php echo 'onclick="if(document.getElementById(\'file_flightplan'.$Event->id.'\').style.display == \'block\')
                        {
                            document.getElementById(\'file_flightplan'.$Event->id.'\').style.display = \'none\';
                        }
                        else document.getElementById(\'file_flightplan'.$Event->id.'\').style.display=\'block\';"';?> >

	<img src="./img/scheme_atc.png" width="15" height="15" class="event_location_point"/>
	<span class="event_location_icao"><?php echo getInfo('name', 'airports', 'icao', $Event->airportICAO);?> (<?php echo $Event->airportICAO;?>) </span>

</span> <a href="./edit_event.php?eventId=<?php echo $Event->id;?>" class="event_button_edit">Edit event</a>

<!-- TIME -->

<div class="event_time">
	<?php

	/* DONNEES POUR L'HISTOGRAMME */
	$EventBegin = explode(':', $Event->beginTime);
	$EventBeginHour = $EventBegin[0];
	$EventBeginMinues = $EventBegin[1];
	$BeginDecimal = $EventBeginHour + ($EventBeginMinues / 60);

	$EventEnd = explode(':', $Event->endTime);
	$EventEndHour = $EventEnd[0];
	$EventEndMinues = $EventEnd[1];
	$EndDecimal = $EventEndHour + ($EventEndMinues / 60);

	$DureeDecimal = $EndDecimal - $BeginDecimal;

	?>

	<div class="histogram_background">
		<div class="histogram" style="width: <?php echo $DureeDecimal*10;?>px; left: <?php echo $BeginDecimal*10;?>px;">
		</div>
	</div>
    <?php echo$humanReadableDayInWeek; ?> - <b><?php echo $Event->beginTime;?></b> UTC -> <b><?php echo $Event->endTime;?></b> UTC

</div>

<?php include('./include/show_flightplans.php'); ?>

<!-- FLIGHTPLAN FILLING -->
<div class="file_flightplan" id="file_flightplan<?php echo $Event->id;?>" method="post" action="./index.php">
    <!-- Blue top left arrow -->
    <em class="blue_arrow"></em>
    <br/>
    <span class="event_information_type">FGCom frequency</span>
    <span class="event_information_content"><?php echo $Event->fgcom;?></span>
    <br/>
    <span class="event_information_type">Documentation</span>
    <span class="event_information_content"><a href="<?php echo $Event->docsLink;?>"><?php echo $Event->docsLink;?></a></span>
    <br/>
    <span class="event_information_type">Remarks</span>
    <span class="event_information_content"><?php echo $Event->remarks;?></span>
    <br/>

</div>

</div>
