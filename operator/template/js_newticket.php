<script src="<?php echo BASE_URL_ORIG;?>js/editor/tinymce.min.js"></script>
<script src="<?php echo BASE_URL;?>js/selectlive.js"></script>
<script src="<?php echo BASE_URL;?>js/datepicker.js" type="text/javascript"></script>

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

  if ($(".datepicker").length != 0) {
      $('.datepicker').datetimepicker({
        format: '<?php echo $duedateformat[1];?>',
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

});

// Insert the standard response into the editor
$('select#supresp').on("change", function() {
  var respid = this.value;
  if (respid != 0) {

    // Get the value from the selected client
    var cvalue = $('#jak_clients').val();
    // Split the value
    var cname = cvalue.split(':#:');

    $.ajax({
    type: "POST",
    url: ls.main_url + 'ajax/oprequests.php',
    data: "oprq=supportresponse&respid="+respid+"&cname="+cname[2]+"&uid="+ls.opid,
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

$(".selectpicker").chosen();
</script>