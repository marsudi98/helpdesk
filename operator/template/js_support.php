<!-- JavaScript for select all -->
<script src="<?= BASE_URL;?>js/datepicker.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function() {
        
	$("#jak_delete_all").click(function() {
		var checked_status = this.checked;
		$(".highlight").each(function()
		{
			this.checked = checked_status;
		});
	});

    $('#jak_start_datefilter').datetimepicker({
      // format: '',
      <?php if(isset($_SESSION["jak_start_datefilter"])) echo "defaultDate: \"".$_SESSION["jak_start_datefilter"]."\"," ?>
      format: 'YYYY-MM-DD',
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
    }).on('dp.change', function (e) {
        $('#jak_end_datefilter').data("DateTimePicker").minDate(e.date);
        $("#start_date_form").submit();
    });

    $('#jak_end_datefilter').datetimepicker({
      // format: '',
      <?php if(isset($_SESSION["jak_end_datefilter"])) echo "defaultDate: \"".$_SESSION["jak_end_datefilter"]."\"," ?>
      useCurrent: false,
      format: 'YYYY-MM-DD',
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
    }).on('dp.change', function (e) {
        $('#jak_start_datefilter').data("DateTimePicker").maxDate(e.date);
        $("#end_date_form").submit();
    });;

    $(document).on("change", "#jak_depid", function() {
        $("#jak_statform").submit();
    });

    $(document).on("change", "#jak_statfilter", function() {
        $("#stat_form").submit();
    });

    $(document).on("change", "#jak_catfilter", function() {
        $("#cat_form").submit();
    });
 
	// DataTables initialisation
    

	var table = $('#dynamic-data').DataTable( {
	    processing: true,
	    serverSide: true,
        responsive: true,
	    columnDefs: [
            { "orderable": false, "targets": [1] },
            { "className": "font-weight-bold", "targets": [2]},
            { "className": "dt-body-center", "targets": [0, 1, 6, 9]}
  		],
        <?php if ($USER_LANGUAGE != "en") { ?>
        language: {
                "url": "<?php echo BASE_URL_ORIG;?>js/dt_lang/<?php echo $USER_LANGUAGE;?>.js"
        },
        <?php } ?>
  		// order: [9, "ASC"],
	    ajax: $.fn.dataTable.pipeline( {
            url: '<?php echo BASE_URL;?>ajax/support.php',
            pages: 5 // number of pages to cache
        }),
        rowCallback: function( row, data ) {
            if(data.check_duedate < 0 && ( data.tdc == 1 || data.tdc == 2)){
                $(row).addClass('table-danger');
            } else if (data.tdc == 1) {
                $(row).addClass('table-primary');
            } else if (data.tdc == 2) {
                $(row).addClass('table-warning');
            } else if (data.check_duedate < 0 || (data.tdc == 3 || data.tdc == 4)) {    
                $(row).addClass('table-success');
            }
        },
        // no reset page
        "bStateSave": true,
        "fnStateSave": function (oSettings, oData) {
            localStorage.setItem('offersDataTables', JSON.stringify(oData));
        },
        "fnStateLoad": function (oSettings) {
            return JSON.parse(localStorage.getItem('offersDataTables'));
        }
        // end no reset page
	});
    // table.ajax.reload(null, false);
    // table.ajax.reload( null, false );


    $("#export_table").on("click", function(){
        if ($("#jak_start_datefilter").val() != "" && $("#jak_end_datefilter").val() != "") {
            let start_date  = $("#jak_start_datefilter").val().split('-');
            console.log(start_date);
            start_date      = parseInt((new Date(start_date[0], start_date[1] - 1, start_date[2]).getTime() / 1000).toFixed(0));
            let end_date    = $("#jak_end_datefilter").val().split('-');
            console.log(end_date);
            end_date        = parseInt((new Date(end_date[0], end_date[1] - 1, end_date[2]).getTime() / 1000).toFixed(0));
            console.log(start_date);
            console.log(end_date);
            console.log(end_date-start_date);
            if ((end_date-start_date) <= 2592000 ) {
                let link = "<?= BASE_URL_ORIG ?>operator/index.php?p=custom_dashboard&sp=support_ticket";
                link += "&priorityid="+$("#jak_catfilter").val();
                link += "&status="+$("#jak_statfilter").val();
                link += "&start_date="+$("#jak_start_datefilter").val();
                link += "&end_date="+$("#jak_end_datefilter").val();
                window.location = link;
                // alert(link);
            } else {
                alert("Selisih From dan To tidak boleh lebih dari 31 hari.");
            }
        } else {
            alert("Tanggal From/To Kosong!");
        }
    });
});

