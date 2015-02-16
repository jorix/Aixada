<?php

function get_SQL($date_for_order) {
    return 'select'.
            ' oi.quantity, oi.date_for_order, oi.product_id,' .
            ' oi.unit_price_stamp, p.unit_price cost_price,'.
            ' oi.order_id, o.revision_status,'.
            ' uf_id, uf.name uf_name,'.
            ' p.name p_name, p.provider_id, pv.name pv_name,'.
            ' um.name um_name'.
        ' from aixada_order_item oi '.
            ' join (aixada_uf uf, aixada_product p,
                  aixada_unit_measure um, aixada_provider pv)'.
            ' on (oi.uf_id=uf.id and oi.product_id=p.id
                  and p.unit_measure_order_id=um.id and p.provider_id=pv.id)'.
            ' left join (aixada_order o)'.
            ' on (oi.order_id=o.id)'.
        " where oi.date_for_order='".$date_for_order."'".
        ' order by pv.name, oi.order_id, ';
}
function formatOrderStatus($revision_status) {
    global $Text;
        switch ($revision_status){
            case null:
                $text_key = 'not_yet_sent';
                break;                
            case "1":
                $text_key = 'ostat_yet_received';
                break;
            case "2": 
               $text_key = 'ostat_is_complete';
                break;
            case "3": 
                $text_key = 'ostat_postponed';
                break;
            case "4": 
                $text_key = 'ostat_canceled';
                break;
            case "5": 
                $text_key = 'ostat_changes';
                break;
            default:
                return '<span class="DATA-not_yet_sent">??OrderStatus="'.
                    $revision_status.'"??</span>';
        
    }
    return '<span class="DATA-'.$text_key.'">'.$Text[$text_key].'</span>';
}
function orderHtml_start(&$brk, $row) {
    $startClass = ($brk['1_order_id_n'] == null ? 'start' : '');
    $brk['1_order_id'] = $row['order_id'];
    $brk['1_provider_id'] = $row['provider_id'];
    $brk['1_desc_order'] = $row['pv_name'].
        ': '.$row['date_for_order'].
        ' #'.($row['order_id']==null ? '??' : $row['order_id']);
    return '<h2 class="'.$startClass.'">'.$brk['1_desc_order'].
        '['.formatOrderStatus($row['revision_status']).']'.
        '</h2>';
}
function orderHtml_end($total_cost, $total_amount, $desc) {
    if ($desc == null) {
        return '';
    }
    return '<div><br><hr>'.
                '<div class="lite cel8">'.$desc.'</div>'.
                '<div class="cel0hh"></div>'.
                '<div class="cel3 num lite">Total Final:</div>'.
                '<b class="cel2 num">'.
                    number_format($total_amount,2).
                '</b>'.
                '<div class="cel3 num lite">Total Cost:</div>'.
                '<div class="cel2 num">'.
                    number_format($total_cost,2).
                '</div>'.
            '</div>';
}
function break2Html_end(&$brk, $detail) {
    $html = '';
    if ($brk['2_id_break'] != null) {
        if ($detail) {
            $html .= '</div>';
        }
        $html .= '</div>'.chr(10);
        $brk['2_id_break'] = null;
    }
    return $html;
}
function get_sum($db, $brk, $whereSQL, $col_name) {
    $sql_sum = 
        'select'.
            ' sum(oi.quantity) sum_quantity,'.
            ' round(sum(oi.quantity * p.unit_price),2) sum_cost,'.
            ' round(sum(oi.quantity * oi.unit_price_stamp),2) sum_amount'.
        ' from aixada_order_item oi '.
        ' join (aixada_product p)'.
        ' on (oi.product_id=p.id)'.
        " where oi.date_for_order='".$brk['date_for_order']."'".
        ' and p.provider_id='.$brk['1_provider_id'].
        ' and '.( $brk['1_order_id'] == null ?
            ' oi.order_id is null' :
            ' oi.order_id='.$brk['1_order_id']
        );
    $rs = $db->Execute($sql_sum.' and '.$whereSQL);
    $row = $rs->fetch_array();
    return $row[$col_name];
}
?>