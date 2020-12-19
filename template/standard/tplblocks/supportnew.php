<?php
// Client Access
if (JAK_CLIENTID) {
  // All access
  if ($jakclient->getVar("support_dep") == 0) {

    // Get the result
    $newsupport = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"], "[>]ticketpriority" => ["priorityid" => "id"], "[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_tickets.content", "support_tickets.status", "ticketpriority.title(titleprio)", "ticketpriority.class", "support_tickets.initiated", "support_departments.title(titledep)", "clients.name"], ["OR" => ["support_tickets.private" => 0, "support_tickets.clientid" => JAK_CLIENTID],
        "ORDER" => ["support_tickets.updated" => "DESC"],
        "LIMIT" => "3"
    ]);

  // Only for certain categories
  } else {

    // Get the result
    $newsupport = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"], "[>]ticketpriority" => ["priorityid" => "id"], "[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_tickets.content", "support_tickets.status", "ticketpriority.title(titleprio)", "ticketpriority.class", "support_tickets.initiated", "support_departments.title(titledep)", "clients.name"], ["OR" => ["support_tickets.private" => 0, "support_tickets.depid" => [$jakclient->getVar("support_dep")], "support_tickets.clientid" => JAK_CLIENTID],
        "ORDER" => ["support_tickets.updated" => "DESC"],
        "LIMIT" => "3"
    ]);

  }
// Can see all active articles
} elseif (JAK_USERID) {

  if ($jakuser->getVar("support_dep") == 0) {

      // Get the result
      $newsupport = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"], "[>]ticketpriority" => ["priorityid" => "id"], "[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_tickets.content", "support_tickets.status", "support_tickets.initiated", "ticketpriority.title(titleprio)", "ticketpriority.class", "support_departments.title(titledep)", "clients.name"], [
          "ORDER" => ["support_tickets.updated" => "DESC"],
          "LIMIT" => "3"
      ]);

  } else { 

      // Get the result
      $newsupport = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"], "[>]ticketpriority" => ["priorityid" => "id"], "[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_tickets.content", "support_tickets.status", "support_tickets.initiated", "ticketpriority.title(titleprio)", "ticketpriority.class", "support_departments.title(titledep)", "clients.name"], ["support_tickets.depid" => [$jakuser->getVar("support_dep")],
          "ORDER" => ["support_tickets.updated" => "DESC"],
          "LIMIT" => "3"
      ]);

  }
// Can see categories for guests
} else {

  // Get the result
  $newsupport = $jakdb->select("support_tickets", ["[>]support_departments" => ["depid" => "id"], "[>]ticketpriority" => ["priorityid" => "id"], "[>]clients" => ["clientid" => "id"]], ["support_tickets.id", "support_tickets.subject", "support_tickets.content", "support_tickets.status", "ticketpriority.title(titleprio)", "ticketpriority.class", "support_tickets.initiated", "support_departments.title(titledep)", "clients.name"], ["AND" => ["support_tickets.private" => 0, "support_departments.guesta" => 1],
      "ORDER" => ["support_tickets.updated" => "DESC"],
      "LIMIT" => "3"
  ]);
}

if (isset($newsupport) && !empty($newsupport)){?>
<div class="content-support-block">
<div class="container">
  <div class="row">
    <div class="col">
      <?php if (isset($cms_text) && !empty($cms_text)) foreach ($cms_text as $t) {
        if ($t["cmsid"] == 9) {
          echo '<div data-editable data-name="title-9">'.$t["title"].'</div>';
          echo '<div data-editable data-name="text-9">'.$t["description"].'</div>';
        }
      } 
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col">
    <table class="table table-responsive table-light w-100 d-block d-md-table">
      <thead class="table-custom-dark">
        <th><?php echo $jkl['hd7'];?></th>
        <th><?php echo $jkl['hd9'];?></th>
        <th><?php echo $jkl['hd8'];?></th>
        <th><?php echo $jkl['hd6'];?></th>
        <th><?php echo $jkl['hd10'];?></th>
        <th><?php echo $jkl['hd11'].'/'.$jkl['hd12'];?></th>
        <th><?php echo $jkl['hd13'];?></th>
      </thead>
<?php foreach($newsupport as $supn) {
  $supnparseurl = JAK_rewrite::jakParseurl(JAK_SUPPORT_URL, 't', $supn["id"], JAK_rewrite::jakCleanurl($supn["subject"]));
?>
<tr>
  <td><a href="<?php echo $supnparseurl;?>"><?php echo $supn["subject"];?></a></td>
  <td><?php echo $supn["titledep"];?></td>
  <td><?php echo $supn["name"];?></td>
  <td class="text-center"><?php echo $supn["id"];?></td>
  <td><?php echo JAK_base::jakTimesince($supn['initiated'], JAK_DATEFORMAT, JAK_TIMEFORMAT);?></td>
  <td><?php echo ($supn["status"] == 1 ? '<span class="badge badge-info">'.$jkl['hd14'].'</span>' : ($supn["status"] == 2 ? '<span class="badge badge-warning">'.$jkl['hd15'].'</span>' : '<span class="badge badge-success">'.$jkl['hd16'].'</span>'));?> / <span class="badge badge-<?php echo $supn["class"];?>"><?php echo $supn["titleprio"];?></span></td>
  <td><a href="<?php echo $supnparseurl;?>" class="btn btn-sm btn-secondary"><?php echo $jkl['hd13'];?></a></td>
</tr>
<?php } ?>
</table>
</div>
</div>

</div>
</div>
<?php } ?>