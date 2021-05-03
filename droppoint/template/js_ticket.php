<script src="<?php echo BASE_URL_ORIG;?>js/editor/tinymce.min.js" type="text/javascript"></script>
<script src="<?php echo BASE_URL;?>js/datepicker.js" type="text/javascript"></script>
<?php if ($jakuser->getVar("files") && ($JAK_FORM_DATA['status'] == 1 || $JAK_FORM_DATA['status'] == 2)) { ?>
<script type="text/javascript" src="<?php echo BASE_URL_ORIG;?>js/dropzone.js"></script>
<?php } ?>

<script type="text/javascript">
$(document).ready(function() {
	tinymce.init({
    selector: '#content-editor',
    height: 300,
    menubar: false,
    plugins: [
      'advlist autolink lists link image charmap print preview anchor textcolor',
      'searchreplace visualblocks code fullscreen',
      'insertdatetime media table contextmenu paste code responsivefilemanager codesample'
    ],
    toolbar: 'insert | undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | removeformat codesample code',
    language: '<?php echo $BT_LANGUAGE;?>',
    <?php if ($jkl["rtlsupport"]) { ?>
    directionality : 'rtl',
    <?php } ?>
    external_filemanager_path:"<?php echo BASE_URL_ORIG;?>js/editor/filemanager/",
    filemanager_title:"Filemanager" ,
    external_plugins: { "filemanager" : "<?php echo BASE_URL_ORIG;?>js/editor/filemanager/plugin.min.js"},
    relative_urls: false
  });

  if ($("#jak_duedate").length != 0) {
    $('#jak_duedate').datetimepicker({
      // format: '<?php //echo $duedateformat[1];?>',
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
    });
  }
  
  if ($("#created_date").length != 0) {
    $('#created_date').datetimepicker({
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
    });
  }

  // $("#sc-label, #sc-select").css('display','none');
  url_sc = '<?php echo BASE_URL_ADMIN.'index.php?p=support&amp;sp=sub-category'?>';
  $('#jak_priority').on('change', function() {
    var priorityid = this.value;
    $.ajax({
      url: url_sc,
      type: "POST",
      data: {
        priorityid: priorityid
      },
      cache: false,
      success: function(result) {
        console.log(result);
        if (result === "-") {
            $("#sc-label, #sc-select").css('display','none');
        } else {
          console.log('haish');
          $("#sc-label, #sc-select").css('display','block');
          $("#jak_toption").empty(); 
          var res = JSON.parse(result);
          $.each(res, function(key, value) { 
            $('#jak_toption')
              .append($("<option></option>")
              .attr("value", value.id)
              .text(value.title)); 
              $("#jak_toption").val(value.id);
              $("#jak_toption").selectpicker("refresh");
          });
        }
      }
    });
  });
});

// Insert the standard response into the editor
$('select#supresp').on("change", function() {
  var respid = this.value;
  if (respid != 0) {

    $.ajax({
    type: "POST",
    url: ls.main_url + 'ajax/oprequests.php',
    data: "oprq=supportresponse&respid="+respid+"&cname=<?php echo $JAK_FORM_DATA["name"];?>&uid="+ls.opid,
    dataType: 'json',
    success: function(msg){

      if (msg.status) {
        tinymce.activeEditor.execCommand('mceInsertContent', false, msg.response);
        md.showNotification('check', '<?php echo $jkl['g14'];?>', 'success', 'top', 'right');
      } else {
        md.showNotification('warning', '<?php echo $jkl['g116'];?>', 'danger', 'top', 'right');
      }
    }
  });
    $('#supresp').val(0);
  }
});

$("#private_note").change(function() {
    if(this.checked) {
      $(".mce-path").addClass('bg-warning');
    } else {
      $(".mce-path").removeClass('bg-warning');
    }
  });

<?php if ($jakuser->getVar("files") && ($JAK_FORM_DATA['status'] == 1 || $JAK_FORM_DATA['status'] == 2)) { ?>
Dropzone.autoDiscover = false;
  $(function() {
      // Now that the DOM is fully loaded, create the dropzone, and setup the
      // event listeners
      var myDropzone = new Dropzone("#share-attachments", {dictResponseError: "SERVER ERROR",
      dictDefaultMessage: '<i class="fa fa-file-upload fa-3x"></i>',
      acceptedFiles: "<?php echo JAK_ALLOWEDO_FILES;?>",
      addRemoveLinks: true,
      url: "<?php echo BASE_URL_ORIG;?>uploader/uploaderto.php"});
    myDropzone.on("sending", function(file, xhr, formData) {
        // Will send the filesize along with the file as POST data.
        formData.append("ticketId", <?php echo $page2;?>);
        formData.append("userIDU", <?php echo $jakuser->getVar("id");?>);
        formData.append("base_url", "<?php echo BASE_URL_ORIG;?>");
        formData.append("operatorNameU", "<?php echo $jakuser->getVar("name");?>");
        formData.append("operatorLanguage", "<?php echo $USER_LANGUAGE;?>");
    });
    myDropzone.on("success", function(file, serverResponse) {
        //myDropzone.removeAllFiles();
        // Update the counter
        var thefile = "";
        var countSpan = $('#upload-counter').text();
        var newSpan = parseInt(countSpan) + 1;
        $('#upload-counter').html(newSpan);
        console.log(serverResponse);
        data = JSON.parse(serverResponse);
        if (data.isimage) {
          thefile = '<tr><td><a data-toggle="lightbox" href="<?php echo BASE_URL_ORIG;?>'+data.filepath+'"><img src="<?php echo BASE_URL_ORIG;?>'+data.filepath+'" alt="'+data.filename+'" class="img-thumbnail" width="50"></a> <a href="<?php echo BASE_URL_ORIG;?>'+data.filepath+'" class="btn btn-info btn-sm"><i class="fa fa-file-download"></i></a> <a href="'+data.delpath+'" class="btn btn-danger btn-sm btn-confirm" data-title="<?php echo addslashes($jkl["g48"]);?>" data-text="<?php echo addslashes($jkl["e32"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-trash-alt"></i></a></td></tr>';
        } else {
          thefile = '<tr><td>'+data.filename+' <a href="<?php echo BASE_URL_ORIG;?>'+data.filepath+'" class="btn btn-info btn-sm"><i class="fa fa-file-download"></i></a> <a href="'+data.delpath+'" class="btn btn-danger btn-sm btn-confirm" data-title="<?php echo addslashes($jkl["g48"]);?>" data-text="<?php echo addslashes($jkl["e32"]);?>" data-type="warning" data-okbtn="<?php echo addslashes($jkl["g279"]);?>" data-cbtn="<?php echo addslashes($jkl["g280"]);?>"><i class="fa fa-trash-alt"></i></a></td></tr>';
        }

        $('#attach-list').append(thefile);

        $.notify({icon: 'fa fa-check-square', message: '<?php echo $jkl['e38'];?>'}, {type: 'success', animate: {
          enter: 'animated fadeInDown',
          exit: 'animated fadeOutUp'
        }});
        
    });
  });
<?php } ?>

</script>