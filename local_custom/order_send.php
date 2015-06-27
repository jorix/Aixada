<?php include "../php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php 
            $page_title = 'Repartiment de comandes';
      		echo $Text['global_title'] . " - " . $page_title;  ?></title>

 	<link rel="stylesheet" type="text/css"   media="screen" href="../css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="../js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="../css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
     
    <script type="text/javascript" src="../js/jquery/jquery.js"></script>
    <script type="text/javascript" src="../js/jqueryui/jqueryui.js"></script>
    <script type="text/javascript" src="../js/fgmenu/fg.menu.js"></script>
    <script type="text/javascript" src="../js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
    <script type="text/javascript" src="../js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
    <script type="text/javascript" src="../js/aixadautilities/jquery.aixadaUtilities.js" ></script>

<?php
    $db = DBWrap::get_instance();
    $rs = $db->Execute(
        'SELECT min(date_for_order) for_date' .
        ' FROM aixada_product_orderable_for_date'.
        ' where date_for_order >= date(sysdate())'
    );
    $row = $rs->fetch_array();
    $for_date = null;
    if (isset($_GET['date'])) {
        $for_date = $_GET['date'];
        if (strtotime($for_date) === false) {
            $for_date = null;
        }
    } else {
        if ($row) {
            $for_date = $row['for_date'];
        } 
        if ($for_date == null) {
            $rs = $db->Execute(
                'SELECT max(date_for_order) for_date' .
                ' FROM aixada_product_orderable_for_date');
            $row = $rs->fetch_array();
            if ($row) {
                $for_date = $row['for_date'];
            }
        }
    }
    $order_null = false;
    $order_not_null = false;
    if ($for_date) {
        $order_null = (get_list_query(
            'select count(*) order_null from aixada_order_item'.
            " where order_id is null and date_for_order='".$for_date."'") != 0);
        $order_not_null = (get_list_query(
            'select count(*) order_null from aixada_order_item'.
            " where order_id is not null and date_for_order='".$for_date."'")
                                                                          != 0);
    }
?>
</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "../php/inc/menu.inc.php" ?>
	</div>
    <script>
        // rebase menus to up
        $('a').prop('href', 
            function(i, val) {
            console.log(val);
                return val.replace('/local_custom/','/');
            }
        );
    </script>
	<div id="stagewrap">
		<div class="aix-layout-center60 ui-widget">
            <?php if ($for_date) { // START ?>
            <div class="aix-style-entry-widget">
				<table><tr>
                    <?php if ($order_null){ // START ?>
                    <td><button class="aix-layout-fixW150" 
                        onclick="window.open('../manage_orders.php?filter=nextWeek&lastPage=torn.php','_blank');"
                    >Enviar</button>
                    </td>
                    <td><p>
                        Tancar i <b>Enviar</b> la comanda al/s proveïdor/s.<br>
                        (la comanda s'envia al proveïdor i a la UF responsable)
                    </p></td>
                    <?php } else if($order_not_null) { // ELSE IF ?>
                    <td><p>La comanda als proveïdors per <?php echo $for_date; ?> ja estan tancades.</p></td>
                    <?php } else { // ELSE ?>
                    <td><p><b>No hi ha cap comanda</b> per <?php echo $for_date; ?>.</p></td>
                    <?php } // END ?>
				</tr></table>
            </div>
			<div class="aix-style-entry-widget">
                <h2>Informes per <?php echo $for_date; ?></h2>
				<table>
                <tr>
                    <td><button class="aix-layout-fixW150"
                            onclick="window.open('order_report_prod_fam.php?date=<?php echo $for_date; ?>','_blank');"
                            >
                        Prod-Fam
                    </button></td>
					<td><p>
                        Per fer el repartiment dels productes.
                    </p></td>
				</tr>
                <tr>
                    <td><button class="aix-layout-fixW150"
                            onclick="window.open('order_report_fam_prod.php?date=<?php echo $for_date; ?>','_blank');"
                            >
                        Fam-Prod
                    </button></td>
					<td><p>
                        Per anotar els comptes de les families.
                    </p></td>
				</tr>
                <tr>
                    <td><button class="aix-layout-fixW150"
                            onclick="window.open('order_report_fam_prod.php?detail=N&date=<?php echo $for_date; ?>','_blank');"
                            >
                        Resum Fam
                    </button></td>
					<td><p>
                        Resum de l'import total (apox.) per famílies.
                    </p></td>
				</tr>
                </table>
			</div>
            <?php } else { // ELSE ?>
            <div class="aix-style-entry-widget">
                <h2>No hi ha cap data :-((</h2>
            <div>
            <?php } // END ?>
            <div class="aix-style-entry-widget">
                <h2>Informació d'interès</h2>
				<table>
                <tr>
                    <td><button class="aix-layout-fixW150"
                            onclick="window.open('dir_report_prv_fam.php','_blank');"
                            >
                        Directori
                    </button></td>
					<td><p>
                        Relació d'emails i telèfons de proveïdors i famílies.
                    </p></td>
				</tr>
                <tr>
                    <td><button class="aix-layout-fixW150"
                            onclick="window.open('turns_report.php','_blank');"
                            >
                        Torns
                    </button></td>
					<td><p>
                        Dates i UF assignades per fer els repartiments. 
                        <button class="aix-layout-fixW150"
                            onclick="window.location.href = '../manage_data.php?table=cistella_turn'";
                            >Dades</button>
                    </p></td>
				</tr>
                </table>
			</div>
		</div>

	</div>
</div>
</body>
</html>