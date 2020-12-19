<?php include_once 'header.php';?>

<div class="content">

<div class="row">
	<div class="col-md-12">
		<div class="card">
            <div class="card-header card-header-tabs">
            	<div class="nav-tabs-navigation">
                    <div class="nav-tabs-wrapper">
                      <ul class="nav nav-tabs" data-tabs="tabs">
                      	<?php if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) { ?>
                        <li class="nav-item">
                          <a class="nav-link active" href="#tickets" data-toggle="tab">
                            <i class="fa fa-life-ring"></i> <?php echo $jkl["hd"];?>
                          </a>
                        </li>
                    	<?php } if (jak_get_access("leads", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) { ?>
                        <li class="nav-item">
                          <a class="nav-link" href="#chats" data-toggle="tab">
                            <i class="fa fa-comments"></i> <?php echo $jkl["m1"];?>
                          </a>
                        </li>
                    	<?php } if (jak_get_access("off_all", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) { ?>
                        <li class="nav-item">
                          <a class="nav-link" href="#offlinemsg" data-toggle="tab">
                            <i class="fa fa-comments-alt"></i> <?php echo $jkl["m22"];?>
                          </a>
                        </li>
                        <?php } if (jak_get_access("client", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) { ?>
                        <li class="nav-item">
                          <a class="nav-link" href="#clients" data-toggle="tab">
                            <i class="fa fa-user-circle"></i> <?php echo $jkl["hd6"];?>
                          </a>
                        </li>
                    	<?php } ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <div class="tab-content">
                  	<?php if (jak_get_access("support", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) { ?>
                    <div class="tab-pane active" id="tickets">
                      <table class="table">
                      	<thead>
                   			<th><?php echo $jkl['g221'];?></th>
                   			<th><?php echo $jkl['g321'];?></th>
                   			<th><?php echo $jkl['hd251'];?></th>
                   			<th><?php echo $jkl['hd252'];?></th>
                   			<th><?php echo $jkl['hd182'];?></th>
                   		</thead>
                        <tbody>
                       	<?php if (isset($searchtickets) && !empty($searchtickets)) foreach ($searchtickets as $t) { ?>
	                          <tr>
	                            <td><a class="btn btn-sm btn-primary" href="<?php echo JAK_rewrite::jakParseurl('support', 'read', $t['id']);?>"><?php echo $t['subject'];?></a></td>
	                            <td><?php echo jak_cut_text($t['content'], 160, '...');?></td>
	                            <td><?php echo JAK_base::jakTimesince($t['initiated'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></td>
	                            <td><?php echo ($t['updated'] ? JAK_base::jakTimesince($t['updated'], JAK_DATEFORMAT, JAK_TIMEFORMAT) : '-');?></td>
	                            <td><?php echo ($t['ended'] ? JAK_base::jakTimesince($t['ended'], JAK_DATEFORMAT, JAK_TIMEFORMAT) : '-');?></td>
	                          </tr>
                        <?php } else { ?>
                        	<tr>
                        		<td colspan="4"><?php echo $jkl['i3'];?></td>
                        	</tr>
                    	<?php } ?>
                        </tbody>
                      </table>
                    </div>
                	<?php } if (jak_get_access("leads", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) { ?>
                    <div class="tab-pane" id="chats">
                      <table class="table">
                      	<thead>
                   			<th><?php echo $jkl['u'];?></th>
                   			<th><?php echo $jkl['u1'];?></th>
                   			<th><?php echo $jkl['i70'];?></th>
                   			<th><?php echo $jkl['hd182'];?></th>
                   		</thead>
                        <tbody>
                        <?php if (isset($searchleads) && !empty($searchleads)) foreach ($searchleads as $l) { ?>
	                        <tr>
	                        	<td><a class="btn btn-sm btn-primary" href="<?php echo JAK_rewrite::jakParseurl('live', $l['id']);?>"><?php echo $l['name'];?></a></td>
	                            <td><?php echo $l['email'];?></td>
	                            <td><?php echo JAK_base::jakTimesince($l['initiated'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></td>
	                            <td><?php echo ($l['ended'] ? JAK_base::jakTimesince($l['ended'], JAK_DATEFORMAT, JAK_TIMEFORMAT) : '-');?></td>
	                        </tr>
	                    <?php } else { ?>
                        	<tr>
                        		<td colspan="4"><?php echo $jkl['i3'];?></td>
                        	</tr>
                        <?php } ?>
                        </tbody>
                      </table>
                    </div>
               		<?php } if (jak_get_access("off_all", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) { ?>
                    <div class="tab-pane" id="offlinemsg">
                      <table class="table">
                      	<thead>
                   			<th><?php echo $jkl['u'];?></th>
                   			<th><?php echo $jkl['u1'];?></th>
                   			<th><?php echo $jkl['g264'];?></th>
                   			<th><?php echo $jkl['stat_s30'];?></th>
                   		</thead>
                        <tbody>
                        <?php if (isset($searchoff) && !empty($searchoff)) foreach ($searchoff as $o) { ?>
	                        <tr>
	                        	<td><a class="btn btn-sm btn-primary" data-toggle="modal" href="<?php echo JAK_rewrite::jakParseurl('contacts', 'readmsg', $o['id']);?>" data-target="#jakModal"><?php echo $o['name'];?></a></td>
	                            <td><?php echo $o['email'];?></td>
	                            <td><?php echo JAK_base::jakTimesince($o['sent'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></td>
	                            <td><?php echo ($o['answered'] ? JAK_base::jakTimesince($o['answered'], JAK_DATEFORMAT, JAK_TIMEFORMAT) : '-');?></td>
	                        </tr>
	                       <?php } else { ?>
                        	<tr>
                        		<td colspan="4"><?php echo $jkl['i3'];?></td>
                        	</tr>
                        <?php } ?>
                        </tbody>
                      </table>
                    </div>
                    <?php } if (jak_get_access("client", $jakuser->getVar("permissions"), JAK_SUPERADMINACCESS)) { ?>
                    <div class="tab-pane" id="clients">
                      <table class="table">
                   		<thead>
                   			<th><?php echo $jkl['u'];?></th>
                   			<th><?php echo $jkl['u1'];?></th>
                   			<th><?php echo $jkl['i68'];?></th>
                   			<th><?php echo $jkl['i69'];?></th>
                   		</thead>
                      <tbody>
                        <?php if (isset($searchclients) && !empty($searchclients)) foreach ($searchclients as $c) { ?>
	                        <tr>
	                        	<td><a class="btn btn-sm btn-primary" href="<?php echo JAK_rewrite::jakParseurl('users', 'clients', 'edit', $c['id']);?>"><?php echo $c['name'];?></a></td>
	                            <td><?php echo $c['email'];?></td>
	                            <td><?php echo JAK_base::jakTimesince($c['time'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></td>
	                            <td><?php echo ($c['lastactivity'] ? JAK_base::jakTimesince($c['lastactivity'], JAK_DATEFORMAT, JAK_TIMEFORMAT) : '-');?></td>
	                        </tr>
	                    <?php } else { ?>
                        	<tr>
                        		<td colspan="4"><?php echo $jkl['i3'];?></td>
                        	</tr>
                        <?php } ?>
                        </tbody>
                      </table>
                    </div>
                	<?php } ?>
                  </div>
                </div>
              </div>
            </div>
        </div>

</div>
		
<?php include_once 'footer.php';?>