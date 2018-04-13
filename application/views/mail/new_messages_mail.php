Beste <?php echo $firstname; ?> <?php echo $lastname; ?>,<br/>
<br/>
<?php echo $count > 1 ? 'Er zijn '.$count.' nieuwe berichten' : 'Er is 1 nieuw bericht'; ?>
<br/><br/>
Bekijk deze berichten door in te loggen op je account.
<br/><br/>
<a href="<?php echo $login; ?>" title="Inloggen op <?php echo $site_name; ?>"><?php echo $login; ?></a>
<br/>
<br/>
Met vriendelijke groet,<br/>
<?php echo $site_name; ?>