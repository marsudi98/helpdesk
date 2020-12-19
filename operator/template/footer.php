<?php if (JAK_USERID) { ?>
	
<footer class="footer">
        <div class=" container-fluid ">
          <nav>
            <ul>
              <li>
                <a href="https://www.jakweb.ch/faq">
                  FAQ
                </a>
              </li>
              <li>
                <a href="https://www.jakweb.ch/profile">
                  Support
                </a>
              </li>
              <li>
                <a href="https://www.jakweb.ch/blog/c/1/about-jakweb">
                  News
                </a>
              </li>
            </ul>
          </nav>
          <div class="copyright" id="copyright">
            <?php echo date("Y");?>, Designed with <i class="fa fa-heart"></i>. Coded by
            <a href="https://www.jakweb.ch" target="_blank">JAKWEB</a>.
          </div>
        </div>
      </footer>

</div><!-- Main Panel -->

</div><!-- Wrapper -->

<!-- Modal -->
<div id="jakModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="userModal" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"></div></div></div><!-- /.modal -->

<!-- New Pro Active Invitation -->
<div id="proActiveModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="proActiveModal" aria-hidden="true">
	<div class="modal-dialog">
	  <div class="modal-content">
	    <div class="modal-header">
	    	<h4 class="modal-title"><?php echo $jkl["u12"];?></h4>
	      	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	    </div>
	    <div class="modal-body">
			<input type="text" name="proactivemsg" id="proactivemsg" class="form-control" value="<?php echo $jakuser->getVar("invitationmsg"); ?>">
			<input type="hidden" name="proactiveuid" id="proactiveuid">
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $jkl["g103"];?></button>
			<button class="btn btn-primary" onclick="sendInvitation();"><?php echo $jkl["g4"];?></button>
		</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php if ($page == "") { ?>
<!-- Calendar Modal -->
<div class="modal fade" id="calModal" tabindex="-1" role="dialog" aria-labelledby="calModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="calModalLabel"></h5>
      </div>
      <div class="modal-body">
        <form method="post" class="jak_form" action="<?php echo BASE_URL;?>ajax/calendar.php">
        	<div class="row">
        		<div class="col-md-6">
		        	<div class="form-group">
		            	<label for="cal-start" class="col-form-label"><?php echo $jkl['hd243'];?></label>
		            	<input type="text" class="form-control datepicker" id="cal-start" name="cal-start">
		          	</div>
		        </div>
		        <div class="col-md-6">
		          	<div class="form-group">
		            	<label for="cal-end" class="col-form-label"><?php echo $jkl['hd244'];?></label>
		            	<input type="text" class="form-control datepicker" id="cal-end" name="cal-end">
		         	</div>
		        </div>
		    </div>
		    
          	<div class="form-group">
            	<label for="cal-title" class="col-form-label"><?php echo $jkl['g16'];?></label>
            	<input type="text" class="form-control" id="cal-title" name="cal-title">
          	</div>

          	<div class="form-group">
            	<label for="cal-content" class="col-form-label"><?php echo $jkl['g321'];?></label>
            	<textarea class="form-control" id="cal-content" rows="5" name="cal-content"></textarea>
          	</div>

          	<div class="form-group">
          		<p><label for="cal-title" class="col-form-label"><?php echo $jkl['style_s1'];?></label></p>
          		<select name="cal-color" class="selectpicker" id="color">
					<option style="color:#0071c5;" value="#0071c5">&#9724; Blue</option>
					<option style="color:#008000;" value="#008000">&#9724; Green</option>						  
					<option style="color:#FFD700;" value="#FFD700">&#9724; Yellow</option>
					<option style="color:#FF8C00;" value="#FF8C00">&#9724; Orange</option>
					<option style="color:#FF0000;" value="#FF0000">&#9724; Red</option>
					<option style="color:#000000;" value="#000000">&#9724; Black</option>
				</select>
			</div>

			<div class="form-check" id="cal-delete">
				<label class="form-check-label">
					<input class="form-check-input" type="checkbox" name="cal-delete" value="1">
				    <span class="form-check-sign"></span> <?php echo $jkl["g48"];?>
				</label>
			</div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal"><?php echo $jkl['g180'];?></button>
        <button type="submit" class="btn btn-primary"><?php echo $jkl['g38'];?></button>
      </div>
      <input type="hidden" name="cal-id" id="cal-id">
      <input type="hidden" name="cal-action" id="cal-action">
      </form>
    </div>
  </div>
</div>
<?php } ?>

<!-- Modal FileManager -->
<div id="jakFM" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="userModal" aria-hidden="true"><div class="modal-dialog modal-xlg"><div class="modal-content"><iframe src="<?php echo BASE_URL_ORIG;?>js/editor/filemanager/dialog.php?type=1&amp;lang=en_EN&amp;field_id=previmg" width="100%" height="600" frameborder="0"></iframe></div></div></div><!-- /.modal -->

<?php } ?>

