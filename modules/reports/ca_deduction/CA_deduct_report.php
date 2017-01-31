<?php
session_start();
//include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/js/jsUI.php");
//include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/session.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/config.php");
//include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/config/class_db.php");
include($_SERVER['DOCUMENT_ROOT']."/PETTY_CASH/fpdf/fpdf.php");

$seldep 	=	$_GET['seldep'];
if($seldep != '')
{
	$dep	=	" AND U.DEPARTMENT = '{$seldep}' ";
}
$sel	=	"SELECT CA.*, U.* FROM ".PCDBASE.".CASH_ADVANCE_HDR CA
			 LEFT JOIN ".PCDBASE.".USERS U ON U.USERID = CA.REQUESTEDBY
			 WHERE CA.ISLIQUIDATED  = 'N' AND  CA.STATUS = 'ISSUED' $dep";
$rssel	=	$conn_172->Execute($sel);
if ($rssel == false)
{
	echo $conn_172->ErrorMsg()."::".__LINE__;exit();
}
class PDF extends FPDF 
{
	function Header()
	{
		$this->SetFont('Times','B',9);
		$this->SetX(10);$this->Cell(0,5,'FILSTAR DISTRIBUTORS CORPORATION',0,1,'L');
		$this->SetX(10);$this->Cell(0,5,'ADVANCES TO EMPLOYEES',0,1,'L');
		$this->Ln(10);
		$this->SetFont('Times','B',8);	
		$this->SetX(10);$this->Cell(0,5,'Cash Advcance',0,1,'L');
		$this->Ln(5);
		
	}
	function Footer()
	{
		$this->SetFont('Times','B',8);
		$this->SetY(283);$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		
		$this->SetFont('Times','',7);
		$this->SetY( 283);$this->Cell(0,5, "Printed Date: ".date('Y-m-d H:i:s A'),0,0,'L');	
	}
}
$pdf= new PDF('P','mm','A4');
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak('auto',15);
$pdf->AddPage();
$pdf->SetFont('Times','',7);
$aData	=	array();
$found	=	0;
while (!$rssel->EOF) 
{
	$id			=	$rssel->fields['USERID'];
	$dayslimit	=	$rssel->fields['DAYSLIMIT'];
	$issueddt	=	$rssel->fields['RELEASEDDT'];
	$age		=	(abs(strtotime(date('Y-m-d H:i:s')) - (strtotime($issueddt))))/86400;
	$nocount	=	0;
	for ($c = 1; $c <= $age; $c++)
	{
		$day		=	date('Y-m-d', strtotime($issueddt . "+$c days"));
		$dayname	=	date('l',strtotime($day));
		
		if($dayname == 'Saturday' || $dayname == 'Sunday')
		{
			$nocount++;
		}
	}
	$age	=	$age - $nocount;
	if($age > $dayslimit)
	{
		$found	=	1;
		$department	=	$rssel->fields['DEPARTMENT'];
		$employeeNo	=	$rssel->fields['USERNAME'];
		$name		=	$rssel->fields['NAME'];
		$CAno		=	$rssel->fields['CA_NO'];
		$CAamount	=	$rssel->fields['AMOUNT'];
		
		$aData[$department][$id]['employeeNo']		=	$employeeNo;
		$aData[$department][$id]['name']			=	$name;
		$aData[$department][$id]['CAno']			=	$CAno;
		$aData[$department][$id]['CAamount']		+=	$CAamount;
	}
$rssel->MoveNext();
}
if($found	== 0)
{
	echo "<script>alert('No records found.');window.close();</script>";
	exit();
}
foreach ($aData as $dept=>$val1)
{
	$pdf->SetFont('Times','B',7);
	$pdf->SetX(10);$pdf->Cell(0,5,$dept,1,1,'L');
	$pdf->SetX(10);$pdf->Cell(22,5,'Employee No.',1,0,'C');
	$pdf->SetX(32);$pdf->Cell(45,5,'Name',1,0,'C');
	$pdf->SetX(77);$pdf->Cell(27,5,'C.A. No.',1,0,'C');
	$pdf->SetX(104);$pdf->Cell(20,5,'C.A. Amount',1,0,'C');
	$pdf->SetX(124);$pdf->Cell(35,5,'Acknowledged by',1,0,'C');
	$pdf->SetX(159);$pdf->Cell(41,5,'Approved by Dept. Mngr.',1,1,'C');
	$pdf->SetFont('Times','',7);
	foreach ($val1 as $id=>$val2)
		{
			$pdf->SetX(10);$pdf->Cell(22,5,$val2['employeeNo'],1,0,'C');
			$pdf->SetX(32);$pdf->Cell(45,5,$val2['name'],1,0,'L');
			$pdf->SetX(77);$pdf->Cell(27,5,$val2['CAno'],1,0,'C');
			$pdf->SetX(104);$pdf->Cell(20,5,number_format($val2['CAamount'], 2),1,0,'R');
			$pdf->SetX(124);$pdf->Cell(35,5,'',1,0,'C');
			$pdf->SetX(159);$pdf->Cell(41,5,'',1,1,'C');
		}
$pdf->Ln(10);
}
$pdf->Ln(10);
$pdf->SetX(10);$pdf->Cell(22,5,'Prepared by:',0,1,'L');
$pdf->Ln(10);$pdf->SetFont('Times','B',7);
$pdf->SetX(10);$pdf->Cell(22,5,strtoupper($_SESSION["PC"]['NAME']),0,1,'L');$pdf->SetFont('Times','',7);
if($_SESSION["PC"]['USERLEVEL'] != 'Manager')
{
	$pdf->SetX(10);$pdf->Cell(22,5,$_SESSION["PC"]['USERLEVEL'],0,1,'L');	
}
else 
{
	$pdf->SetX(10);$pdf->Cell(22,5,'Accounting Manager',0,1,'L');	
}
$pdf->Ln(10);	
$pdf->SetX(10);$pdf->Cell(22,5,'Noted by:',0,1,'L');
$pdf->Ln(10);$pdf->SetFont('Times','B',7);
$pdf->SetX(10);$pdf->Cell(22,5,'SILVINO G. CABALEJO',0,1,'L');$pdf->SetFont('Times','',7);
$pdf->SetX(10);$pdf->Cell(22,5,'Accounting Manager',0,1,'L');
$pdf->Ln(10);

$pdf->SetX(10);$pdf->Cell(22,5,'Received by:',0,1,'L');
$pdf->Ln(10);$pdf->SetFont('Times','B',7);
$pdf->SetX(10);$pdf->Cell(22,5,'ROGELIO TALOSIG',0,1,'L');$pdf->SetFont('Times','',7);
$pdf->SetX(10);$pdf->Cell(22,5,'Payroll In-Charge',0,1,'L');
$pdf->Ln(10);
echo $pdf->Output();	
?>