<?php if (session_status() == PHP_SESSION_NONE) {
    session_start(); }?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['sysconn']); ?>

<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

$colname_ordprev = "-1";
if (isset($_GET['id'])) {
  $colname_ordprev = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}

mysql_select_db($database_swmisconn, $swmisconn);
$query_ordprev = sprintf("Select o.id ordid, o.medrecnum, o.visitid, o.feeid, o.doctor, o.status, o.billstatus, o.urgency, o.item,  substr(o.urgency,1,1) urg, DATE_FORMAT(o.entrydt,'%%d-%%b-%%Y %%H:%%i') entrydt, o.entryby, o.amtdue, o.rate, o.ratereason, o.amtpaid, o.comments, o.ofee, f.dept, f.section, f.name, f.descr, f.fee FROM orders o join fee f on o.feeid = f.id WHERE o.id = %s", $colname_ordprev);
$ordprev = mysql_query($query_ordprev, $swmisconn) or die(mysql_error());
$row_ordprev = mysql_fetch_assoc($ordprev);
$totalRows_ordprev = mysql_num_rows($ordprev);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
if(isset($_POST['feeid']) && $_POST['feeid'] == 30){
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_Fee = sprintf("SELECT ofee from orders where id = '".$_POST['id']."'");
	$Fee = mysql_query($query_Fee, $swmisconn) or die(mysql_error());
	$row_Fee = mysql_fetch_assoc($Fee);
	$totalRows_Fee = mysql_num_rows($Fee);
	   $billstatus = $_POST['billstatus'];
		$urgency = $_POST['urgency'];
      $rate = $_POST['rate']; 
		if(isset($_POST['urgency']) &&  $_POST['urgency'] == 'STAT'){
  		  $billstatus = 'paylater';
	     $rate = '125%';
		}
		if($row_ordprev['urgency'] == 'STAT' && $row_ordprev['rate'] == 125 && $_POST['urgency'] != 'STAT') {
  		 $billstatus = 'Due';
	     $rate = '100';
			}
		$amtdue = $row_Fee['ofee']*($rate/100);
} else {	
	mysql_select_db($database_swmisconn, $swmisconn);
	$query_Fee = sprintf("SELECT fee from fee where id = '".$_POST['feeid']."'");
	$Fee = mysql_query($query_Fee, $swmisconn) or die(mysql_error());
	$row_Fee = mysql_fetch_assoc($Fee);
	$totalRows_Fee = mysql_num_rows($Fee);
	   $billstatus = $_POST['billstatus'];
		$urgency = $_POST['urgency'];
      $rate = $_POST['rate']; 
	    $amtdue = $row_Fee['fee']*($_POST['rate']/100);
		if(isset($_POST['urgency']) &&  $_POST['urgency'] == 'STAT'){
		  $billstatus = 'paylater';
	     $rate = '125%';
		}
				if($row_ordprev['urgency'] == 'STAT')  {  //&& $row_ordprev['rate'] == 125 && $_POST['urgency'] != 'STAT')
  		 $billstatus = 'Due';
	     $rate = '100';
			}

	     $amtdue = $row_Fee['fee']*($rate/100);
}
  $updateSQL = sprintf("UPDATE orders SET rate=%s, ratereason=%s, amtdue=%s, urgency=%s, doctor=%s, status=%s, billstatus=%s, comments=%s, entryby=%s, entrydt=%s WHERE id=%s",
                       GetSQLValueString($rate, "int"),
                       GetSQLValueString($_POST['ratereason'], "int"),
                       GetSQLValueString($amtdue, "int"),
                       GetSQLValueString($_POST['urgency'], "text"),
                       GetSQLValueString($_POST['doctor'], "text"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($billstatus, "text"),
                       GetSQLValueString($_POST['comments'], "text"),
                       GetSQLValueString($_POST['entryby'], "text"),
                       GetSQLValueString($_POST['entrydt'], "date"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_swmisconn, $swmisconn);
  $Result1 = mysql_query($updateSQL, $swmisconn) or die(mysql_error());

  $updateGoTo = "PatShow1.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= str_replace('&ordchg=PatOrdersEdit.php','',$_SERVER['QUERY_STRING']); // replace function takes &ordchg=PatOrdersEdit.php out of $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_swmisconn, $swmisconn);
$query_reason = "Select id, list, name, seq from dropdownlist where list = 'Rate Reason' Order By seq";
$reason = mysql_query($query_reason, $swmisconn) or die(mysql_error());
$row_reason = mysql_fetch_assoc($reason);
$totalRows_reason = mysql_num_rows($reason);

$colname_ordedit = "-1";
if (isset($_GET['id'])) {
  $colname_ordedit = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_swmisconn, $swmisconn);
$query_ordedit = sprintf("Select o.id ordid, o.medrecnum, o.visitid, o.feeid, o.doctor, o.status, o.billstatus, o.urgency, o.item,  substr(o.urgency,1,1) urg, DATE_FORMAT(o.entrydt,'%%d-%%b-%%Y %%H:%%i') entrydt, o.entryby, o.amtdue, o.rate, o.ratereason, o.amtpaid, o.comments, o.ofee, f.dept, f.section, f.name, f.descr, f.fee FROM orders o join fee f on o.feeid = f.id WHERE o.id = %s", $colname_ordedit);
$ordedit = mysql_query($query_ordedit, $swmisconn) or die(mysql_error());
$row_ordedit = mysql_fetch_assoc($ordedit);
$totalRows_ordedit = mysql_num_rows($ordedit);

//mysql_select_db($database_swmisconn, $swmisconn);
//$query_ordered = "SELECT o.id, o.medrecnum, o.visitid, o.feeid, o.rate, o.doctor, substr(o.status,1,7) status, substr(o.urgency,1,1) urg, DATE_FORMAT(o.entrydt,'%d%b%y %H:%i') entrydt, o.entryby, Format(f.fee*(o.rate/100),0) as amtdue, o.amtpaid, f.section, f.name, f.descr FROM orders o, fee f WHERE o.feeid = f.id and f.dept = 'Laboratory' and o.medrecnum ='". $colname_mrn."' and o.visitid ='". $colname_vid."' ORDER BY entrydt ASC";
//$ordered = mysql_query($query_ordered, $swmisconn) or die(mysql_error());
//$row_ordered = mysql_fetch_assoc($ordered);
//$totalRows_ordered = mysql_num_rows($ordered);

mysql_select_db($database_swmisconn, $swmisconn);
$query_doctor = "SELECT userid FROM users WHERE active = 'Y' and docflag = 'Y' ORDER BY userid ASC";
$doctor = mysql_query($query_doctor, $swmisconn) or die(mysql_error());
$row_doctor = mysql_fetch_assoc($doctor);
$totalRows_doctor = mysql_num_rows($doctor);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="../../CSS/Level3_1.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="40%">
  <tr>
    <td><form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
      <table width="100%" bgcolor="#F8FDCE">
        <tr>
          <td nowrap="nowrap" title="M:<?php echo $row_ordedit['medrecnum'] ?> V:<?php echo $row_ordedit['visitid'] ?> O:<?php echo $row_ordedit['ordid'] ?>"><?php echo $row_ordedit['entrydt'] ?></td>
          <td nowrap="nowrap"><div align="center" class="BlackBold_18">Order Edit&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="nav11"><a href="PatShow1.php?mrn=<?php echo $_SESSION['mrn']; ?>&vid=<?php echo $_SESSION['vid']; ?>&visit=PatVisitView.php&act=hist&pge=PatOrdersView.php">Close</a></span> </div></td>
          <td nowrap="nowrap"><div align="center">
              <input type="submit" name="Submit" style="background-color:aqua; border-color:blue; color:black;text-align: center;border-radius: 4px;" value="Edit" />
          </div></td>
        </tr>
        <tr>
          <td nowrap="nowrap">Dept: <span class="BlackBold_14"><?php echo $row_ordedit['dept']  ?></span></td>
<?php  	if($row_ordedit['feeid'] == 30) {?>

           <td nowrap="nowrap" colspan="2">Section:<span class="BlackBold_14"> <?php echo $row_ordedit['section']  ?></span>&nbsp;&nbsp;Order:<span class="BlackBold_14"><?php echo $row_ordedit['item'] ?></span></td>
<?php    } else {?>
          <td  nowrap="nowrap">Section:<span class="BlackBold_14"> <?php echo $row_ordedit['section']  ?></span>&nbsp;&nbsp;Order: <span class="BlackBold_14"><?php echo $row_ordedit['name']  ?></span></td>
          <td nowrap="nowrap">&nbsp;</td>
<?php    } ?>
        </tr>
        <tr>
          <td nowrap="nowrap">Rate:
            <select name="rate" id="rate">
                <option value="200" <?php if (!(strcmp(200, $row_ordedit['rate']))) {echo "selected=\"selected\"";} ?>>200%</option>
                <option value="150" <?php if (!(strcmp(150, $row_ordedit['rate']))) {echo "selected=\"selected\"";} ?>>150%</option>
                <option value="125" <?php if (!(strcmp(125, $row_ordedit['rate']))) {echo "selected=\"selected\"";} ?>>125%</option>
                <option value="100" <?php if (!(strcmp(100, $row_ordedit['rate']))) {echo "selected=\"selected\"";} ?>>Standard</option>
                <option value="75" <?php if (!(strcmp(75, $row_ordedit['rate']))) {echo "selected=\"selected\"";} ?>>75%</option>
                <option value="50" <?php if (!(strcmp(50, $row_ordedit['rate']))) {echo "selected=\"selected\"";} ?>>50%</option>
                <option value="25" <?php if (!(strcmp(25, $row_ordedit['rate']))) {echo "selected=\"selected\"";} ?>>25%</option>
                <option value="0" <?php if (!(strcmp(0, $row_ordedit['rate']))) {echo "selected=\"selected\"";} ?>>None</option>
              </select>          </td>
          <td nowrap="nowrap">Rate Reason:
            <select name="ratereason">
                <option value="103" <?php if (!(strcmp(103, $row_ordedit['ratereason']))) {echo "selected=\"selected\"";} ?>>None</option>
                <?php
do {  
?>
                <option value="<?php echo $row_reason['id']?>"<?php if (!(strcmp($row_reason['id'], $row_ordedit['ratereason']))) {echo "selected=\"selected\"";} ?>><?php echo $row_reason['name']?></option>
                <?php
} while ($row_reason = mysql_fetch_assoc($reason));
  $rows = mysql_num_rows($reason);
  if($rows > 0) {
      mysql_data_seek($reason, 0);
	  $row_reason = mysql_fetch_assoc($reason);
  }
?>
            </select></td>
     <?php if($row_ordedit['feeid'] == 30){?>
          <td nowrap="nowrap"> Fee: <span class="BlackBold_14"><?php echo $row_ordedit['ofee']?></span>&nbsp;Amt Due: <span class="BlackBold_14"><?php echo $row_ordedit['amtdue']  ?></span></td>
     
    <?php  } else {?>
          <td nowrap="nowrap"> Fee: <span class="BlackBold_14"><?php echo $row_ordedit['fee']?></span>&nbsp;Amt Due: <span class="BlackBold_14"><?php echo $row_ordedit['amtdue']  ?></span></td>
   <?php   }   ?> 
        </tr>
        <tr>
          <td title="Billstatus: <?php echo $row_ordedit['billstatus'] ?>" nowrap="nowrap">Status: <span class="BlackBold_14"><?php echo $row_ordedit['status']  ?></span></td>
          <td nowrap="nowrap">Urg:
            <select name="urgency" id="urgency">
                <option value="Routine" <?php if (!(strcmp("Routine", $row_ordedit['urgency']))) {echo "selected=\"selected\"";} ?>>Routine</option>
                <option value="Scheduled" <?php if (!(strcmp("Scheduled", $row_ordedit['urgency']))) {echo "selected=\"selected\"";} ?>>Scheduled</option>
                <option value="ASAP" <?php if (!(strcmp("ASAP", $row_ordedit['urgency']))) {echo "selected=\"selected\"";} ?>>ASAP</option>
                <option value="STAT" <?php if (!(strcmp("STAT", $row_ordedit['urgency']))) {echo "selected=\"selected\"";} ?>>STAT</option>
              </select>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Doctor: </td>
          <td nowrap="nowrap"><select name="doctor">
            <option value="NA" <?php if (!(strcmp("NA", $row_ordedit['doctor']))) {echo "selected=\"selected\"";} ?>>NA</option>
            <!--<option value="" <?php if (!(strcmp("", $row_ordedit['doctor']))) {echo "selected=\"selected\"";} ?>>Select</option>-->
            <?php
do {  
?><option value="<?php echo $row_doctor['userid']?>"<?php if (!(strcmp($row_doctor['userid'], $row_ordedit['doctor']))) {echo "selected=\"selected\"";} ?>><?php echo $row_doctor['userid']?></option>
            <?php
} while ($row_doctor = mysql_fetch_assoc($doctor));
  $rows = mysql_num_rows($doctor);
  if($rows > 0) {
      mysql_data_seek($doctor, 0);
	  $row_doctor = mysql_fetch_assoc($doctor);
  }?>
		  </select>
              <!--<input name="entrydt" type="hidden" id="entrydt" value="<?php echo $row_ordedit['amtdue']; ?>" />-->
              <input name="entrydt" type="hidden" id="entrydt" value="<?php echo date("Y-m-d H:i"); ?>" />
              <input name="entryby" type="hidden" id="entryby" value="<?php echo $_SESSION['user']; ?>" />
              <input name="feeid" type="hidden" id="feeid" value="<?php echo $row_ordedit['feeid']; ?>" />
              <input name="status" type="hidden" id="status" value="<?php echo $row_ordedit['status']; ?>" />
              <input name="id" type="hidden" id="id" value="<?php echo $row_ordedit['ordid']; ?>" />
              <input name="medrecnum" type="hidden" id="medrecnum" value="<?php echo $row_ordedit['medrecnum']; ?>" />
              <input name="visitid" type="hidden" id="visitid" value="<?php echo $row_ordedit['visitid']; ?>" /></td>
              
        </tr>
        <tr>
          <td colspan="2" nowrap="nowrap">Comments:
            <input name="comments" type="text" id="comments" value="<?php echo $row_ordedit['comments']; ?>" size="40" /></td>
     <?php if(allow(61,3) == 1) { ?>
          <td>Pay Later:
            <select name="billstatus" id="billstatus">
               <option value="Due" <?php if (!(strcmp("Due", $row_ordedit['billstatus']))) {echo "selected=\"selected\"";} ?>>No</option>
               <option value="paylater" <?php if (!(strcmp("paylater", $row_ordedit['billstatus']))) {echo "selected=\"selected\"";} ?>>Yes</option>
           </select></td> 
    <?php } else { ?>
         <td><input type="hidden" name="billstatus" id="billstatus" Value=<?php echo $row_ordedit['billstatus'] ?>></td>
    <?php } ?>
        </tr>
      </table>
      <input type="hidden" name="MM_update" value="form1">
    </form>    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($ordedit);

//mysql_free_result($ordered);

mysql_free_result($doctor);

mysql_free_result($reason);
?>
