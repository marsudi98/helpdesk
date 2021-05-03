<?php include_once APP_PATH.'template/modern/tplblocks/header.php';?>

<div class="container">
<div class="row">
  <div class="col-md-12">
    <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
      if ($t["cmsid"] == 10) {
        echo '<div data-editable data-name="title-10">'.$t["title"].'</div>';
        echo '<div data-editable data-name="text-10">'.$t["description"].'</div>';
      }
    } 
    ?>
  </div>
</div>
</div>

<div class="container">
  <div class="row">
    <div class="col-md-6">
      <?php if (JAK_USERISLOGGED || JAK_TICKET_GUEST_WEB) { ?>
        <p><a href="<?php echo (JAK_USERID ? JAK_rewrite::jakParseurl('operator', 'support', 'new') : JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 'n'));?>" class="btn btn-primary"><i class="fa fa-ticket-alt-alt"></i> <?php echo $jkl['hd47'];?></a></p>
        
        <?php if (isset($_SESSION["webembed"])) { ?>
          &nbsp;<a href="<?php echo JAK_rewrite::jakParseurl(JAK_CLIENT_URL);?>" class="text-success"><i class="fa fa-user"></i> <?php echo (JAK_CLIENTID ? $jakclient->getVar("name") : $jkl['hd18']);?></a>
      <?php } } ?>
    </div>
    <!-- <div class="col-md-6">
      <?php //if (isset($dep_filter) && is_array($dep_filter) && !empty($dep_filter)) { ?>
        <form id="jak_statform" method="post" action="<?php //echo $_SERVER['REQUEST_URI'];?>">
          <select name="jak_depid" id="jak_depid" class="form-control">
            <option value="0"><?php //echo $jkl['g88'];?></option>
            <?php //foreach ($dep_filter as $v) { ?>
              <option value="<?php //echo $v["id"];?>"<?php //if (isset($page2) && $page2 == $v["id"]) echo ' selected';?>><?php //echo $v["title"];?></option>
            <?php //} ?>
          </select>
          <input type="hidden" name="action" value="depid">
        </form>
      <?php //} ?>
    </div> -->
  </div>
</div>

<hr>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="table-responsive">
        <table id="dynamic-data" class="table table-striped" cellspacing="0" width="100%">
          <thead class="table-custom-dark">
            <th><?php echo $jkl['hd7'];?></th>
            <th><?php echo $jkl['hd9'];?></th>
            <th><?php echo $jkl['hd8'];?></th>
            <th><?php echo $jkl['hd6'];?></th>
            <th><?php echo $jkl['hd10'];?></th>
            <th><?php echo $jkl['hd11'].'/'.$jkl['hd12'];?></th>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include_once APP_PATH.'template/modern/tplblocks/footer.php';?>