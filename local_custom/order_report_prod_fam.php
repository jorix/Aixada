<?php 
    require_once '../php/inc/header.inc.php';
    require_once 'order_report_common.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
$date_for_order = get_param_date('date'); //'2014-12-18';
$detail = ( get_param('detail', 'S') !== 'N' );
$order_id = get_param_int('order_id');
if ($order_id === null) {
    $order_id = get_param_int('id');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo 'Prod-Fam '.
        ( $order_id !== null ? 
            "order_id=".$order_id :
            ($date_for_order !== null ? $date_for_order : '(falta data)') ).
        ($detail?'':' summary');?></title>
    <link rel="stylesheet" type="text/css"               href="css/reports_paper.css"/>
    <link rel="stylesheet" type="text/css"               href="css/reports_layout.css"/>
    <link rel="stylesheet" type="text/css" media="print" href="css/reports-print.css"/>
</head>
<body>
<div class="page-A4p">
<?php write_orders_prod($date_for_order, null, $order_id, $detail); ?>
</div>
<?php
function write_orders_prod($date_for_order, $provider_id, $order_id, $detail) {
    $db = DBWrap::get_instance();
    
    $strSQL = getSumaryOrders_SQL($date_for_order, $provider_id, $order_id);
    $strSQL .= 'pv_name, order_id';
    $rs = $db->Execute($strSQL);
    while ($row = $rs->fetch_array()) {
        $row_pv = get_row_query(
            "SELECT notes FROM aixada_provider where id={$row['provider_id']}");
        if (!is_null($row_pv['notes']) && $row_pv['notes'] === "FAM" && $detail) {
            write_order_prod_fam(
                $row['date_for_order'], 
                $row['provider_id'], 
                $row['order_id'],
                false, true, false
            );
            write_order_fam_prod(
                $row['date_for_order'], 
                $row['provider_id'], 
                $row['order_id'],
                $detail, false, true
            );			
        } else {
            write_order_prod_fam(
                $row['date_for_order'], 
                $row['provider_id'], 
                $row['order_id'],
                $detail
            );
        }
    }
}
function write_order_prod_fam($date_for_order, $provider_id, $order_id, $detail,
        $head = true, $foot = true) {
    $db = DBWrap::get_instance();
    
    $strSQL = get_SQL($date_for_order, $provider_id, $order_id);
    $cfg = configuration_vars::get_instance();
    if (isset($cfg->order_review_uf_sequence) &&
              $cfg->order_review_uf_sequence == 'asc') {
        $strSQL .= 'pv_name, order_id, p_name, uf_id';
    } else {
        $strSQL .= 'pv_name, order_id, p_name, uf_id desc';
    }
    $rs = $db->Execute($strSQL);
    $brk = array(
        'date_for_order' => $date_for_order,
        '1_provider_id' => null,
        '1_order_id_n' => null,
        '1_desc_order' => null,
        '2_id_break' => null
    );
    $total_cost = 0;
    $total_costUF = 0;
    $total_amount = 0;
    $aaa_quantity =0;
    $html = '';
    while ($row = $rs->fetch_array()) {
        if ($brk['1_provider_id'] != $row['provider_id'] || 
                    $brk['1_order_id'] != $row['order_id']) {
            // end previus order
            $html .= break2Html_end($brk, $detail);
            $html .= orderHtml_end($total_cost, $total_costUF,
                    $total_amount, $brk['1_desc_order']);
            // start order
            $total_cost = 0;
            $total_costUF = 0;
            $total_amount = 0;
            $html_h = orderHtml_start($brk, $row);
            if ($head) {
                $html .= $html_h;
            }
        }
        if ($brk['2_id_break'] != $row['product_id']) {
            $html .= break2Html_end($brk, $detail);
            // start uf
            $brk['2_id_break'] = $row['product_id'];
            $sum_quantity_row = get_sum($db, 
                $brk['date_for_order'], 
                $brk['1_provider_id'], $brk['1_order_id'],
                'oi.product_id = '.$brk['2_id_break']);
            $sum_quantity = $sum_quantity_row['sum_quantity'];
            // Rounded by product
            $total_cost += round($row['cost_price']*$sum_quantity, 2);
            if ($detail) {
                $html .= '<div class="block">';
            } else {
                $html .= '<div class="block" style="margin-top: 0.25cm">';
            }
            $html .= '<h3><div class="cel9">'.$row['p_name'].'</div>'.
                '<div class="cel0hh"></div><div class="cel0hh"></div>'.
                '<div class="cel3 lite num">'.$row['um_name'].'</div>'.
                '<div class="cel1h num">'.
                    clean_zeros($sum_quantity).'</div>'.
                'x<div class="cel1h aprov">'.
                    number_format($row['cost_price'], 2).'</div>'.
                '=<div class="cel2 aprov">'.
                    number_format($row['cost_price']*$sum_quantity, 2).'</div>'.
                '</h3>'.chr(10);
            if ($detail) {
                $html .= '<div class="block-body">'.chr(10);
            }
        }
        // Rounded by UF
        $total_costUF += round($row['cost_price']*$row['quantity'], 2);
        $total_amount += round($row['final_price']*$row['quantity'], 2);
        if ($detail) {
            $html .= '<div>';
            $html .= '<div class="cel1 num">'.clean_zeros($row['quantity']).'</div>';
            $html .= '&nbsp;<div class="cel8">';
            $html .= '#'.$row['uf_id'].'-'.$row['uf_name'];
            $html .= '</div>';
            $html .= '</div>'.chr(10);
        }
    }
    $html .= break2Html_end($brk, $detail);
    if ($foot) {
        $html .= orderHtml_end(
            $total_cost, $total_costUF, $total_amount, $brk['1_desc_order']);
    }
    echo $html;
}
function write_order_fam_prod(
        $date_for_order, $provider_id, $order_id, $detail,
        $head = true, $foot = true) {
    $db = DBWrap::get_instance();
    
    $strSQL = get_SQL($date_for_order, $provider_id, $order_id);
    $cfg = configuration_vars::get_instance();
    if (isset($cfg->order_review_uf_sequence) &&
              $cfg->order_review_uf_sequence == 'asc') {
        $strSQL .= 'pv_name, order_id, uf_id, p_name';
    } else {
        $strSQL .= 'pv_name, order_id, uf_id desc, p_name';
    }
    $rs = $db->Execute($strSQL);
    $brk = array(
        'date_for_order' => $date_for_order,
        '1_provider_id' => null,
        '1_order_id_n' => null,
        '1_desc_order' => null,
        '2_id_break' => null
    );
    $total_cost = 0;
    $total_costUF = 0;
    $total_amount = 0;
    $aaa_quantity =0;
    $sum_quantity=0;
    $html = '';
    while ($row = $rs->fetch_array()) {
        if ($brk['1_provider_id'] != $row['provider_id'] || 
                    $brk['1_order_id'] != $row['order_id']) {
            // end previus order
            $html .= break2Html_end($brk, $detail);
            $html .= orderHtml_end($total_cost, $total_costUF,
                    $total_amount, $brk['1_desc_order']);
            // start order
            $total_cost = 0;
            $total_costUF = 0;
            $total_amount = 0;
            $html_h = orderHtml_start($brk, $row);
            if ($head) {
                $html .= $html_h;
            }
        }
        if ($brk['2_id_break'] != $row['uf_id']) {
            $html .= break2Html_end($brk, $detail);
            // start uf
            $det_lin = 0;
            $brk['2_id_break'] = $row['uf_id'];
            $html .= '<div class="block">';
            $html .= '<h3><div class="cel10">'.
                    '#'.$row['uf_id'].'-<b>'.$row['uf_name'].'</b></div>'.
                '</h3>'.chr(10);
            if ($detail) {
                $html .= '<div class="block-body">'.chr(10);
            }
        }
        // Rounded by UF
        $total_costUF += round($row['cost_price']*$row['quantity'], 2);
        $total_amount += round($row['final_price']*$row['quantity'], 2);
        $total_cost += round($row['cost_price']*$row['quantity'], 2);
        if ($detail) {
            $border_det = "border-bottom: dotted 1px #777;";
            if ($det_lin === 0) {
                $border_det .= "border-top: dotted 1px #777;";
            }
            $det_lin++;
            $html .= '<div style="'.$border_det.
                    ' margin-left:.5cm; padding:.05cm 0">';
            $html .= 
                '<div class="cel1h num">'.
                        clean_zeros($row['quantity']).'</div>'.
                '<div class="cel1">x&nbsp;</div>'.
                '<div class="cel8">'.$row['p_name'].'</div>'.
                '<div class="cel4 lite">('.$row['um_name'].')</div>';
            $html .= '</div>'.chr(10);
        }
    }
    $html .= break2Html_end($brk, $detail);
    if ($foot || true) {
        $html .= orderHtml_end(
            $total_cost, $total_costUF, $total_amount, $brk['1_desc_order']);
    }
    echo $html;
}
function orderHtml_start(&$brk, $row) {
    $brk['1_order_id'] = $row['order_id'];
    $brk['1_provider_id'] = $row['provider_id'];
    $brk['1_desc_order'] = $row['pv_name'].
        ': '.$row['date_for_order'].
        ' #'.($row['order_id']==null ? '??' : $row['order_id']);
    return '<h2>'.$brk['1_desc_order'].
        '['.formatOrderStatus($row['revision_status']).']'.
        '</h2>';
}
function orderHtml_end($total_cost, $total_costUF, $total_amount, $desc) {
    if ($desc == null) {
        return '';
    }
    return '<div class="foot">'.
                '<div class="cel6 lite gray">'.$desc.'</div>'.
                '<div class="cel0hh"></div>'.
                '<div class="cel4 num lite gray"><br>Total Final UFs:</div>'.
                '<b class="cel2 num gray"><br>'.
                    number_format($total_amount,2).
                '</b>'.
                '<div class="cel4 num lite gray">Total Cost:<br>Cost arrodonit a UF:</div>'.
                '<div class="cel2 aprov">'.
                    number_format($total_cost,2).'<br><span class="gray">'.
                    number_format($total_costUF,2).'</span>'.
                '</div>'.
            '</div>';
}
?>
</body>
</html>
