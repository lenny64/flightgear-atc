<?php

class DashboardEventsList extends Event {

    public $dashboardEvents = Array();

    function selectEvents ($userId)
    {
        global $db;
        // We gather every sessions this user made
        $events = $db->query("SELECT eventId FROM events WHERE userId = $userId ORDER BY `date` DESC LIMIT 0,10");
        
        $this->dashboardEvents = $events->fetchAll();
        return $this->dashboardEvents;
    }

    function getEventInfo ($eventId)
    {
        global $db;
        // We pick the event
        $Event = new Event();
        $LastEvent = $Event->selectById($eventId);

        // We pick the airport ID to ident it
        /*$airportId = getInfo('airportId', 'airports', 'ICAO', $Event->airportICAO);

        if ($airportId != NULL)
        {
            // We list the airport which is concerned
            $airports = $db->query("SELECT * FROM airports WHERE airportId = $airportId");
            // We gather the information
            $this->airport = $airports->fetch(PDO::FETCH_ASSOC);
        }*/

        return $LastEvent;
    }
}

?>
