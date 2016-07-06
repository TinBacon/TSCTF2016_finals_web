<?php if(!class_exists("View", false)) exit("no direct access allowed");?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>个人资料 - <?php echo htmlspecialchars($common['site_name'], ENT_QUOTES, "UTF-8"); ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo htmlspecialchars($common['theme'], ENT_QUOTES, "UTF-8"); ?>/style/general.css" />
<link rel="stylesheet" type="text/css" href="<?php echo htmlspecialchars($common['theme'], ENT_QUOTES, "UTF-8"); ?>/style/user.css" />
<link rel="stylesheet" type="text/css" href="<?php echo htmlspecialchars($common['theme'], ENT_QUOTES, "UTF-8"); ?>/style/jquery.Jcrop.min.css" />
<script type="text/javascript" src="<?php echo htmlspecialchars($common['theme'], ENT_QUOTES, "UTF-8"); ?>/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars($common['theme'], ENT_QUOTES, "UTF-8"); ?>/js/general.js"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars($common['theme'], ENT_QUOTES, "UTF-8"); ?>/js/juicer.js"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars($common['theme'], ENT_QUOTES, "UTF-8"); ?>/js/jquery.Jcrop.min.js"></script>
<script type="text/javascript">
var jcrop_obj;

function uploadAvatar(){
  $('#upload-avatar-btn').removeClass('sm-blue').addClass('sm-gray').text('正在上传...');
  $('#avatar-form').submit();
}

function showCrop(status, data){
  if(status == 1){
  $('#target-avatar').attr('src', data).load(function(){
    $('#crop').css({ 
      left: ($(window).width() - $('#crop').width()) / 2,
      top: ($(window).height() - $('#crop').height()) /2,
    }).removeClass('hide');
    masker('show');
    $('#target-avatar').Jcrop({
      aspectRatio: 1,
      onSelect: function(c){
        $('#avatar-x').val(c.x);$('#avatar-y').val(c.y);
        $('#avatar-w').val(c.w);$('#avatar-h').val(c.h);
      }
    }, function(){jcrop_obj = this;});
    });
  }else if(status == 0){
    alert('上传头像失败...');
    $('#upload-avatar-btn').removeClass('sm-gray').addClass('sm-blue').text('上传头像图片');
  }else {
    alert('您还没有登陆或登录超时');
    window.location.href = "<?php echo url(array('c'=>'user', 'a'=>'login', ));?>";
  }
}

function saveCrop(){
  var streams = $('#target-avatar').attr('src').split(';base64,'),
      mime = streams[0].replace('data:', '');
      
  $.ajax({
    type: "post",
    dataType: "json",
    url: "<?php echo url(array('c'=>'user', 'a'=>'avatar', 'step'=>'crop', ));?>",
    data: {"streams":streams[1], "mime":mime, "x":$('#avatar-x').val(), "y":$('#avatar-y').val(), "w":$('#avatar-w').val(), "h":$('#avatar-h').val()},
    beforeSend: function(){
      $('#save-avatar-btn').removeClass('sm-green').addClass('sm-gray').text('正在剪切头像...');
    },
    success: function(res){
      if(res.status == 1){
        $('#user-avatar').attr('src', hostUrl+'/upload/user/avatar/'+res.avatar+'?'+Math.random());
        closeCrop();
      }else if(res.status == 0){
        alert('保存头像失败...');
        $('#save-avatar-btn').removeClass('sm-gray').addClass('sm-green').text('保存头像');
      } else {
        alert('您还没有登陆或登录超时');
        window.location.href = "<?php echo url(array('c'=>'user', 'a'=>'login', ));?>";
      }
    },
    error: function(){alert('处理请时发生错误...');$('#save-avatar-btn').removeClass('sm-gray').addClass('sm-green').text('保存头像');}
  });
}

function closeCrop(){
  masker('hide');
  $('#target-wrapper').find('img').remove();
  jcrop_obj.destroy();
  $('#crop').addClass('hide');
  $('#upload-avatar-btn').removeClass('sm-gray').addClass('sm-blue').text('上传头像图片');
  $('#save-avatar-btn').removeClass('sm-gray').addClass('sm-green').text('保存头像');
}

function updateProfile(e){
  $('#name').vdsChecker({required:true, maxlength:60});
  $('#mobile_no').vdsChecker({mobile:true}, {mobile:'手机号码格式不正确'}, {mobile:/^$|^1[3|4|5|8]\d{9}$/.test($('#mobile_no').val())});
  $('#qq').vdsChecker({format:true}, {format:'QQ号码格式不正确'}, {format:/^$|^[1-9][0-9]{4,12}$/.test($('#qq').val())});
  $('#signature').vdsChecker({maxlength:240});
  $(e).text(function(i, t){
    $(e).text('正在更新...');
    if(false == $('#profile-form').vdsSubmit(true)) $(e).text(t);
  });
}
</script>
</head>
<body>
<!-- 顶部开始 -->
<?php echo layout_topper(array('common'=>$common, ));?>
<!-- 顶部结束 -->
<!-- 头部开始 -->
<?php echo layout_header(array('common'=>$common, ));?>
<!-- 头部结束 -->
<div class="loc w1100">
  <div><a href="<?php echo htmlspecialchars($common['url'], ENT_QUOTES, "UTF-8"); ?>">网站首页</a><b>></b><font>用户面板</font></div>
