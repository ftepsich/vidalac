<?php
# Author: Gerardo Fisanotti - AFIP/SDGSIT/DiITEC/DeARIN - 4-oct-10
# Function: Show a basic functionality WSFEV1 SOAP client
#
# This program is supposed to be executed from the CLI, not under Apache control
# it should be invoked from the command line, like this:
#
#       $ php wsfev1-client.php
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
define ("WSDL", "wsfev1.wsdl");          # The WSDL corresponding to WSFEX
define ("URL", "https://10.30.205.195/wsfev1/service.asmx");
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
  if (isset($X->Errors))
    {
      foreach ($X->Errors->Err as $E)
        {
          printf("Method=%s / Code=%s / Msg=%s\n",$method, $E->Code, $E->Msg);
        }
      exit (1);
    }
}
#==============================================================================
function FEDummy($client)
{
  $results=$client->FEDummy();
  CheckErrors($results, 'FEDummy', $client);
  printf("Appserver: %s\nDbserver: %s\nAuthserver: %s\n",
    $results->FEDummyResult->AppServer,
    $results->FEDummyResult->DbServer,
    $results->FEDummyResult->AuthServer);
}
#==============================================================================
function FEParamGetCotizacion ($client, $token, $sign, $CUIT, $MON)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $params->MonId = $MON;
  $results=$client->FEParamGetCotizacion($params);
  CheckErrors($results, 'FEParamGetCotizacion', $client);
  $X=$results->FEParamGetCotizacionResult->ResultGet;
  printf("Id=%s  /  Cotiz=%f  / Fecha=%s\n", $X->MonId, $X->MonCotiz, 
         $X->FchCotiz);
  return $X->MonCotiz;
}
#==============================================================================
function FEParamGetTiposTributos ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEParamGetTiposTributos($params);
  CheckErrors($results, 'FEParamGetTiposTributos', $client);
  $X=$results->FEParamGetTiposTributosResult;
  $fh=fopen("TiposTributos.txt","w");
  foreach ($X->ResultGet->TributoTipo as $Y)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$Y->Id, $Y->Desc));
    }
  fclose($fh);
}
#==============================================================================
function FEParamGetTiposMonedas ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEParamGetTiposMonedas($params);
  CheckErrors($results, 'FEParamGetTiposMonedas', $client);
  $X=$results->FEParamGetTiposMonedasResult;
  $fh=fopen("TiposMonedas.txt","w");
  foreach ($X->ResultGet->Moneda as $Y)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$Y->Id, $Y->Desc));
    }
  fclose($fh);
}
#==============================================================================
function FEParamGetTiposIva ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEParamGetTiposIva($params);
  CheckErrors($results, 'FEParamGetTiposIva', $client);
  $X=$results->FEParamGetTiposIvaResult;
  $fh=fopen("TiposIva.txt","w");
  foreach ($X->ResultGet->IvaTipo as $Y)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$Y->Id, $Y->Desc));
    }
  fclose($fh);
}
#==============================================================================
function FEParamGetTiposOpcional ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEParamGetTiposOpcional($params);
  CheckErrors($results, 'FEParamGetTiposOpcional', $client);
  $X=$results->FEParamGetTiposOpcionalResult;
  $fh=fopen("TiposOpcional.txt","w");
  foreach ($X->ResultGet->OpcionalTipo as $Y)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$Y->Id, $Y->Desc));
    }
  fclose($fh);
}
#==============================================================================
function FEParamGetTiposConcepto ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEParamGetTiposConcepto($params);
  CheckErrors($results, 'FEParamGetTiposConcepto', $client);
  $X=$results->FEParamGetTiposConceptoResult;
  $fh=fopen("TiposConcepto.txt","w");
  foreach ($X->ResultGet->ConceptoTipo as $Y)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$Y->Id, $Y->Desc));
    }
  fclose($fh);
}
#==============================================================================
function FEParamGetPtosVenta ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEParamGetPtosVenta($params);
  CheckErrors($results, 'FEParamGetPtosVenta', $client);
  $X=$results->FEParamGetPtosVentaResult;
  $fh=fopen("PtosVenta.txt","w");
  foreach ($X->ResultGet->PtoVentaTipo as $Y)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$Y->Id, $Y->Desc));
    }
  fclose($fh);
}
#==============================================================================
function FEParamGetTiposCbte ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEParamGetTiposCbte($params);
  CheckErrors($results, 'FEParamGetTiposCbte', $client);
  $X=$results->FEParamGetTiposCbteResult;
  $fh=fopen("TiposCbte.txt","w");
  foreach ($X->ResultGet->CbteTipo as $Y)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$Y->Id, $Y->Desc));
    }
  fclose($fh);
}
#==============================================================================
function FEParamGetTiposDoc ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FEParamGetTiposDoc($params);
  CheckErrors($results, 'FEParamGetTiposDoc', $client);
  $X=$results->FEParamGetTiposDocResult;
  $fh=fopen("TiposDoc.txt","w");
  foreach ($X->ResultGet->DocTipo as $Y)
    {
      fwrite($fh,sprintf("%5s  %-30s\n",$Y->Id, $Y->Desc));
    }
  fclose($fh);
}
#==============================================================================
function FECompTotXRequest ($client, $token, $sign, $CUIT)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $results=$client->FECompTotXRequest($params);
  CheckErrors($results, 'FECompTotXRequest', $client);
  $X=$results->FECompTotXRequestResult;
  return $X->RegXReq;
}
#==============================================================================
function FECompUltimoAutorizado ($client, $token, $sign, $CUIT, $PV, $TC)
{
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $params->PtoVta = $PV;
  $params->CbteTipo = $TC;
  $results=$client->FECompUltimoAutorizado($params);
  CheckErrors($results, 'FECompUltimoAutorizado', $client);
  $X=$results->FECompUltimoAutorizadoResult;
  printf("PV=%s  / TC=%s  /  Ult.Cbte=%s\n",$PV, $TC,$X->CbteNro);
  return $X->CbteNro;
}
#==============================================================================
function FECAESolicitar ($client, $params)
{
  $results=$client->FECAESolicitar($params);
  CheckErrors($results, 'FECAESolicitar', $client);
  $C=$results->FECAESolicitarResult->FeCabResp;
  $D=$results->FECAESolicitarResult->FeDetResp;
  printf("Resultado Cabecera=%s\n",$C->Resultado);
  foreach ($D->FECAEDetResponse as $d)
    {
      printf("Resultado Cbte#%s = %s  /  CAE=%s  /  vto=%s\n", 
        $d->CbteDesde, $d->Resultado, $d->CAE, $d->CAEFchVto);
      if (isset($d->Observaciones))
        {
          foreach ($d->Observaciones->Obs as $O)
            {
              printf("Obs: Code=%s  /  Msg=%s\n",$O->Code, $O->Msg);
            }
        }
    }
}
#==============================================================================
function EmitirFC ($client, $token, $sign, $CUIT, $PV, $CANT)
{
  $TC=1;
  $ULT_CBTE=FECompUltimoAutorizado($client, $token, $sign, CUIT, $PV, 1);
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $FeCabReq->CantReg = $CANT;
  $FeCabReq->PtoVta = $PV;
  $FeCabReq->CbteTipo = $TC;
  $FeDetReq=array();
  for ($i=0;$i<$CANT;$i++)
    {
      $FEDetRequest='';
      $FEDetRequest->Concepto=1;
      $FEDetRequest->DocTipo=80;
      $FEDetRequest->DocNro=33693450239;
      $FEDetRequest->CbteDesde=$ULT_CBTE+$i+1;
      $FEDetRequest->CbteHasta=$ULT_CBTE+$i+1;
      $FEDetRequest->CbteFch=date('Ymd',date('U'));
      $FEDetRequest->ImpTotal=100 * 1.21 + 0.00;
      $FEDetRequest->ImpTotConc=0;
      $FEDetRequest->ImpNeto=100;
      $FEDetRequest->ImpOpEx=0;
      $FEDetRequest->ImpTrib=0;
      $FEDetRequest->ImpIVA=100 * 0.21;
      $FEDetRequest->FchVtoPago=date('Ymd',date('U'));
      $FEDetRequest->MonId='PES';
      $FEDetRequest->MonCotiz=1;
      $FEDetRequest->FchServDesde=date('Ymd',date('U'));
      $FEDetRequest->FchServHasta=date('Ymd',date('U'));
      $FEDetRequest->Iva->AlicIva->Id=5;
      $FEDetRequest->Iva->AlicIva->BaseImp=100;
      $FEDetRequest->Iva->AlicIva->Importe=21;
      $FeDetReq[$i]=$FEDetRequest;
    }
  $params->FeCAEReq->FeCabReq = $FeCabReq;
  $params->FeCAEReq->FeDetReq = $FeDetReq;
  FECAESolicitar($client, $params);
}
#==============================================================================
function FECompConsultar ($client, $token, $sign, $CUIT, $PV, $TC)
{
  $ULT_CBTE=FECompUltimoAutorizado($client, $token, $sign, CUIT, $PV, $TC);
  $params->Auth->Token = $token;
  $params->Auth->Sign = $sign;
  $params->Auth->Cuit = $CUIT;
  $params->FeCompConsReq->CbteTipo=$TC;
  $params->FeCompConsReq->CbteNro=$ULT_CBTE;
  $params->FeCompConsReq->PtoVta=$PV;
  $results=$client->FECompConsultar($params);
  CheckErrors($results, 'FECompConsultar', $client);
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
#FEDummy($client);
#FEParamGetTiposTributos($client, $token, $sign, CUIT);
#FEParamGetTiposMonedas($client, $token, $sign, CUIT);
#FEParamGetTiposIva($client, $token, $sign, CUIT);
#FEParamGetTiposOpcional($client, $token, $sign, CUIT);
#FEParamGetTiposConcepto($client, $token, $sign, CUIT);
##FEParamGetPtosVenta($client, $token, $sign, CUIT);
#FEParamGetTiposCbte($client, $token, $sign, CUIT);
#FEParamGetTiposDoc($client, $token, $sign, CUIT);
#FEParamGetCotizacion($client, $token, $sign, CUIT, "DOL");
$MAX_CBTE=FECompTotXRequest($client, $token, $sign, CUIT);
printf("Max Cbte por request=%s\n",$MAX_CBTE);
$PV=5;
$CANT=1;
EmitirFC($client, $token, $sign, CUIT, $PV, $CANT);
FECompConsultar($client, $token, $sign, CUIT, $PV, 1);
?>
