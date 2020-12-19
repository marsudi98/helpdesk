<?php include_once APP_PATH.'template/standard/tplblocks/header.php';?>
	
<?php if (isset($allcsupport) && is_array($allcsupport)){?>

<div class="content-dashboard-block">
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
        if ($t["cmsid"] == 12) {
          echo '<div data-editable data-name="title-12">'.$t["title"].'</div>';
          echo '<div data-editable data-name="text-12">'.$t["description"].'</div>';
        }
      }
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <p><a href="<?php echo JAK_rewrite::jakParseurl('operator', 'support', 'new');?>" class="text-danger"><i class="fa fa-ticket-alt"></i> <?php echo $jkl['hd47'];?></a></p>
    </div>
  </div>
  <div class="row">
    <div class="col-md-9">
    <table class="table table-responsive table-light w-100 d-block d-md-table">
      <thead class="table-custom-dark">
        <th><?php echo $jkl['hd7'];?></th>
        <th><?php echo $jkl['hd9'];?></th>
        <th><?php echo $jkl['hd6'];?></th>
        <th><?php echo $jkl['hd10'];?></th>
        <th><?php echo $jkl['hd11'].'/'.$jkl['hd12'];?></th>
        <th><?php echo $jkl['hd13'];?></th>
      </thead>
        <?php foreach($allcsupport as $sup) {
          $supparseurl = JAK_rewrite::jakParseurl('operator', 'support', 'read', $sup["id"]);
        ?>
        <tr>
          <td><a href="<?php echo $supparseurl;?>"><?php echo $sup["subject"];?></a></td>
          <td><?php echo $sup["titledep"];?></td>
          <td class="text-center"><?php echo $sup["id"];?></td>
          <td><?php echo JAK_base::jakTimesince($sup['initiated'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></td>
          <td><?php echo ($sup["status"] == 1 ? '<span class="badge badge-info">'.$jkl['hd14'].'</span>' : ($sup["status"] == 2 ? '<span class="badge badge-warning">'.$jkl['hd15'].'</span>' : '<span class="badge badge-success">'.$jkl['hd16'].'</span>'));?>/<span class="badge badge-<?php echo $sup["class"];?>"><?php echo $sup["titleprio"];?></span></td>
          <td><a href="<?php echo $supparseurl;?>" class="btn btn-sm btn-secondary"><?php echo $jkl['hd13'];?></a></td>
        </tr>
        <?php } ?>
    </table>
    <?php if ($JAK_PAGINATE) echo $JAK_PAGINATE;?>

    <!-- Translation only -->
    <div class="row">
      <div class="col-md-12">
          <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
            if ($t["cmsid"] == 14) {
              echo '<div data-editable data-name="title-14">'.$t["title"].'</div>';
              echo '<div data-editable data-name="text-14">'.$t["description"].'</div>';
            }
          }
          ?>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
          <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
            if ($t["cmsid"] == 13) {
              echo '<div data-editable data-name="title-13">'.$t["title"].'</div>';
              echo '<div data-editable data-name="text-13">'.$t["description"].'</div>';
            }
          } 
          ?>
      </div>
    </div>

</div>
<div class="col-md-3">
  <div class="card">
  <img class="card-img-top card-profile" src="<?php echo BASE_URL.JAK_FILES_DIRECTORY.$jakuser->getVar("picture");?>" alt="profile-picture">
  <div class="card-body">
    <h4 class="card-title"><?php echo $jakuser->getVar("name");?></h4>
    <p class="card-text"><strong><?php echo $jkl['g47'];?></strong><br><?php echo $jakuser->getVar("email");?></p>
    <p class="card-text"><strong><?php echo $jkl['hd36'];?></strong><br><?php echo JAK_base::jakTimesince($jakuser->getVar("lastactivity"), JAK_DATEFORMAT, JAK_TIMEFORMAT);?></p>
    <p class="card-text"><a href="<?php echo JAK_rewrite::jakParseurl('logout');?>" class="btn btn-sm btn-block btn-danger"><?php echo $jkl['g26'];?></a></p>
  </div>
</div>
</div>

</div>
</div>
</div>
<?php } ?>

<?php include_once APP_PATH.'template/standard/tplblocks/footer.php';?>