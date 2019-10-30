<?php
/**
 * @file    /adm/eyoom_admin/core/member/visit_list.php
 */
if (!defined('_EYOOM_IS_ADMIN_')) exit;

$sub_menu = "200800";

auth_check($auth[$sub_menu], 'r');

/**
 * 탭메뉴 활성화 구분자
 */
$visit_mode = 'visit_list';

include_once(EYOOM_ADMIN_CORE_PATH . '/member/visit.sub.php');

$sql_common = " from {$g5['visit_table']} ";
$sql_search = " where (1) ";

if ($fr_date && $to_date) {
    $sql_search .= " and vi_date between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
    $qstr .= "&amp;fr_date={$fr_date}&amp;to_date={$to_date}";
}

if (isset($domain))
    $sql_search .= " and vi_referer like '%{$domain}%' ";

$sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
            {$sql_common}
            {$sql_search}
            order by vi_id desc
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);

for ($i=0; $row=sql_fetch_array($result); $i++) {
    $brow = $row['vi_browser'];
    if(!$brow)
        $brow = eb_get_brow($row['vi_agent']);

    $os = $row['vi_os'];
    if(!$os)
        $os = eb_get_os($row['vi_agent']);

    $device = $row['vi_device'];

    $link = '';
    $link2 = '';
    $referer = '';
    $title = '';
    if ($row['vi_referer']) {

        $referer = get_text(cut_str($row['vi_referer'], 255, ''));
        $referer = urldecode($referer);

        if (!is_utf8($referer)) {
            $referer = iconv_utf8($referer);
        }

        $title = str_replace(array('<', '>', '&'), array("&lt;", "&gt;", "&amp;"), $referer);
        $link = "<a href='".get_text($row['vi_referer'])."' target='_blank'>";
        $link = str_replace('&', "&amp;", $link);
        $link2 = '</a>';
    }

    if ($is_admin == 'super')
        $ip = $row['vi_ip'];
    else
        $ip = preg_replace("/([0-9]+).([0-9]+).([0-9]+).([0-9]+)/", G5_IP_DISPLAY, $row['vi_ip']);

    if ($brow == '기타') { $brow = "<span title='".get_text($row['vi_agent'])."'>".$brow."</span>"; }
    if ($os == '기타') { $os = "<span title='".get_text($row['vi_agent'])."'>".$os."</span>"; }

    $list[$i] = $row;
    $list[$i]['ip'] = $ip;
    $list[$i]['link'] = $link;
    $list[$i]['title'] = $title;
    $list[$i]['link2'] = $link2;
    $list[$i]['brow'] = $brow;
    $list[$i]['os'] = $os;
    $list[$i]['device'] = $device;
}

if (isset($domain))
    $qstr .= "&amp;domain=$domain";
$qstr .= "&amp;page=";

/**
 * 페이징
 */
$paging = $eb->set_paging('./?dir=member&amp;pid=visit_list&amp;'.$qstr.'&amp;page=');