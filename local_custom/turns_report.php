<?php 
    require_once '../php/inc/header.inc.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
$from_date = null;
if (isset($_GET['from_date'])) {
    $from_date = $_GET['from_date'];
    if (strtotime($from_date) === false) {
        $from_date = null;
    }
}
if ($from_date === null) {
    $aux = new DateTime("-2 weeks");
    $from_date = $aux->format('Y-m-d');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo 'Torns des de '.$from_date;?></title>
	<link rel="stylesheet" type="text/css"               href="css/reports.css" />
  	<link rel="stylesheet" type="text/css" media="print" href="css/reports-print.css"/>
</head>
<body>
    <div class="page-A4p">
    <h2 class="start"><?php echo 'Torns a partir de: '.$from_date; ?></h2>
    <?php echo write_ufs($from_date); ?>
    </div>
</body>
</html>
<?php
function write_ufs($from_date) {
    $db = DBWrap::get_instance();
    $strSQL = 'select date_turn,'.
        ' t.uf_id, uf.active uf_active, uf.name uf_name,'.
        ' min(m.name) min_m_name, max(m.name) max_m_name'.
        ' from cistella_turn t'.
        ' join (aixada_uf uf, aixada_member m)'.
        ' on (t.uf_id=uf.id and uf.id=m.uf_id)'.
        ' where uf.active = m.active and date_turn >=\''.$from_date.'\''.
        ' group by date_turn, t.uf_id, uf.name'.
        ' order by date_turn, uf.name';
    $rs = $db->Execute($strSQL);
    $brk = array(
        '1_brk' => null
    );
    $html = '';
    while ($row = $rs->fetch_array()) {
        if ($brk['1_brk'] != $row['date_turn']) {
            if ($brk['1_brk'] != null) {
                $html .= '</div>'.chr(10);
            }
            $brk['1_brk'] = $row['date_turn'];
            $html .= '<div class="block" style="margin: 0.3cm; border-top:1px solid #ccc;">';
            $html .= '<div>';
            $html .= '<div class="cel4">'.$row['date_turn'].'</div>';
        } else {
            $html .= '<div>';
            $html .= '<div class="cel4">&nbsp;</div>';
        }
        $html .= '<div class="cel12">';
            if ($row['uf_active'] == 0) {
                $html .= '<span style="color:red">*ÉS BAIXA*</span> ';
            }
            $html .= $row['uf_name'].'#'.$row['uf_id'];
            if ($row['min_m_name'] == $row['max_m_name']) {
                $html .= ' ('.$row['max_m_name'].')';
            } else {
                $html .= ' ('.$row['min_m_name'].' - '.$row['max_m_name'].')';
            }
            $html .= '</div>';
        $html .= '</div>'.chr(10);
    }
    if ($brk['1_brk'] != null) {
        $html .= '</div>'.chr(10);
    }
    return $html;
}
?>
