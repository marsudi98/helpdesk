<script src="<?php echo BASE_URL;?>js/editor/tinymce.min.js"></script>
<?php if ($ticketwrite && $ticketopen && $uploadactive) { ?>
<script type="text/javascript" src="<?php echo BASE_URL;?>js/dropzone.js"></script>
<?php } ?>

<script type="text/javascript">
$(document).ready(function() {
    tinymce.init({
      selector: '#content-editor',
      height: 300,
      menubar: false,
      plugins: [
        'advlist autolink lists link charmap print preview textcolor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime table contextmenu paste code codesample'
      ],
      toolbar: 'insert | undo redo | bold italic underline | bullist numlist | removeformat codesample code',
      language: '<?php echo $BT_LANGUAGE;?>',
      <?php if ($jkl["rtlsupport"]) { ?>
      directionality : 'rtl',
      <?php } ?>
      relative_urls: false
    });
});

<?php if ($ticketwrite && $ticketopen && $uploadactive) { ?>
Dropzone.autoDiscover = false;
  $(function() {
      // Now that the DOM is fully loaded, create the dropzone, and setup the
      // event listeners
      var myDropzone = new Dropzone("#share-attachments", {dictResponseError: "SERVER ERROR",
      dictDefaultMessage: '<i class="fa fa-upload fa-3x"></i>',
      acceptedFiles: "<?php echo JAK_ALLOWED_FILES;?>",
      addRemoveLinks: true,
      url: "<?php echo BASE_URL;?>uploader/uploadert.php"});
    myDropzone.on("sending", function(file, xhr, formData) {
        // Will send the filesize along with the file as POST data.
        formData.append("ticketId", <?php echo $page2;?>);
        formData.append("userIDU", <?php echo (JAK_USERID ? $jakuser->getVar("id") : 0);?>);
        formData.append("userIDC", <?php echo (JAK_CLIENTID ? $jakclient->getVar("id") : 0);?>);
        formData.append("base_url", "<?php echo BASE_URL;?>");
    });
    myDropzone.on("success", function(file, serverResponse) {
        //myDropzone.removeAllFiles();
        // Update the counter
        var thefile = "";
        var countSpan = $('#upload-counter').text();
        var newSpan = parseInt(countSpan) + 1;
        $('#upload-counter').html(newSpan);
        data = JSON.parse(serverResponse);
        if (data.isimage) {
          thefile = '<tr><td><a href="<?php echo BASE_URL;?>_showfile.php?i='+data.filepath+'" target="_blank"><img src="<?php echo BASE_URL;?>_showfile.php?i='+data.filepath+'" alt="'+data.filename+'" class="img-thumbnail" width="50"></a></td></tr>';
        } else {
          thefile = '<tr><td><a href="<?php echo BASE_URL;?>_showfile.php?i='+data.filepath+'" target="_blank">'+data.filename+'</a></td></tr>';
        }

        $('#attach-list').append(thefile);

        $.notify({icon: 'fa fa-check-square-o', message: '<?php echo $jkl['hd92'];?>'}, {type: 'success', animate: {
          enter: 'animated fadeInDown',
          exit: 'animated fadeOutUp'
        }});
        
    });
  });
<?php } if (JAK_SUPERADMINACCESS || JAK_USERISLOGGED) { ?>
function edit_post(id) {
  var editmsg = this.document.getElementById("edit-content"+id).innerHTML;
  tinymce.activeEditor.setContent(editmsg);
  tinyMCE.activeEditor.focus();
  document.getElementById("edit-post").value = id;
}
<?php } ?>
</script>