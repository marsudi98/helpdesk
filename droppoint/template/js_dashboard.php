<script type="text/javascript" src="<?php echo BASE_URL;?>js/calendar.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL;?>js/fullcalendar.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL;?>js/fullcalendar-locale.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL;?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL;?>js/jvectormap.js"></script>
<?php if ($jakhs['hostactive'] && $jakwidget['validtill'] < strtotime("+30 day")) { ?>
<script src="https://checkout.stripe.com/checkout.js"></script>
<?php } ?>

<!-- JavaScript for select all -->
<script type="text/javascript">

<?php if ($jakhs['hostactive'] && $jakwidget['validtill'] < strtotime("+30 day")) { ?>
$('#stripe').on('click', function(e) {

	$(this).find(".jak-loadbtn").addClass("fa fa-spinner fa-spin");

	var amount = $("#month").val();

	var stripe_amount = amount*100;

	e.preventDefault();
	var handler = StripeCheckout.configure({
		key: '<?php echo $sett["stripepublic"];?>',
		image: 'payment/img/stripe_logo.png',
		locale: 'auto',
		token: function(token) {
			// You can access the token ID with `token.id`.
			// Get the token ID to your server-side code for use.
			$("#stripeToken").val(token.id);
			$("#stripeEmail").val(token.email);
			var utok = $("input#stripeToken").val();
			var uemail = $("input#stripeEmail").val();

			if (!utok){ 
				return false; 
			} else {
						        		
				$.ajax({
					url: "<?php echo $_SERVER['REQUEST_URI'];?>",
					type: "POST",
					data: "check=paymember&paidhow=stripe&token="+utok+"&email="+uemail+"&amount="+amount,
					dataType: "json",
					cache: false
				}).done(function(data) {

					if (data.status == 1) {
						$.notify({message: data.infomsg}, {type:'success'});
						$('#expiredmsg').hide();
						$('#memberdate').html(data.date);
					} else {
						$.notify({message: data.infomsg}, {type:'danger'});
					}

				});

			}
		}
	});

	// Open Checkout with further options:
	handler.open({
		name: '<?php echo JAK_TITLE;?>',
		description: '<?php echo $jkl["g295"];?>',
		email: '<?php echo $jakclient->getVar("email");?>',
		amount: parseInt(stripe_amount),
		currency: '<?php echo strtolower($sett["currency"]);?>',
		closed: function () {
			$.notify({message: '<?php echo stripcslashes($jkl['g297']);?>'}, {type:'danger'});
			$("#stripe").find(".jak-loadbtn").removeClass("fa fa-spinner fa-spin");
        }
	});
});

$('#paypal').on('click', function(e) {

	e.preventDefault();

	$(this).find(".jak-loadbtn").addClass("fa fa-spinner fa-spin");

	var amount = $("#month").val();

	$.ajax({
		url: "<?php echo $_SERVER['REQUEST_URI'];?>",
		type: "POST",
		data: "check=paymember&paidhow=paypal&amount="+amount,
		dataType: "json",
		cache: false
	}).done(function(data) {
							        			
		if (data.status == 1) {
			$("#paypal_form").html(data.content);
			$('#gateway_form').submit();
		} else {
			$("#paypal").find(".jak-loadbtn").removeClass("fa fa-spinner fa-spin");
			$.notify({message: data.infomsg}, {type:'danger'});
		}
	});

$('#twoco').on('click', function(e) {

	e.preventDefault();

	$(this).find(".jak-loadbtn").addClass("fa fa-spinner fa-spin");

	var amount = $("#month").val();

	$.ajax({
		url: "<?php echo $_SERVER['REQUEST_URI'];?>",
		type: "POST",
		data: "check=paymember&paidhow=twoco&amount="+amount,
		dataType: "json",
		cache: false
	}).done(function(data) {
							        			
		if (data.status == 1) {
			$("#paypal_form").html(data.content);
			$('#gateway_form').submit();
		} else {
			$("#twoco").find(".jak-loadbtn").removeClass("fa fa-spinner fa-spin");
			$.notify({message: data.infomsg}, {type:'danger'});
		}
	});

});
<?php } ?>
$(document).ready(function() {
	var initialLocaleCode = '<?php echo ($USER_LANGUAGE == 'en' ? 'en-gb' : $USER_LANGUAGE);?>';
	$('#calendar').fullCalendar({
		defaultDate: moment(),
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listMonth'
		},
		scrollTime: '09:00:00',
		weekNumbers: true,
		navLinks: true, // can click day/week names to navigate views
		editable: true,
		defaultView: 'agendaWeek',
		locale: initialLocaleCode,
		eventLimit: true, // allow "more" link when too many events
		refetchResourcesOnNavigate: true,
		selectable: true,
		selectHelper: true,
		select: function(start, end) {
			$('#calModal #calModalLabel').html('<?php echo stripslashes($jkl['hd245']);?>');
			$('#calModal #cal-start').val(moment(start).format('YYYY-MM-DD HH:mm:ss'));
			$('#calModal #cal-end').val(moment(end).format('YYYY-MM-DD HH:mm:ss'));
			$('#calModal #cal-action').val("cal-new");
			$('#calModal #cal-delete').hide();
			$('#calModal').modal('show');
		},
		eventRender: function(event, element) {
			element.bind('dblclick', function() {
				$('#calModal #cal-id').val(event.id);
				$('#calModal #cal-action').val("cal-edit");
				$('#calModal #cal-start').val(moment(event.start).format('YYYY-MM-DD HH:mm:ss'));
				$('#calModal #cal-end').val(moment(event.end).format('YYYY-MM-DD HH:mm:ss'));
				$('#calModal #cal-title').val(event.title);
				$('#calModal #cal-content').val(event.content);
				$('#calModal #color').val(event.color);
				$('#calModal #cal-delete').show();
				$('#calModal').modal('show');
			});

			$('#calModal').on('hidden.bs.modal', function (e) {
			  	$('#calModal #cal-title').val("");
				$('#calModal #cal-content').val("");
			});

		},
		eventDrop: function(event, delta, revertFunc) {
			// update the time from the new drop position
			cal_edit_event(event);

		},
		eventResize: function(event,dayDelta,minuteDelta,revertFunc) {
			// We have rezised the task update
			cal_edit_event(event);
		},
		events: '<?php echo BASE_URL;?>ajax/events.php'
	});

	if ($(".datepicker").length != 0) {
      $('.datepicker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        icons: {
          time: "fa fa-clock",
          date: "fa fa-calendar",
          up: "fa fa-chevron-up",
          down: "fa fa-chevron-down",
          previous: 'fa fa-chevron-left',
          next: 'fa fa-chevron-right',
          today: 'fa fa-screenshot',
          clear: 'fa fa-trash',
          close: 'fa fa-remove'
        }
      });
    }

    loadmap = {
    initVectorMap: function() {
    var mapData = {
    <?php if (isset($ctlres) && !empty($ctlres)) foreach ($ctlres as $uj) { ?>
    	"<?php echo strtoupper($uj['countrycode']);?>": <?php echo $uj["total_country"];?>,
    <?php } ?>
    };

    $('#worldMap').vectorMap({
      map: 'world_mill_en',
      backgroundColor: "transparent",
      zoomOnScroll: false,
      regionStyle: {
        initial: {
          fill: '#e4e4e4',
          "fill-opacity": 0.9,
          stroke: 'none',
          "stroke-width": 0,
          "stroke-opacity": 0
        }
      },

    series: {
        regions: [{
          values: mapData,
          scale: ["#AAAAAA", "#444444"],
          normalizeFunction: 'polynomial'
        }]
      },
    });
  }

};

  loadmap.initVectorMap();
		
});
function cal_edit_event(event) {
	start = event.start.format('YYYY-MM-DD HH:mm:ss');
	if (event.end) {
		end = event.end.format('YYYY-MM-DD HH:mm:ss');
	} else {
		end = start;
	}
	id =  event.id;

	$.ajax({
	  url: '<?php echo BASE_URL;?>ajax/calendar.php',
	  type: "POST",
	  data: "cal-id=" + event.id + "&cal-action=cal-date&cal-start=" + start + "&cal-end=" + end,
	  dataType: "json",
	  cache: false
	}).done(function(data) {

		if (data.status == 1) {
			$.notify("<?php echo $jkl['g14'];?>", {type:'success',position:'top-center'});
		} else {
			$.notify("<?php echo $jkl['i3'];?>", {type:'danger',position:'top-center'});
		}
	});
			
}
ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
ls.orig_main_url = "<?php echo BASE_URL_ORIG;?>";
ls.main_lang = "<?php echo JAK_LANG;?>";
</script>