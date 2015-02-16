<?php 
    require_once '../php/inc/header.inc.php';
    require_once 'order_report_common.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
$date_for_order=$_GET['date']; //'2014-12-18';
$detail = ((isset($_GET['detail'])?$_GET['detail']:'S') != 'N');
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo 'Fam-Prod '.$date_for_order;?></title>
	<link rel="stylesheet" type="text/css"               href="css/reports.css" />
  	<link rel="stylesheet" type="text/css" media="print" href="css/reports-print.css"/>
</head>
<body>
<div class="page-A4p">
<?php echo write_fam_prod($date_for_order, $detail); ?>
</div>
<?php
function write_fam_prod($date_for_order, $detail) {
    $db = DBWrap::get_instance();
    $strSQL = get_SQL($date_for_order).'uf.name, p.name';
    $rs = $db->Execute($strSQL);
    $brk = array(
        'date_for_order' => $date_for_order,
        '1_provider_id' => null,
        '1_order_id_n' => null,
        '1_desc_order' => null,
        '2_id_break' => null
    );
    $total_cost = 0;
    $total_amount = 0;
    $html = '';
    while ($row = $rs->fetch_array()) {
        if ($brk['1_provider_id'] != $row['provider_id'] || 
                    $brk['1_order_id'] != $row['order_id']) {
            // end previus order
            $html .= break2Html_end($brk, $detail);
            $html .= orderHtml_end($total_cost, $total_amount, $brk['1_desc_order']);
            // start order
            $total_cost = 0;
            $total_amount = 0;
            $html .= orderHtml_start($brk, $row);
        }
        if ($brk['2_id_break'] != $row['uf_id']) {
            $html .= break2Html_end($brk, $detail);
            // start uf
            $brk['2_id_break'] = $row['uf_id'];
            $sum_cost = get_sum($db, $brk, 
                'oi.uf_id = '.$brk['2_id_break'],
                'sum_cost'
            );
            $sum_amount = get_sum($db, $brk, 
                'oi.uf_id = '.$brk['2_id_break'],
                'sum_amount'
            );
            $html .= '<div class="block">';
            $html .= 
                '<h3 style="border-bottom: dotted 1px #777">'.
                    '<div class="cel10">'.
                            $row['uf_name'].'#'.$row['uf_id'].'</div>'.
                    '<div class="cel1h">&nbsp;</div>'.
                    '<div class="cel4 num lite">Final: <b>'.number_format($sum_amount, 2).'</b></div>'.
                    '<div class="cel3 num"><span class="lite">Cost: </span>'.number_format($sum_cost, 2).'</div>'.
                    '</h3>'.chr(10);
            if ($detail) {
                $html .= '<div class="block-body">'.chr(10);
            }
        }
        $cost = $row['cost_price']*$row['quantity'];
        $total_cost += $cost;
        $total_amount += $row['unit_price_stamp']*$row['quantity'];
        if ($detail) {
            $html .= '<div style="border-bottom: dotted 1px #777; padding:.05cm 0">';
            $html .= 
                '<div class="cel10">'.$row['p_name'].'</div>'.
                '<div class="cel2 lite num">'.$row['um_name'].'</div>'.
                '<div class="cel0hh num"></div>'.
                '<div class="cel1h num">'.
                        clean_zeros($row['quantity']).' x</div>'.
                '<div class="cel1h num">'.
                    number_format($row['cost_price'],2).'</div>'.
                '<div class="cel2 num">'.number_format($cost,2).'</div>';
            $html .= '</div>'.chr(10);
        }
    }
    $html .= break2Html_end($brk, $detail);
    $html .= orderHtml_end($total_cost, $total_amount, $brk['1_desc_order']);
    return $html;
}
?>
</body>
</html>
