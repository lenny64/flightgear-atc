
<?php

// SPECIAL EVENTS
if (isset($_POST['specialEventId']) AND isset($_POST['eventId']))
{
    if ($_POST['specialEventId'] != NULL AND $_POST['eventId'] != NULL)
    {
        $specialEventId = $_POST['specialEventId'];
        $eventId = $_POST['eventId'];
        $userId = $User->id;

        $SpecialEvent = new SpecialEvent();
        $SpecialEvent->selectById($specialEventId);

        if ($_POST['operation'] == 'add')
        {
            $SpecialEvent->addEventToSpecialEvent($eventId, $userId);
            echo "<div class='alert alert-info'>Your event has successfully been added to ".$SpecialEvent->title.".</div>";
        }

        else if ($_POST['operation'] == 'remove')
        {
            $SpecialEvent->removeEventFromSpecialEvent($eventId);
            echo "<div class='alert alert-info'>Your event has successfully been removed from ".$SpecialEvent->title.".</div>";
        }

    }
}

// If the notifications or parameters are changing
if (isset($_POST['change_settings']))
{
    /* Notification part */
    if (isset($_POST['flightplan_notification']) AND $_POST['flightplan_notification'] == "1") { $User->changeNotification(true); }
    else { $User->changeNotification(false); }

    /* Name part /!\ Requires the "users_names" table */
    if (isset($_POST['atcName']))
    {
        $atcName = $_POST['atcName'];
        $User->changeName($atcName);
    }
    if (isset($_POST['atcMail']))
    {
        $atcMail = $_POST['atcMail'];
        $User->changeMail($atcMail);
    }
    if (isset($_POST['atcPassword']) && $_POST['atcPassword'] != NULL && $_POST['atcPassword'] != "password")
    {
        $newPassword = $_POST['atcPassword'];
        $User->changePassword($newPassword);
    }

    /* Other parameters part */
    // FPForm visibility on home screen
    if (isset($_POST['FPForm_visibility']) AND $_POST['FPForm_visibility'] == "1") { $FPForm_visibility = 'visible'; }
    else { $FPForm_visibility = 'hidden'; }
    // We list every parameter into an array that will be inserted into DB
    $userParameters = ['FPForm_visibility' => $FPForm_visibility];
    $User->changeParameters($userParameters);

    // Anyway we show this information
    echo "<div class='alert alert-info'>";
    echo "Your settings have been saved at ".date('H:i:s');
    echo "</div>";
}

// Detection of strong password
$update_password_bg = '';
$update_password_text = "(do not change or leave empty to keep your current password)";
$update_password_textColor = '';
$update_password_style = '';
if (!$User->checkPasswordSecured())
{
    $update_password_bg = 'bg-danger';
    $update_password_text = "Please change your password";
    $update_password_textColor = 'text-danger';
    $update_password_style = 'padding: 10px;';
}

if (isset($_POST['deleteAccount']) && $_POST['deleteAccount'] == 1)
{
    if (isset($_POST['deleteAccountConfirm']) && $_POST['deleteAccountConfirm'] == 1)
    {
        $User->deleteAccount();
        $_SESSION['mode'] = 'guest';
        echo "<div class='alert alert-success'>";
        echo "Your account has been deleted";
        echo "</div>";
    }
    else
    {
        echo "<div class='alert alert-info'>";
        echo "Your account has not been deleted";
        echo "</div>";
    }
}

?>

    <h3><span class="glyphicon glyphicon glyphicon-wrench"></span> Settings</h3>
    <form role="form" action="./dashboard.php?settings" method="post">
        <input type="hidden" name="change_settings"/>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="flightplan_notification" value="1" <?php if ($User->notifications == true) echo "checked";?>/> I want to be notified once a flightplan is filed (<?php echo $User->mail;?>)
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="FPForm_visibility" value="1" <?php if ($User->parameters['FPForm_visibility'] == 'visible') echo "checked";?>/> I want to see the flightplan filling form on the home page
            </label>
        </div>
        <div class="form-group">
            <label for="atcName">My pseudo</label>
            <input type="text" class="form-control" id="atcName" name="atcName" value="<?php if(isset($User->name) AND $User->name != NULL) echo $User->name; ?>" />
            <p class="help-block">(blank = your pseudo won't appear in your ATC events)</p>
        </div>
        <div class="form-group">
            <label for="atcName">My email</label>
            <input type="text" class="form-control" id="atcMail" name="atcMail" value="<?php if(isset($User->mail) AND $User->mail != NULL) echo $User->mail; ?>" />
        </div>
        <div class="form-group <?= $update_password_bg; ?>" style="<?= $update_password_style;?>">
            <label for="atcPassword" class="<?= $update_password_textColor; ?>">My password</label>
            <input type="password" class="form-control" id="atcPassword" name="atcPassword" value="password" />
            <p class="help-block"><?= $update_password_text; ?></p>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-default" value="Change settings">Change settings</button> <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalDeleteAccount">Delete account</button>
        </div>
    </form>


<div id="modalDeleteAccount" class="modal fade" role="dialog">
    <div class="modal-dialog">

    <!-- Modal content-->
    <form role="form" action="./dashboard.php?settings" method="post" class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Account deletion</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete your account?</p>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="deleteAccountConfirm" value="1"/> I confirm I want to delete my account
            </label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-danger" name="deleteAccount" value="1">Yes delete my account</button>
        <button type="button" class="btn btn-success" data-dismiss="modal">No, do not delete my account</button>
      </div>
    </form>

    </div>
</div>
