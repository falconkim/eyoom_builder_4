<?php
/**
 * @file    /adm/eyoom_admin/core/theme/ebcontents_itemlist_update.php
 */
if (!defined('_EYOOM_IS_ADMIN_')) exit;

$sub_menu = "999610";

check_demo();

if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

if ($_POST['act_button'] == "선택수정") {

    auth_check($auth[$sub_menu], 'w');

    for ($i=0; $i<count($_POST['chk']); $i++) {

        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        $sql = " update {$g5['eyoom_contents_item']}
                    set ci_sort = '{$_POST['ci_sort'][$k]}',
                        ci_state = '{$_POST['ci_state'][$k]}',
                        ci_view_level = '{$_POST['ci_view_level'][$k]}'
                 where ci_no = '{$_POST['ci_no'][$k]}' and ci_theme = '{$_POST['theme']}' ";
        sql_query($sql);
    }
    $msg = "정상적으로 수정하였습니다.";

    if (!$page) $page = 1;
    $qstr = "page={$page}";

} else if ($_POST['act_button'] == "선택삭제") {

    auth_check($auth[$sub_menu], 'd');

    for ($i=0; $i<count($_POST['chk']); $i++) {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];
        $del_ci_no[$i] = $_POST['ci_no'][$k];
    }

    /**
     * 쿼리 조건문
     */
    $where = " find_in_set(ci_no, '".implode(',', $del_ci_no)."') and ci_theme = '{$_POST['theme']}' ";

    /**
     * EB 슬라이더 아이템 파일 경로
     */
    $ebcontents_folder = G5_DATA_PATH.'/ebcontents/' . $_POST['theme'];

    $sql = "select ci_img from {$g5['eyoom_contents_item']} where {$where}";
    $res = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($res); $i++) {
        $ci_file = $ebcontents_folder . "/{$row['ci_img']}";
        if (!is_dir($ci_file) && file_exists($ci_file)) {
            @unlink($ci_file);
        }
    }

    /**
     * EB슬라이더 아이템 레코드 삭제
     */
    $sql = "delete from {$g5['eyoom_contents_item']} where {$where} ";
    sql_query($sql);
    $msg = "선택한 EB슬라이더의 아이템을 삭제하였습니다.";
}

/**
 * 설정된 정보를 파일로 저장 - 캐쉬 기능
 */
$thema->save_ebcontents_item($_POST['ec_code'] , $_POST['theme']);


alert($msg, G5_ADMIN_URL . "/?dir=theme&amp;pid=ebcontents_itemlist&amp;ec_code={$_POST['ec_code']}&amp;thema='{$_POST['theme']}'&amp;w=u&amp;wmode=1");