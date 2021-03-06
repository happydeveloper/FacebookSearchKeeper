<!DOCTYPE html>
<html lang="en">


<?php
//선언부
if (($loader = require_once 'vendor/autoload.php') == null)  {
  die('Vendor directory not found, Please run composer install.');
}

//OnLoad 초기 로드시 작업
require_once 'classes/basetaskfacebook.php';
require_once 'classes/fqlmanager.php';

?>

<?php
//호출부
$basetaskfacebook = new baseTaskFacebook();
 
if($basetaskfacebook->user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $basetaskfacebook->facebook->api('/me');
  } catch (FacebookApiException $e) {
   error_log($e);
    $basetaskfacebook->user = null;
  }
}


if($basetaskfacebook->user) {
   //Create Query
    fqlManager::getInstance();
    $params = array(
        'method' => 'fql.query',
        'query' => fqlManager::loadFql('MY_FRIENDS').$basetaskfacebook->user.")",
    );
 
    //Run Query
    $result = $basetaskfacebook->facebook->api($params);
}
?>	
<head>
    <?php
	require '_head.php';
    ?>
    <meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width-device-width">
    <title>FB_SEARCH</title>
    <style>
    </style>
  </head>
  <body>
<?php
	include_once 'nav.php';
?>

    <?php if ($basetaskfacebook->user): ?>
      <a href="<?php echo 'common/logout.php'; ?>">나가기</a>
    <?php else: ?>
      <div>
        <a href="<?php echo $basetaskfacebook->getUserState(); ?>">얼굴책 들어가기</a>
      </div>
    <?php endif ?>
    <hr />
    <?php
	if($basetaskfacebook->user) {
	foreach($result as $row){
	$name ="";
	$pic = ""; 
	$uid = "";
			foreach($row as $key=>$value){
				if($key == 'name') {
					$name = $value;
				}
				if($key == 'pic') {
					$pic = $value;
				}
				if($key == 'uid') {
					$uid = $value;
				}
			}
				echo "<a href='https://www.facebook.com/".$uid."'><img alt='".$name."' class='friends' src='".$pic."' ></a>";
	}
	}
	else {
		echo "<span>얼굴책 명령어 라인</span>";
	}

	if($basetaskfacebook->user){
		var_dump($basetaskfacebook->user);

        	echo  fqlManager::loadFql('MY_FRIENDS').$basetaskfacebook->user;
	}

    ?>
  </body>

</html>
