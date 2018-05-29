<?php
# Author: Gerardo Fisanotti - AFIP/SDGSIT/DiITEC/DeARIN - 18-aug-10
# Function: Show a basic functionality WSFEX SOAP client
#
# This program is supposed to be executed from the CLI, not under Apache control
# it should be invoked from the command line, like this:
#
#       $ php wsfex-client.php
#
# Depending of the methods invoked (see main program at the end of this file), 
# this program will generate varios files in your default directory with the
# results obtained.
# If LOG_XMLS is TRUE, then, this program will log various files in the default
# directory, containing the request/response SOAP XML of each method invoked.
# In order to use this program you need a Ticket de Acceso (TA.xml) as provided
# by WSAA.
# The main program is at the end of this source, check it an uncomment/comment
# the lines that invoke the web methods.
#==============================================================================
# You may need to modify the following definitions to adjust to your local
# requirements.
define ("WSDL", "wsfex.wsdl");          # The WSDL corresponding to WSFEX
define ("URL", "http://wswhomo.afip.gov.ar/wsfex/service.asmx");
define ("TA", "TA.xml");                # Ticket de Acceso, from WSAA
define ("CUIT", 20131507969);           # CUIT del emisor de las FC/NC/ND
define ("PROXY_HOST", "10.20.152.112"); # Proxy IP, to reach the Internet
define ("PROXY_PORT", "80");            # Proxy TCP port
define ("LOG_XMLS", TRUE);              # For debugging purposes
#==============================================================================
function CheckErrors($results, $method, $client)
{
  if (LOG_XMLS)
  {
    file_put_contents("request-".$method.".xml",$client->__getLastRequest());
    file_put_contents("response-".$method.".xml",$client->__getLastResponse());
  }
  if (is_soap_fault($results)) 
  { printf("Fault: %s\nFaultString: %s\n",
            $results->faultcode, $results->faultstring); 
    exit (1);
  }
  $Y=$method.'Result';
  $X=$results->$Y;
  if ($X->FEXErr->ErrCode != 0)
    {
      printf("Method=%s\n",$method);
      printf("errcode=%s\n",$X->FEXErr->ErrCode);
      printf("errmsg=%s\n",$X->FEXErr->ErrMsg);
      exit (1);
    }
  if ($X->FEXEvents->EventCode != 0)
    {
      printf("Method=%s\n",$method);
      printf("eventcode=%s\n",
        $X->FEXEvents->EventCode);
      printf("eventmsg=%s\n",
        $X->FEXEvents->EventMsg);
      exit (1);
    }
}
#==============================================================================
function FEXGetPARAM_MON ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token=$token;
  $params->Auth->Sign=$sign;
  $params->Auth->Cuit=$CUIT;
  $results=$client->FEXGetPARAM_MON($params);
  CheckErrors($results, 'FEXGetPARAM_MON', $client);
  $X=$results->FEXGetPARAM_MONResult->FEXResultGet;
  $fh=fopen("moneda.txt","w");
  foreach ($X->ClsFEXResponse_Mon as $MON)
    {
      fwrite($fh,sprintf("%5s  %-30s  %8s  %8s\n",$MON->Mon_Id, $MON->Mon_Ds, 
         $MON->Mon_vig_desde, $MON->Mon_vig_hasta));
    }
  fclose($fh);
}
#==============================================================================
function FEXGetPARAM_Tipo_Cbte ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEXGetPARAM_Tipo_Cbte($params);
  CheckErrors($results, 'FEXGetPARAM_Tipo_Cbte', $client);
  $X=$results->FEXGetPARAM_Tipo_CbteResult->FEXResultGet;
  $fh=fopen("TiposCpbte.txt","w");
  foreach ($X->ClsFEXResponse_Tipo_Cbte as $TIPO)
    {
      fwrite($fh,sprintf("%5s  %-20s  %8s  %8s\n",$TIPO->Cbte_Id, 
          chop($TIPO->Cbte_Ds), $TIPO->Cbte_vig_desde, $TIPO->Cbte_vig_hasta));
    }
  fclose($fh);
}
#==============================================================================
function FEXGetPARAM_Tipo_Expo ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEXGetPARAM_Tipo_Expo($params);
  CheckErrors($results, 'FEXGetPARAM_Tipo_Expo', $client);
  $X=$results->FEXGetPARAM_Tipo_ExpoResult->FEXResultGet;
  $fh=fopen("TiposExpo.txt","w");
  foreach ($X->ClsFEXResponse_Tex as $TIPO)
    {
      fwrite($fh,sprintf("%5s  %-30s  %8s  %8s\n",$TIPO->Tex_Id, 
             $TIPO->Tex_Ds, $TIPO->Tex_vig_desde, $TIPO->Tex_vig_hasta));
    }
  fclose($fh);
}
#==============================================================================
function FEXGetPARAM_Idiomas ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEXGetPARAM_Idiomas($params);
  CheckErrors($results, 'FEXGetPARAM_Idiomas', $client);
  $X=$results->FEXGetPARAM_IdiomasResult->FEXResultGet;
  $fh=fopen("idiomas.txt","w");
  foreach ($X->ClsFEXResponse_Idi as $IVA)
    {
      fwrite($fh,sprintf("%5s  %-30s  %8s  %8s\n",$IVA->Idi_Id, $IVA->Idi_Ds, 
         $IVA->Idi_vig_desde, $IVA->Idi_vig_hasta));
    }
  fclose($fh);
}
#==============================================================================
function FEXGetPARAM_UMed ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEXGetPARAM_UMed($params);
  CheckErrors($results, 'FEXGetPARAM_UMed', $client);
  $X=$results->FEXGetPARAM_UMedResult->FEXResultGet;
  $fh=fopen("UnidadesMedida.txt","w");
  foreach ($X->ClsFEXResponse_UMed as $UMED)
    {
      fwrite($fh, sprintf("%5s  %-30s  %8s  %8s\n",$UMED->Umed_Id, 
        $UMED->Umed_Ds, $UMED->Umed_vig_desde, $UMED->Umed_vig_hasta));
    }
  fclose($fh);
}
#==============================================================================
function FEXGetPARAM_DST_pais ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEXGetPARAM_DST_pais($params);
  CheckErrors($results, 'FEXGetPARAM_DST_pais', $client);
  $X=$results->FEXGetPARAM_DST_paisResult->FEXResultGet;
  $fh=fopen("DstPais.txt","w");
  foreach ($X->ClsFEXResponse_DST_pais as $DOC)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$DOC->DST_Codigo, 
         $DOC->DST_Ds));
    }
  fclose($fh);
}
#==============================================================================
function FEXGetPARAM_DST_CUIT ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEXGetPARAM_DST_CUIT($params);
  CheckErrors($results, 'FEXGetPARAM_DST_CUIT', $client);
  $X=$results->FEXGetPARAM_DST_CUITResult->FEXResultGet;
  $fh=fopen("DstCUIT.txt","w");
  foreach ($X->ClsFEXResponse_DST_cuit as $DOC)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$DOC->DST_CUIT, 
         $DOC->DST_Ds));
    }
  fclose($fh);
}
#==============================================================================
function FEXGetPARAM_Ctz ($client, $token, $sign, $CUIT, $curr)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $params->Mon_id = $curr;
  $results=$client->FEXGetPARAM_Ctz($params);
  CheckErrors($results, 'FEXGetPARAM_Ctz', $client);
  $X=$results->FEXGetPARAM_CtzResult->FEXResultGet;
  printf("Moneda ID: %s    Cotiz: %10.4f    Fecha: %s\n", $curr, 
         $X->Mon_ctz,
         $X->Mon_fecha);
}
#==============================================================================
function FEXGetPARAM_PtoVenta ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEXGetPARAM_PtoVenta($params);
  CheckErrors($results, 'FEXGetPARAM_PtoVenta', $client);
}
#==============================================================================
function FEXGetPARAM_Incoterms ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEXGetPARAM_Incoterms($params);
  CheckErrors($results, 'FEXGetPARAM_Incoterms', $client);
  $X=$results->FEXGetPARAM_IncotermsResult->FEXResultGet;
  $fh=fopen("Incoterms.txt","w");
  foreach ($X->ClsFEXResponse_Inc as $DOC)
    {
      fwrite($fh, sprintf("%3s  %3s  %8s  %8s\n",$DOC->Inc_Id, 
        $DOC->Inc_Ds, $DOC->Inc_vig_desde, $DOC->Inc_vig_hasta));
    }
  fclose($fh);
}
#==============================================================================
function FEXGetLast_CMP ($client, $token, $sign, $CUIT, $pv, $tc)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $params->Auth->Pto_venta = $pv;
  $params->Auth->Tipo_cbte = $tc;
  $results=$client->FEXGetLast_CMP($params);
  CheckErrors($results, 'FEXGetLast_CMP', $client);
  return($results->FEXGetLast_CMPResult->FEXResult_LastCMP->Cbte_nro);
}
#==============================================================================
function FEXGetCMP ($client, $token, $sign, $CUIT, $pv, $tc, $cbte)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $params->Cmp->Tipo_cbte = $tc;
  $params->Cmp->Punto_vta = $pv;
  $params->Cmp->Cbte_nro = $cbte;
  $results=$client->FEXGetCMP($params);
  CheckErrors($results, 'FEXGetCMP', $client);
  $cbte=$results->FEXGetCMPResult->FEXResultGet;
  printf("\nId=%s\nFechaCbte=%s\nTipoCbte=%s\nPtoVta=%s\nCbtNro=%s\n" . 
    "TipoExpo=%s\nPermisoExistente=%s\nObs_comerciales=%s\n",
    $cbte->Id,
    $cbte->Fecha_cbte,
    $cbte->Tipo_cbte,
    $cbte->Punto_vta,
    $cbte->Cbte_nro,
    $cbte->Tipo_expo,
    $cbte->Permiso_existente,
    $cbte->Obs_comerciales
  );
  foreach ($cbte->Items->Item as $ITEM)
    {
      if (!is_null($ITEM))
      {
        printf("%5s %-30s %5s %2s %10.2f %10.2f\n",
         $ITEM->Pro_codigo, 
         $ITEM->Pro_ds, 
         $ITEM->Pro_qty,
         $ITEM->Pro_umed,
         $ITEM->Pro_precio_uni,
         $ITEM->Pro_total_item
        );
      }
    }
}
#==============================================================================
function FEXGetLast_ID ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEXGetLast_ID($params);
  CheckErrors($results, 'FEXGetLast_ID', $client);
  return($results->FEXGetLast_IDResult->FEXResultGet->Id);
}
#==============================================================================
function FEXDummy ($client)
{
  $results=$client->FEXDummy();
  if (is_soap_fault($results)) 
  { printf("Fault: %s\nFaultString: %s\n",
            $results->faultcode, $results->faultstring); 
    exit (1);
  }
  $X=$results->FEXDummyResult;
  printf("Appserver: %s\nDbserver: %s\nAuthserver: %s\n",
    $X->AppServer,
    $X->DbServer,
    $X->AuthServer);
}
#==============================================================================
function CompletarDatosBasicos ($client, $token, $sign, $CUIT, $pv, $tc)
{
  $cbte=FEXGetLast_CMP($client, $token, $sign, CUIT, $pv, $tc);
  $id=FEXGetLast_ID($client, $token, $sign, CUIT);
  $params->Auth->Token = $token;
  $params->Auth->Sign  = $sign;
  $params->Auth->Cuit  = $CUIT;
  $params->Cmp->Id     = $id+1;
  $params->Cmp->Tipo_cbte = $tc;
  $params->Cmp->Punto_vta = $pv;
  $params->Cmp->Cbte_nro = $cbte+1;
  $params->Cmp->Fecha_cbte = date('Ymd');
  $params->Cmp->Dst_cmp = 225;
  $params->Cmp->Cliente = 'Jose Yorugua';
  $params->Cmp->Cuit_pais_cliente = 50000000016;
  $params->Cmp->Domicilio_cliente = 'Montevideo, UY';
  $params->Cmp->Id_impositivo = 'RUC 123123';
  $params->Cmp->Moneda_Id = 'DOL';
  $params->Cmp->Moneda_ctz = 3.85;
  $params->Cmp->Obs_comerciales = 'PAIS DE AQUISI��O: REP.ARG.';
  $params->Cmp->Imp_total = 138.176;
  $params->Cmp->Obs = 'Observaciones';
  $params->Cmp->Forma_pago = 'Taka taka';
  $params->Cmp->Incoterms = 'FOB';
  $params->Cmp->Incoterms_Ds = 'Freight on board';
  $params->Cmp->Idioma_cbte = 2;
  $items = array();
  $items[0]->Pro_codigo = "kbd";
  $items[0]->Pro_ds = "Keyboard (uruguayan layout)";
  $items[0]->Pro_qty = 2.55;
  $items[0]->Pro_umed = 7;
  $items[0]->Pro_precio_uni = 50.555;
  $items[0]->Pro_total_item = 128.176;
  $items[1]->Pro_codigo = "FLETE";
  $items[1]->Pro_ds = "Flete";
  $items[1]->Pro_qty = 0;
  $items[1]->Pro_umed = 0;
  $items[1]->Pro_precio_uni = 0;
  $items[1]->Pro_total_item = 10;
  $params->Cmp->Items = $items;
  return $params;
}
#==============================================================================
function PrintCbte ($cbte)
{
  printf("Id=%5s\nCUIT=%11s\nTipoCbte=%s\nPunto Vta=%s\nCbte #=%s\nCAE=%8s\n".
       "Fch_venc_Cae=%s\nFch_cbte=%6s\nResultado=%s\nReproceso=%s\nObs=%s\n\n",
    $cbte->Id,
    $cbte->Cuit,
    $cbte->Tipo_cbte,
    $cbte->Punto_vta,
    $cbte->Cbte_nro,
    $cbte->Cae,
    $cbte->Fch_venc_Cae,
    $cbte->Fch_cbte,
    $cbte->Resultado,
    $cbte->Reproceso,
    $cbte->Motivos_Obs
  );
}
#==============================================================================
function EmitirFC ($client, $token, $sign, $CUIT, $pv)
{
  $params=CompletarDatosBasicos ($client, $token, $sign, $CUIT, $pv, 19);
  $params->Cmp->Tipo_expo = 1;
  $permisos = array();
  $permisos[0]->Id_permiso = 'xxyyxxyyxxyyxxyy';
  $permisos[0]->Dst_merc = 225;
  $permisos[1]->Id_permiso = 'xxyyxxyyxxyyxxyy';
  $permisos[1]->Dst_merc = 225;
  $params->Cmp->Permiso_existente = 'S';
  $params->Cmp->Permisos = $permisos;
  $results=$client->FEXAuthorize($params);
  CheckErrors($results, 'FEXAuthorize', $client);
  PrintCbte($results->FEXAuthorizeResult->FEXResultAuth);
}
#==============================================================================
function EmitirNC ($client, $token, $sign, $CUIT, $pv)
{
  $params=CompletarDatosBasicos ($client, $token, $sign, $CUIT, $pv, 21);
  $params->Cmp->Tipo_expo = 2;
  $params->Cmp->Permiso_existente = '';
  $cmps = array();
  $cmps[0]->CBte_tipo = 19;
  $cmps[0]->Cbte_punto_vta = 10;
  $cmps[0]->Cbte_nro = 2;
  $params->Cmp->Cmps_asoc = $cmps;
  $results=$client->FEXAuthorize($params);
  CheckErrors($results, 'FEXAuthorize', $client);
  PrintCbte($results->FEXAuthorizeResult->FEXResultAuth);
}
#==============================================================================
ini_set("soap.wsdl_cache_enabled", "0");
if (!file_exists(WSDL)) {exit("Failed to open ".WSDL."\n");}
if (!file_exists(TA)) {exit("Failed to open ".TA."\n");}
$client=new soapClient(WSDL,
  array('soap_version' => SOAP_1_2,
        'location'     => URL,
#        'proxy_host'   => PROXY_HOST,
#        'proxy_port'   => PROXY_PORT,
        'exceptions'   => 0,
        'encoding'     => 'ISO-8859-1',
        'features'     => SOAP_USE_XSI_ARRAY_TYPE + SOAP_SINGLE_ELEMENT_ARRAYS,
        'trace'        => 1)); # needed by getLastRequestHeaders and others
