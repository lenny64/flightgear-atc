<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<?php

if (isset($_POST['mail']) AND isset($_POST['subject']) AND isset($_POST['content']))
{
    if ($_POST['mail'] != NULL AND $_POST['subject'] != NULL AND $_POST['content'] != NULL)
    {
        $mail = mysql_real_escape_string(htmlspecialchars($_POST['mail']));
        $subject = mysql_real_escape_string(htmlspecialchars($_POST['subject']));
        $content = mysql_real_escape_string(htmlspecialchars($_POST['content']));
        
        mail('lenny64@free.fr', $subject, 'Mail de '.$mail.' : '.$content);
    }
}

else if (isset($_POST['pseudo']) AND isset($_POST['note']) AND isset($_POST['improvements']))
{
	if ($_POST['pseudo'] != NULL)
	{
		$pseudo = mysql_real_escape_string(htmlspecialchars($_POST['pseudo']));
		$note = $_POST['note'];
		$improvements = mysql_real_escape_string(htmlspecialchars($_POST['improvements']));
		
		mysql_query("INSERT INTO improvements VALUES('',NOW(),'$pseudo','$note','$improvements')") or die(mysql_error());
		
	}
}

?>

<h3>Contact</h3>

<form action="./contact.php5" method="post">
    <input type="text" name="mail"/> Your email
    <br/>
    <input type="text" name="subject"/> Subject
    <br/>
    <textarea rows="7" cols="40" name="content"></textarea>
    <br/><br/>
    <input type="submit" value="Send message"/>
</form>

<h3>Get involved : what could be improved ?</h3>


<form action="./contact.php5" method="post">
	What is your perception about the website ?
	<br/>
	<select name="note">
		<option value=""> </option>
		<option value="good"> + Good</option>
		<option value="medium"> ~ Quite good/Quite bad</option>
		<option value="bad"> - Bad</option>
	</select>
	
	<br/><br/>
	
	Your name :
	<br/>
	<input type="text" name="pseudo"/>
	<br/>
	Improvements :
	<br/>
	<textarea name="improvements" rows="7" cols="40"></textarea>
	<br/>
	Your ideas will be shown below
	<br/><br/>
	<input type="submit" value="Send"/>
</form>

<div class="commentaires">
	
	<?php

	$liste_commentaires = mysql_query("SELECT * FROM improvements ORDER BY improvement_id DESC LIMIT 0,20");

	while ($commentaire = mysql_fetch_array($liste_commentaires))
	{?>
		<div class="commentaire">
		<span class="commentaire_pseudo"><?php echo $commentaire['pseudo'];?></span>
		<span class="commentaire_date"><?php echo $commentaire['datetime'];?></span>
		<span class="commentaire_improvement"><?php echo nl2br($commentaire['improvement']);?></span>
		</div>
	<?php
	}

	?>
</div>


<br/>
<br/>
<?php include('./include/footer.php5'); ?>
