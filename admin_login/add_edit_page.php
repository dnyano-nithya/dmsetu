<?php
session_start();
include'dbconnection.php';
require("libs/config.php");

$msg = '';
if (isset($_POST["sub"])) {


    $page_id = db_prepare_input($_POST["page_id"]);
    $page_title = db_prepare_input($_POST["page_title"]);
    $page_desc = db_prepare_input($_POST["page_desc"]);
    $meta_keywords = db_prepare_input($_POST["meta_keywords"]);
    $meta_desc = db_prepare_input($_POST["meta_desc"]);
    $sort_order = (int) db_prepare_input($_POST["sort_order"]);
    $parent = (int) db_prepare_input($_POST["parent"]);
    $status = db_prepare_input($_POST["status"]);
    $page_alias = db_prepare_input($_POST["page_alias"]);

    $status = ($status <> "") ? $status : "I";


    if ($page_title <> "" && $status <> "" && $parent <> "" && $page_alias <> "") {


        if ($page_id <> "") {
		
		
            $sql = "UPDATE " . TABLE_PAGES . " SET  `page_title` =  :pt, "
                    . " `page_desc` =  :pdsc, meta_keywords = :mkey, "
                    . " `meta_desc` =  :mdesc, `sort_order` =  :so,"
                    . " `parent` = :parent, `status` =  :status,"
                    . " `page_alias` =  :palias "
                    . " WHERE `page_id` = :pid";
            
            
            try {
                $stmt = $DB->prepare($sql);
                $stmt->bindValue(":pt", $page_title);
                $stmt->bindValue(":pdsc", $page_desc);
                $stmt->bindValue(":mkey", $meta_keywords);
                $stmt->bindValue(":mdesc", $meta_desc);
                $stmt->bindValue(":parent", $parent);
                $stmt->bindValue(":so", $sort_order);
                $stmt->bindValue(":status", $status);
                $stmt->bindValue(":palias", $page_alias);
                 $stmt->bindValue(":pid", $page_id);
				 
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $msg = successMessage("Page update successfully");
                }  else if ($stmt->rowCount() == 0) {
                    $msg = successMessage("No changes affected");
                } else {
                    $msg = errorMessage("Failed to update page");
                }
            } catch (Exception $ex) {
                echo errorMessage($ex->getMessage());
            }
            
        } else {
             $sql = "INSERT INTO " . TABLE_PAGES . " (`page_title`, `page_desc`, `meta_keywords`, `meta_desc`, `parent`, `sort_order`, `status`, `page_alias`) VALUES 
				(:pt, :pdsc, :mkey, :mdesc, :parent, :so, :status, :palias)";

            try {
                $stmt = $DB->prepare($sql);
                $stmt->bindValue(":pt", $page_title);
                $stmt->bindValue(":pdsc", $page_desc);
                $stmt->bindValue(":mkey", $meta_keywords);
                $stmt->bindValue(":mdesc", $meta_desc);
                $stmt->bindValue(":parent", $parent);
                $stmt->bindValue(":so", $sort_order);
                $stmt->bindValue(":status", $status);
                $stmt->bindValue(":palias", $page_alias);
               
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $msg = successMessage("Page added successfully");
                } else if ($stmt->rowCount() == 0) {
                    $msg = successMessage("No changes affected");
                } else {
                    $msg = errorMessage("Failed to add page");
                }
            } catch (Exception $ex) {
                echo errorMessage($ex->getMessage());
            }

          
        }
    } else {
        $msg = errorMessage("All fields are mandatory");
    }
}

if (isset($_GET["edit"]) && $_GET["edit"] != "") {
    $pageTitle = "Edit Page";

    try {
        $stmt = $DB->prepare("SELECT * FROM " . TABLE_PAGES . " WHERE `page_id` = :pid");
        $stmt->bindValue(":pid", intval(db_prepare_input($_GET["edit"])));
        $stmt->execute();
        $details = $stmt->fetchAll();
    } catch (Exception $ex) {
        echo errorMessage($ex->getMessage());
    }
} else {
    $pageTitle = "Add Page";
}

$sql = "SELECT * FROM " . TABLE_PAGES . " WHERE status = 'A' AND parent = -1 ORDER BY page_title ASC";


