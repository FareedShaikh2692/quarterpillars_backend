<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<style>
    body {
  background: #eee;
}

.bgWhite {
    border-radius: 10px;
  background: white;
  box-shadow: 0px 3px 6px 0px #cacaca;
}

.title {
  font-weight: 600;
  margin-top: 20px;
  font-size: 24px;
}

.customBtn {
  border-radius: 0px;
  padding: 10px;
}

form input {
  display: inline-block;
  width: 50px;
  height: 50px;
  text-align: center;
}
.card-img{
    height: 100px;
    width: 100%;
}
.footer{
    font-weight: bold;
}
</style>
<div class="container">
  <div class="row justify-content-md-center">
      <div class="col-md-4 text-center">
        <div class="row">
          <div class="col-sm-12 mt-5 bgWhite">
            <div class="card-img">
                <img src="http://qp.flymingotech.in/quarterpillars_backend/public/qp_logo.jpeg" class="card-img-top" alt="...">
            </div>
            <div class="title">
                Verify your email address
            </div>
            <br>
            <p>Welcome to quarterpillars</p>
            <p>Below are the OTP for email verify </p>
            <h2>{{ $otp }}</h2>
            <hr class="mt-4">
            <p class="footer">quarterpillars.com</p>
          </div>
        </div>
      </div>
  </div>
</div>