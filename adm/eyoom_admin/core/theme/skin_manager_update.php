<?php
/**
 * @file    /adm/eyoom_admin/core/theme/skin_manager_update.php
 */
if (!defined('_EYOOM_IS_ADMIN_')) exit;

auth_check($auth[$sub_menu], "w");

check_admin_token();

unset($theme);
$theme = clean_xss_tags(trim($_POST['theme']));

/**
 * $eyoom 변수파일 재정의
 */
unset($eyoom);
$eyoom_config_file = G5_DATA_PATH . '/eyoom.'.$theme.'.config.php';
include($eyoom_config_file);

foreach ($_POST as $key => $skin) {
    if ($key == 'token' || $key == 'theme') continue;
    $eyoom[$key] = $skin;
}

/**
 * 설정정보 업데이트
 */
$qfile->save_file('eyoom', $eyoom_config_file, $eyoom);
$msg = "정상적으로 스킨을 설정하였습니다.";
alert($msg, G5_ADMIN_URL.'/?dir=theme&amp;pid=skin_manager&amp;thema='.$theme.'&amp;wmode=1');