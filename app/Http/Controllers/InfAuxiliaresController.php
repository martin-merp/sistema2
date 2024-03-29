<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Empresa;
//use App\Categoria;

class InfAuxiliaresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        echo "indexx";
    }

    public function selectInformes(Request $request){
        //if (!$request->ajax()) return redirect('/');
        $cons="SELECT numero,nom_informe from informes_contables where condicion = 1 and tipo_informe = 'Auxiliares' order by nom_informe";        
        //echo $cons;
        $informes = DB::select($cons);
        return ['informes' => $informes];
    }
    public function AuxConSaldos(Request $request){
       // print_r($request); exit; die;
        $fec_ini = $request->fecha_ini;
        $fec_fin = $request->fecha_fin;
        $cuenta_ini =""; $cuenta_fin = ""; $id_tercero="";
        if($request->cuenta_ini) $cuenta_ini =" and num_cuenta>='".$request->cuenta_ini."'";
        if($request->cuenta_fin) $cuenta_fin =" and num_cuenta<='".$request->cuenta_fin."'";
        if($request->id_tercero) $id_tercero =" and cuentas.tercero='".$request->id_tercero."'";
        if($request->id_formato) $id_tercero =" and conf_formatos.id='".$request->id_formato."'";
        $Aux = array();
        $html = "";
        //print_r($_GET);
        //echo "cuenta_ini = $cuenta_ini";
        $cons="SELECT cast(num_cuenta as integer) as num_cuenta,cuentas.fecha,nombre_formato,cuentas.numero ,cuentas.doc_afecta_long , cuentas.detalle, personas.nombre,nombre1,nombre2,apellido1,apellido2,num_documento,debe,haber , (debe - haber) as saldo, cuentas.id, plan_cuentas.nombre as nom_cuenta FROM cuentas,conf_formatos,formatos, personas, plan_cuentas WHERE cuentas.id_formato = formatos.id and formatos.formato = conf_formatos.id and plan_cuentas.id = cuentas.cuenta and personas.id = cuentas.tercero and cuentas.fecha >= '$fec_ini' and cuentas.fecha <='$fec_fin' $cuenta_ini $cuenta_fin $id_tercero order by num_cuenta asc, cuentas.fecha";
        $AuxConSaldos = DB::select($cons);
        $cuenta_ant = "";
        $sum_d = 0; $sum_h = 0; $sum_s = 0;
        foreach($AuxConSaldos as $acs){
            if($cuenta_ant != $acs->num_cuenta){
                $cuenta_ant = $acs->num_cuenta;
            }
            if(!isset($Aux[$cuenta_ant]['debe']))
                $Aux[$cuenta_ant]['debe']=0;
            if(!isset($Aux[$cuenta_ant]['haber']))
                $Aux[$cuenta_ant]['haber']=0;
            if(!isset($Aux[$cuenta_ant]['saldo']))
                $Aux[$cuenta_ant]['saldo']=0;
            if(!isset($Aux[$cuenta_ant]['cuenta']))
                $Aux[$cuenta_ant]['cuenta']=$acs->num_cuenta;
            if(!isset($Aux[$cuenta_ant]['nom_cuenta']))
                $Aux[$cuenta_ant]['nom_cuenta']=$acs->nom_cuenta;
            $sum_d += $acs->debe;
            $sum_h += $acs->haber;
            $sum_s += $acs->saldo;
            $Aux[$cuenta_ant]['dets_cuentas'][] = $acs;
            $Aux[$cuenta_ant]['debe']+= $acs->debe;
            $Aux[$cuenta_ant]['haber']+= $acs->haber;
            $Aux[$cuenta_ant]['saldo']+= $acs->saldo;

        }
        //print_r($Aux);
        $long = count($Aux); $cont=0;
        foreach($Aux as $clave => $a){
            $cont++;
            $html.= '<table class="table table-bordered table-striped table-sm" style="font-size: 12px !important;">';
            $html.='<tr><th>Cuenta</th><th colspan="7"><center>'.$a['cuenta'].' - '.$a['nom_cuenta'].'</center></th><th>Saldo</th><th>0.00</th></tr><tr><th style="text-align: center;">Fecha</th><th style="text-align: center;">Comprobante</th><th style="text-align: center;">Número</th><th style="text-align: center;">Doc Ref</th><th style="text-align: center;">Descripcion</th><th style="text-align: center;">Tercero</th><th style="text-align: center;">Identficación</th><th style="text-align: center;">Debitos</th><th style="text-align: center;">Creditos</th><th style="text-align: center;">Saldo</th></tr>';
            foreach($a['dets_cuentas'] as $key => $dc){
                if($dc->doc_afecta_long=="''"){$dc->doc_afecta_long=null;}
                if($dc->nombre!="''") 
                    $nombre=$dc->nombre;
                else{
                    $nombre= $dc->nombre1." ".$dc->nombre2." ".$dc->apellido1." ".$dc->apellido2; 
                }
                $html.='<tr><td>'.$dc->fecha.'</td><td>'.$dc->nombre_formato.'</td><td>'.$dc->numero.'</td><td>'.$dc->doc_afecta_long.'</td><td>'.$dc->detalle.'</td><td>'.$nombre.' </td><td>'.$dc->num_documento.'</td><td style="    text-align: right;">'.number_format($dc->debe,2).'</td><td style="    text-align: right;">'.number_format($dc->haber,2).'</td><td style="    text-align: right;">'.number_format($dc->saldo,2).'</td></tr>';

            }
            $html.='<tr><th style="text-align: right;" colspan="7">TOTALES</th><th style="text-align: right;">'.number_format($a['debe'],2).'</th><th style="text-align: right;">'.number_format($a['haber'],2).'</th><th style="text-align: right;">'.number_format($a['saldo'],2).'</th></tr>';


            if($cont==$long){
                $html.= '<tr><th style="text-align: right;" colspan="7">GRAN TOTAL</th><th style="text-align: right;">'.number_format($sum_d,2).'</th><th style="text-align: right;">'.number_format($sum_h,2).'</th><th style="text-align: right;">'.number_format($sum_s,2).'</th></tr>';
            }
            $html.= '</table><br>';

        }

        return ['html' => $html ] ;
    }

    public function AuxSaldosExel(Request $request){
        //echo "llegaaaa"; exit; die;
        $fec_ini = $request->fecha_ini;
        $fec_fin = $request->fecha_fin;
        $cuenta_ini =""; $cuenta_fin = ""; $id_tercero="";
        if($request->cuenta_ini) $cuenta_ini =" and num_cuenta>='".$request->cuenta_ini."'";
        if($request->cuenta_fin) $cuenta_fin =" and num_cuenta<='".$request->cuenta_fin."'";
        if($request->id_tercero) $id_tercero =" and cuentas.tercero='".$request->id_tercero."'";
        if($request->id_formato) $id_tercero =" and conf_formatos.id='".$request->id_formato."'";
        $Aux = array();
        $html = "";
        //print_r($_GET);
        //echo "cuenta_ini = $cuenta_ini";
        $cons="SELECT cast(num_cuenta as integer) as num_cuenta,cuentas.fecha,nombre_formato,cuentas.numero ,cuentas.doc_afecta_long , cuentas.detalle, personas.nombre,nombre1,nombre2,apellido1,apellido2,num_documento,debe,haber , (debe - haber) as saldo, cuentas.id, plan_cuentas.nombre as nom_cuenta FROM cuentas,conf_formatos,formatos, personas, plan_cuentas WHERE cuentas.id_formato = formatos.id and formatos.formato = conf_formatos.id and plan_cuentas.id = cuentas.cuenta and personas.id = cuentas.tercero and cuentas.fecha >= '$fec_ini' and cuentas.fecha <='$fec_fin' $cuenta_ini $cuenta_fin $id_tercero order by num_cuenta asc, cuentas.fecha";
        $AuxConSaldos = DB::select($cons);
        $cuenta_ant = "";
        $sum_d = 0; $sum_h = 0; $sum_s = 0;
        foreach($AuxConSaldos as $acs){
            if($cuenta_ant != $acs->num_cuenta){
                $cuenta_ant = $acs->num_cuenta;
            }
            if(!isset($Aux[$cuenta_ant]['debe']))
                $Aux[$cuenta_ant]['debe']=0;
            if(!isset($Aux[$cuenta_ant]['haber']))
                $Aux[$cuenta_ant]['haber']=0;
            if(!isset($Aux[$cuenta_ant]['saldo']))
                $Aux[$cuenta_ant]['saldo']=0;
            if(!isset($Aux[$cuenta_ant]['cuenta']))
                $Aux[$cuenta_ant]['cuenta']=$acs->num_cuenta;
            if(!isset($Aux[$cuenta_ant]['nom_cuenta']))
                $Aux[$cuenta_ant]['nom_cuenta']=$acs->nom_cuenta;
            $sum_d += $acs->debe;
            $sum_h += $acs->haber;
            $sum_s += $acs->saldo;
            $Aux[$cuenta_ant]['dets_cuentas'][] = $acs;
            $Aux[$cuenta_ant]['debe']+= $acs->debe;
            $Aux[$cuenta_ant]['haber']+= $acs->haber;
            $Aux[$cuenta_ant]['saldo']+= $acs->saldo;
            

        }
        //print_r($Aux);
        $long = count($Aux); $cont=0;
        foreach($Aux as $clave => $a){
            $cont++;
            $html.= '<table class="table table-bordered table-striped table-sm" style="font-size: 12px !important;">';
            $html.='<tr><th>Cuenta</th><th colspan="7"><center>'.$a['cuenta'].' - '.$a['nom_cuenta'].'</center></th><th>Saldo</th><th>0.00</th></tr><tr><th style="text-align: center;">Fecha</th><th style="text-align: center;">Comprobante</th><th style="text-align: center;">Número</th><th style="text-align: center;">Doc Ref</th><th style="text-align: center;">Descripcion</th><th style="text-align: center;">Tercero</th><th style="text-align: center;">Identficación</th><th style="text-align: center;">Debitos</th><th style="text-align: center;">Creditos</th><th style="text-align: center;">Saldo</th></tr>';
            foreach($a['dets_cuentas'] as $key => $dc){
                if($dc->doc_afecta_long=="''"){$dc->doc_afecta_long=null;}
                if($dc->nombre!="''") 
                    $nombre=$dc->nombre;
                else{
                    $nombre= $dc->nombre1." ".$dc->nombre2." ".$dc->apellido1." ".$dc->apellido2; 
                }
                $html.='<tr><td>'.$dc->fecha.'</td><td>'.$dc->nombre_formato.'</td><td>'.$dc->numero.'</td><td>'.$dc->doc_afecta_long.'</td><td>'.$dc->detalle.'</td><td>'.$nombre.' </td><td>'.$dc->num_documento.'</td><td style="    text-align: right;">'.number_format($dc->debe,2).'</td><td style="    text-align: right;">'.number_format($dc->haber,2).'</td><td style="    text-align: right;">'.number_format($dc->saldo,2).'</td></tr>';
                
            }
            $html.='<tr><th style="text-align: right;" colspan="7">TOTALES</th><th style="text-align: right;">'.number_format($a['debe'],2).'</th><th style="text-align: right;">'.number_format($a['haber'],2).'</th><th style="text-align: right;">'.number_format($a['saldo'],2).'</th></tr>';

            
            if($cont==$long){
                $html.= '<tr><th style="text-align: right;" colspan="7">GRAN TOTAL</th><th style="text-align: right;">'.number_format($sum_d,2).'</th><th style="text-align: right;">'.number_format($sum_h,2).'</th><th style="text-align: right;">'.number_format($sum_s,2).'</th></tr>';
            }
            $html.= '</table><br>';
            
        }
        
        

                      
       // return ['html' => $html ] ;
       return view('excel.aux_informes')->with('html', $html);
    }

    public function redirect_log(){
        return redirect('/');
    }
   

    
}