<script type="text/javascript" src="<?php echo BASE_URL_ORIG;?>js/jquery.js?=<?php echo JAK_UPDATED;?>"></script>
<script type="text/javascript" src="<?php echo BASE_URL_ORIG;?>js/functions.js?=<?php echo JAK_UPDATED;?>"></script>
<script type="text/javascript" src="<?php echo BASE_URL;?>js/admin.js?=<?php echo JAK_UPDATED;?>"></script>

<?php if (JAK_USERID) { ?>
<script type="text/javascript" src="<?php echo BASE_URL;?>js/client.ajax.js"></script>

<script type="text/javascript">
	ls.usrAvailable = <?php echo $jakuser->getVar("available");?>;
	ls.ls_ringing = "<?php echo $jakuser->getVar("ringing");?>";
	ls.opid = <?php echo $jakuser->getVar("id");?>;
	ls.oname = '<?php echo stripcslashes($jakuser->getVar("username"));?>';
	// Push Notifications
	ls.pushnotify = <?php echo $jakuser->getVar("push_notifications");?>;	
	// sound
	ls.muted = <?php echo $jakuser->getVar("sound");?>;	
	// Chat latency
	clatency = <?php echo $jakuser->getVar("chat_latency");?>;
	
	$("#jakModal").on("show.bs.modal", function(e) {
    	var link = $(e.relatedTarget);
    	$(this).find(".modal-content").load(link.attr("href"));
	});
		
	$('#jakModal').on('hidden.bs.modal', function() {
		$(this).removeData();
	});

	ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
	ls.orig_main_url = "<?php echo BASE_URL_ORIG;?>";
	ls.main_lang = "<?php echo JAK_LANG;?>";

	// Finally start the event.
	sseJAK(<?php echo $jakuser->getVar("id");?>, <?php echo $jakuser->getVar("operatorlist");?>, <?php echo $jakuser->getVar("operatorchat");?>, <?php echo $JAK_UONLINE;?>);
	setInterval("setTimer(<?php echo $jakuser->getVar("id");?>);", 120000);
</script>
<!-- Operator Chat -->
<?php if ($jakuser->getVar("operatorchat") == 1 && JAK_OPENOP == 0){?>

<script type="text/javascript" src="<?php echo BASE_URL;?>js/operator.chat.js"></script>

<script type="text/javascript">
	sseJAKOPC(<?php echo $jakuser->getVar("id");?>);
</script>

<!-- reopen old opened chatboxes with the last state-->
<?php if (isset($_SESSION['chatbox_status'])) {
	echo '<script type="text/javascript">';
	echo '$(function() {';
	foreach ($_SESSION['chatbox_status'] as $openedchatbox) {
		echo 'PopupChat('.$openedchatbox['partner_id'].',"'.$openedchatbox['partner_username'].'",'.$openedchatbox['chatbox_status'].');';
	}
	echo "});";
	echo '</script>';
	}

} ?>
<?php } ?>

<script type="text/javascript">
<?php if (isset($_SESSION["infomsg"])) { ?>
$.notify({icon: 'fa fa-info-circle', message: '<?php echo addslashes($_SESSION["infomsg"]);?>'}, {type: 'info', animate: {
		enter: 'animated fadeInDown',
		exit: 'animated fadeOutUp'
	}});
<?php } if (isset($_SESSION["successmsg"])) { ?>
$.notify({icon: 'fa fa-check-square', message: '<?php echo addslashes($_SESSION["successmsg"]);?>'}, {type: 'success', animate: {
		enter: 'animated fadeInDown',
		exit: 'animated fadeOutUp'
	}});
<?php } if (isset($_SESSION["errormsg"])) { ?>
$.notify({icon: 'fa fa-exclamation-triangle', message: '<?php echo addslashes($_SESSION["errormsg"]);?>'}, {type: 'danger', animate: {
		enter: 'animated fadeInDown',
		exit: 'animated fadeOutUp'
	}});
<?php } ?>
</script>

<?php if ($js_file_footer) include_once(APP_PATH.JAK_OPERATOR_LOC.'/template/'.$js_file_footer);?>

</body>
</html>