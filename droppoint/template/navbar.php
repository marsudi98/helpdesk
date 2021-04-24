<ul class="nav">
<?php if (jak_get_access("support", $jakuser->getVar("permissions"), true)){?>
<li<?php if ($page == '' || $page == 'support') echo ' class="active"';?>><a href="<?php echo JAK_rewrite::jakParseurl('support');?>"><i class="fas fa-ticket-alt"></i> <p><?php echo $jkl["hd"];?></p></a></li>
<?php } ?>
</ul>