</div>
<!-- 主体开始 -->
<div class="container w1100 mt10">
  <div class="module cut">
    <!-- 左侧开始 -->
    <div class="w180 fl cut">
      <!-- 用户菜单开始 -->
      <?php echo layout_usermenu();?>
      <!-- 用户菜单结束 -->
    </div>
    <!-- 左侧结束 -->
    <!-- 右侧开始 -->
    <div class="w910">
      <div class="profile mcter">
        <h2>个人资料</h2>
        <div class="form cut">
          <dl class="avatar">
            <?php if (!empty($profile['avatar'])) : ?>
            <dt><img id="user-avatar" src="<?php echo htmlspecialchars($common['url'], ENT_QUOTES, "UTF-8"); ?>/upload/user/avatar/<?php echo htmlspecialchars($profile['avatar'], ENT_QUOTES, "UTF-8"); ?>" /></dt>
            <?php else : ?>
            <dt><img id="user-avatar" src="<?php echo htmlspecialchars($common['theme'], ENT_QUOTES, "UTF-8"); ?>/images/no-avatar.gif" /></dt>
            <?php endif; ?>
            <dd>
              <p class="c666">我的头像</p>
              <div class="browsefile">
                <form id="avatar-form" action="<?php echo url(array('c'=>'user', 'a'=>'avatar', 'step'=>'upload', ));?>" enctype="multipart/form-data" method="post" target="uploadframe">
                  <button type="button" class="sm-blue" id="upload-avatar-btn">上传头像图片</button>
                  <input id="avatar-file" name="avatar_file" class="upavatar" onChange="uploadAvatar()" type="file" />
                  <input type="hidden" name="callback_func" value="showCrop" />
                  <!-- 回调函数方法名 -->
                </form>
              </div>
              <iframe id="uploadframe" name="uploadframe" class="hide"></iframe>
            </dd>
          </dl>
          <!-- 剪裁头像区开始 -->
          <div class="avatar-crop hide" id="crop">
            <h3><button class="fr" type="button" onclick="closeCrop()">x</button>上传头像图片</h3>
            <div class="aln-c"><img id="target-avatar" src="" /></div>
            <div class="save"><button type="button" class="sm-green" id="save-avatar-btn" onclick="saveCrop()">保存头像</button></div>
            <input type="hidden" id="avatar-x" value="0" />
            <input type="hidden" id="avatar-y" value="0" />
            <input type="hidden" id="avatar-w" value="300" />
            <input type="hidden" id="avatar-h" value="300" />
          </div>
          <!-- 剪裁头像区结束 -->
          <form method="post" action="<?php echo url(array('c'=>'user', 'a'=>'profile', 'step'=>'update', ));?>" id="profile-form">
            <dl class="mt30">
              <dt><label for="name">姓名称呼：</label></dt>
              <dd><input title="姓名称呼" name="name" id="name" class="txt" type="text" value="<?php echo htmlspecialchars($profile['name'], ENT_QUOTES, "UTF-8"); ?>" /></dd>
            </dl>
            <dl>
              <dt><label for="mobileno">手机号码：</label></dt>
              <dd><input title="手机号码" name="mobile_no" id="mobile_no" class="txt" type="text" value="<?php echo htmlspecialchars($profile['mobile_no'], ENT_QUOTES, "UTF-8"); ?>" /></dd>
            </dl>
            <dl>
              <dt><label for="qq">QQ：</label></dt>
              <dd><input title="QQ" name="qq" id="qq" class="txt" type="text" value="<?php echo htmlspecialchars($profile['qq'], ENT_QUOTES, "UTF-8"); ?>" /></dd>
            </dl>
            <dl>
              <dt><label>性别：</label></dt>
              <dd class="gender">
                <label><input name="gender" type="radio" value="0"<?php if ($profile['gender'] == 0) : ?>checked="checked"<?php endif; ?> /><font class="ml5">保密</font></label>
                <label class="ml10"><input name="gender" type="radio" value="1"<?php if ($profile['gender'] == 1) : ?>checked="checked"<?php endif; ?> /><font class="ml5">男</font></label>
                <label class="ml10"><input name="gender" type="radio"  value="2"<?php if ($profile['gender'] == 2) : ?>checked="checked"<?php endif; ?> /><font class="ml5">女</font></label>
              </dd>
            </dl>
            <dl>
              <dt><label>生日：</label></dt>
              <dd>
                <select class="slt" name="birth_year" id="birth_year">
                  <?php echo html_date_options(array('type'=>"year", 'default'=>$profile['birth_year'], ));?>
                </select>
                <select class="slt" name="birth_month" id="birth_month">
                  <?php echo html_date_options(array('type'=>"month", 'default'=>$profile['birth_month'], ));?>
                </select>
                <select class="slt" name="birth_day" id="birth_day">
                  <?php echo html_date_options(array('type'=>"day", 'default'=>$profile['birth_day'], ));?>
                </select>
              </dd>
            </dl>
            <dl>
              <dt><label for="signature">个性签名：</label></dt>
              <dd><textarea title="个性签名" name="signature" id="signature" cols="60" rows="4" placeholder="个性签名不能超过100个字节"><?php echo htmlspecialchars($profile['signature'], ENT_QUOTES, "UTF-8"); ?></textarea></dd>
            </dl>
            <div class="profile-btn mt30"><button type="button" class="sm-green" onclick="updateProfile(this)">更新资料</button></div>
          </form>
        </div>
      </div>
    </div>
    <!-- 右侧结束 -->
  </div>
  <div class="cl"></div>
  <?php echo layout_helper();?>
</div>
<!-- 主体结束 -->
<div class="cl"></div>
<!-- 页脚开始 -->
<?php echo layout_footer();?>
<!-- 页脚结束 -->
</body>
</html>