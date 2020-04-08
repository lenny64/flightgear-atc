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
$style_previous_events = "btn-primary";
if ($today_minus_x_days < date('Y-m-d', strtotime($real_today." - 3 days"))) {
    $style_previous_events = "btn-outline-primary disabled";
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
    }
    public function getEventsList($events) {
        if (isset($events)) {
            $this->events_list = filterEvents('date', $this->day_counter, $events);
        }
        $this->no_events_message = "";
        if (sizeof($this->events_list) == 0) {
            $this->no_events_message .= "<div class='card'>";
            $this->no_events_message .= "<div class='card-header'>";
            $this->no_events_message .= "No events yet";
            $this->no_events_message .= "</div>";
            $this->no_events_message .= "</div>";
        }
    }
}
