<?php
include_once('../components/footer.php');
include_once('../components/header.php');

make_header([], ["../styles/registerpage.css"]); ?>
<div class="container-xl">
  <form action="" class=".form-inline">
    <div class="row justify-content-center">
      <div class="col-sm-6">
        <h1 class="text-center form-title">Register</h1>
      </div>
    </div>
    <!--  -->
    <div class="row justify-content-center">
      <img src="../assets/user.png" class="mx-auto d-block img-fluid rounded-circle border border-dark rounded" alt="User Image" id="user-img">

    </div>
    <!-- -->
    <div class="row form-group justify-content-center">
      <div class="col-sm-6">
        <input type="text" name="username" id="username" class="form-control registerinput" placeholder="Username" aria-describedby="helpUser">
        <label for="password"></label>
        <input type="email" name="email" id="email" class="form-control registerinput" placeholder="Email" aria-describedby="helpId">
        <label for="password"></label>
        <input type="text" name="address" id="address" class="form-control registerinput" placeholder="Address" aria-describedby="helpId">
      </div>
    </div>
    <!--  -->
    <div class="row justify-content-center">
      <div class="col-sm-6">
        <h4 id="birthday">Birthday</h4>
      </div>
    </div>
    <!--  -->
    <div class="row form-group justify-content-center birthday">
      <div class="col-sm-2 ">
        <select class="custom-select custom-select-sm registerinput registerSelect" name="day" id="day">
          <option selected class="text-muted optionplaceholder" hidden>Day</option>
          <option value="">1</option>
          <option value="">2</option>
          <option value="">3</option>
        </select>
      </div>
      <div class="col-sm-2 ">
        <select class="custom-select custom-select-sm registerinput registerSelect" name="month" id="month">
          <option selected class="text-muted optionplaceholder" hidden>Month</option>
          <option value="">January</option>
          <option value="">February</option>
          <option value="">December</option>
        </select>
      </div>
      <div class="col-sm-2 ">
        <select class="custom-select custom-select-sm registerinput registerSelect" name="year" id="year">
          <option selected class="text-muted optionplaceholder" hidden>Year</option>
          <option value="">1999</option>
          <option value="">2000</option>
          <option value="">2001</option>
        </select>
      </div>

    </div>
    <!--  -->
    <div class="form-group row justify-content-center">
      <div class="col-sm-6">
        <input type="password" name="password" id="password" class="form-control registerinput" placeholder="Password" aria-describedby="helpId">
      </div>
    </div>
    <!--  -->
    <div class="row justify-content-center">
      <div class="col-sm-6 ">
        <button type="submit" class="btn btn-primary col-sm-12" id="submitbutton">Register</button>
      </div>
    </div>
  </form>
</div>
<?php
make_footer();
?>