try {
    $stmt = $DB->prepare($sql);
    $stmt->execute();
    $optionsRs = $stmt->fetchAll();
} catch (Exception $ex) {
    echo errorMessage($ex->getMessage());
}
?>  
<link rel="stylesheet" type="text/css" href="CLEditor/jquery.cleditor.css" />
<script type="text/javascript" src="js/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="CLEditor/jquery.cleditor.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#page_desc").cleditor();
    });
    function changeAlias() {
        var title = $.trim($("#page_title").val());
        title = title.replace(/[^a-zA-Z0-9-]+/g, '-');
        $("#page_alias").val(title.toLowerCase());
    }
			
</script> 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="description" content="true">
	<!--<meta name="description" content="">-->
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

    <title>Admin | Dashboard </title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">
  </head>

  <body>

  <section id="container" >
      <header class="header black-bg">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
              </div>
            <a href="#" class="logo"><b>Admin Dashboard</b></a>
            <div class="nav notify-row" id="top_menu">
               
                         
                   
                </ul>
            </div>
            <div class="top-menu">
            	<ul class="nav pull-right top-menu">
				    <li><button style="font-size:18px;margin-top:15px;margin-right:16px;height:30px;" onclick="window.location.href='website-settings.php'"><i class="fa fa-gear"></i></button></li>
				    <li><button style="font-size:18px;margin-top:15px;margin-right:16px;height:30px;" onclick="window.location.href='../index.php'">
					<i class="fa fa-home"></i></button></li>
                    <li><a class="logout" href="logout.php">Logout</a></li>
            	</ul>
            </div>
        </header>
      <aside>
          <div id="sidebar"  class="nav-collapse ">
              <ul class="sidebar-menu" id="nav-accordion">
              
              	  <p class="centered"><a href="#"><img src="assets/img/admin-icon.jpg" class="img-circle" width="60"></a></p>
              	  <h5 class="centered"><?php echo $_SESSION['login'];?></h5>
				  
				  <li class="mt">
                      <a href="admin-dashboard.php">
                          <i class="fa fa-dashboard"></i>
                          <span>Dashboard</span>
                      </a>
                  </li>
              	  	
                  <li class="sub-menu">
                      <a href="change-password.php">
                          <i class="fa fa-file"></i>
                          <span>Change Password</span>
                      </a>
                  </li>

                  <li class="sub-menu">
                      <a href="manage-users.php" >
                          <i class="fa fa-users"></i>
                          <span>Manage Users</span>
                      </a>
                   
                  </li>
				  
				  <li class="sub-menu">
                      <a href="manage-pages.php" >
                          <i class="fa fa-file-code-o"></i>
                          <span>Manage Pages</span>
                      </a>
                   
                  </li>
				  
				  <li class="sub-menu">
                      <a href="manage-event-gallery.php" >
                          <i class="fa fa-users"></i>
                          <span>Event gallery</span>
                      </a>
                   
                  </li>
				  
				  <li class="sub-menu">
                      <a href="all-contacts.php" >
                          <i class="fa fa-users"></i>
                          <span>Manage contacts</span>
                      </a>
                   
                  </li>
              
              
                 
                 
              </ul>
          </div>
      </aside>
      <section id="main-content">
          <section class="wrapper">
          	<h3><i class="fa fa-angle-right"></i>Add Pages</h3>
				<div class="row">
				
				<div class="col-md-12">
                      <div class="content-panel">
                          
	                  	  	  
							  <?php echo $msg; ?>
							    
                                     
                                         <p align="center" style="color:#F00;">
										 <form class="form-horizontal style-form" style="margin-left: 10px;" name="pages" method="post" action="" >
										 <div class="form-group">    
                                            <input type="hidden" name="page_id" value="<?php echo $details[0]["page_id"]; ?>"  />
                                            <div class="form-group">
                                                <label class="col-sm-2 col-sm-2 control-label" ><span class="required">*</span>Title: </label>
                                                <div class="col-sm-10">
												    <input type="text" name="page_title" id="page_title" style="width:96%;" class="form-control" value="<?php echo stripslashes($details[0]["page_title"]); ?>" autocomplete="off"  onkeyup="changeAlias();"  />
                                                </div>
											</div>
											
											<div class="form-group">
                                                <label class="col-sm-2 col-sm-2 control-label" ><span class="required">*</span>Parent: </label>
                                                    <div class="col-sm-10">
                                                        <select name="parent">
                                                            <option value="-1">-Please Select-</option>
                                                                <?php
                                                                    foreach ($optionsRs as $rs) {
                                                                ?>
                                                                <option value="<?php echo stripslashes($rs["page_id"]); ?>" <?php echo ($details[0]["parent"] == $rs["page_id"]) ? 'selected="selected"' : ''; ?>  >
                                                                <?php echo stripslashes($rs["page_title"]); ?>
                                                            </option>
                                                                <?php
                                                                    }
                                                                ?>
                                                        </select>
                                                    </div>
											</div>
											
									        <div class="form-group">
                                                <label class="col-sm-2 col-sm-2 control-label" ><span class="required">*</span>Page Alias: </label>
									                <div class="col-sm-10">
                                                        <input type="text" name="page_alias" id="page_alias" style="width:96%;" class="form-control"  value="<?php echo stripslashes($details[0]["page_alias"]); ?>" /> (must be unique)</td>
                                                    </div>
									        </div>
                                    
									        <div class="form-group">
                                                <label class="col-sm-2 col-sm-2 control-label" >Description: </label> 
                                                    <div class="col-sm-10">
                                                        <textarea name="page_desc" class="form-control" style="width:96%;" id="page_desc"><?php echo stripslashes($details[0]["page_desc"]); ?></textarea>
                                                    </div>
                                            </div>
                                    
									        <div class="form-group">
                                                <label class="col-sm-2 col-sm-2 control-label" >Meta Keywords: </label> 
										            <div class="col-sm-10">
                                                        <input type="text" name="meta_keywords" style="width:96%;"  class="form-control" value="<?php echo stripslashes($details[0]["meta_keywords"]); ?>" /> 
                                                    </div>
									        </div>
                                    
                                            <div class="form-group">
									            <label class="col-sm-2 col-sm-2 control-label" >Meta Description: </label>
										            <div class="col-sm-10">
                                                        <input type="text" name="meta_desc" style="width:96%;" class="form-control" value="<?php echo stripslashes($details[0]["meta_desc"]); ?>" /> </td>
                                                    </div>
									        </div>
                                    
									        <div class="form-group">
                                                <label class="col-sm-2 col-sm-2 control-label" >Sort Order: </label>
										            <div class="col-sm-10"> 
                                                        <input type="text" name="sort_order" style="width:96%;" class="form-control"  placeholder="0" value="<?php echo stripslashes($details[0]["sort_order"]); ?>" /> </td>
                                                    </div>
									        </div>
									
									
                                            <div class="form-group">
                                                <label class="col-sm-2 col-sm-2 control-label" ><span class="required">*</span>Status : </label>&nbsp;&nbsp;&nbsp;
                                                    <div class="col-sm-10"> 
													    <?php if (isset($_REQUEST["edit"]) && $_REQUEST["edit"] != "") { ?>
                                                            <label><input type="radio" name="status" value="A" <?php echo ($details[0]["status"] == 'A') ? 'checked' : ''; ?>  />Active</label> &nbsp; 
                                                            <label><input type="radio" name="status" value="I" <?php echo ($details[0]["status"] == 'I') ? 'checked' : ''; ?>  />Inactive</label>
                                                        <?php } else { ?>
                                                            <label><input type="radio" name="status" value="A" checked  />Active</label> &nbsp; <label><input type="radio" name="status" value="I"  />Inactive</label>
                                                        <?php } ?>
									                </div>
											</div>


                                            
                                            
                                            <input type="submit" name="sub" class="btn btn-theme" value="Save" /> &nbsp;  <input type="button" name="" onclick="javascript:window.location = 'manage_pages.php';" class="btn btn-theme" value="back to lists" /> </td>
                                    </form>
</div>
<script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js"></script>
    <script src="assets/js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="assets/js/common-scripts.js"></script>
  <script>
      $(function(){
          $('select.styled').customSelect();
      });

  </script>
  </body>
</html>
  

<?php
/*include("footer.php");*/
?>
