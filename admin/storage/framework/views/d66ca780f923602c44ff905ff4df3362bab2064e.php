<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>代理后台登录</title>

  <!-- Custom fonts for this template-->
  <link href="/agent/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Custom styles for this template-->
  <link href="/agent/css/sb-admin-2.min.css" rel="stylesheet">
  
  <style>
      #captcha {
          display: flex;
          align-items:center;
          justify-content: space-between;
      }
  </style>

</head>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-6 col-lg-6 col-md-6 mt-4">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
              <!-- <div class="col-lg-6 d-none d-lg-block bg-login-image"></div> -->
              <div class="col-lg-12">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">代理后台管理</h1>
                  </div>
                  <form class="user" method="post" action="<?php echo e(url('/login'), false); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" name="name" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="请输入账号" required>
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" name="password" id="exampleInputPassword" placeholder="请输入密码" required>
                    </div>
                    <div class="form-group" id="captcha">
                      <input type="text" class="form-control form-control-user" style="width:300px;" name="captcha" aria-describedby="emailHelp" placeholder="请输入验证码" required>
                      <a onclick="javascript:re_captcha();" ><img src="<?php echo e(URL('kit/captcha/1'), false); ?>"  alt="验证码" title="刷新图片" width="100" height="40" id="c2c98f0de5a04167a9e427d883690ff6" border="0"></a>
                    </div>
                    <!--<input type="text" name="captcha" class="form-control" style="width: 300px;">-->
          
                    <!-- <div class="form-group">
                      <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="customCheck" name="remember_me" value="1">
                        <label class="custom-control-label" for="customCheck">记住账号密码</label>
                      </div>
                    </div> -->
                    <button class="btn btn-primary btn-user btn-block">
                        登录
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="/agent/vendor/jquery/jquery.min.js"></script>
  <script src="/agent/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="/agent/vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="/agent/js/sb-admin-2.min.js"></script>

  <?php if(session('opMsg')): ?>
  <script>
      var msg = "<?php echo e(session('opMsg'), false); ?>";
      alert(msg);
  </script>
  <?php endif; ?>
  
  <script>  
  function re_captcha() {
    $url = "<?php echo e(URL('kit/captcha'), false); ?>";
        $url = $url + "/" + Math.random();
        document.getElementById('c2c98f0de5a04167a9e427d883690ff6').src=$url;
  }
</script>

</body>

</html>
<?php /**PATH D:\www\xingyun\admin\resources\views/agent/auth/login.blade.php ENDPATH**/ ?>