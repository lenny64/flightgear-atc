<?php
if (isset($_GET['getVerified']))
{
    if (!$User->parameters['verified'])
    {
        // First we put the user as not verified, just to make sure the !$User->parameters['verified'] will be false next time
        $userParams = $User->parameters;
        $userParams['verified'] = "pending";
        $User->changeParameters($userParams);
        $mail_subject = 'Verification query '.$User->id;
        $mail_content = 'Query from '.$User->id.' ('.$User->mail.').';
        mail('thibault.armengaud@free.fr', $mail_subject, $mail_content);
        ?>
        <div class="alert alert-success">
            Congratulations! Your account is being verified. the process may take some time.
            <br/>
            You can still create sessions. The <span class="label label-success">green label</span> will come soon :)
        </div>
        <?php
    }
    else if ($User->parameters['verified'] == 'true')
    {
        ?>
        <div class="alert alert-success">
            Your account is already verified :)
        </div>
        <?php
    }
    else if ($User->parameters['verified'] == 'false')
    {
        ?>
        <div class="alert alert-danger">
            Sorry the verification has been refused :(
            <br/>
            You can still create sessions but they won't be appear with the green label.
        </div>
        <?php
    }
    else if ($User->parameters['verified'] == 'pending')
    {
        ?>
        <div class="alert alert-success">
            Your account verification is pending. Please wait some few days...
            <br/>
            You can still create sessions. The <span class="label label-success">green label</span> will come soon :)
        </div>
        <?php
    }
}
else if (!$User->parameters['verified'])
{
    ?>
    <div class="alert alert-info">
        Your account has not been verified yet. Please <a href="./dashboard.php?getVerified">click here</a> if you want your account to be verified.
        <br/>
        Verified accounts will appear in a <span class="label label-success">green label</span> on the home page.
    </div>
    <?php
}
?>
