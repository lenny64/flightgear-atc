<?php
// We select the event
$Event = new Event();
$Event->selectById($events[$eventCounter]['Id']);

?>


<div class="calendar_event<?php if(isset($highlightEvent) AND $highlightEvent == $Event->id) echo " calendar_event_highlight"; ?>" >



<!-- LOCATION -->
<div class="calendar_event_location"<?php echo 'onclick="if(document.getElementById(\'calendar_event_infos'.$Event->id.'\').style.display == \'block\')
                        {
                            document.getElementById(\'calendar_event_infos'.$Event->id.'\').style.display = \'none\';
                        }
                        else document.getElementById(\'calendar_event_infos'.$Event->id.'\').style.display=\'block\';"';?> >

	<img src="./img/scheme_atc.png" width="15" height="15" class="calendar_event_location_point"/>
	<span class="calendar_event_location_icao"><?php echo $Event->airportICAO;?></span>
	<br/>
	<?php $airportName = str_split(getInfo('name', 'airports', 'icao', $Event->airportICAO),12); ?>
	<span class="calendar_event_location_name"><?php echo $airportName[0];?>...</span>
        <?php
        /* New feature : showing the ATC name
         */
        // We select the user responsible of the Event
        $User = new User();
        $User->selectById($Event->userId);
        // If the user name is not empty we print it
        if (isset($User->name) AND $User->name != NULL)
        {?>
            <div class="calendar_event_atcName">ATC : <?php echo $User->name; ?></div>
        <?php
        }
        ?>
</div>

<!-- TIME -->

<div class="calendar_event_time">
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
	<div class="calendar_histogram_background" onmouseover="document.getElementById('calendar_histogram_infos<?php echo $Event->id;?>').style.display='block';" onmouseout="document.getElementById('calendar_histogram_infos<?php echo $Event->id;?>').style.display = 'none';">
		<div class="calendar_histogram" style="width: <?php echo $DureeDecimal*4;?>px; left: <?php echo $BeginDecimal*4;?>px;">
			<div class="calendar_histogram_infos" id="calendar_histogram_infos<?php echo $Event->id;?>"><?php echo $Event->beginTime." ".$Event->endTime;?></div>
		</div>
	</div>

</div>

<?php include('./include/show_flightplans.php'); ?>

<!-- FLIGHTPLAN FILLING -->

<div class="calendar_event_infos" id="calendar_event_infos<?php echo $Event->id;?>" method="post" action="./index.php">
    <!-- Blue top left arrow -->
    <em class="calendar_blue_arrow"></em>
    <br/>
    <span class="calendar_event_information_type">Begin time</span>
    <span class="calendar_event_information_content"><?php echo $Event->beginTime;?></span>
    <br/>
    <span class="calendar_event_information_type">End time</span>
    <span class="calendar_event_information_content"><?php echo $Event->endTime;?></span>
    <br/>
    <span class="calendar_event_information_type">ATC Name</span>
    <span class="calendar_event_information_content"><?php echo $User->name;?></span>
    <br/>
    <span class="calendar_event_information_type">FGCom frequency</span>
    <span class="calendar_event_information_content"><?php echo $Event->fgcom;?></span>
    <br/>
    <span class="calendar_event_information_type">Documentation</span>
    <span class="calendar_event_information_content"><a href="<?php echo $Event->docsLink;?>"><?php echo $Event->docsLink;?></a></span>
    <br/>
    <span class="calendar_event_information_type">Remarks</span>
    <span class="calendar_event_information_content"><?php echo $Event->remarks;?></span>
    <br/>

</div>

</div>