function download_template() {
    // var priorityid = this.value;
    $.ajax({
        url: "<?= BASE_URL_ORIG ?>operator/index.php?p=support&sp=download_template",
        method: "POST",
        dataType: "JSON",
        
        cache: false,
        success: function(result) {
            window.open(result,'_blank' );
        }
    });
}


//
// Pipelining function for DataTables. To be used to the `ajax` option of DataTables
//
$.fn.dataTable.pipeline = function ( opts ) {
    // Configuration options
    var conf = $.extend( {
        pages: 5,     // number of pages to cache
        url: '',      // script url
        method: 'GET' // Ajax HTTP method
    }, opts );
 
    // Private variables for storing the cache
    var cacheLower = -1;
    var cacheUpper = null;
    var cacheLastRequest = null;
    var cacheLastJson = null;
 
    return function ( request, drawCallback, settings ) {
        var ajax          = false;
        var requestStart  = request.start;
        var drawStart     = request.start;
        var requestLength = request.length;
        var requestEnd    = requestStart + requestLength;
         
        if ( settings.clearCache ) {
            // API requested that the cache be cleared
            ajax = true;
            settings.clearCache = false;
        }
        else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
            // outside cached data - need to make a request
            ajax = true;
        }
        else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
                  JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
                  JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
        ) {
            // properties changed (ordering, columns, searching)
            ajax = true;
        }
         
        // Store the request for checking next time around
        cacheLastRequest = $.extend( true, {}, request );
 
        if ( ajax ) {
            // Need data from the server
            if ( requestStart < cacheLower ) {
                requestStart = requestStart - (requestLength*(conf.pages-1));
 
                if ( requestStart < 0 ) {
                    requestStart = 0;
                }
            }
             
            cacheLower = requestStart;
            cacheUpper = requestStart + (requestLength * conf.pages);
 
            request.start = requestStart;
            request.length = requestLength*conf.pages;
 
            // Provide the same `data` options as DataTables.
            if ( $.isFunction ( conf.data ) ) {
                // As a function it is executed with the data object as an arg
                // for manipulation. If an object is returned, it is used as the
                // data object to submit
                var d = conf.data( request );
                if ( d ) {
                    $.extend( request, d );
                }
            }
            else if ( $.isPlainObject( conf.data ) ) {
                // As an object, the data given extends the default
                $.extend( request, conf.data );
            }
 
            settings.jqXHR = $.ajax( {
                "type":     conf.method,
                "url":      conf.url,
                "data":     request,
                "dataType": "json",
                "cache":    false,
                "success":  function ( json ) {
                    cacheLastJson = $.extend(true, {}, json);
 
                    if ( cacheLower != drawStart ) {
                        json.data.splice( 0, drawStart-cacheLower );
                    }
                    if ( requestLength >= -1 ) {
                        json.data.splice( requestLength, json.data.length );
                    }
                     
                    drawCallback( json );
                }
            } );
        }
        else {
            json = $.extend( true, {}, cacheLastJson );
            json.draw = request.draw; // Update the echo for each response
            json.data.splice( 0, requestStart-cacheLower );
            json.data.splice( requestLength, json.data.length );
 
            drawCallback(json);
        }
    }
};
 
// Register an API method that will empty the pipelined data, forcing an Ajax
// fetch on the next draw (i.e. `table.clearPipeline().draw()`)
$.fn.dataTable.Api.register( 'clearPipeline()', function () {
    return this.iterator( 'table', function ( settings ) {
        settings.clearCache = true;
    });
});

ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
ls.orig_main_url = "<?php echo BASE_URL_ORIG;?>";
ls.main_lang = "<?php echo JAK_LANG;?>";
</script>