$TA=simplexml_load_file(TA);
$token=$TA->credentials->token;
$sign=$TA->credentials->sign;
file_put_contents("functions.txt",print_r($client->__getFunctions(),TRUE));
file_put_contents("types.txt",print_r($client->__getTypes(),TRUE));
#
# Uncomment any of the following lines to invoke the corresponding web method.
#
FEXDummy($client);
FEXGetPARAM_MON($client, $token, $sign, CUIT);
FEXGetPARAM_Incoterms($client, $token, $sign, CUIT);
FEXGetPARAM_Tipo_Cbte($client, $token, $sign, CUIT);
FEXGetPARAM_Tipo_Expo($client, $token, $sign, CUIT);
FEXGetPARAM_Idiomas($client, $token, $sign, CUIT);
FEXGetPARAM_UMed($client, $token, $sign, CUIT);
FEXGetPARAM_DST_pais($client, $token, $sign, CUIT);
FEXGetPARAM_DST_CUIT($client, $token, $sign, CUIT);
FEXGetPARAM_Ctz($client, $token, $sign, CUIT, 'DOL');
FEXGetPARAM_PtoVenta($client, $token, $sign, CUIT);
EmitirFC($client, $token, $sign, CUIT, 1);
#EmitirNC($client, $token, $sign, CUIT, 1);
$cbte=FEXGetLast_CMP($client, $token, $sign, CUIT, 1, 19);
#FEXGetCMP($client, $token, $sign, CUIT, 1, 19, $cbte);
?>
