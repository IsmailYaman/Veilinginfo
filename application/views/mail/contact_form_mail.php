Beste <?php echo $firstname_owner; ?> <?php echo $lastname_owner; ?>,<br/>
<br/>
Dit is een bericht verstuurd vanaf het contactformulier op <?php echo $page_name; ?>.<br/>
<br/>
Verzonden door: <?php echo $firstname; ?> <?php echo $lastname; ?> (<?php echo $email; ?>)<br/>
<br/>
Bericht:<br/>
<br/>
<?php echo nl2br(htmlentities($message)); ?>
<br/>
<br/>
Met vriendelijke groet,<br/>
<?php echo $site_name; ?>