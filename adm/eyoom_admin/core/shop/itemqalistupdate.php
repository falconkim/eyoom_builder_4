<?php
/**
 * @file    /adm/eyoom_admin/core/shop/itemqalist.php
 */
if (!defined('_EYOOM_IS_ADMIN_')) exit;

$sub_menu = "400660";

check_demo();

check_admin_token();

if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

if ($_POST['act_button'] == "선택삭제") {

    auth_check($auth[$sub_menu], 'd');

    for ($i=0; $i<count($_POST['chk']); $i++) {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];
        $iiq_id = (int) $_POST['iq_id'][$k];

        $sql = "delete from {$g5['g5_shop_item_qa_table']} where iq_id = '{$iiq_id}' ";
        sql_query($sql);
    }
}

// query string
$qstr .= $sdt ? '&amp;sdt='.$sdt: '';
$qstr .= $fr_date ? '&amp;fr_date='.$fr_date: '';
$qstr .= $to_date ? '&amp;to_date='.$to_date: '';
$qstr .= $cate_a ? '&amp;cate_a='.$cate_a: '';
$qstr .= $cate_b ? '&amp;cate_b='.$cate_b: '';
$qstr .= $cate_c ? '&amp;cate_c='.$$cate_c: '';
$qstr .= $cate_d ? '&amp;cate_d='.$cate_d: '';

alert("선택한 상품문의를 삭제처리하였습니다.", G5_ADMIN_URL . "/?dir=shop&amp;pid=itemqalist&amp;{$qstr}");