<script src="<?php echo BASE_URL_ORIG;?>js/editor/tinymce.min.js"></script>

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
});

</script>