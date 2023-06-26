<?php include('task.php');?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>TM</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="roll_no_001.js"></script>
<style>
/* Style for positioning toast */
.toast{
    position: absolute; 
    top: 10px; 
    right: 10px;
}
.error_input:focus{
  border-color: transparent;
}
.error_input{
  border-color: red;
    outline: 0;
}
</style>
</head>
<body>
<div id="app">

<?php
if(!isset($_SESSION['user_id']))
{       
  ?>
  <div class="container">
    <div class="row">
    <div class="col-lg-4 col-sm-12">
    </div>
      <div class="col-lg-4 col-sm-12">
          <form id="login" action="login_user">
              <div class="form-group">
                  <label for="email">Email address:</label>
                  <input type="email" class="form-control" id="user_email">
              </div>
              <div class="form-group">
                  <label for="pwd">Password:</label>
                  <input type="password" class="form-control" id="user_password">
              </div>
              <button type="submit" class="btn btn-default" id="submitter">Submit</button>
          </form>
      </div>
      <div class="col-lg-4 col-sm-12">
      </div>
    </div>
  </div>
  <?php  
}else{
?>
  <h2>Dashboard</h2>
  <a class="btn btn-primary" href="task.php?action=logout">Logout</a>
<?php
}
?>
<div class="toast" id="notifyToast" data-bs-autohide="true" data-bs-delay="3000">
    <div class="toast-header">
        <strong class="me-auto"><i class="bi bi-bug-fill" id="res-head"> </i> </strong>
        <small style="color: red">Alert</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body" id="res-bdy"></a>
</div>
</div>

</body>
</html>
