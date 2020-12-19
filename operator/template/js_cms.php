<script src="<?php echo BASE_URL_ORIG;?>js/editor/tinymce.min.js"></script>
<script src="<?php echo BASE_URL;?>js/urlslug.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	tinymce.init({
      selector: 'textarea.w-editor',
      content_css : "<?php echo BASE_URL_ORIG;?>css/stylesheet.css",
      height: 500,
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

  // Get the slug
	$("#title").keyup(function(){
			// Checked, copy values
			$("#url_slug").val(jakSlug($("#title").val()));
	});

  $("#bg_change").change(function() {
    var bgimg = $(this).val();
    if (bgimg) {
      // remove hidden
      $("#bg_container").fadeIn();
      // Change avatar
      $("#bg_prev").attr("src", "<?php echo BASE_URL_ORIG;?>template/<?php echo JAK_FRONT_TEMPLATE;?>/img/"+bgimg);
    } else {
      $("#bg_container").fadeOut();
    }
  });
});
</script>