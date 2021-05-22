<?php

$number_days_displayed = 4;
$real_today = date('Y-m-d');
if (isset($_GET['dateBegin']) && $_GET['dateBegin'] != NULL) {
    $today = $_GET['dateBegin'];
} else {
    $today = date('Y-m-d');
}
$today_plus_x_days = date('Y-m-d', strtotime($today." +".$number_days_displayed." days"));
$today_minus_x_days = date('Y-m-d', strtotime($today." -".$number_days_displayed." days"));
$style_previous_events = "btn-secondary";
if ($today_minus_x_days < date('Y-m-d', strtotime($real_today." - 3 days"))) {
    $style_previous_events = "btn-outline-secondary disabled";
}

class Day {
    public function __construct($calendarDay) {
        $this->real_today = date('Y-m-d');
        $this->calendar_day = $calendarDay;
    }
    public function getDayCounter($today) {
        $this->day_counter = date('Y-m-d', strtotime($today." +".$this->calendar_day." days"));
    }
    public function getDayDisplayInfo() {
        $this->additional_card_class = "border-info";
        if ($this->calendar_day == 0 AND $this->day_counter == $this->real_today) {
            $this->day_line = "Today";
            $this->additional_card_class = "border-secondary";
        }
        else if ($this->calendar_day == 1 AND $this->day_counter == date('Y-m-d', strtotime($this->real_today." +1 day"))) {
            $this->day_line = "Tomorrow";
        }
        else if ($this->calendar_day > 1 AND $this->calendar_day < 6) {
            $this->day_line = "On ".date('l', strtotime($this->day_counter));
        }
        else {
            $this->day_line = date('D j M', strtotime($this->day_counter));
        }
        $this->day_in_week = date('l', strtotime($this->day_counter));
    }
    public function getEventsBadgeText() {
        $events_badge_text = "no events";
        if ($this->nb_events > 1) {
            $events_badge_text = $this->nb_events . " events";
        }
        else if ($this->nb_events == 1) {
            $events_badge_text = $this->nb_events . " event";
        }
        return $events_badge_text;
    }
    public function getTotalFlightplansBadgeText($list_total_flightplans) {
        $flightplans_badge_text = "<span class='badge badge-light'>no flightplans</span>";
        $nb_total_flightplans = sizeof($list_total_flightplans);
        if ($nb_total_flightplans > 1) {
            $flightplans_badge_text = "<a class='badge badge-info btn-show-flightplans' href='#' data-toggle='modal' data-target='#showFlightplansModal'>" . $nb_total_flightplans . " flightplans <span class='oi oi-chevron-bottom' title='Show/hide flightplans' aria-hidden='true'></span></a>";
        }
        else if ($nb_total_flightplans == 1) {
            $flightplans_badge_text = "<a class='badge badge-info btn-show-flightplans' href='#' data-toggle='modal' data-target='#showFlightplansModal'>" . $nb_total_flightplans . " flightplan <span class='oi oi-chevron-bottom' title='Show/hide flightplans' aria-hidden='true'></span></a>";
        }
        return $flightplans_badge_text;
    }
    public function getEventsList($events) {
        if (isset($events)) {
            $this->events_list = filterEvents('date', $this->day_counter, $events);
        }
        $this->nb_events = sizeof($this->events_list);
        $this->no_events_message = "";
        if (sizeof($this->events_list) == 0) {
            $this->no_events_message .= "<div class='card mb-2'>";
            $this->no_events_message .= "<div class='card-header'>";
            $this->no_events_message .= "No ATC events yet";
            $this->no_events_message .= "</div>";
            $this->no_events_message .= "<div class='card-body'>";
            $this->no_events_message .= "<a href='./new_event.php?date=".$this->day_counter."' class='btn btn-primary'><span class='oi oi-plus' title='Add an event' aria-hidden='true'></span> Create a new event</a>";
            $this->no_events_message .= "<br/><br/>";
            $this->no_events_message .= '<a href="#" class="btn btn-info" data-toggle="modal" data-target="#myModal" onclick="document.getElementById(\'file_flightplan-date\').value=\''.$this->day_counter.'\';"><span class="oi oi-plus" aria-hidden="true"></span> Create a flight plan</a>';
            $this->no_events_message .= "</div>";
            $this->no_events_message .= "</div>";
        }
    }
}
