<ul class="nav">
<?php if (jak_get_access("support", $jakuser->getVar("permissions"), true)){?>
<li<?php if ($page == '' || $page == 'support') echo ' class="active"';?>><a href="<?php echo JAK_rewrite::jakParseurl('support');?>"><i class="fas fa-ticket-alt"></i> <p><?php echo $jkl["hd"];?></p></a></li>
<?php } ?>
<li<?php if (in_array($page, array('report'))) echo ' class="active"';?>>
<a data-toggle="collapse" href="#navstatistics">
              <i class="fas fa-analytics"></i>
              <p>
                <?php echo $jkl["m10"];?>
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse<?php if (in_array($page, array('report'))) echo ' show';?>" id="navstatistics">
            <ul class="nav">
<li<?php if ($page == 'report') echo ' class="active"';?>><a href="<?php echo JAK_rewrite::jakParseurl('report');?>">
	<span class="sidebar-mini-icon">R</span>
    <span class="sidebar-normal">Report</span>
</a></li>
</ul>
</div>
</li>
</ul>