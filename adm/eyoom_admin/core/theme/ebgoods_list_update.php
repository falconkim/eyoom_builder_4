<?php
/**
 * @file    /adm/eyoom_admin/core/theme/ebgoods_list_update.php
 */
if (!defined('_EYOOM_IS_ADMIN_')) exit;

$sub_menu = "999500";

check_demo();

if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

if ($_POST['act_button'] == "선택수정") {

    auth_check($auth[$sub_menu], 'w');

    for ($i=0; $i<count($_POST['chk']); $i++) {

        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        $sql = " update {$g5['eyoom_goods']}
                    set eg_state = '{$_POST['eg_state'][$k]}'
                 where eg_no = '{$_POST['eg_no'][$k]}' and eg_theme = '{$_POST['theme']}' ";
        sql_query($sql);

        /**
         * EB상품추출 마스터 설정파일
         */
        unset($eg_master);
        $eg_master_file = G5_DATA_PATH . '/ebgoods/'.$_POST['theme'].'/eg_master_' . $_POST['eg_code'][$k] . '.php';
        include ($eg_master_file);
        $eg_master['eg_state'] = $_POST['eg_state'][$k];

        /**
         * 설정파일 저장
         */
        $qfile->save_file('eg_master', $eg_master_file, $eg_master);
    }
    $msg = "정상적으로 수정하였습니다.";

    if (!$page) $page = 1;
    $qstr = "page={$page}";

} else if ($_POST['act_button'] == "선택삭제") {

    auth_check($auth[$sub_menu], 'd');

    for ($i=0; $i<count($_POST['chk']); $i++) {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];
        $del_eg_no[$i] = $_POST['eg_no'][$k];
        $del_eg_code[$i] = $_POST['eg_code'][$k];
    }

    /**
     * 쿼리 조건문
     */
    $where = " find_in_set(eg_no, '".implode(',', $del_eg_no)."') and eg_theme = '{$_POST['theme']}' ";

    /**
     * EB상품추출 마스터 테이블 레코드 삭제
     */
    $sql = "delete from {$g5['eyoom_goods']} where {$where} ";
    sql_query($sql);

    /**
     * 쿼리 조건문
     */
    $where = " find_in_set(eg_code, '".implode(',', $del_eg_code)."') and gi_theme = '{$_POST['theme']}' ";

    /**
     * EB상품추출 아이템 레코드 삭제
     */
    $sql = "delete from {$g5['eyoom_goods_item']} where {$where} ";
    sql_query($sql);
    $msg = "선택한 EB상품추출을 삭제하였습니다.";
}

/**
 * qstr에 wmode추가
 */
$qstr .= $wmode ? '&amp;wmode=1': '';

alert($msg, G5_ADMIN_URL . '/?dir=theme&amp;pid=ebgoods_list&amp;'.$qstr);