<script src="<?php echo BASE_URL;?>js/urlslug.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
  // Get the slug
	$("#title").keyup(function(){
			// Checked, copy values
			$("#slug").val(jakSlug($("#title").val()));
	});
});
</script>