<?php
namespace app\myexec\controller;
use think\Db;
use think\Request;
class Exceltocsut
{
   function test(){
       //cs_orders_ship=>cs_orders_price
       $i = Request::instance()->param('num');
       $opencountry = Db::connect('db_csut')
        ->table('open_countries')->field('ename,currency_unit,currency_symbols')->select();
       $countrys = array();
       foreach($opencountry as $val){
           $countrys[$val['currency_unit']] = [$val['ename'],$val['currency_symbols']];
       }
       $i = !empty($i)?$i:0;
       $n=2000;
       do{
           $start = $i*$n;
           $list = Db::connect('db_csut')
            ->table('cs_orders_ship')->where('status',1)->limit($start,$n)->select();
           $a = count($list);
           foreach($list as $key => $val){
               Db::startTrans();
               try{
                   $address = array();
                   $address['order_id'] = $val['orderid'];
                   $res = $this->checkaddr($val['address']);
                   if(!$res) continue;
                   $addr = array(
                       'addr'=>$res['address'],
                       'country'=>$res['country'],
                       'name'=>$val['user_name'],
                       'shipping_code' => $val['shipping_code'],
                       'emailname'=>$this->myemail2($val['user_name']),
                   );
                   $address['addr'] = json_encode($addr);
                   $address['source'] = 'mv';
                   Db::connect('db_csut')
                   ->table('ec_order_address')->insert($address);
                   $data = array();
                   $data['order_id'] = $val['orderid'];
                   $data['sale_price'] = $data['act_price'] = intval($val['amount']*100);
                   $data['order_status'] = 2;
                   $data['order_time'] = $data['pay_time'] = strtotime($val['trans_date'])*1000;
                   $data['ratecode'] = $val['currency'];
                   $data['currency'] = $countrys[$val['currency']][1];
                   $data['pay_way'] = 'UnionPay';
                   $data['business_id'] = 1007900;
                   $data['type'] = 'excel';
                   $data['operate_remark'] = $val['cardno'];
                   Db::connect('db_csut')
                        ->table('cs_orders_price_1')->insert($data);
                   echo $key."\n";
                   
               }catch (\Exception $e){
                    Db::rollback();
                    echo $e->getMessage()."\n";
               }
                    Db::commit();
           }
           $i++;
           dump([$a,$n]);
       }while($a==$n);
   }
   
   function checkaddr($addr){
       //250 east 144th street apt 2f,Bronx,United States
       $pattern = '/[^a-zA-Z0-9\s\,\/#@\$\&\(\)\-\_\.]/';
       preg_match($pattern, $addr, $matches);
       if(count($matches)>0){
           //中国日本韩国的不要
           return false;
       }
       $res = array();
       $address = explode(',', $addr);
       $res['country'] = end($address);
       //验证国家是否存在
       $check = Db::table('msp_tfa_country_codes')->where('name',$res['country'])->find();
       if(empty($check)) return false;
       $res['address'] = $addr;
       return $res;
   }
   
   function myemail2($name=''){
       $name = str_replace(' ', '_', $name);
       $name = str_replace(',', '_', $name);
       $name = str_replace('\'', '', $name);
       return strtolower($name);
   }
   
   
}