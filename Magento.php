<?php
namespace app\myexec\controller;
use think\Db;

class Magento
{
    /* 复制订单  */
    function orders(){
        //sales_order
        $orders = Db::table('sales_order')
        ->where('entity_id','<',46)
        ->where('customer_id','>',9)
        ->where('customer_id','<',19)
        ->where('store_id','in','1,2,3,4,10,15')
        ->select();
        $res = Db::table('sales_order_address')
        ->where('parent_id','<',46)
        ->select();
        
        foreach($res as $kk=>$vv){
            unset($vv['entity_id']);
            $address[$vv['parent_id']][] = $vv;
        }
        $res = Db::table('sales_order_grid')
        ->where('entity_id','<',46)
        ->select();
        foreach($res as $kk=>$vv){
            $orderid= $vv['entity_id'];
            unset($vv['entity_id']);
            unset($vv['increment_id']);
            $grid[$orderid] = $vv;
        }
        $res = Db::table('sales_order_item')
        ->where('order_id','<',46)
        ->select();
        foreach($res as $kk=>$vv){
            $orderid= $vv['order_id'];
            unset($vv['order_id']);
            unset($vv['item_id']);
            $item[$orderid][] = $vv;
        }
        $res = Db::table('sales_order_payment')
        ->where('parent_id','<',46)
        ->select();
        foreach($res as $kk=>$vv){
            $orderid= $vv['parent_id'];
            unset($vv['parent_id']);
            unset($vv['entity_id']);
            $payment[$orderid] = $vv;
        }
        for($i=0;$i<5000;$i++){
            $time = date("Y-m-d H:i:s",mt_rand(1420041600, 1556640000));
            foreach($orders as $key => $val){
                $orderid = $val['entity_id'];
                unset($val['entity_id']);
                unset($val['increment_id']);
                $val['created_at'] = $val['updated_at'] = $time;
                $n = Db::table('sales_order')->insert($val);
                $orderId = Db::name('sales_order')->getLastInsID();
                foreach($address[$orderid] as $k=> $v){
                    $address[$orderid][$k]['parent_id'] = $orderId;
                }
                $n = Db::table('sales_order_address')->insertAll($address[$orderid]);
                $grid[$orderid]['entity_id'] = $orderId;
                $n = Db::table('sales_order_grid')->insert($grid[$orderid]);
                foreach($item[$orderid] as $k => $v){
                    $item[$orderid][$k]['order_id'] = $orderId;
                }
                if(count($item[$orderid])>0){
                   $n =  Db::table('sales_order_item')->insertAll($item[$orderid]);
                }
                $payment[$orderid]['parent_id'] = $orderId;
                $n = Db::table('sales_order_payment')->insert($payment[$orderid]);
            }
            echo $i."\n";
        }
    }
    
    /* 复制订单  */
    function orders2(){
        //sales_order
        $act_order_id = 46;
        $orders = Db::table('sales_order')
        ->where('entity_id','<',$act_order_id)
        ->where('customer_id','<',13)
        ->where('store_id','in','1,2,3,4,10,15')
        ->select();
        $res = Db::table('sales_order_address')
        ->where('parent_id','<',$act_order_id)
        ->select();
        foreach($res as $kk=>$vv){
            unset($vv['entity_id']);
            $address[$vv['parent_id']][] = $vv;
        }
//         $res = Db::query('SELECT AUTO_INCREMENT as id FROM information_schema.tables WHERE table_name="sales_order"');
        $res = Db::query("select max(entity_id) as id from sales_order");
        $orderId = $res[0]['id']+1000;
        $res = Db::table('sales_order_grid')
        ->where('entity_id','<',$act_order_id)
        ->select();
        foreach($res as $kk=>$vv){
            $orderid= $vv['entity_id'];
            unset($vv['entity_id']);
            unset($vv['increment_id']);
            $grid[$orderid] = $vv;
        }
        $res = Db::table('sales_order_item')
        ->where('order_id','<',$act_order_id)
        ->select();
        foreach($res as $kk=>$vv){
            $orderid= $vv['order_id'];
            unset($vv['order_id']);
            unset($vv['item_id']);
            $item[$orderid][] = $vv;
        }
        $res = Db::table('sales_order_payment')
        ->where('parent_id','<',$act_order_id)
        ->select();
        foreach($res as $kk=>$vv){
            $orderid= $vv['parent_id'];
            unset($vv['parent_id']);
            unset($vv['entity_id']);
            $payment[$orderid] = $vv;
        }
        for($i=0;$i<10;$i++){
            $file = 'magento38_'.$i.'.sql';
            for($j=0;$j<60000;$j++){
            $time = date("Y-m-d H:i:s",mt_rand(1514736000, 1556640000));
            foreach($orders as $key => $val){
                $orderid = $val['entity_id'];
                $val['entity_id']=$orderId;
                unset($val['increment_id']);
                $val['created_at'] = $val['updated_at'] = $time;
                $n = Db::table('sales_order')->fetchSql(true)->insert($val);
                file_put_contents('/home/www/'.$file, $n.";\n",FILE_APPEND);
                
                foreach($address[$orderid] as $k=> $v){
                    $address[$orderid][$k]['parent_id'] = $orderId;
                }
                $n = Db::table('sales_order_address')->fetchSql(true)->insertAll($address[$orderid]);
                file_put_contents('/home/www/'.$file, $n.";\n",FILE_APPEND);
                $grid[$orderid]['entity_id'] = $orderId;
                $n = Db::table('sales_order_grid')->fetchSql(true)->insert($grid[$orderid]);
                file_put_contents('/home/www/'.$file, $n.";\n",FILE_APPEND);
                foreach($item[$orderid] as $k => $v){
                    $item[$orderid][$k]['order_id'] = $orderId;
                }
                if(count($item[$orderid])>0){
                    $n =  Db::table('sales_order_item')->fetchSql(true)->insertAll($item[$orderid]);
                    file_put_contents('/home/www/'.$file, $n.";\n",FILE_APPEND);
                }
                $payment[$orderid]['parent_id'] = $orderId;
                $n = Db::table('sales_order_payment')->fetchSql(true)->insert($payment[$orderid]);
                file_put_contents('/home/www/'.$file, $n.";\n",FILE_APPEND);
                $orderId++;
            }
            if($j%10<1) {
                echo $j."\n";
            }
        }
        echo $i."\n";
        }
    }
    
//     delete from sales_order where entity_id>37;
//     delete from sales_order_address where parent_id>37;
//     delete from sales_order_grid where entity_id>37;
//     delete from sales_order_item where order_id>37;
//     delete from sales_order_payment where parent_id>37;
    /* 复制121订单  */
    function orders3(){
        //sales_order
        $act_order_id = 21;
        $orders = Db::table('sales_order')
        ->where('entity_id','<',$act_order_id)
//         ->where('store_id','in','1,2,3,4,10,15')
        ->select();
        $count = count($orders);
        $res = Db::table('sales_order_address')
        ->where('parent_id','<',$act_order_id)
        ->select();
        foreach($res as $kk=>$vv){
            unset($vv['entity_id']);
            $address[$vv['parent_id']][] = $vv;
        }
        $address = array_values($address);
        //         $res = Db::query('SELECT AUTO_INCREMENT as id FROM information_schema.tables WHERE table_name="sales_order"');
        $res = Db::query("select max(entity_id) as id from sales_order");
        $orderId = $res[0]['id']+1;
        $res = Db::table('sales_order_grid')
        ->where('entity_id','<',$act_order_id)
        ->select();
        foreach($res as $kk=>$vv){
            $orderid= $vv['entity_id'];
            unset($vv['entity_id']);
            unset($vv['increment_id']);
            $grid[$orderid] = $vv;
        }
        $res = Db::table('sales_order_item')
        ->where('order_id','<',$act_order_id)
        ->select();
        foreach($res as $kk=>$vv){
            $orderid= $vv['order_id'];
            unset($vv['order_id']);
            unset($vv['item_id']);
            $item[$orderid][] = $vv;
        }
        $res = Db::table('sales_order_payment')
        ->where('parent_id','<',$act_order_id)
        ->select();
        foreach($res as $kk=>$vv){
            $orderid= $vv['parent_id'];
            unset($vv['parent_id']);
            unset($vv['entity_id']);
            $payment[$orderid] = $vv;
        }
        for($i=1;$i<50;$i++){
            $file = 'magento888_'.$i.'.sql';
            for($j=0;$j<10000;$j++){
                $time = date("Y-m-d H:i:s",mt_rand(1559061171, 1559664000));
                foreach($orders as $key => $val){
                    $orderid = $val['entity_id'];
                    $val['entity_id']=$orderId;
                    //000000001;
                    $idlen = strlen($orderId);
                    if($idlen>9){
                        $val['increment_id'] = $orderId;
                    }else{
                        $val['increment_id'] = sprintf("%09d", $orderId);
                    }
                    $val['created_at'] = $val['updated_at'] = $time;
                    $val['customer_firstname'] = $this->myfirstName();
                    $val['customer_lastname'] = $this->mylastName();
                    $n = Db::table('sales_order')->fetchSql(true)->insert($val);
                    file_put_contents('/home/www/'.$file, $n.";\n",FILE_APPEND);
                    $adi = rand(1, $count)-1;
                    foreach($address[$adi] as $k=> $v){
                        $address[$adi][$k]['parent_id'] = $orderId;
                        $address[$adi][$k]['firstname'] = $val['customer_firstname'];
                        $address[$adi][$k]['lastname'] = $val['customer_lastname'];
                    }
                    $n = Db::table('sales_order_address')->fetchSql(true)->insertAll($address[$adi]);
                    file_put_contents('/home/www/'.$file, $n.";\n",FILE_APPEND);
                    $grid[$orderid]['entity_id'] = $orderId;
                    $grid[$orderid]['increment_id'] = $val['increment_id'];
                    $grid[$orderid]['created_at'] = $grid[$orderid]['updated_at'] = $time;
                    $n = Db::table('sales_order_grid')->fetchSql(true)->insert($grid[$orderid]);
                    file_put_contents('/home/www/'.$file, $n.";\n",FILE_APPEND);
                    foreach($item[$orderid] as $k => $v){
                        $item[$orderid][$k]['order_id'] = $orderId;
                        $item[$orderid][$k]['created_at'] = $time;
                        $item[$orderid][$k]['updated_at'] = $time;
                    }
                    if(count($item[$orderid])>0){
                        $n =  Db::table('sales_order_item')->fetchSql(true)->insertAll($item[$orderid]);
                        file_put_contents('/home/www/'.$file, $n.";\n",FILE_APPEND);
                    }
                    $payment[$orderid]['parent_id'] = $orderId;
                    $n = Db::table('sales_order_payment')->fetchSql(true)->insert($payment[$orderid]);
                    file_put_contents('/home/www/'.$file, $n.";\n",FILE_APPEND);
                    $orderId++;
                }
                if($j%10<1) {
                    echo $j."\n";
                }
            }
                echo $i."\n";
        }
    }
    
    
    /* 真实订单  */
    function ecorders(){
        //导入2018年的订单
        $i=0;
        $currency_array = array('USD'=>'1','GBP'=>'0.763','EUR'=>'0.891','HKD'=>'7.81','CNY'=>'6.72','MXN'=>'18.83');//,'GBP'=>'0.763','EUR'=>'0.891','HKD'=>'7.81','CNY'=>'6.72','MXN'=>'18.83'
        $codes = array_keys($currency_array);
        //15Canceled 11Pending 12Shipped 2Complete 14Reversed
        $order_status_ec = array('a15'=>'canceled', 'a11'=>'pending', 'a12'=>'processing', 'a2'=>'processing', 'a14'=>'paypal_reversed');
        $n = 500;
        $acount = Db::connect('db_address')
        ->table('fakeper_2')->count();
        $acount--;
        $insertMember = config('isapp')>0?'insertMember':'insertMember2';
        $count = Db::connect('db_csut')
        ->table('cs_member')
        ->count();
        $count--;
        do{
            $start = $i*$n;//
            $list = Db::connect('db_csut')
            ->table('cs_orders_price_dy')
            ->where(function ($query) {
                $query->where('type', 'ecommerce')->whereor('type', 'gateway');
            })
            ->where('ratecode','in',$codes)
            ->where('sale_price','>','2800')
            ->order('order_time','asc')
            //                     ->where('order_time','>','1551257600000')
            //                     ->where('order_time','<','1541001600000')//2018-11-1
            ->limit($start,$n)
            ->select();
    
            $ulist = Db::connect('db_weibo')
            ->table('face_user')
            ->limit($start,$n)
            ->select();
            $a = count($list);
            $i++;
            if($a>0){
                //cs_orders_goods
                //ec_order_address
                //cs_user_coupon
                foreach($list as $key => $val){
                    //  sales_order
                    // 	sales_order_address
                    // 	sales_order_grid
                    // 	sales_order_item
                    // 	sales_order_payment
                    /* 全部转成USD */
    
                    $ratecode = $val['ratecode'];
                    $fee = 1/$currency_array[$ratecode];
                    $val['sale_price'] = $val['sale_price']*$fee;
                    $val['reduce_price'] = $val['reduce_price']*$fee;
                    $val['mail_fee'] = $val['mail_fee']*$fee;
                    $val['vat_fee'] = $val['vat_fee']*$fee;
                    $val['ratecode'] = 'USD';
                    /* 网关订单加上邮费税费 */
                    if($val['type']=='gateway'){
                        $vat_fee = ($val['sale_price']-100)/21;
                        $val['sale_price'] = $vat_fee*20;//
                        $val['mail_fee'] = 100;//邮费一元
                        $val['vat_fee'] = $vat_fee;
                    }
                    //                     Db::startTrans();
                    //                     try{
                    $fname = $ulist[$key]['user_name'];
                    $val['pay_way'] =  $val['pay_way']=='UnionPay'?'PayPal':$val['pay_way'];
                    $data = $adata = $address = array();
                    $old_order_id = $val['order_id'];
                    $uid = $val['uid'];
                    if(config('isapp')<1){
                        $sst = mt_rand(100, $count);
                        $sql = "SELECT * FROM cs_member limit $sst,1";
                        $res11 = Db::connect('db_csut')->query($sql);
                        $uid = $res11[0]['Uid'];//随机生成uid
                    }
                    $check = Db::table('customer_entity')->where('entity_id',$uid)->find();
                    //                         dump([$val,$uid]);exit;
                    if(!$check){
                        //导入用户
                        $addid = mt_rand(0, $acount);
                        $order_time = floor($val['order_time']*0.001);
    
                        $address = $this->$insertMember($uid,$fname,$addid,$order_time);
                        $address['contact_name'] = !empty($fname)?$fname:$address['contact_name'];
                        if(!$address){
                            echo "2222222222\n";
                            continue;
                        }else{
                            if(isset($address['result']) && $address['result']<1){
                                echo "22222222225\n";
                                continue;
                            }
                        }
                    }else{
                        $add = Db::table('customer_address_entity')->where("parent_id=$uid")->find();
                        $address['email'] = $check['email'];$fname;
                        $address['contact_name'] = !empty($check['firstname'])?$check['firstname']:$add['firstname'];
                        $address['address_id'] = count($add)>0?$add['entity_id']:0;
                        $address['state'] = $add['region'];
                        $address['zipcode'] = $add['postcode'];
                        $address['street_addr'] = $add['street'];
                        $address['city'] = $add['city'];
                        $address['mobile'] = $add['telephone'];
                        $address['country_id'] = $add['country_id'];
                        //复购率
                    }
                    $status = isset($order_status_ec['a'.$val['order_status']])?$order_status_ec['a'.$val['order_status']]:'holded';
                    $data['state'] =  $status=='pending'?'new':$status;
                    $data['status'] = $status;
                    $data['protect_code']  =    substr(
                        hash('sha256', uniqid($val['order_id'], true) . ':' . microtime(true)),
                        5,32);
                    $data['shipping_description'] = 'Flat Rate - Fixed';
                    $data['is_virtual'] = '0';
                    $data['store_id'] = '1';
                    $data['customer_id'] = $uid;
                    $data['base_discount_amount'] = $val['reduce_price']/100;//折扣金额
                    $data['base_discount_refunded'] = 0;//折扣返还
                    $data['base_grand_total'] = ($val['sale_price']+$val['mail_fee'])/100;//总价51.5
                    $data['base_shipping_amount'] = $data['shipping_amount'] = $val['mail_fee']/100;//5
                    $data['base_shipping_tax_amount'] = 0;//运费税
                    $data['base_tax_invoiced'] = $val['vat_fee']/100;//税费
                    $data['base_total_invoiced'] = $val['sale_price']/100;
    
                    $data['base_subtotal'] = $val['sale_price']/100;//小计46.5
                    $data['base_subtotal_invoiced'] = $data['subtotal_invoiced'] = $val['sale_price']/100;//小计46.5
                    $data['base_tax_amount'] = $val['vat_fee']/100;//税费
                    $data['base_to_global_rate'] = 1;//基于全球税率
                    $data['base_to_order_rate'] = 1;//基于订单税率
                    $data['grand_total'] = ($val['sale_price']+$val['mail_fee']+$val['vat_fee'])/100;//总价
                    $data['total_invoiced'] = $data['grand_total'];
                    $data['base_shipping_invoiced'] = $data['shipping_invoiced'] = $val['mail_fee']/100;
                    $data['base_total_paid'] = $data['total_paid'] = $data['grand_total']-$data['base_discount_amount'];
                    $data['shipping_tax_amount'] = 0;//运费税
                    $data['store_to_base_rate'] = 0;
                    $data['store_to_order_rate'] = 0;
                    $data['subtotal'] = $val['sale_price']/100;//小计
                    $data['tax_amount'] = $val['vat_fee']/100;//税费
                    $data['total_qty_ordered'] = 1;//总商品数量
                    $data['customer_is_guest'] = 1;
                    $data['customer_note_notify'] = 1;
                    $data['billing_address_id'] = 1;//帐单地址_id
                    $data['customer_group_id'] = 0;
                    $data['email_sent'] = 1;
                    $data['send_email'] = 1;
                    $data['quote_id'] = 1; //引用ID
                    $data['shipping_address_id'] = 1;//
                    $data['base_subtotal_incl_tax'] = $data['base_subtotal']+$data['tax_amount']; //基本小计含税
                    $data['base_total_due'] = $data['base_grand_total']; //基数总额到期
                    $data['subtotal_incl_tax'] = $data['base_subtotal']+$data['tax_amount']; //小计含税
                    $data['total_due'] = $data['base_grand_total']; //到期总额
                    //                         $data['customer_dob'] = 1;
                    $data['base_currency_code'] = $val['ratecode'];
                    $data['customer_email'] = isset($address['email'])?$address['email']:$this->myemail($key);
                    $data['customer_firstname'] = '';
                    $data['customer_lastname'] = $address['contact_name'];
                    $data['global_currency_code'] = 'USD';
                    $data['order_currency_code'] = $val['ratecode'];
                    $data['remote_ip'] = 1; // 远程ip
                    $data['shipping_method'] = 'flatrate_flatrate';
                    $data['store_currency_code'] = $val['ratecode'];
                    $data['store_name'] = 'Default Store View';//???????????????
                    $data['created_at'] = date("Y-m-d H:i:s",intval($val['order_time']/1000));
                    $data['updated_at'] = $data['created_at'];
                    if($val['type']=='gateway'){
                        for($jj=1;$jj<50;$jj++){
                            $sql = "select * from ec_goods_info_wm ";
                            $sql .= "where ABS($jj*act_price-{$data['base_grand_total']})<1";
                            $igood = Db::connect('db_csut')->query($sql);
                            if(count($igood)>0) break;
                        }
                        if(count($igood)<1) continue;
                        $abc = mt_rand(1, count($igood));
                        $abc--;
                        $goods[0]['goods_num'] = $jj;
                        $goods[0]['goods_id'] = $igood[$abc]['goods_id'];
                        $goods[0]['goods_name'] = $igood[$abc]['title'];
                        $goods[0]['total_price'] = $igood[$abc]['act_price']*$jj*100;
                        $items = json_decode(base64_decode($igood[$abc]['items']),true);
                        $igood[$abc]['items'] = json_encode(array('1'=>$items[1]));
                        unset($igood[$abc]['description']);
                        $goods[0]['goods_detail'] = json_encode($igood[$abc]);
                        $data['total_item_count'] = $jj;
                        echo $goods[0]['goods_id']."---999\n";
                    }else{
                        $goods = Db::connect('db_csut')->table('cs_orders_goods')->where("order_id",$val['order_id'])->select();
                        if(is_array($goods) && count($goods)>0){
                            echo $goods[0]['goods_id']."---888\n";
                        }
                        $data['total_item_count'] = count($goods);
                    }
                    $data['customer_gender'] = 1; //客户性别
                    $data['shipping_incl_tax'] = $val['mail_fee']/100;
                    $data['base_shipping_incl_tax'] = $val['mail_fee']/100;
                    Db::table('sales_order')->insert($data);
                    $nn = Db::table('sales_order')->getLastInsID();
                    $res['increment_id'] = $this->rzorderid($nn); //
    
                    //sales_order_address/////////////////////////////////////
                    $adata['parent_id'] = $nn;
                    //$adata['quote_address_id'] = $n;
                    $adata['region_id'] = 12;//区域id
                    $cr = Db::table('directory_country_region')
                    ->where('country_id',$address['country_id'])
                    ->where('default_name',$address['state'])
                    ->find();
                    if($cr) $adata['region_id'] = $cr['region_id'];
                    $adata['customer_address_id'] = $address['address_id'];
                    $adata['region'] = $address['state'];
                    $adata['postcode'] = $address['zipcode'];
                    $adata['lastname'] = $address['contact_name'];
                    $adata['street'] = $address['street_addr'];
                    $adata['city'] = $address['city'];
                    $adata['email'] = $address['email'];
                    $adata['telephone'] = $address['mobile'];
                    $adata['country_id'] = $address['country_id'];
                    $adata['firstname'] = '';
                    $adata['address_type'] = 'shipping';
                    $op = Db::table('sales_order_address')->insert($adata);
                    $res['shipping_address_id'] = Db::table('sales_order_address')->getLastInsID();
                    echo $op."------------address1\n";
                    $adata['address_type'] = 'billing';
                    $op = Db::table('sales_order_address')->insert($adata);
                    $res['billing_address_id'] = Db::table('sales_order_address')->getLastInsID();
                    echo $op."------------address2\n";
                    Db::table('sales_order')->where('entity_id',$nn)->update($res);
                    ///sales_order_grid//////////////////////////////////////////////////////////
                    $grid = array();
                    $grid['entity_id'] = $nn;
                    $grid['status'] = $status;
                    $grid['store_id'] = 1;
                    $grid['store_name'] = 'Default Store View';
                    $grid['customer_id'] = $uid;
                    $grid['base_grand_total'] = $data['base_grand_total'];
                    $grid['grand_total'] = $data['grand_total'];
                    $grid['increment_id'] = $res['increment_id'];
                    $grid['base_currency_code'] = $val['ratecode'];
                    $grid['order_currency_code'] = $val['ratecode'];
                    $grid['shipping_name'] = $address['contact_name'];
                    $grid['billing_name'] = $address['contact_name'];
                    $grid['created_at'] = $data['created_at'];
                    $grid['updated_at'] = $data['created_at'];
                    $grid['billing_address'] = $address['street_addr'].','.$address['city'].','.$address['state'].','.$address['zipcode'];
                    $grid['shipping_address'] = $grid['billing_address'];
                    $grid['shipping_information'] = 'Flat Rate - Fixed';
                    $grid['customer_email'] = $address['email'];
                    $grid['subtotal'] = $data['subtotal'];
                    $grid['shipping_and_handling'] = $data['shipping_amount'];
                    $grid['payment_method'] = $val['pay_way'];
                    $grid['order_approval_status'] = 1;//订单审批状态
                    $op = Db::table('sales_order_grid')->insert($grid);
                    echo $op."------------grid\n";
                    ///sales_order_item/////////////////////////////////////////////////////////
                    $good = array();
                    $good['order_id'] = $nn;
                    $good['created_at'] = $data['created_at'];
                    $good['updated_at'] = $data['created_at'];
                    $total_qty_ordered = 0;
                    foreach($goods as $vg){
                        $good['store_id'] = 1;
                        $good['product_id'] = 2;
                        if(empty($vg['goods_id'])){
                            echo "555555555\n";
                            continue;
                        }
                        $good['sku'] = 'Ali_'.$vg['goods_id'];
                        $good['row_total'] = $vg['total_price']*0.01;
                        $preprice = $vg['total_price']/$vg['goods_num']*0.01;
                        $product_entity = Db::table('catalog_product_entity')->where("sku", $good['sku'])->find();
                        if(empty($product_entity)){
                            $product_entity = $this->getproduct($preprice);
                            if(empty($product_entity)) {
                                echo "88--\n";
                                continue;
                            }
                        }
                        $good['product_id'] = $product_entity['entity_id'];
                        $good['quote_item_id'] = 1;
                        $good['product_type'] = 'simple';
                        $good['product_options'] = $this->getoptions($vg['goods_detail'],$vg['goods_num'],$good['product_id']);//'{"label":"Size","value":"S","print_value":"S","option_id":"22654","option_type":"drop_down","option_value":"116237","custom_view":false}]}';
                        $array = json_decode($vg['goods_name'],true);
                        $good['name'] = is_array($array)?$array[0]:$vg['goods_name'];
                        $good['name'] = stripslashes($good['name']);
                        //'SHEIN Yellow Split Sleeve Belted Outerwear Office Ladies Long Sleeve Plain Wrap Workwear Blazer Women Autumn Elegant Coat';
                        $good['qty_ordered'] = $vg['goods_num'];
    
                        $good['price'] = $preprice;
                        $good['base_price'] = $preprice;
                        $good['original_price'] = $preprice;
    
                        $good['base_row_total'] = $vg['total_price']*0.01;
                        $good['price_incl_tax'] = $preprice;
                        $good['base_price_incl_tax'] = $preprice;
                        $good['row_total_incl_tax'] = $vg['total_price']*0.01;
                        $good['base_row_total_incl_tax'] = $vg['total_price']*0.01;
                        $op = Db::table('sales_order_item')->insert($good);
                        echo $op."------------item\n";
                    }
                    $payment = array();
                    $payment['parent_id'] = $nn;
                    $payment['base_shipping_amount'] = $data['base_shipping_amount'];
                    $payment['shipping_amount'] = $data['shipping_amount'];
                    $payment['base_amount_ordered'] = $data['base_grand_total'];
                    $payment['amount_ordered'] = $data['grand_total'];
                    $payment['method'] = $val['pay_way'];
                    $payment['additional_information'] = '{"method_title":"Check / Money order"}';
                    $op = Db::table('sales_order_payment')->insert($payment);
                    echo $op."------------payment\n";
                    //                     }catch (\Exception $e){
                    //                         Db::rollback();
                    //                         echo $e->getMessage()."\n";
                    //                     }
                    //                     Db::commit();
                }
            }
        }while($a==$n);
        echo "*********END**********";
    }
    
    /* 真实订单  */
    function ecorders2(){
        //导入2018年的订单
        $i=0;
        $currency_array = array('USD'=>'1','GBP'=>'0.763','EUR'=>'0.891','HKD'=>'7.81','CNY'=>'6.72','MXN'=>'18.83');//,'GBP'=>'0.763','EUR'=>'0.891','HKD'=>'7.81','CNY'=>'6.72','MXN'=>'18.83'
        $codes = array_keys($currency_array);
        //15Canceled 11Pending 12Shipped 2Complete 14Reversed
        $order_status_ec = array('a15'=>'canceled', 'a11'=>'pending', 'a12'=>'processing', 'a2'=>'processing', 'a14'=>'paypal_reversed');
        $n = 500;
        $acount = Db::connect('db_address')
                    ->table('fakeper_2')->count();
        $acount--;
        $insertMember = config('isapp')>0?'insertMember':'insertMember2';
        $count = Db::connect('db_csut')
        ->table('cs_member')
        ->count();
        $count--;
        do{
            $start = $i*$n;//
            $list = Db::connect('db_csut')
                    ->table('cs_orders_price_dy')
                    ->where(function ($query) {
                        $query->where('type', 'ecommerce')->whereor('type', 'gateway');
                    })
                    ->where('ratecode','in',$codes)
                    ->where('sale_price','>','2800')
                    ->order('order_time','asc')
//                     ->where('order_time','>','1551257600000')
//                     ->where('order_time','<','1541001600000')//2018-11-1
                    ->limit($start,$n)
                    ->select();
              
            $ulist = Db::connect('db_weibo')
            ->table('face_user')
            ->limit($start,$n)
            ->select();
            $a = count($list);
            $i++;
            if($a>0){
                //cs_orders_goods
                //ec_order_address
                //cs_user_coupon
                foreach($list as $key => $val){
                    //  sales_order
                    // 	sales_order_address
                    // 	sales_order_grid
                    // 	sales_order_item
                    // 	sales_order_payment
                    /* 全部转成USD */
                    
                    $ratecode = $val['ratecode'];
                    $fee = 1/$currency_array[$ratecode];
                    $val['sale_price'] = $val['sale_price']*$fee;
                    $val['reduce_price'] = $val['reduce_price']*$fee;
                    $val['mail_fee'] = $val['mail_fee']*$fee;
                    $val['vat_fee'] = $val['vat_fee']*$fee;
                    $val['ratecode'] = 'USD';
                    /* 网关订单加上邮费税费 */
                    if($val['type']=='gateway'){
                        $vat_fee = ($val['sale_price']-100)/21;
                        $val['sale_price'] = $vat_fee*20;//
                        $val['mail_fee'] = 100;//邮费一元
                        $val['vat_fee'] = $vat_fee;
                    }
//                     Db::startTrans();
//                     try{
                        $fname = $ulist[$key]['user_name'];
                        $val['pay_way'] =  $val['pay_way']=='UnionPay'?'PayPal':$val['pay_way'];
                        $data = $adata = $address = array();
                        $old_order_id = $val['order_id'];
                        $uid = $val['uid'];
                        if(config('isapp')<1){
                            $sst = mt_rand(100, $count);
                            $sql = "SELECT * FROM cs_member limit $sst,1";
                            $res11 = Db::connect('db_csut')->query($sql);
                            $uid = $res11[0]['Uid'];//随机生成uid
                        }
                        $check = Db::table('customer_entity')->where('entity_id',$uid)->find();
//                         dump([$val,$uid]);exit;
                        if(!$check){
                            //导入用户
                            $addid = mt_rand(0, $acount);
                            $order_time = floor($val['order_time']*0.001);
                            
                            $address = $this->$insertMember($uid,$fname,$addid,$order_time);
                            $address['contact_name'] = !empty($fname)?$fname:$address['contact_name'];
                            if(!$address){
                                echo "2222222222\n";
                                continue;
                            }else{
                                if(isset($address['result']) && $address['result']<1){
                                    echo "22222222225\n";
                                    continue;
                                }
                            }
                        }else{
                            $add = Db::table('customer_address_entity')->where("parent_id=$uid")->find();
                            $address['email'] = $check['email'];$fname;
                            $address['contact_name'] = !empty($check['firstname'])?$check['firstname']:$add['firstname'];
                            $address['address_id'] = count($add)>0?$add['entity_id']:0;
                            $address['state'] = $add['region'];
                            $address['zipcode'] = $add['postcode'];
                            $address['street_addr'] = $add['street'];
                            $address['city'] = $add['city'];
                            $address['mobile'] = $add['telephone'];
                            $address['country_id'] = $add['country_id'];
                            //复购率
                        }
                        $status = isset($order_status_ec['a'.$val['order_status']])?$order_status_ec['a'.$val['order_status']]:'holded';
                        $data['state'] =  $status=='pending'?'new':$status;
                        $data['status'] = $status;
                         $data['protect_code']  =    substr(
                                hash('sha256', uniqid($val['order_id'], true) . ':' . microtime(true)),
                                5,32);
                        $data['shipping_description'] = 'Flat Rate - Fixed';
                        $data['is_virtual'] = '0';
                        $data['store_id'] = '1';
                        $data['customer_id'] = $uid;
                        $data['base_discount_amount'] = $val['reduce_price']/100;//折扣金额
                        $data['base_discount_refunded'] = 0;//折扣返还
                        $data['base_grand_total'] = ($val['sale_price']+$val['mail_fee'])/100;//总价51.5
                        $data['base_shipping_amount'] = $data['shipping_amount'] = $val['mail_fee']/100;//5
                        $data['base_shipping_tax_amount'] = 0;//运费税
                        $data['base_tax_invoiced'] = $val['vat_fee']/100;//税费
                        $data['base_total_invoiced'] = $val['sale_price']/100;
                        
                        $data['base_subtotal'] = $val['sale_price']/100;//小计46.5
                        $data['base_subtotal_invoiced'] = $data['subtotal_invoiced'] = $val['sale_price']/100;//小计46.5
                        $data['base_tax_amount'] = $val['vat_fee']/100;//税费
                        $data['base_to_global_rate'] = 1;//基于全球税率
                        $data['base_to_order_rate'] = 1;//基于订单税率
                        $data['grand_total'] = ($val['sale_price']+$val['mail_fee']+$val['vat_fee'])/100;//总价
                        $data['total_invoiced'] = $data['grand_total'];
                        $data['base_shipping_invoiced'] = $data['shipping_invoiced'] = $val['mail_fee']/100;
                        $data['base_total_paid'] = $data['total_paid'] = $data['grand_total']-$data['base_discount_amount'];
                        $data['shipping_tax_amount'] = 0;//运费税
                        $data['store_to_base_rate'] = 0;
                        $data['store_to_order_rate'] = 0;
                        $data['subtotal'] = $val['sale_price']/100;//小计
                        $data['tax_amount'] = $val['vat_fee']/100;//税费
                        $data['total_qty_ordered'] = 1;//总商品数量
                        $data['customer_is_guest'] = 1;
                        $data['customer_note_notify'] = 1;
                        $data['billing_address_id'] = 1;//帐单地址_id
                        $data['customer_group_id'] = 0;
                        $data['email_sent'] = 1;
                        $data['send_email'] = 1;
                        $data['quote_id'] = 1; //引用ID
                        $data['shipping_address_id'] = 1;//
                        $data['base_subtotal_incl_tax'] = $data['base_subtotal']+$data['tax_amount']; //基本小计含税
                        $data['base_total_due'] = $data['base_grand_total']; //基数总额到期
                        $data['subtotal_incl_tax'] = $data['base_subtotal']+$data['tax_amount']; //小计含税
                        $data['total_due'] = $data['base_grand_total']; //到期总额
//                         $data['customer_dob'] = 1;
                        $data['base_currency_code'] = $val['ratecode'];
                        $data['customer_email'] = isset($address['email'])?$address['email']:$this->myemail($key);
                        $data['customer_firstname'] = '';
                        $data['customer_lastname'] = $address['contact_name'];
                        $data['global_currency_code'] = 'USD';
                        $data['order_currency_code'] = $val['ratecode'];
                        $data['remote_ip'] = 1; // 远程ip
                        $data['shipping_method'] = 'flatrate_flatrate';
                        $data['store_currency_code'] = $val['ratecode'];
                        $data['store_name'] = 'Default Store View';//???????????????
                        $data['created_at'] = date("Y-m-d H:i:s",intval($val['order_time']/1000));
                        $data['updated_at'] = $data['created_at'];
                        if($val['type']=='gateway'){
                            for($jj=1;$jj<50;$jj++){
                                $sql = "select * from ec_goods_info_wm ";
                                $sql .= "where ABS($jj*act_price-{$data['base_grand_total']})<1";
                                $igood = Db::connect('db_csut')->query($sql);
                                if(count($igood)>0) break;
                            }
                            if(count($igood)<1) continue;
                            $abc = mt_rand(1, count($igood));
                            $abc--;
                            $goods[0]['goods_num'] = $jj;
                            $goods[0]['goods_id'] = $igood[$abc]['goods_id'];
                            $goods[0]['goods_name'] = $igood[$abc]['title'];
                            $goods[0]['total_price'] = $igood[$abc]['act_price']*$jj*100;
                            $items = json_decode(base64_decode($igood[$abc]['items']),true);
                            $igood[$abc]['items'] = json_encode(array('1'=>$items[1]));
                            unset($igood[$abc]['description']);
                            $goods[0]['goods_detail'] = json_encode($igood[$abc]);
                            $data['total_item_count'] = $jj;
                            echo $goods[0]['goods_id']."---999\n";
                        }else{
                            $goods = Db::connect('db_csut')->table('cs_orders_goods')->where("order_id",$val['order_id'])->select();
                            if(is_array($goods) && count($goods)>0){
                                echo $goods[0]['goods_id']."---888\n";
                            }
                            $data['total_item_count'] = count($goods);
                        }
                        $data['customer_gender'] = 1; //客户性别
                        $data['shipping_incl_tax'] = $val['mail_fee']/100;
                        $data['base_shipping_incl_tax'] = $val['mail_fee']/100;
                        Db::table('sales_order')->insert($data);
                        $nn = Db::table('sales_order')->getLastInsID();
                        $res['increment_id'] = $this->rzorderid($nn); //
                        
                        //sales_order_address/////////////////////////////////////
                        $adata['parent_id'] = $nn;
                        //$adata['quote_address_id'] = $n;
                        $adata['region_id'] = 12;//区域id
                        $cr = Db::table('directory_country_region')
                        ->where('country_id',$address['country_id'])
                        ->where('default_name',$address['state'])
                        ->find();
                        if($cr) $adata['region_id'] = $cr['region_id'];
                        $adata['customer_address_id'] = $address['address_id'];
                        $adata['region'] = $address['state'];
                        $adata['postcode'] = $address['zipcode'];
                        $adata['lastname'] = $address['contact_name'];
                        $adata['street'] = $address['street_addr'];
                        $adata['city'] = $address['city'];
                        $adata['email'] = $address['email'];
                        $adata['telephone'] = $address['mobile'];
                        $adata['country_id'] = $address['country_id']; 
                        $adata['firstname'] = ''; 
                        $adata['address_type'] = 'shipping';
                        $op = Db::table('sales_order_address')->insert($adata);
                        $res['shipping_address_id'] = Db::table('sales_order_address')->getLastInsID();
                        echo $op."------------address1\n";
                        $adata['address_type'] = 'billing';
                        $op = Db::table('sales_order_address')->insert($adata);
                        $res['billing_address_id'] = Db::table('sales_order_address')->getLastInsID();
                        echo $op."------------address2\n";
                        Db::table('sales_order')->where('entity_id',$nn)->update($res);
                        ///sales_order_grid//////////////////////////////////////////////////////////
                        $grid = array();
                        $grid['entity_id'] = $nn;
                        $grid['status'] = $status;
                        $grid['store_id'] = 1;
                        $grid['store_name'] = 'Default Store View';
                        $grid['customer_id'] = $uid;
                        $grid['base_grand_total'] = $data['base_grand_total'];
                        $grid['grand_total'] = $data['grand_total'];
                        $grid['increment_id'] = $res['increment_id'];
                        $grid['base_currency_code'] = $val['ratecode'];
                        $grid['order_currency_code'] = $val['ratecode'];
                        $grid['shipping_name'] = $address['contact_name'];
                        $grid['billing_name'] = $address['contact_name'];
                        $grid['created_at'] = $data['created_at'];
                        $grid['updated_at'] = $data['created_at'];
                        $grid['billing_address'] = $address['street_addr'].','.$address['city'].','.$address['state'].','.$address['zipcode'];
                        $grid['shipping_address'] = $grid['billing_address'];
                        $grid['shipping_information'] = 'Flat Rate - Fixed';
                        $grid['customer_email'] = $address['email'];
                        $grid['subtotal'] = $data['subtotal'];
                        $grid['shipping_and_handling'] = $data['shipping_amount'];
                        $grid['payment_method'] = $val['pay_way'];
                        $grid['order_approval_status'] = 1;//订单审批状态
                        $op = Db::table('sales_order_grid')->insert($grid);
                        echo $op."------------grid\n";
                        ///sales_order_item/////////////////////////////////////////////////////////
                        $good = array();
                        $good['order_id'] = $nn;
                        $good['created_at'] = $data['created_at'];
                        $good['updated_at'] = $data['created_at'];
                        $total_qty_ordered = 0;
                        foreach($goods as $vg){
                            $good['store_id'] = 1;
                            $good['product_id'] = 2;
                            if(empty($vg['goods_id'])){
                                echo "555555555\n";
                                continue;
                            }
                            $good['sku'] = 'Ali_'.$vg['goods_id'];
                            $good['row_total'] = $vg['total_price']*0.01;
                            $preprice = $vg['total_price']/$vg['goods_num']*0.01;
                            $product_entity = Db::table('catalog_product_entity')->where("sku", $good['sku'])->find();
                            if(empty($product_entity)){
                                $product_entity = $this->getproduct($preprice);
                                if(empty($product_entity)) {
                                    echo "88--\n";
                                    continue;
                                }
                            }
                            $good['product_id'] = $product_entity['entity_id'];
                            $good['quote_item_id'] = 1;
                            $good['product_type'] = 'simple';
                            $good['product_options'] = $this->getoptions($vg['goods_detail'],$vg['goods_num'],$good['product_id']);//'{"label":"Size","value":"S","print_value":"S","option_id":"22654","option_type":"drop_down","option_value":"116237","custom_view":false}]}';
                            $array = json_decode($vg['goods_name'],true);
                            $good['name'] = is_array($array)?$array[0]:$vg['goods_name'];
                            $good['name'] = stripslashes($good['name']);
                            //'SHEIN Yellow Split Sleeve Belted Outerwear Office Ladies Long Sleeve Plain Wrap Workwear Blazer Women Autumn Elegant Coat';
                            $good['qty_ordered'] = $vg['goods_num'];
                            
                            $good['price'] = $preprice;
                            $good['base_price'] = $preprice;
                            $good['original_price'] = $preprice;
                            
                            $good['base_row_total'] = $vg['total_price']*0.01;
                            $good['price_incl_tax'] = $preprice;
                            $good['base_price_incl_tax'] = $preprice;
                            $good['row_total_incl_tax'] = $vg['total_price']*0.01;
                            $good['base_row_total_incl_tax'] = $vg['total_price']*0.01;
                            $op = Db::table('sales_order_item')->insert($good);
                            echo $op."------------item\n";
                        }
                        $payment = array();
                        $payment['parent_id'] = $nn;
                        $payment['base_shipping_amount'] = $data['base_shipping_amount'];
                        $payment['shipping_amount'] = $data['shipping_amount'];
                        $payment['base_amount_ordered'] = $data['base_grand_total'];
                        $payment['amount_ordered'] = $data['grand_total'];
                        $payment['method'] = $val['pay_way'];
                        $payment['additional_information'] = '{"method_title":"Check / Money order"}';
                        $op = Db::table('sales_order_payment')->insert($payment);
                        echo $op."------------payment\n";
//                     }catch (\Exception $e){
//                         Db::rollback();
//                         echo $e->getMessage()."\n";
//                     }
//                     Db::commit();
                }
            }
        }while($a==$n);
        echo "*********END**********";
    }
    
    /* 线下十条订单模拟物流  */
    function ecorders3(){
        $table = 'ec_goods_info_wm_2019_6_10';
        $i=0;
        $currency_array = array('USD'=>'1','HKD'=>'7.81','GBP'=>'0.763','CNY'=>'6.72','EUR'=>'0.891','MXN'=>'18.83');//,'GBP'=>'0.763','EUR'=>'0.891','HKD'=>'7.81','CNY'=>'6.72','MXN'=>'18.83'
        $codes = array_keys($currency_array);
        //15Canceled 11Pending 12Shipped 2Complete 14Refunded
        $order_status_ec = array('a15'=>'canceled', 'a11'=>'pending', 'a12'=>'processing', 'a2'=>'complete', 'a14'=>'refunded','a9'=>'refunded');
        $n = 2000;//一次查询两千条
        $acount = Db::connect('db_address')
        ->table('fakeper_2')->count();
        $acount--;
        $insertMember = config('isapp')>0?'insertMember':'insertMember2';
        $count = Db::connect('db_csut')
            ->table('cs_member')
            ->count(); 
        
        $mictime = (time()-7*86400)*1000;
        $cf = false;
        $index = 0;//导入十条数据 十个分类 不同国家
        do{
            $start = $i*$n;//
            $list = Db::connect('db_csut')
                ->table('cs_orders_price_dy')
                ->where(function ($query) {
                        $query->where('type', 'ecommerce')->whereor('type', 'gateway');//->where('type', 'ecommerce');//
                    })
                ->where('ratecode','in',$codes)
//               ->where('order_status','in',['2','9','11','12','14','15'])
                ->where('order_status','in',['2'])
                ->where('sale_price','>','500')
                ->limit($start,$n)
                ->field('description,pictrues,main_pic,items,out_url,hash_url',true)
                ->select();
            $ulist = Db::connect('db_weibo')
                ->table('face_user')
                ->field('user_name')
                ->limit($start,$n)
                ->select();
            $a = count($list);
            var_dump([4,time()]);
            $count=$count-$n-1;
            $sst = mt_rand(100, $count);
            $res11 = Db::connect('db_csut')->table('cs_member')->field('Uid')->limit($sst,$n)->select();
            var_dump([5,time()]); 
            $i++;
            if($a>0){
                foreach($list as $key => $val){
                        $check = Db::table('sales_order')->where('increment_id',$val['order_id'])->find();
                        if(!$check){
                        }else{
                            continue;
                        }
                    ///////////////正式/////////////////////////
//                     $ratecode = $val['ratecode'];
                    $rin = $key%6;
                    $val['ratecode'] = $ratecode = $codes[$rin];
                    $fee = 1/$currency_array[$ratecode];
                    ////////////////////////////////////
//                     $val['sale_price'] = intval($val['sale_price']*$fee);
//                     $val['reduce_price'] = intval($val['reduce_price']*$fee);
//                     $val['mail_fee'] = intval($val['mail_fee']*$fee);
//                     $val['vat_fee'] = intval($val['vat_fee']*$fee);
//                     $val['ratecode'] = 'USD';
                    ///////////////////////////////////////
//                     echo $val['ratecode']."\n";
                    $flag = true;//能否匹配商品
                    if($val['type']=='ecommerce'){
                        $rgoods = Db::connect('db_csut')->table('cs_orders_goods')->where("order_id",$val['order_id'])->select();
                        if(count($rgoods)<1){
                            $flag=false;
                        }
                    }
                    if($val['type']=='gateway' || !$flag){
                        $sql = "select goods_id from $table ";
                        $pppprice = sprintf("%.2f",($val['sale_price']*$fee-50)*0.01);
                        $sql .= "where act_price>$pppprice limit 1";
                        $igood = Db::connect('db_csut')->query($sql);
//                         var_dump([$igood,$sql]);
                        if(count($igood)>0){
                            $pppprice = $pppprice+0.5;
                            $resg = $this->getgoodsByprice3($pppprice,$index,$table);
                            $jj = $resg[0];
                            $igood = $resg[1];
                        }
                        if(count($igood)<1) continue;
                        $index ++;
                        if($index>10) exit('10101010');
                        $abc = mt_rand(1, count($igood));
                        $abc--;
                        $val['sale_price'] = $igood[$abc]['act_price']*100;
                        $val['act_price'] = $val['sale_price']-$val['reduce_price'];
                        if($val['act_price']<0) continue;
                    }
                    /* 网关订单加上邮费税费 */
                    $stype = $this->getshiptypes($val['sale_price']);//**********************//
                    if($val['type']=='gateway'){
                        $val['mail_fee'] = $stype[2];
                    }
                    Db::startTrans();
                    try{
                        $fname = $ulist[$key]['user_name'];
                        $val['pay_way'] =  $val['pay_way']=='UnionPay'?'CreditCard':'CreditCard';//都是卡支付
                        $data = $adata = $address = array();
                        $old_order_id = $val['order_id'];
                        $uid = $val['uid'];
                        if(config('isapp')<1){
                            $uid = $res11[$key]['Uid'];
                        }
                        $check = Db::table('customer_entity')->where('entity_id',$uid)->find();
//                         var_dump([7,time()]);
                        if(!$check){
                            //导入用户
                            $addid = mt_rand(0, $acount);
                            $order_time = floor($val['order_time']*0.001); 
                            $address = $this->$insertMember($uid,$fname,$addid,$order_time,$ratecode);
//                             var_dump([8,time()]);
                            $address['contact_name'] = !empty($fname)?$fname:$address['contact_name'];
                            if(!$address){
                                echo "2222222222\n";
                                continue;
                            }else{
                                if(isset($address['result']) && $address['result']<1){
                                    echo "22222222225\n";
                                    continue;
                                }
                            }
                        }else{
                            $add = Db::table('customer_address_entity')->where("parent_id=$uid")->find();
                            $address['email'] = $check['email'];
                            $aname = $check['firstname'].' '.$check['lastname'];
                            $address['contact_name'] = !empty($aname)?$aname:$fname;
                            $address['address_id'] = count($add)>0?$add['entity_id']:0;
                            $address['state'] = $add['region'];
                            $address['zipcode'] = $add['postcode'];
                            $address['street_addr'] = $add['street'];
                            $address['city'] = $add['city'];
                            $address['mobile'] = $add['telephone'];
                            $address['country_id'] = $add['country_id'];
                        }
                        $status = isset($order_status_ec['a'.$val['order_status']])?$order_status_ec['a'.$val['order_status']]:'complete';
                        if($val['order_time']<$mictime){//超过七天没有pending
                            $status = $status=='pending'?'complete':$status;
                            $status = $status=='processing'?'complete':$status;
                        }
                        $data['state'] =  $status=='pending'?'new':$status;
                        $data['status'] = $status;
                        $data['protect_code']  =    substr(
                            hash('sha256', uniqid($val['order_id'], true) . ':' . microtime(true)),
                            5,32);
                        $data['shipping_description'] = 'Flat Rate - Fixed';
                        $data['is_virtual'] = '0';
                        $data['store_id'] = '1';
                        $data['customer_id'] = $uid;
                        $data['base_discount_amount'] = $val['reduce_price']/100;//折扣金额
                        $data['base_discount_refunded'] = 0;//折扣返还
                        $data['base_grand_total'] = sprintf("%.2f",($val['sale_price']+$val['mail_fee'])/100);//总价51.5
                        $data['base_shipping_amount'] = $data['shipping_amount'] = sprintf("%.2f",$val['mail_fee']/100);//5
                        $data['base_shipping_tax_amount'] = 0;//运费税
                        $data['base_tax_invoiced'] = sprintf("%.2f",$val['vat_fee']/100);//税费
                        $data['base_total_invoiced'] = sprintf("%.2f",$val['sale_price']/100);
    
                        $data['base_subtotal'] = sprintf("%.2f",$val['sale_price']/100);//小计46.5
                        $data['base_subtotal_invoiced'] = $data['subtotal_invoiced'] = sprintf("%.2f",$val['sale_price']/100);//小计46.5
                        $data['base_tax_amount'] = sprintf("%.2f",$val['vat_fee']/100);//税费
                        $data['base_to_global_rate'] = $currency_array[$val['ratecode']];//基于全球税率
                        $data['base_to_order_rate'] = $currency_array[$val['ratecode']];//基于订单税率
                        $data['grand_total'] = sprintf("%.2f",($val['sale_price']+$val['mail_fee']+$val['vat_fee'])/100);//总价
                        $data['grand_total'] = sprintf("%.2f",$data['grand_total']);
                        $data['total_invoiced'] = $data['grand_total'];
                        $data['base_shipping_invoiced'] = $data['shipping_invoiced'] = sprintf("%.2f",$val['mail_fee']/100);
                        $data['base_total_paid'] = $data['total_paid'] = $data['grand_total']-$data['base_discount_amount'];
                        $data['shipping_tax_amount'] = 0;//运费税
                        $data['store_to_base_rate'] = 0;
                        $data['store_to_order_rate'] = 0;
                        $data['subtotal'] = $val['sale_price']/100;//小计
                        $data['tax_amount'] = $val['vat_fee']/100;//税费
                        $data['total_qty_ordered'] = 1;//总商品数量
                        $data['customer_is_guest'] = 0;
                        $data['customer_note_notify'] = 1;
                        $data['billing_address_id'] = 1;//帐单地址_id
                        $data['customer_group_id'] = 0;
                        $data['email_sent'] = 1;
                        $data['send_email'] = 1;
                        $data['quote_id'] = 1; //引用ID
                        $data['shipping_address_id'] = 1;//
                        $data['base_subtotal_incl_tax'] = $data['base_subtotal']+$data['tax_amount']; //基本小计含税
                        $data['base_total_due'] = $data['base_grand_total']; //基数总额到期
                        $data['subtotal_incl_tax'] = $data['base_subtotal']+$data['tax_amount']; //小计含税
                        $data['total_due'] = $data['base_grand_total']; //到期总额
                        //                         $data['customer_dob'] = 1;
                        $data['base_currency_code'] = $val['ratecode'];
                        $data['customer_email'] = isset($address['email'])?$address['email']:$this->myemail($key);
                        $data['customer_firstname'] = '';
                        $data['customer_lastname'] = $address['contact_name'];
                        $data['global_currency_code'] = $ratecode;
                        $data['order_currency_code'] = $val['ratecode'];
                        $data['remote_ip'] = 1; // 远程ip
                        $data['shipping_method'] = 'flatrate_flatrate';
                        $data['store_currency_code'] = $val['ratecode'];
                        $data['store_name'] = 'Default Store View';//???????????????
                        $data['created_at'] = date("Y-m-d H:i:s",intval($val['order_time']/1000));
                        $data['updated_at'] = $data['created_at'];
                        if($val['type']=='gateway' || !$flag){
                            var_dump([9,time()]);
                            $goods[0]['goods_num'] = $jj;
                            $goods[0]['goods_id'] = $igood[$abc]['goods_id'];
                            $goods[0]['goods_name'] = $igood[$abc]['title'];
                            $goods[0]['total_price'] = $igood[$abc]['act_price']*$jj*100;
                            $items = json_decode(base64_decode($igood[$abc]['items']),true);
                            $igood[$abc]['items'] = json_encode(array('1'=>$items[1]));
                            unset($igood[$abc]['description']);
                            $goods[0]['goods_detail'] = json_encode($igood[$abc]);
                            $data['total_item_count'] = $jj;
                            echo $goods[0]['goods_id']."---999\n";
                        }else{
                            $goods = $rgoods;
                            $data['total_item_count'] = count($goods);
                        }
                        $data['customer_gender'] = 1; //客户性别
                        $data['shipping_incl_tax'] = $val['mail_fee']/100;
                        $data['base_shipping_incl_tax'] = $val['mail_fee']/100;
                        Db::table('sales_order')->insert($data);
                        $nn = Db::table('sales_order')->getLastInsID();
                        $res['increment_id'] = $val['order_id'];//$this->rzorderid($nn); //
    
                        //sales_order_address/////////////////////////////////////
                        $adata['parent_id'] = $nn;
                        //$adata['quote_address_id'] = $n;
                        $adata['region_id'] = 12;//区域id
                        $cr = Db::table('directory_country_region')
                        ->where('country_id',$address['country_id'])
                        ->where('default_name',$address['state'])
                        ->find();
                        if($cr) $adata['region_id'] = $cr['region_id'];
                        $adata['customer_address_id'] = $address['address_id'];
                        $adata['region'] = $address['state'];
                        $adata['postcode'] = $address['zipcode'];
                        $adata['lastname'] = $address['contact_name'];
                        $adata['street'] = $address['street_addr'];
                        $adata['city'] = $address['city'];
                        $adata['email'] = $address['email'];
                        $adata['telephone'] = $address['mobile'];
                        $adata['country_id'] = $address['country_id'];
                        $adata['firstname'] = '';
                        $adata['address_type'] = 'shipping';
                        $op = Db::table('sales_order_address')->insert($adata);
                        $res['shipping_address_id'] = Db::table('sales_order_address')->getLastInsID();
                        echo $op."------------address1\n";
                        $adata['address_type'] = 'billing';
                        $op = Db::table('sales_order_address')->insert($adata);
                        $res['billing_address_id'] = Db::table('sales_order_address')->getLastInsID();
                        echo $op."------------address2\n";
                        Db::table('sales_order')->where('entity_id',$nn)->update($res);
                        ///sales_order_grid//////////////////////////////////////////////////////////
                        $grid = array();
                        $grid['entity_id'] = $nn;
                        $grid['status'] = $status;
                        $grid['store_id'] = 1;
                        $grid['store_name'] = 'Default Store View';
                        $grid['customer_id'] = $uid;
                        $grid['base_grand_total'] = $data['base_grand_total'];
                        $grid['grand_total'] = $data['grand_total'];
                        $grid['increment_id'] = $res['increment_id'];
                        $grid['base_currency_code'] = $val['ratecode'];
                        $grid['order_currency_code'] = $val['ratecode'];
                        $grid['shipping_name'] = $address['contact_name'];
                        $grid['billing_name'] = $address['contact_name'];
                        $grid['created_at'] = $data['created_at'];
                        $grid['updated_at'] = $data['created_at'];
                        $grid['billing_address'] = $address['street_addr'].','.$address['city'].','.$address['state'].','.$address['zipcode'];
                        $grid['shipping_address'] = $grid['billing_address'];
                        $grid['shipping_information'] = 'Flat Rate - Fixed';
                        $grid['customer_email'] = $address['email'];
                        $grid['subtotal'] = $data['subtotal'];
                        $grid['shipping_and_handling'] = $data['shipping_amount'];
                        $grid['payment_method'] = $val['pay_way'];
                        $grid['order_approval_status'] = 1;//订单审批状态
                        $op = Db::table('sales_order_grid')->insert($grid);
                        echo $op."------------grid\n";
                        ///sales_order_item/////////////////////////////////////////////////////////
                        $good = array();
                        $good['order_id'] = $nn;
                        $good['created_at'] = $data['created_at'];
                        $good['updated_at'] = $data['created_at'];
                        $total_qty_ordered = 0;
                        foreach($goods as $vk => $vg){
                            $good['store_id'] = 1;
                            $good['product_id'] = 2;
                            if(empty($vg['goods_id'])){
                                echo "555555555\n";
                                continue;
                            }
                            $goods[$vk]['sku'] = $good['sku'] = 'Ali_'.$vg['goods_id'];
                            $good['row_total'] = $vg['total_price']*0.01;
                            $preprice = $vg['total_price']/$vg['goods_num']*0.01;
                            $product_entity = Db::table('catalog_product_entity')->where("sku", $good['sku'])->find();
                            if(empty($product_entity)){
                                for($j=1;$j<20;$j++){
                                    $pre = $j*0.1;
                                    $min_price = $preprice-$pre<0?0.01:$preprice-$pre;
                                    $max_price = $preprice+$pre;
                                    $product_entity = Db::table('catalog_product_index_price')
                                    ->where("price",'>',$min_price)
                                    ->where("price",'<',$max_price)
                                    ->find();
                                    if($product_entity) break;
                                }
                                if(empty($product_entity)){
                                    echo $good['sku']."-----88\n";
                                    continue;
                                }
                                $goods_entity = Db::table('catalog_product_entity')
                                    ->where("entity_id",$product_entity['entity_id'])
                                    ->field('sku')
                                    ->find();
                                if(empty($goods_entity)){
                                    echo "-----pp88\n";
                                    continue;
                                }
                                $goods[$vk]['sku'] = $goods_entity['sku'];
                            }
                            $good['product_id'] = $goods[$vk]['product_id'] = $product_entity['entity_id'];
                            $good['quote_item_id'] = 1;
                            $good['product_type'] = 'simple';
                            $good['product_options'] = $this->getoptions($vg['goods_detail'],$vg['goods_num'],$good['product_id']);//'{"label":"Size","value":"S","print_value":"S","option_id":"22654","option_type":"drop_down","option_value":"116237","custom_view":false}]}';
                            $array = json_decode($vg['goods_name'],true);
                            $good['name'] = is_array($array)?$array[0]:$vg['goods_name'];
                            $good['name'] = stripslashes($good['name']);
                            //'SHEIN Yellow Split Sleeve Belted Outerwear Office Ladies Long Sleeve Plain Wrap Workwear Blazer Women Autumn Elegant Coat';
                            $good['qty_ordered'] = $vg['goods_num'];
    
                            $good['price'] = $preprice;
                            $good['base_price'] = $preprice;
                            $good['original_price'] = $preprice;
    
                            $good['base_row_total'] = $vg['total_price']*0.01;
                            $good['price_incl_tax'] = $preprice;
                            $good['base_price_incl_tax'] = $preprice;
                            $good['row_total_incl_tax'] = $vg['total_price']*0.01;
                            $good['base_row_total_incl_tax'] = $vg['total_price']*0.01;
                            $op = Db::table('sales_order_item')->insert($good);
                            $goods[$vk]['order_item_id'] = Db::table('sales_order_item')->getLastInsID();
                            echo $op."------------item\n";
                        }
                        $payment = array();
                        $payment['parent_id'] = $nn;
                        $payment['base_shipping_amount'] = $data['base_shipping_amount'];
                        $payment['shipping_amount'] = $data['shipping_amount'];
                        $payment['base_amount_ordered'] = $data['base_grand_total'];
                        $payment['amount_ordered'] = $data['grand_total'];
                        $payment['method'] = $val['pay_way'];
                        "Check / Money order";
                        $payment['additional_information'] = '{"method_title":"' . $payment['method'] . '"}';
                        $op = Db::table('sales_order_payment')->insert($payment);
                        echo $op."------------payment\n";
                        //物流信息sales_shipment sales_shipment_grid 
                        //sales_shipment_item sales_shipment_track
                        $shipment = array();
                        $shipment['store_id'] = 1;
                        $shipment['total_qty'] = count($goods);
                        $shipment['order_id'] = $nn;
                        $shipment['customer_id'] = $uid;
                        $shipment['shipping_address_id'] = $res['shipping_address_id'];
                        $shipment['billing_address_id'] = $res['billing_address_id'];
                        $shipment['increment_id'] = 1;//?????
                        $shipment['created_at'] = $data['created_at'];
                        $shipment['updated_at'] = $data['created_at'];
                        $op = Db::table('sales_shipment')->insert($shipment);
                        echo $op."------------shipment\n";
                        $id = Db::table('sales_shipment')->getLastInsID();
//                         $increment_id = $this->rzorderid($id);
                        Db::table('sales_shipment')->where('entity_id',$id)->update(['increment_id'=>$res['increment_id']]);
                        $pgrid = array();
                        $pgrid['entity_id'] = $id;
                        $pgrid['increment_id'] = $res['increment_id'];
                        $pgrid['store_id'] = 1;
                        $pgrid['order_increment_id'] = $res['increment_id'];
                        $pgrid['order_id'] = $nn;
                        $pgrid['order_created_at'] = $data['created_at'];
                        $pgrid['customer_name'] = $address['contact_name'];
                        $pgrid['total_qty'] = count($goods);
                        $pgrid['order_status'] = $status;
                        $pgrid['billing_address'] = $grid['billing_address'];
                        $pgrid['shipping_address'] = $grid['shipping_address'];
                        $pgrid['billing_name'] = $grid['billing_name'];
                        $pgrid['shipping_name'] = $grid['shipping_name'];
                        $pgrid['customer_email'] = $address['email'];
                        $pgrid['payment_method'] = $val['pay_way'];
                        $pgrid['shipping_information'] = 'Flat Rate - Fixed';
                        $pgrid['created_at'] = $data['created_at'];
                        $pgrid['updated_at'] = $data['created_at'];
                        $op = Db::table('sales_shipment_grid')->insert($pgrid);
                        echo $op."------------shipment_grid\n";
                        foreach($goods as $vg){
                            $pitem = [];
                            $pitem['parent_id'] = $id;
                            $pitem['price'] = $vg['total_price'];
                            $pitem['qty'] = $vg['goods_num'];
                            $pitem['product_id'] = $vg['product_id'];
                            $pitem['order_item_id'] = $vg['order_item_id'];
                            $pitem['name'] = $vg['goods_name'];
                            $pitem['sku'] = $vg['sku'];
                            $op = Db::table('sales_shipment_item')->insert($pitem);
                            echo $op."------------shipment_item\n";
                        }
                        $track = array();
                        
                        $ordersships = Db::connect('db_csut')->table('cs_orders_ship')->where([
                            'shipping_type'=>$stype[1],
                        ])->orderRaw('rand()')->limit(1)
                        ->select();
                        $track_number = count($ordersships)>0 && !empty($ordersships[0]['trackno'])?$ordersships[0]['trackno']:'';
                        $track['parent_id'] = $id;
                        $track['order_id'] = $nn;
                        $track['track_number'] = $track_number; 
                        $track['title'] = $stype[1];
                        $track['carrier_code'] = $stype[0];
                        $track['created_at'] = $data['created_at'];
                        $track['updated_at'] = $data['created_at'];
                        $op = Db::table('sales_shipment_track')->insert($track);
                        echo $op."------------shipment_track\n";
                    }catch (\Exception $e){
                        Db::rollback();
                        echo $e->getMessage()."\n";
                    }
                    Db::commit();
                }
            }
        }while($a==$n);
        echo "*********END**********";
    }
    
    /* test */
    function ecorders4(){
        $table = 'ec_goods_info_wm_2019_6_10';
        $order_table = 'cs_orders_price';
        $i=0;
        $currency_array = array('USD'=>'1','HKD'=>'7.81','GBP'=>'0.763','CNY'=>'6.72','EUR'=>'0.891','MXN'=>'18.83');//,'GBP'=>'0.763','EUR'=>'0.891','HKD'=>'7.81','CNY'=>'6.72','MXN'=>'18.83'
        $codes = array_keys($currency_array);
        //15Canceled 11Pending 12Shipped 2Complete 14Refunded
        $order_status_ec = array('a15'=>'canceled', 'a11'=>'pending', 'a12'=>'processing', 'a2'=>'complete', 'a14'=>'refunded','a9'=>'refunded');
        $n = 2000;//一次查询两千条
        $acount = Db::connect('db_address')
        ->table('fakeper_2')->count();
        $acount--;
        $insertMember = config('isapp')>0?'insertMember':'insertMember2';
        $count = Db::connect('db_csut')
        ->table('cs_member')
        ->count();
        $mictime = (time()-7*86400)*1000;
        $cf = false;
        $index = 0;//导入十条数据 十个分类 不同国家
        do{
            $start = $i*$n;//
            $list = Db::connect('db_csut')
            ->table($order_table)
            ->where(function ($query) {
                $query->where('type', 'excel');//->where('type', 'ecommerce')->whereor('type', 'gateway');
            })
            ->where('ratecode','in',$codes)
                          ->where('order_status','in',['2','9','11','12','14','15'])
            ->where('sale_price','>','50')
            ->limit($start,$n)
            ->field('description,pictrues,main_pic,items,out_url,hash_url',true)
//             ->fetchSql(true)
            ->select();
            $ulist = Db::connect('db_weibo')
            ->table('face_user')
            ->field('user_name')
            ->limit($start,$n)
            ->select();
            $a = count($list);
            var_dump([4,time()]);
            $count=$count-$n-1;
            $sst = mt_rand(100, $count);
            $res11 = Db::connect('db_csut')->table('cs_member')->field('Uid')->limit($sst,$n)->select();
            var_dump([5,time()]);
            $i++;
            if($a>0){
                foreach($list as $key => $val){
                    $check = Db::table('sales_order')->where('increment_id',$val['order_id'])->find();
                    if(!$check){
                    }else{
                        continue;
                    }
//                     echo msectime()."--1\n";
                    ///////////////正式/////////////////////////
                    $ratecode = $val['ratecode'];
                    $fee = 1/$currency_array[$ratecode];
                    $shipcode = '';
                    $flag = false;//能否匹配商品
                    if($val['type']=='ecommerce'){
                        $rgoods = Db::connect('db_csut')->table('cs_orders_goods')->where("order_id",$val['order_id'])->select();
                        if(count($rgoods)>0){
                            $flag=true;
                        }
                    }
//                     echo msectime()."--2\n";
                    if($val['type']=='gateway' || !$flag || $val['type']=='excel'){
                        
                        
                        ////////慢////////////
                        $sql = "select goods_id from $table ";
                        $pppprice = sprintf("%.2f",($val['sale_price']*$fee-50)*0.01);
                        $sql .= "where act_price>$pppprice limit 1";
                        
                        $igood = Db::connect('db_csut')->query($sql);
//                         echo msectime()."--3\n";
                        //                         var_dump([$igood,$sql]);
                        if(count($igood)>0){
                            $pppprice = $pppprice+0.5;
                            //按顺序获取分类
//                             $resg = $this->getgoodsByprice3($pppprice,$index,$table);
                            //按概率获取分类
                            $resg = $this->getgoodsByprice3($pppprice,-1,$table);
                            $jj = $resg[0];
                            $igood = $resg[1];
                        }
//                         echo msectime()."--4\n";
                        ////////慢////////////
                        
                        
                        if(count($igood)<1) continue;
                        $index ++;
                        $abc = mt_rand(1, count($igood));
                        $abc--;
//                         $val['sale_price'] = $igood[$abc]['act_price']*100;
                        $val['act_price'] = $val['sale_price']-$val['reduce_price'];
                        if($val['act_price']<0) continue;
                    }
                    
                    Db::startTrans();
                    try{
                        $fname = $ulist[$key]['user_name'];
                        $val['pay_way'] =  $val['pay_way']=='UnionPay'?'CreditCard':'CreditCard';//都是卡支付
                        $data = $adata = $address = array();
                        $old_order_id = $val['order_id'];
                        $uid = $val['uid'];
                        $check = $ecdd = false;
                        if($uid<=0){
                            if($val['type']=='gateway'){
                                $uid = $res11[$key]['Uid'];
                            }elseif($val['type']=='excel'){
                                //根据名字获取uid (名字一致则是同一个人复购率)
                                $ecaddr = Db::connect('db_csut')->table('ec_order_address')->where('order_id',$old_order_id)->find();
                                $ecdd = json_decode($ecaddr['addr'],true);
                                $check = Db::table('customer_entity')->where('firstname',$ecdd['name'])->find();
                                if($check) $uid = $check['entity_id'];
                            }
                        }else{
                            $check = Db::table('customer_entity')->where('entity_id',$uid)->find();
                        }
                        echo msectime()."--5\n";
                        /* 网关订单加上邮费税费 */
                        $shipcode = is_array($ecdd)&& !empty($ecdd['shipping_code'])?$ecdd['shipping_code']:'';
                        $stype = $this->getshiptypes($val['sale_price'],$shipcode);//**********************//
                        if($val['type']=='gateway'||$val['type']=='excel'){
                            $val['mail_fee'] = $stype[2];
                        }
                        //                         var_dump([7,time()]);
                        if(!$check){
                            //导入用户返回用户信息
                            $addid = mt_rand(0, $acount);
                            $order_time = floor($val['order_time']*0.001);
                            $address = $this->$insertMember($uid,$fname,$addid,$order_time,$ratecode,$ecdd);
                            $uid = $address['uid'];
                            //                             var_dump([8,time()]);
                            $address['contact_name'] = !empty($address['contact_name'])?$address['contact_name']:$fname;
                            if(!$address){
                                echo "2222222222\n";
                                continue;
                            }else{
                                if(isset($address['result']) && $address['result']<1){
                                    echo "22222222225\n";
                                    continue;
                                }
                            }
                        }else{
                            //获取用户信息
                            $add = Db::table('customer_address_entity')->where("parent_id=$uid")->find();
                            $address['email'] = $check['email'];
                            $aname = $check['firstname'].' '.$check['lastname'];
                            $address['contact_name'] = !empty($aname)?$aname:$fname;
                            $address['address_id'] = count($add)>0?$add['entity_id']:0;
                            $address['state'] = $add['region'];
                            $address['zipcode'] = $add['postcode'];
                            $address['street_addr'] = $add['street'];
                            $address['city'] = $add['city'];
                            $address['mobile'] = $add['telephone'];
                            $address['country_id'] = $add['country_id'];
                        }
                        $status = isset($order_status_ec['a'.$val['order_status']])?$order_status_ec['a'.$val['order_status']]:'complete';
                        if($val['order_time']<$mictime){//超过七天没有pending
                            $status = $status=='pending'?'complete':$status;
                            $status = $status=='processing'?'complete':$status;
                        }
                        $data['state'] =  $status=='pending'?'new':$status;
                        $data['status'] = $status;
                        //密码加密方法？？？？？？？？？？？？
                        $data['protect_code']  =    substr(
                            hash('sha256', uniqid($val['order_id'], true) . ':' . microtime(true)),
                            5,32);
                        $data['shipping_description'] = 'Flat Rate - Fixed';
                        $data['is_virtual'] = '0';
                        $data['store_id'] = '1';
                        $data['customer_id'] = $uid;
                        $data['base_discount_amount'] = $val['reduce_price']/100;//折扣金额
                        $data['base_discount_refunded'] = 0;//折扣返还
                        $data['base_grand_total'] = sprintf("%.2f",($val['sale_price']+$val['mail_fee'])/100);//总价51.5
                        $data['base_shipping_amount'] = $data['shipping_amount'] = sprintf("%.2f",$val['mail_fee']/100);//5
                        $data['base_shipping_tax_amount'] = 0;//运费税
                        $data['base_tax_invoiced'] = sprintf("%.2f",$val['vat_fee']/100);//税费
                        $data['base_total_invoiced'] = '0';
    
                        $data['base_subtotal'] = sprintf("%.2f",$val['sale_price']/100);//小计46.5
                        $data['base_subtotal_invoiced'] = $data['subtotal_invoiced'] = sprintf("%.2f",$val['sale_price']/100);//小计46.5
                        $data['base_tax_amount'] = sprintf("%.2f",$val['vat_fee']/100);//税费
                        $data['base_to_global_rate'] = $currency_array[$val['ratecode']];//基于全球税率
                        $data['base_to_order_rate'] = $currency_array[$val['ratecode']];//基于订单税率
                        $data['grand_total'] = sprintf("%.2f",($val['sale_price']+$val['mail_fee']+$val['vat_fee'])/100);//总价
                        $data['grand_total'] = sprintf("%.2f",$data['grand_total']);
                        $data['total_invoiced'] = $data['grand_total'];
                        $data['base_shipping_invoiced'] = $data['shipping_invoiced'] = sprintf("%.2f",$val['mail_fee']/100);
                        $data['base_total_paid'] = $data['total_paid'] = $data['grand_total']-$data['base_discount_amount'];
                        $data['shipping_tax_amount'] = 0;//运费税
                        $data['store_to_base_rate'] = 0;
                        $data['store_to_order_rate'] = 0;
                        $data['subtotal'] = $val['sale_price']/100;//小计
                        $data['tax_amount'] = $val['vat_fee']/100;//税费
                        $data['total_qty_ordered'] = 1;//总商品数量
                        $data['customer_is_guest'] = 0;
                        $data['customer_note_notify'] = 1;
                        $data['billing_address_id'] = 1;//帐单地址_id
                        $data['customer_group_id'] = 0;
                        $data['email_sent'] = 1;
                        $data['send_email'] = 1;
                        $data['quote_id'] = 1; //引用ID
                        $data['shipping_address_id'] = 1;//
                        $data['base_subtotal_incl_tax'] = $data['base_subtotal']+$data['tax_amount']; //基本小计含税
                        $data['base_total_due'] = $data['base_grand_total']; //基数总额到期
                        $data['subtotal_incl_tax'] = $data['base_subtotal']+$data['tax_amount']; //小计含税
                        $data['total_due'] = $data['base_grand_total']; //到期总额
                        //                         $data['customer_dob'] = 1;
                        $data['base_currency_code'] = $val['ratecode'];
                        $data['customer_email'] = isset($address['email'])?$address['email']:$this->myemail($key);
                        $data['customer_firstname'] = '';
                        $data['customer_lastname'] = $address['contact_name'];
                        $data['global_currency_code'] = $ratecode;
                        $data['order_currency_code'] = $val['ratecode'];
                        $data['remote_ip'] = 1; // 远程ip
                        $data['shipping_method'] = 'flatrate_flatrate';
                        $data['store_currency_code'] = $val['ratecode'];
                        $data['store_name'] = 'Default Store View';//???????????????
                        $data['created_at'] = date("Y-m-d H:i:s",intval($val['order_time']/1000));
                        $data['updated_at'] = $data['created_at'];
                        if($val['type']=='gateway' || !$flag){
                            var_dump([9,time()]);
                            $goods[0]['goods_num'] = $jj;
                            $goods[0]['goods_id'] = $igood[$abc]['goods_id'];
                            $goods[0]['goods_name'] = $igood[$abc]['title'];
                            $goods[0]['total_price'] = $igood[$abc]['act_price']*$jj*100;
                            $items = json_decode(base64_decode($igood[$abc]['items']),true);
                            $igood[$abc]['items'] = json_encode(array('1'=>$items[1]));
                            unset($igood[$abc]['description']);
                            $goods[0]['goods_detail'] = json_encode($igood[$abc]);
                            $data['total_item_count'] = $jj;
                            echo $goods[0]['goods_id']."---999\n";
                        }else{
                            $goods = $rgoods;
                            $data['total_item_count'] = count($goods);
                        }
                        $data['customer_gender'] = 1; //客户性别
                        $data['shipping_incl_tax'] = $val['mail_fee']/100;
                        $data['base_shipping_incl_tax'] = $val['mail_fee']/100;
                        Db::table('sales_order')->insert($data);
                        $nn = Db::table('sales_order')->getLastInsID();
                        $res['increment_id'] = $val['order_id'];//$this->rzorderid($nn); //
//                         echo msectime()."--6\n";
                        //sales_order_address/////////////////////////////////////
                        $adata['parent_id'] = $nn;
                        //$adata['quote_address_id'] = $n;
                        $adata['region_id'] = 12;//区域id
                        $cr = Db::table('directory_country_region')
                        ->where('country_id',$address['country_id'])
                        ->where('default_name',$address['state'])
                        ->find();
                        if($cr) $adata['region_id'] = $cr['region_id'];
                        $adata['customer_address_id'] = $address['address_id'];
                        $adata['region'] = $address['state'];
                        $adata['postcode'] = $address['zipcode'];
                        $adata['lastname'] = $address['contact_name'];
                        $adata['street'] = $address['street_addr'];
                        $adata['city'] = $address['city'];
                        $adata['email'] = $address['email'];
                        $adata['telephone'] = $address['mobile'];
                        $adata['country_id'] = $address['country_id'];
                        $adata['firstname'] = '';
                        $adata['address_type'] = 'shipping';
                        $op = Db::table('sales_order_address')->insert($adata);
                        $res['shipping_address_id'] = Db::table('sales_order_address')->getLastInsID();
                        echo $op."------------address1\n";
                        $adata['address_type'] = 'billing';
                        $op = Db::table('sales_order_address')->insert($adata);
                        $res['billing_address_id'] = Db::table('sales_order_address')->getLastInsID();
                        echo $op."------------address2\n";
//                         echo msectime()."--7\n";
                        Db::table('sales_order')->where('entity_id',$nn)->update($res);
                        ///sales_order_grid//////////////////////////////////////////////////////////
                        $grid = array();
                        $grid['entity_id'] = $nn;
                        $grid['status'] = $status;
                        $grid['store_id'] = 1;
                        $grid['store_name'] = 'Default Store View';
                        $grid['customer_id'] = $uid;
                        $grid['base_grand_total'] = $data['base_grand_total'];
                        $grid['grand_total'] = $data['grand_total'];
                        $grid['increment_id'] = $res['increment_id'];
                        $grid['base_currency_code'] = $val['ratecode'];
                        $grid['order_currency_code'] = $val['ratecode'];
                        $grid['shipping_name'] = $address['contact_name'];
                        $grid['billing_name'] = $address['contact_name'];
                        $grid['created_at'] = $data['created_at'];
                        $grid['updated_at'] = $data['created_at'];
                        $grid['billing_address'] = $address['street_addr'].','.$address['city'].','.$address['state'].','.$address['zipcode'];
                        $grid['shipping_address'] = $grid['billing_address'];
                        $grid['shipping_information'] = 'Flat Rate - Fixed';
                        $grid['customer_email'] = $address['email'];
                        $grid['subtotal'] = $data['subtotal'];
                        $grid['shipping_and_handling'] = $data['shipping_amount'];
                        $grid['payment_method'] = $val['pay_way'];
                        $grid['order_approval_status'] = 1;//订单审批状态
                        $op = Db::table('sales_order_grid')->insert($grid);
                        echo $op."------------grid\n";
                        ///sales_order_item/////////////////////////////////////////////////////////
                        $good = array();
                        $good['order_id'] = $nn;
                        $good['created_at'] = $data['created_at'];
                        $good['updated_at'] = $data['created_at'];
                        $total_qty_ordered = 0;
                        foreach($goods as $vk => $vg){
                            $good['store_id'] = 1;
                            $good['product_id'] = 2;
                            if(empty($vg['goods_id'])){
                                echo "555555555\n";
                                continue;
                            }
                            $goods[$vk]['sku'] = $good['sku'] = 'Ali_'.$vg['goods_id'];
                            $good['row_total'] = $vg['total_price']*0.01;
                            $preprice = $vg['total_price']/$vg['goods_num']*0.01;
                            $product_entity = Db::table('catalog_product_entity')->where("sku", $good['sku'])->find();
                            if(empty($product_entity)){
                                for($j=1;$j<20;$j++){
                                    $pre = $j*0.1;
                                    $min_price = $preprice-$pre<0?0.01:$preprice-$pre;
                                    $max_price = $preprice+$pre;
                                    $product_entity = Db::table('catalog_product_index_price')
                                    ->where("price",'>',$min_price)
                                    ->where("price",'<',$max_price)
                                    ->find();
                                    if($product_entity) break;
                                }
                                if(empty($product_entity)){
                                    echo $good['sku']."-----88\n";
                                    continue;
                                }
                                $goods_entity = Db::table('catalog_product_entity')
                                ->where("entity_id",$product_entity['entity_id'])
                                ->field('sku')
                                ->find();
                                if(empty($goods_entity)){
                                    echo "-----pp88\n";
                                    continue;
                                }
                                $goods[$vk]['sku'] = $goods_entity['sku'];
                            }
                            $good['product_id'] = $goods[$vk]['product_id'] = $product_entity['entity_id'];
                            $good['quote_item_id'] = 1;
                            $good['product_type'] = 'simple';
                            $good['product_options'] = $this->getoptions($vg['goods_detail'],$vg['goods_num'],$good['product_id']);//'{"label":"Size","value":"S","print_value":"S","option_id":"22654","option_type":"drop_down","option_value":"116237","custom_view":false}]}';
                            $array = json_decode($vg['goods_name'],true);
                            $good['name'] = is_array($array)?$array[0]:$vg['goods_name'];
                            $good['name'] = stripslashes($good['name']);
                            //'SHEIN Yellow Split Sleeve Belted Outerwear Office Ladies Long Sleeve Plain Wrap Workwear Blazer Women Autumn Elegant Coat';
                            $good['qty_ordered'] = $vg['goods_num'];
    
                            $good['price'] = $preprice;
                            $good['base_price'] = $preprice;
                            $good['original_price'] = $preprice;
    
                            $good['base_row_total'] = $vg['total_price']*0.01;
                            $good['price_incl_tax'] = $preprice;
                            $good['base_price_incl_tax'] = $preprice;
                            $good['row_total_incl_tax'] = $vg['total_price']*0.01;
                            $good['base_row_total_incl_tax'] = $vg['total_price']*0.01;
                            $op = Db::table('sales_order_item')->insert($good);
                            $goods[$vk]['order_item_id'] = Db::table('sales_order_item')->getLastInsID();
                            echo $op."------------item\n";
                        }
//                         echo msectime()."--8\n";
                        $payment = array();
                        $payment['parent_id'] = $nn;
                        $payment['base_shipping_amount'] = $data['base_shipping_amount'];
                        $payment['shipping_amount'] = $data['shipping_amount'];
                        $payment['base_amount_ordered'] = $data['base_grand_total'];
                        $payment['amount_ordered'] = $data['grand_total'];
                        $payment['method'] = $val['pay_way'];
                        "Check / Money order";
                        $payment['additional_information'] = '{"method_title":"' . $payment['method'] . '"}';
                        $op = Db::table('sales_order_payment')->insert($payment);
                        echo $op."------------payment\n";
                        //物流信息sales_shipment sales_shipment_grid
                        //sales_shipment_item sales_shipment_track
                        $shipment = array();
                        $shipment['store_id'] = 1;
                        $shipment['total_qty'] = count($goods);
                        $shipment['order_id'] = $nn;
                        $shipment['customer_id'] = $uid;
                        $shipment['shipping_address_id'] = $res['shipping_address_id'];
                        $shipment['billing_address_id'] = $res['billing_address_id'];
                        $shipment['increment_id'] = 1;//?????
                        $shipment['created_at'] = $data['created_at'];
                        $shipment['updated_at'] = $data['created_at'];
                        $op = Db::table('sales_shipment')->insert($shipment);
                        echo $op."------------shipment\n";
                        $id = Db::table('sales_shipment')->getLastInsID();
                        //                         $increment_id = $this->rzorderid($id);
                        Db::table('sales_shipment')->where('entity_id',$id)->update(['increment_id'=>$res['increment_id']]);
                        $pgrid = array();
                        $pgrid['entity_id'] = $id;
                        $pgrid['increment_id'] = $res['increment_id'];
                        $pgrid['store_id'] = 1;
                        $pgrid['order_increment_id'] = $res['increment_id'];
                        $pgrid['order_id'] = $nn;
                        $pgrid['order_created_at'] = $data['created_at'];
                        $pgrid['customer_name'] = $address['contact_name'];
                        $pgrid['total_qty'] = count($goods);
                        $pgrid['order_status'] = $status;
                        $pgrid['billing_address'] = $grid['billing_address'];
                        $pgrid['shipping_address'] = $grid['shipping_address'];
                        $pgrid['billing_name'] = $grid['billing_name'];
                        $pgrid['shipping_name'] = $grid['shipping_name'];
                        $pgrid['customer_email'] = $address['email'];
                        $pgrid['payment_method'] = $val['pay_way'];
                        $pgrid['shipping_information'] = 'Flat Rate - Fixed';
                        $pgrid['created_at'] = $data['created_at'];
                        $pgrid['updated_at'] = $data['created_at'];
                        $op = Db::table('sales_shipment_grid')->insert($pgrid);
//                         echo msectime()."--9\n";
                        echo $op."------------shipment_grid\n";
                        foreach($goods as $vg){
                            $pitem = [];
                            $pitem['parent_id'] = $id;
                            $pitem['price'] = $vg['total_price'];
                            $pitem['qty'] = $vg['goods_num'];
                            $pitem['product_id'] = $vg['product_id'];
                            $pitem['order_item_id'] = $vg['order_item_id'];
                            $pitem['name'] = $vg['goods_name'];
                            $pitem['sku'] = $vg['sku'];
                            $op = Db::table('sales_shipment_item')->insert($pitem);
                            echo $op."------------shipment_item\n";
                        }
                        $track = array();
                        $track['parent_id'] = $id;
                        $track['order_id'] = $nn;
                        $track['track_number'] = $stype[3];
                        $track['title'] = $stype[1];
                        $track['carrier_code'] = $stype[0];
                        $track['created_at'] = $data['created_at'];
                        $track['updated_at'] = $data['created_at'];
                        $op = Db::table('sales_shipment_track')->insert($track);
                        echo $op."------------shipment_track\n";
//                         echo msectime()."--10\n";
                    }catch (\Exception $e){
                        Db::rollback();
                        echo $e->getMessage()."\n";
                    }
                    Db::commit();
                }
            }
        }while($a==$n);
        echo "*********END**********";
    }
    
    function getoptions($goods_detail,$num,$product_id){
        $a = json_decode($goods_detail,true);
        if(!isset($a['goods_id'])){
            return '';
        }
        $value = array();//?????????????????????????
        $value['info_buyRequest']['uenc'] = 'aHR0cDovLzE5Mi4xNjguMS4xNDU6ODMvY2FydHF1aWNrcHJvL2NhdGFsb2dfcHJvZHVjdC9vcHRpb25zL2lkLzE3MzA5L3JhbmR0aW1lLzE1NTk2MTkxNzkzODcv';
        $value['info_buyRequest']['product'] = $a['goods_id'];
        $value['info_buyRequest']['selected_configurable_option'] = '';
        $value['info_buyRequest']['related_product'] = '';
        $value['info_buyRequest']['item'] = $a['goods_id'];
        $value['info_buyRequest']['qty'] = $num;
        $value['options']= [];
        if(!isset($a['items']) || empty($a['items'])){
            return json_encode($value);
        }
        $a['items'] = is_array($a['items'])?$a['items']:json_decode($a['items'],true);
        foreach($a['items'] as $val){
            foreach($val as $k=>$v){
                if(strpos($k, 'ps_')!==0){
                    $res = Db::table('catalog_product_option')
                    ->alias('a')
                    ->join('catalog_product_option_title b','a.option_id = b.option_id')
                    ->where('a.product_id',$product_id)
                    ->where('b.title',$k)
                    ->field('a.option_id as option_id')
                    ->find();
                    if(!$res) {
                        continue;
                    }
                    $res2 = Db::table('catalog_product_option_type_value')
                    ->alias('a')
                    ->join('catalog_product_option_type_title b','a.option_type_id = b.option_type_id')
                    ->where('a.option_id',$res['option_id'])
                    ->where('b.title',$v[0])
                    ->field('b.option_type_title_id as title_id')
                    ->find();
                    $value['info_buyRequest']['options'][$res['option_id']] = $res2['title_id']; 
                    $value['options'][] = array(
                        "label"=> $k,//Color
                        "value"=> $v[0],//Red
                        "print_value"=> $v[0],
                        "option_id"=> $res['option_id'],
                        "option_type"=> "drop_down",
                        "option_value"=> $res2['title_id'],
                        "custom_view"=> false
                    );
                }
            }
        }
        return json_encode($value);
    }
    
    function insertMember($uid,$fname,$addid){
        //customer_entity
        //customer_grid_flat
        //customer_address_entity
        $member = Db::connect('db_csut')
        ->table('cs_member')->where("Uid=$uid")->find();
        if(empty($member)){
            echo '111111111111-----'.$uid."\n";
            return false;
        }
    
        $address = Db::connect('db_csut')
        ->table('ec_address')->where("uid=$uid")->order('is_default desc')->find();
        if(empty($address)){
            return false;
        }
    
        if(empty($member['FirstName']) && empty($member['LastName'])) $member['FirstName'] = $address['contact_name'];
        $data = $grid = $addData = array();
        $addData['parent_id'] = $grid['entity_id'] = $data['entity_id'] = $uid;
        //         $member['Password'];
        $data['website_id'] = 1;
        $data['store_id'] = 1;
        $data['is_active'] = 1;
        $data['password_hash'] = '';
        $data['rp_token'] = '';
        $grid['email'] = $data['email'] = '';
        $grid['created_in'] = $data['created_in'] = 'Default Store View';
        $addData['created_at'] = $grid['created_at'] = $data['created_at'] = $member['RegisterTime'];
        $addData['updated_at'] = $data['updated_at'] = date("Y-m-d H:i:s",$member['UpdateTime']);
        $address['firstname'] = $addData['firstname'] = $grid['billing_firstname'] = $data['firstname'] = $member['FirstName'];
        $address['lastname'] = $addData['lastname'] = $grid['billing_lastname'] = $data['lastname'] = $member['LastName'];
        $grid['name'] = $member['FirstName'].' '.$member['LastName'];
        $grid['name'] = rtrim($grid['name']);
        $member['FacebookInfo'];//{"email":"667777@aol.com","phone":"18824341354"}
        if(!empty($member['FacebookInfo'])){
            $face = json_decode($member['FacebookInfo'],true);
            if(!empty($face) && isset($face['email']) && !empty($face['email'])){
                $grid['email'] = $data['email'] = $face['email'];
            }
        }
        //msp_tfa_country_codes code === name
        if(!empty($address['country'])){
            $find = Db::table('msp_tfa_country_codes')->where('name',$address['country'])->find();
        }
        if(empty($grid['email'])){
            $grid['email'] = $data['email'] = $address['email'];
        }
        $addData['country_id'] =isset($find) && !empty($find)?$find['code']:'US';
        $addData['region'] = $grid['billing_region'] = $address['state'];
        $addData['region_id'] = 12;
        $addData['city'] = $grid['billing_city'] = $address['city'];
        $addData['street'] = $grid['billing_street'] = $address['street_addr'];
        $grid['shipping_full'] = $grid['billing_full'] = $address['apt_addr'];
        $addData['postcode'] = $grid['billing_postcode'] = $address['zipcode'];
        $addData['telephone'] = $address['mobile'];
        $address['contact_name'];
        $grid['billing_telephone'] = $address['telcode'];//+86
    
        $grid['group_id'] = 1;
        for($i=0;$i<100;$i++){
            $check = Db::table('customer_entity')->where('email',$data['email'])->find();
            if($check){
                $data['email'] = $this->myemail($i);
            }else{
                break;
            }
        }
        $re = Db::table('customer_entity')->insert($data);
        if($re) {
            Db::table('customer_grid_flat')->insert($grid);
            Db::table('customer_address_entity')->insert($addData);
        }else{
            dump($re);echo '33333333333333333'."\n";
        }
        $address['result'] = $re;
        $address['address_id'] = Db::table('customer_address_entity')->getLastInsID();
        $address['country_id'] = $addData['country_id'];
        return $address;
    }
    
    
    function insertMember2($uid,$name,$addid,$createTime,$ratecode,$ecdd=false){
        //$ecdd excel 地址 
        // 	"addr": "946 Bd Escudier . Le Sevigne A1 ,Toulon,France",
        // 	"country": "France",
        // 	"name": "Christelle Alvarez",
        // 	"shipping_code": "RH357772813CN"
        $addr = Db::connect('db_address')->table('fakeper_2')->limit($addid,1)->select();
        $addr = $addr[0];
        $address['telcode'] = $addr['Mobile_Number'];
        $address['mobile'] = $addr['Mobile_Number'];
        $address['state'] = $address['city'] = $address['street_addr'] = $address['zipcode'] = $address['telcode'] = '';
        if($ecdd){
            if(!empty($ecdd['addr'])){
                $addr = $ecdd['addr'];
            }
            $pattern = '/[^a-zA-Z0-9\s\,\/#@\$\&\(\)\-\_\.]/';
            preg_match($pattern, $addr, $matches);
            if(count($matches)<=0){
                $name = $ecdd['name'];
            }
            $address['country'] = $ecdd['country'];
            $ss = explode(',', $ecdd['addr']);
            array_pop($ss);
            $address['apt_addr'] = implode(',', $ss);
            $addr_amn = array('street_addr','city','state');
            foreach($ss as $key=>$val){
                if($key>2) break;
                $address[$addr_amn[$key]] = $val;
            }
        }else{
            $member = Db::connect('db_csut')
            ->table('cs_member')->where("Uid=$uid")->find();
            if(empty($member)){
                echo '111111111111-----'.$uid."\n";
                return false;
            }
            $address['country'] = 'United States';
            $address['state'] = $addr['State_Full'];
            $address['city'] = $addr['City'];
            $address['street_addr'] = $addr['Street_Address'];
            $address['zipcode'] = $addr['Zipcode'];
            $address['apt_addr'] = $addr['Street_Address'].','.$addr['City'].','.$addr['State_Full'].','.$addr['Zipcode'];
        }
        
        $deff = mt_rand(600, 186400);
        $createTime = $createTime-$deff;//随机分配注册时间
        $address['contact_name'] = $name;
        $address['email'] = $this->myemail2($name);
        $member['FirstName'] = $address['contact_name'];
        $member['LastName'] = '';
        $data = $grid = $addData = array();
        if($uid>0) $data['entity_id'] = $addData['parent_id'] = $grid['entity_id'] = $uid;
//         $member['Password'];
        $data['website_id'] = 1;
        $data['store_id'] = 1;
        $data['is_active'] = 1;
        $data['password_hash'] = '';
        $data['rp_token'] = '';
        $grid['email'] = $data['email'] = '';
        $grid['created_in'] = $data['created_in'] = 'Default Store View';
        $addData['created_at'] = $grid['created_at'] = $data['created_at'] = date("Y-m-d H:i:s",$createTime);//$member['RegisterTime'];
        $addData['updated_at'] = $data['updated_at'] = date("Y-m-d H:i:s",$createTime);
        
        $address['firstname'] = $addData['firstname'] = $grid['billing_firstname'] = $data['firstname'] = $member['FirstName'];
        $address['lastname'] = $addData['lastname'] = $grid['billing_lastname'] = $data['lastname'] = $member['LastName'];
        $grid['name'] = $member['FirstName'].' '.$member['LastName'];
        $grid['name'] = rtrim($grid['name']);
        //msp_tfa_country_codes code === name
        if(!empty($address['country'])){
            $find = Db::table('msp_tfa_country_codes')->where('name',$address['country'])->find();
        }
        
        $grid['email'] = $data['email'] = $address['email'];
        
        $addData['country_id'] =isset($find) && !empty($find)?$find['code']:'US';
        $addData['region'] = $grid['billing_region'] = $address['state'];
        $addData['region_id'] = 12;
        $addData['city'] = $grid['billing_city'] = $address['city'];
        $addData['street'] = $grid['billing_street'] = $address['street_addr'];
        $grid['shipping_full'] = $grid['billing_full'] = $address['apt_addr'];
        $addData['postcode'] = $grid['billing_postcode'] = $address['zipcode'];
        $addData['telephone'] = $address['mobile'];
        $grid['billing_telephone'] = $address['telcode'];//+86
        
        $grid['group_id'] = 1;
        
        for($i=0;$i<100;$i++){
            $check = Db::table('customer_entity')->where('email',$data['email'])->find();
            if($check){
                $data['email'] = $this->myemail2($name.$i);
            }else{
                break;
            }
        }
        $re = Db::table('customer_entity')->insert($data);
        if($uid<=0) {
            $uid = Db::table('customer_entity')->getLastInsID();
        }
        $address['uid'] = $addData['parent_id'] = $grid['entity_id'] = $uid;
        if($re) {
            Db::table('customer_grid_flat')->insert($grid);
            Db::table('customer_address_entity')->insert($addData);
        }else{
            dump($re);echo '33333333333333333'."\n";
        }
        $address['result'] = $re;
        $address['address_id'] = Db::table('customer_address_entity')->getLastInsID();
        $address['country_id'] = $addData['country_id'];
        return $address;
    }
    
    function myfirstName(){
        $names = '{"data":["Emma","Isabella","Emily","Madison","Ava","Olivia","Sophia","Abigail","Elizabeth","Chloe","Samantha","Addison","Natalie","Mia","Alexis","Alyssa","Hannah","Ashley","Ella","Sarah","Grace","Taylor","Brianna","Lily","Hailey","Anna","Victoria","Kayla","Lillian","Lauren","Kaylee","Allison","Savannah","Nevaeh","Gabriella","Sofia","Makayla","Avery","Riley","Julia","Leah","Aubrey","Jasmine","Audrey","Katherine","Morgan","Brooklyn","Destiny","Sydney","Alexa","Kylie","Brooke","Kaitlyn","Evelyn","Layla","Madeline","Kimberly","Zoe","Jessica","Peyton","Alexandra","Claire","Madelyn","Maria","Mackenzie","Arianna","Jocelyn","Amelia","Angelina","Trinity","Andrea","Maya","Valeria","Sophie","Rachel","Vanessa","Aaliyah","Mariah","Gabrielle","Katelyn","Ariana","Bailey","Camila","Jennifer","Melanie","Gianna","Charlotte","Paige","Autumn","Payton","Faith","Sara","Isabelle","Caroline","Genesis","Isabel","Mary","Zoey","Gracie","Megan","Haley","Mya","Michelle","Molly","Stephanie","Nicole","Jenna","Natalia","Sadie","Jada","Serenity","Lucy","Ruby","Eva","Kennedy","Rylee","Jayla","Naomi","Rebecca","Lydia","Daniela","Bella","Keira","Adriana","Lilly","Hayden","Miley","Katie","Jade","Jordan","Gabriela","Amy","Angela","Melissa","Valerie","Giselle","Diana","Amanda","Kate","Laila","Reagan","Jordyn","Kylee","Danielle","Briana","Marley","Leslie","Kendall","Catherine","Liliana","Mckenzie","Jacqueline","Ashlyn","Reese","Marissa","London","Juliana","Shelby","Cheyenne","Angel","Daisy","Makenzie","Miranda","Erin","Amber","Alana","Ellie","Breanna","Ana","Mikayla","Summer","Piper","Adrianna","Jillian","Sierra","Jayden","Sienna","Alicia","Lila","Margaret","Alivia","Brooklynn","Karen","Violet","Sabrina","Stella","Aniyah","Annabelle","Alexandria","Kathryn","Skylar","Aliyah","Delilah","Julianna","Kelsey","Khloe","Carly","Amaya","Mariana","Christina","Alondra","Tessa","Eliana","Bianca","Jazmin","Clara","Vivian","Josephine","Delaney","Scarlett","Elena","Cadence","Alexia","Maggie","Laura","Nora","Ariel","Elise","Nadia","Mckenna","Chelsea","Lyla","Alaina","Jasmin","Hope","Leila","Caitlyn","Cassidy","Makenna","Allie","Izabella","Eden","Callie","Haylee","Caitlin","Kendra","Karina","Kyra","Kayleigh","Addyson","Kiara","Jazmine","Karla","Camryn","Alina","Lola","Kyla","Kelly","Fatima","Tiffany","Kira","Crystal","Mallory","Esmeralda","Alejandra","Eleanor","Angelica","Jayda","Abby","Kara","Veronica","Carmen","Jamie","Ryleigh","Valentina","Allyson","Dakota","Kamryn","Courtney","Cecilia","Madeleine","Aniya","Alison","Esther","Heaven","Aubree","Lindsey","Leilani","Nina","Melody","Macy","Ashlynn","Joanna","Cassandra","Alayna","Kaydence","Madilyn","Aurora","Heidi","Emerson","Kimora","Madalyn","Erica","Josie","Katelynn","Guadalupe","Harper","Ivy","Lexi","Camille","Savanna","Dulce","Daniella","Lucia","Emely","Joselyn","Kiley","Kailey","Miriam","Cynthia","Rihanna","Georgia","Rylie","Harmony","Kiera","Kyleigh","Monica","Bethany","Kaylie","Cameron","Teagan","Cora","Brynn","Ciara","Genevieve","Alice","Maddison","Eliza","Tatiana","Jaelyn","Erika","Ximena","April","Marely","Julie","Danica","Presley","Brielle","Julissa","Angie","Iris","Brenda","Hazel","Rose","Malia","Shayla","Fiona","Phoebe","Nayeli","Paola","Kaelyn","Selena","Audrina","Rebekah","Carolina","Janiyah","Michaela","Penelope","Janiya","Anastasia","Adeline","Ruth","Sasha","Denise","Holly","Madisyn","Hanna","Tatum","Marlee","Nataly","Helen","Janelle","Lizbeth","Serena","Anya","Jaslene","Kaylin","Jazlyn","Nancy","Lindsay","Desiree","Hayley","Itzel","Imani","Madelynn","Asia","Kadence","Madyson","Talia","Jane","Kayden","Annie","Amari","Bridget","Raegan","Jadyn","Celeste","Jimena","Luna","Yasmin","Emilia","Annika","Estrella","Sarai","Lacey","Ayla","Alessandra","Willow","Nyla","Dayana","Lilah","Lilliana","Natasha","Hadley","Harley","Priscilla","Claudia","Allisson","Baylee","Brenna","Brittany","Skyler","Fernanda","Danna","Melany","Cali","Lia","Macie","Lyric","Logan","Gloria","Lana","Mylee","Cindy","Lilian","Amira","Anahi","Alissa","Anaya","Lena","Ainsley","Sandra","Noelle","Marisol","Meredith","Kailyn","Lesly","Johanna","Diamond","Evangeline","Juliet","Kathleen","Meghan","Paisley","Athena","Hailee","Rosa","Wendy","Emilee","Sage","Alanna","Elaina","Cara","Nia","Paris","Casey","Dana","Emery","Rowan","Aubrie","Kaitlin","Jaden","Kenzie","Kiana","Viviana","Norah","Lauryn","Perla","Amiyah","Alyson","Rachael","Shannon","Aileen","Miracle","Lillie","Danika","Heather","Kassidy","Taryn","Tori","Francesca","Kristen","Amya","Elle","Kristina","Cheyanne","Haylie","Patricia","Anne","Samara","Skye","Kali","America","Lexie","Parker","Halle","Londyn","Abbigail","Linda","Hallie","Saniya","Bryanna","Bailee","Jaylynn","Mckayla","Quinn","Jaelynn","Jaida","Caylee","Jaiden","Melina","Abril","Sidney","Kassandra","Elisabeth","Adalyn","Kaylynn","Mercedes","Yesenia","Elliana","Brylee","Dylan","Isabela","Ryan","Ashlee","Daphne","Kenya","Marina","Christine","Mikaela","Kaitlynn","Justice","Saniyah","Jaliyah","Ingrid","Marie","Natalee","Joy","Juliette","Simone","Adelaide","Krystal","Kennedi","Mila","Tamia","Addisyn","Aylin","Dayanara","Sylvia","Clarissa","Maritza","Virginia","Braelyn","Jolie","Jaidyn","Kinsley","Kirsten","Laney","Marilyn","Whitney","Janessa","Raquel","Anika","Kamila","Aria","Rubi","Adelyn","Amara","Ayanna","Teresa","Zariah","Kaleigh","Amani","Carla","Yareli","Gwendolyn","Paulina","Nathalie","Annabella","Jaylin","Tabitha","Deanna","Madalynn","Journey","Aiyana","Skyla","Yaretzi","Ada","Liana","Karlee","Jenny","Myla","Cristina","Myah","Lisa","Tania","Isis","Jayleen","Jordin","Arely","Azul","Helena","Aryanna","Jaqueline","Lucille","Destinee","Martha","Zoie","Arielle","Liberty","Marlene","Elisa","Isla","Noemi","Raven","Jessie","Aleah","Kailee","Kaliyah","Lilyana","Haven","Tara","Giana","Camilla","Maliyah","Irene","Carley","Maeve","Lea","Macey","Sharon","Alisha","Marisa","Jaylene","Kaya","Scarlet","Siena","Adyson","Maia","Shiloh","Tiana","Jaycee","Gisselle","Yazmin","Eve","Shyanne","Arabella","Sherlyn","Sariah","Amiya","Kiersten","Madilynn","Shania","Aleena","Finley","Kinley","Kaia","Aliya","Taliyah","Pamela","Yoselin","Ellen","Carlie","Monserrat","Jakayla","Reyna","Yaritza","Carolyn","Clare","Lorelei","Paula","Zaria","Gracelyn","Kasey","Regan","Alena","Angelique","Regina","Britney","Emilie","Mariam","Jaylee","Julianne","Greta","Elyse","Lainey","Kallie","Felicity","Zion","Aspen","Carlee","Annalise","Iliana","Larissa","Akira","Sonia","Catalina","Phoenix","Joslyn","Anabelle","Mollie","Susan","Judith","Destiney","Hillary","Janet","Katrina","Mareli","Ansley","Kaylyn","Alexus","Gia","Maci","Elsa","Stacy","Kaylen","Carissa","Haleigh","Lorena","Jazlynn","Milagros","Luz","Leanna","Renee","Shaniya","Charlie","Abbie","Cailyn","Cherish","Elsie","Jazmyn","Elaine","Emmalee","Luciana","Dahlia","Jamya","Belinda","Mariyah","Chaya","Dayami","Rhianna","Yadira","Aryana","Rosemary","Armani","Cecelia","Celia","Barbara","Cristal","Eileen","Rayna","Campbell","Amina","Aisha","Amirah","Ally","Araceli","Averie","Mayra","Sanaa","Patience","Leyla","Selah","Zara","Chanel","Kaiya","Keyla","Miah","Aimee","Giovanna","Amelie","Kelsie","Alisson","Angeline","Dominique","Adrienne","Brisa","Cierra","Paloma","Isabell","Precious","Alma","Charity","Jacquelyn","Janae","Frances","Shyla","Janiah","Kierra","Karlie","Annabel","Jacey","Karissa","Jaylah","Xiomara","Edith","Marianna","Damaris","Deborah","Jaylyn","Evelin","Mara","Olive","Ayana","India","Kendal","Kayley","Tamara","Briley","Charlee","Nylah","Abbey","Moriah","Saige","Savanah","Giada","Hana","Lizeth","Matilda","Ann","Jazlene","Gillian","Beatrice","Ireland","Karly","Mylie","Yasmine","Ashly","Kenna","Maleah","Corinne","Keely","Tanya","Tianna","Adalynn","Ryann","Salma","Areli","Karma","Shyann","Kaley","Theresa","Evie","Gina","Roselyn","Kaila","Jaylen","Natalya","Meadow","Rayne","Aliza","Yuliana","June","Lilianna","Nathaly","Ali","Alisa","Aracely","Belen","Tess","Jocelynn","Litzy","Makena","Abagail","Giuliana","Joyce","Libby","Lillianna","Thalia","Tia","Sarahi","Zaniyah","Kristin","Lorelai","Mattie","Taniya","Jaslyn","Gemma","Valery","Lailah","Mckinley","Micah","Deja","Frida","Brynlee","Jewel","Krista","Mira","Yamilet","Adison","Carina","Karli","Magdalena","Stephany","Charlize","Raelynn","Aliana","Cassie","Mina","Karley","Shirley","Marlie","Alani","Taniyah","Cloe","Sanai","Lina","Nola","Anabella","Dalia","Raina","Mariela","Ariella","Bria","Kamari","Monique","Ashleigh","Reina","Alia","Ashanti","Lara","Lilia","Justine","Leia","Maribel","Abigayle","Tiara","Alannah","Princess","Sydnee","Kamora","Paityn","Payten","Naima","Gretchen","Heidy","Nyasia","Livia","Marin","Shaylee","Maryjane","Laci","Nathalia","Azaria","Anabel","Chasity","Emmy","Izabelle","Denisse","Emelia","Mireya","Shea","Amiah","Dixie","Maren","Averi","Esperanza","Micaela","Selina","Alyvia","Chana","Avah","Donna","Kaylah","Ashtyn","Karsyn","Makaila","Shayna","Essence","Leticia","Miya","Rory","Desirae","Kianna","Laurel","Neveah","Amaris","Hadassah","Dania","Hailie","Jamiya","Kathy","Laylah","Riya","Diya","Carleigh","Iyana","Kenley","Sloane","Elianna","Jacob","Michael","Ethan","Joshua","Daniel","Alexander","Anthony","William","Christopher","Matthew","Jayden","Andrew","Joseph","David","Noah","Aiden","James","Ryan","Logan","John","Nathan","Elijah","Christian","Gabriel","Benjamin","Jonathan","Tyler","Samuel","Nicholas","Gavin","Dylan","Jackson","Brandon","Caleb","Mason","Angel","Isaac","Evan","Jack","Kevin","Jose","Isaiah","Luke","Landon","Justin","Lucas","Zachary","Jordan","Robert","Aaron","Brayden","Thomas","Cameron","Hunter","Austin","Adrian","Connor","Owen","Aidan","Jason","Julian","Wyatt","Charles","Luis","Carter","Juan","Chase","Diego","Jeremiah","Brody","Xavier","Adam","Carlos","Sebastian","Liam","Hayden","Nathaniel","Henry","Jesus","Ian","Tristan","Bryan","Sean","Cole","Alex","Eric","Brian","Jaden","Carson","Blake","Ayden","Cooper","Dominic","Brady","Caden","Josiah","Kyle","Colton","Kaden","Eli","Miguel","Antonio","Parker","Steven","Alejandro","Riley","Richard","Timothy","Devin","Jesse","Victor","Jake","Joel","Colin","Kaleb","Bryce","Levi","Oliver","Oscar","Vincent","Ashton","Cody","Micah","Preston","Marcus","Max","Patrick","Seth","Jeremy","Peyton","Nolan","Ivan","Damian","Maxwell","Alan","Kenneth","Jonah","Jorge","Mark","Giovanni","Eduardo","Grant","Collin","Gage","Omar","Emmanuel","Trevor","Edward","Ricardo","Cristian","Nicolas","Kayden","George","Jaxon","Paul","Braden","Elias","Andres","Derek","Garrett","Tanner","Malachi","Conner","Fernando","Cesar","Javier","Miles","Jaiden","Alexis","Leonardo","Santiago","Francisco","Cayden","Shane","Edwin","Hudson","Travis","Bryson","Erick","Jace","Hector","Josue","Peter","Jaylen","Mario","Manuel","Abraham","Grayson","Damien","Kaiden","Spencer","Stephen","Edgar","Wesley","Shawn","Trenton","Jared","Jeffrey","Landen","Johnathan","Bradley","Braxton","Ryder","Camden","Roman","Asher","Brendan","Maddox","Sergio","Israel","Andy","Lincoln","Erik","Donovan","Raymond","Avery","Rylan","Dalton","Harrison","Andre","Martin","Keegan","Marco","Jude","Sawyer","Dakota","Leo","Calvin","Kai","Drake","Troy","Zion","Clayton","Roberto","Zane","Gregory","Tucker","Rafael","Kingston","Dominick","Ezekiel","Griffin","Devon","Drew","Lukas","Johnny","Ty","Pedro","Tyson","Caiden","Mateo","Braylon","Cash","Aden","Chance","Taylor","Marcos","Maximus","Ruben","Emanuel","Simon","Corbin","Brennan","Dillon","Skyler","Myles","Xander","Jaxson","Dawson","Kameron","Kyler","Axel","Colby","Jonas","Joaquin","Payton","Brock","Frank","Enrique","Quinn","Emilio","Malik","Grady","Angelo","Julio","Derrick","Raul","Fabian","Corey","Gerardo","Dante","Ezra","Armando","Allen","Theodore","Gael","Amir","Zander","Adan","Maximilian","Randy","Easton","Dustin","Luca","Phillip","Julius","Charlie","Ronald","Jakob","Cade","Brett","Trent","Silas","Keith","Emiliano","Trey","Jalen","Darius","Lane","Jerry","Jaime","Scott","Graham","Weston","Braydon","Anderson","Rodrigo","Pablo","Saul","Danny","Donald","Elliot","Brayan","Dallas","Lorenzo","Casey","Mitchell","Alberto","Tristen","Rowan","Jayson","Gustavo","Aaden","Amari","Dean","Braeden","Declan","Chris","Ismael","Dane","Louis","Arturo","Brenden","Felix","Jimmy","Cohen","Tony","Holden","Reid","Abel","Bennett","Zackary","Arthur","Nehemiah","Ricky","Esteban","Cruz","Finn","Mauricio","Dennis","Keaton","Albert","Marvin","Mathew","Larry","Moises","Issac","Philip","Quentin","Curtis","Greyson","Jameson","Everett","Jayce","Darren","Elliott","Uriel","Alfredo","Hugo","Alec","Jamari","Marshall","Walter","Judah","Jay","Lance","Beau","Ali","Landyn","Yahir","Phoenix","Nickolas","Kobe","Bryant","Maurice","Russell","Leland","Colten","Reed","Davis","Joe","Ernesto","Desmond","Kade","Reece","Morgan","Ramon","Rocco","Orlando","Ryker","Brodie","Paxton","Jacoby","Douglas","Kristopher","Gary","Lawrence","Izaiah","Solomon","Nikolas","Mekhi","Justice","Tate","Jaydon","Salvador","Shaun","Alvin","Eddie","Kane","Davion","Zachariah","Dorian","Titus","Kellen","Camron","Isiah","Javon","Nasir","Milo","Johan","Byron","Jasper","Jonathon","Chad","Marc","Kelvin","Chandler","Sam","Cory","Deandre","River","Reese","Roger","Quinton","Talon","Romeo","Franklin","Noel","Alijah","Guillermo","Gunner","Damon","Jadon","Emerson","Micheal","Bruce","Terry","Kolton","Melvin","Beckett","Porter","August","Brycen","Dayton","Jamarion","Leonel","Karson","Zayden","Keagan","Carl","Khalil","Cristopher","Nelson","Braiden","Moses","Isaias","Roy","Triston","Walker","Kale","Jermaine","Leon","Rodney","Kristian","Mohamed","Ronan","Pierce","Trace","Warren","Jeffery","Maverick","Cyrus","Quincy","Nathanael","Skylar","Tommy","Conor","Noe","Ezequiel","Demetrius","Jaylin","Kendrick","Frederick","Terrance","Bobby","Jamison","Jon","Rohan","Jett","Kieran","Tobias","Ari","Colt","Gideon","Felipe","Kenny","Wilson","Orion","Kamari","Gunnar","Jessie","Alonzo","Gianni","Omari","Waylon","Malcolm","Emmett","Abram","Julien","London","Tomas","Allan","Terrell","Matteo","Tristin","Jairo","Reginald","Brent","Ahmad","Yandel","Rene","Willie","Boston","Billy","Marlon","Trevon","Aydan","Jamal","Aldo","Ariel","Cason","Braylen","Javion","Joey","Rogelio","Ahmed","Dominik","Brendon","Toby","Kody","Marquis","Ulises","Armani","Adriel","Alfonso","Branden","Will","Craig","Ibrahim","Osvaldo","Wade","Harley","Steve","Davin","Deshawn","Kason","Damion","Jaylon","Jefferson","Aron","Brooks","Darian","Gerald","Rolando","Terrence","Enzo","Kian","Ryland","Barrett","Jaeden","Ben","Bradyn","Giovani","Blaine","Madden","Jerome","Muhammad","Ronnie","Layne","Kolby","Leonard","Vicente","Cale","Alessandro","Zachery","Gavyn","Aydin","Xzavier","Malakai","Raphael","Cannon","Rudy","Asa","Darrell","Giancarlo","Elisha","Junior","Zackery","Alvaro","Lewis","Valentin","Deacon","Jase","Harry","Kendall","Rashad","Finnegan","Mohammed","Ramiro","Cedric","Brennen","Santino","Stanley","Tyrone","Chace","Francis","Johnathon","Teagan","Zechariah","Alonso","Kaeden","Kamden","Gilberto","Ray","Karter","Luciano","Nico","Kole","Aryan","Draven","Jamie","Misael","Lee","Alexzander","Camren","Giovanny","Amare","Rhett","Rhys","Rodolfo","Nash","Markus","Deven","Mohammad","Moshe","Quintin","Dwayne","Memphis","Atticus","Davian","Eugene","Jax","Antoine","Wayne","Randall","Semaj","Uriah","Clark","Aidyn","Jorden","Maxim","Aditya","Lawson","Messiah","Korbin","Sullivan","Freddy","Demarcus","Neil","Brice","King","Davon","Elvis","Ace","Dexter","Heath","Duncan","Jamar","Sincere","Irvin","Remington","Kadin","Soren","Tyree","Damarion","Talan","Adrien","Gilbert","Keenan","Darnell","Adolfo","Tristian","Derick","Isai","Rylee","Gauge","Harold","Kareem","Deangelo","Agustin","Coleman","Zavier","Lamar","Emery","Jaydin","Devan","Jordyn","Mathias","Prince","Sage","Seamus","Jasiah","Efrain","Darryl","Arjun","Mike","Roland","Conrad","Kamron","Hamza","Santos","Frankie","Dominique","Marley","Vance","Dax","Jamir","Kylan","Todd","Maximo","Jabari","Matthias","Haiden","Luka","Marcelo","Keon","Layton","Tyrell","Kash","Raiden","Cullen","Donte","Jovani","Cordell","Kasen","Rory","Alfred","Darwin","Ernest","Bailey","Gaige","Hassan","Jamarcus","Killian","Augustus","Trevin","Zain","Ellis","Rex","Yusuf","Bruno","Jaidyn","Justus","Ronin","Humberto","Jaquan","Josh","Kasey","Winston","Dashawn","Lucian","Matias","Sidney","Ignacio","Nigel","Van","Elian","Finley","Jaron","Addison","Aedan","Braedon","Jadyn","Konner","Zayne","Franco","Niko","Savion","Cristofer","Deon","Krish","Anton","Brogan","Cael","Coby","Kymani","Marcel","Yair","Dale","Bo","Jordon","Samir","Darien","Zaire","Ross","Vaughn","Devyn","Kenyon","Clay","Dario","Ishaan","Jair","Kael","Adonis","Jovanny","Clinton","Rey","Chaim","German","Harper","Nathen","Rigoberto","Sonny","Glenn","Octavio","Blaze","Keshawn","Ralph","Ean","Nikhil","Rayan","Sterling","Branson","Jadiel","Dillan","Jeramiah","Koen","Konnor","Antwan","Houston","Tyrese","Dereon","Leonidas","Zack","Fisher","Jaydan","Quinten","Nick","Urijah","Darion","Jovan","Salvatore","Beckham","Jarrett","Antony","Eden","Makai","Zaiden","Broderick","Camryn","Malaki","Nikolai","Howard","Immanuel","Demarion","Valentino","Jovanni","Ayaan","Ethen","Leandro","Royce","Yael","Yosef","Jean","Marquise","Alden","Leroy","Gaven","Jovany","Tyshawn","Aarav","Kadyn","Milton","Zaid","Kelton","Tripp","Kamren","Slade","Hezekiah","Jakobe","Nathanial","Rishi","Shamar","Geovanni","Pranav","Roderick","Bentley","Clarence","Lyric","Bernard","Carmelo","Denzel","Maximillian","Reynaldo","Cassius","Gordon","Reuben","Samson","Yadiel","Jayvon","Reilly","Sheldon","Abdullah","Jagger","Thaddeus","Case","Kyson","Lamont","Chaz","Makhi","Jan","Marques","Oswaldo","Donavan","Keyon","Kyan","Simeon","Trystan","Andreas","Dangelo","Landin","Reagan","Turner","Arnav","Brenton","Callum","Jayvion","Bridger","Sammy","Deegan","Jaylan","Lennon","Odin","Abdiel","Jerimiah","Eliezer","Bronson","Cornelius","Pierre","Cortez","Baron","Carlo","Carsen","Fletcher","Izayah","Kolten","Damari","Hugh","Jensen","Yurem"]}';
        $names = json_decode($names,true);
        $index = mt_rand(0, 1999);
        return $names['data'][$index];
    }
    
    function mylastName(){
        $lastname = '{"data":["Smith","Johnson","Williams","Brown","Jones","Miller","Davis","Garcia","Rodriguez","Wilson","Martinez","Anderson","Taylor","Thomas","Hernandez","Moore","Martin","Jackson","Thompson","White","Lopez","Lee","Gonzalez","Harris","Clark","Lewis","Robinson","Walker","Perez","Hall","Young","Allen","Sanchez","Wright","King","Scott","Green","Baker","Adams","Nelson","Hill","Ramirez","Campbell","Mitchell","Roberts","Carter","Phillips","Evans","Turner","Torres","Parker","Collins","Edwards","Stewart","Flores","Morris","Nguyen","Murphy","Rivera","Cook","Rogers","Morgan","Peterson","Cooper","Reed","Bailey","Bell","Gomez","Kelly","Howard","Ward","Cox","Diaz","Richardson","Wood","Watson","Brooks","Bennett","Gray","James","Reyes","Cruz","Hughes","Price","Myers","Long","Foster","Sanders","Ross","Morales","Powell","Sullivan","Russell","Ortiz","Jenkins","Gutierrez","Perry","Butler","Barnes","Fisher","Henderson","Coleman","Simmons","Patterson","Jordan","Reynolds","Hamilton","Graham","Kim","Gonzales","Alexander","Ramos","Wallace","Griffin","West","Cole","Hayes","Chavez","Gibson","Bryant","Ellis","Stevens","Murray","Ford","Marshall","Owens","Mcdonald","Harrison","Ruiz","Kennedy","Wells","Alvarez","Woods","Mendoza","Castillo","Olson","Webb","Washington","Tucker","Freeman","Burns","Henry","Vasquez","Snyder","Simpson","Crawford","Jimenez","Porter","Mason","Shaw","Gordon","Wagner","Hunter","Romero","Hicks","Dixon","Hunt","Palmer","Robertson","Black","Holmes","Stone","Meyer","Boyd","Mills","Warren","Fox","Rose","Rice","Moreno","Schmidt","Patel","Ferguson","Nichols","Herrera","Medina","Ryan","Fernandez","Weaver","Daniels","Stephens","Gardner","Payne","Kelley","Dunn","Pierce","Arnold","Tran","Spencer","Peters","Hawkins","Grant","Hansen","Castro","Hoffman","Hart","Elliott","Cunningham","Knight","Bradley","Carroll","Hudson","Duncan","Armstrong","Berry","Andrews","Johnston","Ray","Lane","Riley","Carpenter","Perkins","Aguilar","Silva","Richards","Willis","Matthews","Chapman","Lawrence","Garza","Vargas","Watkins","Wheeler","Larson","Carlson","Harper","George","Greene","Burke","Guzman","Morrison","Munoz","Jacobs","Obrien","Lawson","Franklin","Lynch","Bishop","Carr","Salazar","Austin","Mendez","Gilbert","Jensen","Williamson","Montgomery","Harvey","Oliver","Howell","Dean","Hanson","Weber","Garrett","Sims","Burton","Fuller","Soto","Mccoy","Welch","Chen","Schultz","Walters","Reid","Fields","Walsh","Little","Fowler","Bowman","Davidson","May","Day","Schneider","Newman","Brewer","Lucas","Holland","Wong","Banks","Santos","Curtis","Pearson","Delgado","Valdez","Pena","Rios","Douglas","Sandoval","Barrett","Hopkins","Keller","Guerrero","Stanley","Bates","Alvarado","Beck","Ortega","Wade","Estrada","Contreras","Barnett","Caldwell","Santiago","Lambert","Powers","Chambers","Nunez","Craig","Leonard","Lowe","Rhodes","Byrd","Gregory","Shelton","Frazier","Becker","Maldonado","Fleming","Vega","Sutton","Cohen","Jennings","Parks","Mcdaniel","Watts","Barker","Norris","Vaughn","Vazquez","Holt","Schwartz","Steele","Benson","Neal","Dominguez","Horton","Terry","Wolfe","Hale","Lyons","Graves","Haynes","Miles","Park","Warner","Padilla","Bush","Thornton","Mccarthy","Mann","Zimmerman","Erickson","Fletcher","Mckinney","Page","Dawson","Joseph","Marquez","Reeves","Klein","Espinoza","Baldwin","Moran","Love","Robbins","Higgins","Ball","Cortez","Le","Griffith","Bowen","Sharp","Cummings","Ramsey","Hardy","Swanson","Barber","Acosta","Luna","Chandler","Blair","Daniel","Cross","Simon","Dennis","Oconnor","Quinn","Gross","Navarro","Moss","Fitzgerald","Doyle","Mclaughlin","Rojas","Rodgers","Stevenson","Singh","Yang","Figueroa","Harmon","Newton","Paul","Manning","Garner","Mcgee","Reese","Francis","Burgess","Adkins","Goodman","Curry","Brady","Christensen","Potter","Walton","Goodwin","Mullins","Molina","Webster","Fischer","Campos","Avila","Sherman","Todd","Chang","Blake","Malone","Wolf","Hodges","Juarez","Gill","Farmer","Hines","Gallagher","Duran","Hubbard","Cannon","Miranda","Wang","Saunders","Tate","Mack","Hammond","Carrillo","Townsend","Wise","Ingram","Barton","Mejia","Ayala","Schroeder","Hampton","Rowe","Parsons","Frank","Waters","Strickland","Osborne","Maxwell","Chan","Deleon","Norman","Harrington","Casey","Patton","Logan","Bowers","Mueller","Glover","Floyd","Hartman","Buchanan","Cobb","French","Kramer","Mccormick","Clarke","Tyler","Gibbs","Moody","Conner","Sparks","Mcguire","Leon","Bauer","Norton","Pope","Flynn","Hogan","Robles","Salinas","Yates","Lindsey","Lloyd","Marsh","Mcbride","Owen","Solis","Pham","Lang","Pratt","Lara","Brock","Ballard","Trujillo","Shaffer","Drake","Roman","Aguirre","Morton","Stokes","Lamb","Pacheco","Patrick","Cochran","Shepherd","Cain","Burnett","Hess","Li","Cervantes","Olsen","Briggs","Ochoa","Cabrera","Velasquez","Montoya","Roth","Meyers","Cardenas","Fuentes","Weiss","Hoover","Wilkins","Nicholson","Underwood","Short","Carson","Morrow","Colon","Holloway","Summers","Bryan","Petersen","Mckenzie","Serrano","Wilcox","Carey","Clayton","Poole","Calderon","Gallegos","Greer","Rivas","Guerra","Decker","Collier","Wall","Whitaker","Bass","Flowers","Davenport","Conley","Houston","Huff","Copeland","Hood","Monroe","Massey","Roberson","Combs","Franco","Larsen","Pittman","Randall","Skinner","Wilkinson","Kirby","Cameron","Bridges","Anthony","Richard","Kirk","Bruce","Singleton","Mathis","Bradford","Boone","Abbott","Charles","Allison","Sweeney","Atkinson","Horn","Jefferson","Rosales","York","Christian","Phelps","Farrell","Castaneda","Nash","Dickerson","Bond","Wyatt","Foley","Chase","Gates","Vincent","Mathews","Hodge","Garrison","Trevino","Villarreal","Heath","Dalton","Valencia","Callahan","Hensley","Atkins","Huffman","Roy","Boyer","Shields","Lin","Hancock","Grimes","Glenn","Cline","Delacruz","Camacho","Dillon","Parrish","Oneill","Melton","Booth","Kane","Berg","Harrell","Pitts","Savage","Wiggins","Brennan","Salas","Marks","Russo","Sawyer","Baxter","Golden","Hutchinson","Liu","Walter","Mcdowell","Wiley","Rich","Humphrey","Johns","Koch","Suarez","Hobbs","Beard","Gilmore","Ibarra","Keith","Macias","Khan","Andrade","Ware","Stephenson","Henson","Wilkerson","Dyer","Mcclure","Blackwell","Mercado","Tanner","Eaton","Clay","Barron","Beasley","Oneal","Preston","Small","Wu","Zamora","Macdonald","Vance","Snow","Mcclain","Stafford","Orozco","Barry","English","Shannon","Kline","Jacobson","Woodard","Huang","Kemp","Mosley","Prince","Merritt","Hurst","Villanueva","Roach","Nolan","Lam","Yoder","Mccullough","Lester","Santana","Valenzuela","Winters","Barrera","Leach","Orr","Berger","Mckee","Strong","Conway","Stein","Whitehead","Bullock","Escobar","Knox","Meadows","Solomon","Velez","Odonnell","Kerr","Stout","Blankenship","Browning","Kent","Lozano","Bartlett","Pruitt","Buck","Barr","Gaines","Durham","Gentry","Mcintyre","Sloan","Melendez","Rocha","Herman","Sexton","Moon","Hendricks","Rangel","Stark","Lowery","Hardin","Hull","Sellers","Ellison","Calhoun","Gillespie","Mora","Knapp","Mccall","Morse","Dorsey","Weeks","Nielsen","Livingston","Leblanc","Mclean","Bradshaw","Glass","Middleton","Buckley","Schaefer","Frost","Howe","House","Mcintosh","Ho","Pennington","Reilly","Hebert","Mcfarland","Hickman","Noble","Spears","Conrad","Arias","Galvan","Velazquez","Huynh","Frederick","Randolph","Cantu","Fitzpatrick","Mahoney","Peck","Villa","Michael","Donovan","Mcconnell","Walls","Boyle","Mayer","Zuniga","Giles","Pineda","Pace","Hurley","Mays","Mcmillan","Crosby","Ayers","Case","Bentley","Shepard","Everett","Pugh","David","Mcmahon","Dunlap","Bender","Hahn","Harding","Acevedo","Raymond","Blackburn","Duffy","Landry","Dougherty","Bautista","Shah","Potts","Arroyo","Valentine","Meza","Gould","Vaughan","Fry","Rush","Avery","Herring","Dodson","Clements","Sampson","Tapia","Bean","Lynn","Crane","Farley","Cisneros","Benton","Ashley","Mckay","Finley","Best","Blevins","Friedman","Moses","Sosa","Blanchard","Huber","Frye","Krueger","Bernard","Rosario","Rubio","Mullen","Benjamin","Haley","Chung","Moyer","Choi","Horne","Yu","Woodward","Ali","Nixon","Hayden","Rivers","Estes","Mccarty","Richmond","Stuart","Maynard","Brandt","Oconnell","Hanna","Sanford","Sheppard","Church","Burch","Levy","Rasmussen","Coffey","Ponce","Faulkner","Donaldson","Schmitt","Novak","Costa","Montes","Booker","Cordova","Waller","Arellano","Maddox","Mata","Bonilla","Stanton","Compton","Kaufman","Dudley","Mcpherson","Beltran","Dickson","Mccann","Villegas","Proctor","Hester","Cantrell","Daugherty","Cherry","Bray","Davila","Rowland","Levine","Madden","Spence","Good","Irwin","Werner","Krause","Petty","Whitney","Baird","Hooper","Pollard","Zavala","Jarvis","Holden","Haas","Hendrix","Mcgrath","Bird","Lucero","Terrell","Riggs","Joyce","Mercer","Rollins","Galloway","Duke","Odom","Andersen","Downs","Hatfield","Benitez","Archer","Huerta","Travis","Mcneil","Hinton","Zhang","Hays","Mayo","Fritz","Branch","Mooney","Ewing","Ritter","Esparza","Frey","Braun","Gay","Riddle","Haney","Kaiser","Holder","Chaney","Mcknight","Gamble","Vang","Cooley","Carney","Cowan","Forbes","Ferrell","Davies","Barajas","Shea","Osborn","Bright","Cuevas","Bolton","Murillo","Lutz","Duarte","Kidd","Key","Cooke","Ann","Bree","Dawn","Fawn","Fern","Aryn","Jacklyn","Jae","Jaidyn","Kathryn","Krystan","Lee","Lynn","Mae","Sue","Blair","Blaise","Blake","Blanche","Blayne","Brooke","Hope","Jane","June","Kate","Lane","Love","Merle","Raine","Rose","Rylie","Taye","Adele","Joan","Alice","Anise","Arden","Aryn","Ashten","Berlynn","Bernice","Breean","Brighton","Carmden","Candice","Caprice","Caren","Carleen","Carlen","Caylen","Cerise","Coreen","Debree","Denise","Devon","Dustin","Elein","Ellen","Ellice","Erin","Haiden","Hollyn","Javan","Jolee","Jordon","Kae","Kaitlin","Kalan","Korin","Kylie","Lashon","Meaghan","Monteen","Nadeen","Naveen","Ocean","Olive","Payten","Raven","Rayleen","Reagan","Rene","Robin","Selene","Sharon","Sherleen","Suzan","Taylore","Zion","Zoe","Bailee","Allison","Carelyn","Ellison","Julian","Karilyn","Madisen","Abigail","Bianca","Coralie","Amelia","Madeleine","Matilda","Lillian","Louisa","Viola","Silvia","Natalie","Mirabel","Rebecca","Miranda","Rosalind","Naomi","Vanessa","Susannah","Eloise","Gillian","Annabel","Emeline","Imogen","Claudia","Annora","Clelia","Elodie","Lucinda","Juliet","Leonie","Clementine","Georgina","Harriet","Joanna","Marcella","Marguerite","Lilibeth","Verena","Sophia","Linnea","Julina","Amity","Janetta","Evony","Ellory","Evelyn","Gwendolen","Miriam","Irene","Vivian","Coralie","Anneliese","Adelaide","Syllable","Ace","Abe","Beck","Blake","Dean","Grant","Hugh","James","Charles","George","Jude","Rhett","Kent","Lee","Brett","Luke","Chase","Claude","Paul","Reese","Sean","Trey","Bram","Brandt","Cash","Grey","Dex","Jack","Judd","Lane","Coy","Brock","Dash","Clark","Drew","Ray","Heath","Finn","Seth","Neil","Zane","Will","Troy","Shane","Jax","Reeve","Glenn","Drake","Wade","Arthur","David","Robert","Aiden","Conrad","Bailey","Damon","Michael","Justin","Noel","Dante","Brendon","Thomas","Vincent","Edward","Louis","Randall","Byron","Henry","Preston","Quintin","Joseph","Lawrence","Aaron","Riley","Noah","Isaac","Levi","Felix","Caleb","Cody","Sutton","Ryder","Kai","Justice","Oscar","Denver","Gavin","Jared","Eli","Warren","Tristan","Doran","Jasper","Juan","Leo","Francis","Murphy","Nevin","Porter","Oren","Dezi","Jackson","Kingston","Lydon","Hyrum","Trevor","Tanner","Vernon","Tyson","Myron","Rory","Anthony","Daniel","Avery","William","Tavian","Xavier","Gabriel","Garrison","Tobias","Marcellus","Gregory","Everett","Emerson","Dominick","Apollo","Abraham","Varian","Orlando","Harrison","Damien","Julian","Sheridan","Matteo","Oliver","Jeremy","Ricardo","Elias","Zachary","Benjamin","Timothy","Isaiah","Cameron","Nicolas","Malachi","Elijah","Fernando","Sullivan","Josiah"]}';
        $lastname = json_decode($lastname,true);
        $index = mt_rand(0, 1299);
        return $lastname['data'][$index];
    }
    
    function myemail2($name=''){
        $json = '{
		"dm": ["gmail.com", "yahoo.com", "aol.com", "hotmail.com", "comcast.net", "msn.com", "sbcglobal.net", "att.net", "verizon.net", "live.com", "me.com", "mac.com", "outlook.com", "icloud.com", "optonline.net", "yahoo.ca"]
	}';
        $data = json_decode($json,true);
        $c = mt_rand(0,15);
        $name = str_replace(' ', '_', $name);
        $name = str_replace(',', '_', $name);
        $name = str_replace('\'', '', $name);
        $name = strtolower($name);
        return $name.'@'.$data['dm'][$c];
    }
    
    function myemail($i=''){
        $json = '{
		"un": ["tattooman", "rasca", "hikoza", "arachne", "rgarcia", "russotto", "nicktrig", "martink", "mnemonic", "luvirini", "ingolfke", "sbmrjbr", "chaki", "dobey", "retoh", "mavilar", "daveewart", "gknauss", "moxfulder", "citadel", "moonlapse", "skoch", "errxn", "rattenbt", "ilyaz", "durist", "msroth", "nweaver", "pierce", "intlprog", "gamma", "panolex", "mastinfo", "torgox", "maradine", "mgemmons", "satishr", "jginspace", "wayward", "falcao", "sethbrown", "frode", "shang", "mugwump", "froodian", "biglou", "samavati", "symbolic", "doormat", "curly", "emmanuel", "oneiros", "privcan", "kmiller", "seurat", "airship", "kodeman", "aibrahim", "lbecchi", "kayvonf", "policies", "linuxhack", "grothoff", "matty", "sequin", "rupak", "sarahs", "tsuruta", "chinthaka", "improv", "timtroyr", "choset", "gboss", "jelmer", "murdocj", "jbailie", "drezet", "noticias", "elmer", "ateniese", "metzzo", "hillct", "claesjac", "bwcarty", "manuals", "danzigism", "hamilton", "bahwi", "fhirsch", "william", "parasite", "kingma", "overbom", "thowell", "jfinke", "petersko", "jdray", "crusader", "euice", "pgottsch", "monkeydo", "gilmoure", "mthurn", "slanglois", "garland", "kempsonc", "unreal", "madler", "novanet", "scato", "liedra", "bescoto", "flavell", "gmcgath", "barnett", "claypool", "formis", "sscorpio", "pplinux", "offthelip", "gerlo", "jaffe", "stomv", "dgatwood", "jonas", "mcmillan", "fudrucker", "jeffcovey", "fbriere", "slaff", "scarolan", "rhialto", "horrocks", "ehood", "corrada", "zeller", "wonderkid", "meder", "harryh", "haddawy", "enintend", "ideguy", "kobayasi", "uncle", "cameron", "jpflip", "dimensio", "amimojo", "credmond", "jbearp", "boser", "naoya", "iamcal", "jipsen", "csilvers", "kronvold", "kidehen", "kiddailey", "jlbaumga", "debest", "stern", "jtorkbob", "bradl", "caidaperl", "brainless", "seano", "giafly", "moinefou", "dalamb", "zilla", "geekoid", "jugalator", "chunzi", "dkasak", "hachi", "cfhsoft", "library", "wiseb", "treeves", "ijackson", "klaudon", "calin", "atmarks", "treit", "hahiss", "wilsonpm", "mddallara", "kimvette", "eimear", "zwood", "kjetilk", "demmel", "rande", "bonmots", "trygstad", "larry", "tangsh", "barlow", "djpig", "matloff", "roamer", "parrt", "pappp", "joglo", "sburke", "alfred", "kildjean", "mrobshaw", "krueger", "cremonini", "storerm", "nasarius", "kjohnson", "madanm", "fluffy", "okroeger", "notaprguy", "chronos", "mailarc", "weidai", "drhyde", "gozer", "itstatus", "maneesh", "marin", "murty", "ahuillet", "arathi", "yenya", "neuffer", "howler", "fallorn", "elflord", "henkp", "bmidd", "tedrlord", "mchugh", "alastair", "subir", "satch", "boomzilla", "andrewik", "ullman", "aglassis", "naupa", "jyoliver", "mrdvt", "lbaxter", "rcwil", "hager", "peoplesr", "british", "sharon", "akoblin", "joelw", "hwestiii", "gemmell", "mschwartz", "pizza", "mahbub", "dvdotnet", "cgreuter", "padme", "podmaster", "yzheng", "floxy", "bbirth", "nighthawk", "jaxweb", "konst", "mwilson", "bhtower", "miyop", "balchen", "shazow", "ovprit", "telbij", "njpayne", "fraser", "drjlaw", "nwiger", "multiplx", "willg", "dburrows", "smcnabb", "solomon", "flakeg", "fglock", "malin", "punkis", "forsberg", "parents", "eabrown", "zavadsky", "grady", "cvrcek", "joehall", "pajas", "gtewari", "eidac", "thaljef", "comdig", "ryanvm", "ewaters", "thurston", "firstpr", "jadavis", "glenz", "geoffr", "bogjobber", "sekiya", "jimmichie", "stecoop", "denism", "fangorn", "rmcfarla", "benits", "tarreau", "steve", "lamprecht", "vlefevre", "ajohnson", "loscar", "clkao", "jhardin", "jrifkin", "granboul", "leakin", "animats", "smpeters", "marcs", "rnewman", "druschel", "lridener", "mfburgo", "bulletin", "adamk", "gospodin", "gravyface", "leocharre", "mcast", "mxiao", "dexter", "jbryan", "godeke", "lstaf", "fairbank", "dwheeler", "ganter", "dbrobins", "world", "bcevc", "laird", "gomor", "lcheng", "petersen", "dialworld", "cosimo", "marioph", "goresky", "carreras", "jonadab", "tbusch", "johndo", "chaikin", "thrymm", "jdhedden", "malattia", "codex", "cyrus", "onestab", "chrisk", "mallanmba", "mpiotr", "jesse", "goldberg", "kmself", "shawnce", "frikazoyd", "mbrown", "luebke", "dieman", "rgarton", "epeeist", "pkplex", "nimaclea", "ralamosm", "munson", "wainwrig", "juliano", "richard", "kudra", "matthijs", "dprice", "campware", "ryanshaw", "afifi", "burniske", "dmbkiwi", "psharpe", "pavel", "eurohack", "paulv", "parsimony", "monopole", "jsnover", "yxing", "stellaau", "dogdude", "danny", "isorashi", "danneng", "ebassi", "smallpaul", "cliffski", "erynf", "gregh", "milton", "yangyan", "ilial", "uncled", "aukjan", "lipeng", "saridder", "mhanoh", "openldap", "redingtn", "jmcnamara", "suresh", "uraeus", "bachmann", "natepuri", "ramollin", "mrsam", "teverett", "yfreund", "mirod", "bester", "kingjoshi", "sassen", "ccohen", "bmcmahon", "tskirvin", "osrin", "bowmanbs", "duchamp", "purvis", "lydia", "sinkou", "drewf", "dmouse", "chlim", "jnolan", "jfreedma", "jsbach", "kassiesa", "guialbu", "jshirley", "chrisj", "phish", "rbarreira", "nelson", "lishoy", "leslie", "conteb", "pakaste", "jwarren", "konit", "webdragon", "kevinm", "citizenl", "nogin", "jkegl", "gward", "arebenti", "ajlitt", "mstrout", "chaffar", "farber", "pdbaby", "srour", "vmalik", "boein", "msloan", "shaffei", "hauma", "catalog", "eminence", "tamas", "heine", "sopwith", "aracne", "makarow", "hoyer", "bdthomas", "maratb", "kannan", "twoflower", "phizntrg", "pfitza", "tbmaddux", "johnbob", "mobileip", "mlewan", "dbanarse", "budinger", "jsmith", "kludge", "crandall", "mjewell", "bflong", "adhere", "kohlis", "marnanel", "cgarcia", "sisyphus", "seanq", "frosal", "meinkej", "mglee", "philen", "nasor", "fwitness", "dawnsong", "eegsa", "magusnet", "carcus", "stinson", "wojciech", "scarlet", "rgiersig", "ournews", "gavollink", "speeves", "rhavyn", "josem", "hoangle", "dbindel", "jrkorson", "jdhildeb", "rfoley", "blixem", "dkrishna", "fukuchi", "wildixon", "miturria", "jacks", "oechslin", "jgwang", "papathan", "osaru", "ahmad", "smartfart", "hedwig", "phyruxus", "rtanter", "jamuir", "cumarana", "dkeeler", "vganesh", "juerd", "kaiser", "breegster", "frederic", "isotopian", "sherzodr", "kourai", "msherr", "tbeck", "wikinerd", "portscan", "jigsaw", "pjacklam", "specprog", "ardagna", "dleconte", "tmaek", "syrinx", "mccurley", "gbacon", "kspiteri", "jshearer", "bryam", "tubesteak", "mdielmann", "carmena", "sjava", "tokuhirom", "sartak", "miltchev", "lamky", "preneel", "hling", "josephw", "augusto", "rfisher", "markjugg", "drolsky", "ianbuck", "crowemojo", "agolomsh", "hermanab", "attwood", "chrwin", "wbarker", "ducasse", "shrapnull", "gator", "mcraigw", "reziac", "wmszeliga", "agapow", "ilikered", "crypt", "webteam", "earmstro", "hllam", "psichel", "crobles", "lauronen", "cderoove", "kawasaki", "camenisch", "rkobes", "wenzlaff", "aegreene", "szymansk", "isaacson", "michiel", "skajan", "kewley", "dmath", "mwitte", "grinder", "rsteiner", "grolschie", "mosses", "skythe", "staffelb", "violinhi", "tkrotchko", "arnold", "grossman", "hmbrand", "raides", "galbra", "dodong", "garyjb", "martyloo", "frostman", "kwilliams", "seasweb", "louise", "inico", "tlinden", "killmenow", "rddesign", "philb", "andrei", "noahb", "brbarret", "kostas", "spadkins", "zeitlin", "djupedal", "keijser", "dwsauder", "netsfr", "fraterk", "tromey", "sravani", "tjensen", "jimxugle", "dhwon", "cliffordj", "houle", "hampton", "aprakash", "empathy", "ranasta", "stakasa", "fwiles", "lukka", "gtaylor", "mschilli", "quinn", "amcuri", "facet", "morain", "miami", "jonathan", "raines", "sthomas", "paley", "maikelnai", "cisugrad", "kosact", "ylchang", "bockelboy", "lushe", "mgreen", "bsikdar", "malvar", "irving", "janneh", "sfoskett", "scotfl", "yamla", "hellfire", "oevans", "rohitm", "campbell", "aardo", "aaribaud", "timlinux", "skaufman", "yruan", "kalpol", "studyabr", "pemungkah", "yumpy", "dsugal", "pspoole", "jmmuller", "barjam", "bader", "sblack", "daveed", "geeber", "heckerman", "syncnine", "melnik", "johnh", "darin", "muzzy", "jfriedl", "mcsporran", "mkearl", "wkrebs", "tellis", "jemarch", "nichoj", "anicolao", "ngedmond", "thassine", "squirrel", "bartak", "mleary", "bancboy", "sakusha", "snunez", "vsprintf", "benanov", "cparis", "koudas", "mcnihil", "geekgrl", "dinther", "payned", "wsnyder", "majordick", "alias", "fatelk", "burns", "ubergeeb", "helger", "microfab", "keiji", "parkes", "esasaki", "mhouston", "dcoppit", "arandal", "jmgomez", "neonatus", "gastown", "portele", "wortmanj", "mbswan", "esokullu", "gommix", "bebing", "stefano", "bmorrow", "rafasgj", "rsmartin", "pgolle", "gavinls", "karasik", "sabren", "smeier", "quantaman", "penna", "sonnen", "heidrich", "knorr", "lstein", "hermes", "duncand", "baveja", "wildfire", "webinc", "greear", "jeteve", "dowdy", "dunstan", "birddog", "starstuff", "mcrawfor", "fmerges", "rogerspl", "gordonjcp", "seemant", "oracle", "damian", "dmiller", "hakim", "boftx", "jgoerzen", "tmccarth", "rjones", "kenja", "cgcra", "seebs", "qrczak", "esbeck", "jaarnial", "munjal", "mhoffman", "ismail", "flaviog", "pedwards", "bjoern", "heroine", "iapetus", "emcleod", "wagnerch", "tristan", "jbarta", "sagal", "dwendlan", "amichalo", "thomasj", "odlyzko", "sriha", "jandrese", "jgmyers", "alhajj", "jespley", "brickbat", "gumpish", "afeldspar", "matsn", "kuparine", "graham", "janusfury", "evilopie", "rnelson", "weazelman", "bolow", "devphil", "jusdisgi", "andersbr", "mwandel", "gslondon", "tubajon", "hstiles", "donev", "bruck", "crowl", "nachbaur", "ivoibs", "sumdumass", "singer", "jguyer", "harpes", "tezbo", "bdbrown", "tfinniga", "whimsy", "chance", "gfxguy", "rwelty", "nacho", "bigmauler", "peterhoeg", "doche", "hutton", "mmccool", "oster", "noodles", "jschauma", "niknejad", "grdschl", "uqmcolyv", "pereinar", "nanop", "sacraver", "munge", "lpalmer", "pthomsen", "jmorris", "sokol", "keutzer", "stewwy", "warrior", "bartlett", "bryanw", "pkilab", "jaesenj", "simone", "jcholewa", "engelen", "cmdrgravy", "msusa", "stevelim", "bhima", "denton", "ghost", "bastian", "scitext", "nullchar", "xtang", "ghaviv", "jfmulder", "themer", "vertigo", "temmink", "scottzed", "evans", "paina", "ribet", "dsowsy", "crimsane", "jorgb", "dgriffith", "avalon", "lahvak", "fmtbebuck", "amaranth", "dhrakar", "zyghom", "singh", "carroll", "trieuvan", "imightb", "aschmitz", "jramio", "pontipak", "delpino", "xnormal", "ninenine", "mhassel", "valdez", "bjornk", "adillon", "plover", "staikos", "jbuchana", "schwaang", "reeds", "sjmuir", "north", "parksh", "caronni", "skippy", "wetter", "fviegas", "kramulous", "noneme", "leviathan", "muadip", "mbalazin", "report", "schumer", "terjesa", "khris", "techie", "pmint", "draper", "gfody", "sinclair", "kdawson", "ozawa", "dougj", "hyper", "roesch", "mfleming", "dpitts", "qmacro", "scottlee", "research", "ntegrity", "hahsler", "andale", "feamster", "salesgeek", "cantu", "presoff", "dartlife"],
		"dm": ["gmail.com", "yahoo.com", "aol.com", "hotmail.com", "comcast.net", "msn.com", "sbcglobal.net", "att.net", "verizon.net", "live.com", "me.com", "mac.com", "outlook.com", "icloud.com", "optonline.net", "yahoo.ca"]
	}';
        $data = json_decode($json,true);
        $a = mt_rand(0,999);
        $c = mt_rand(0,15);
        $name = $data['un'][$a];
        return $name.$i.'@'.$data['dm'][$c];
    }
    
    /* 导入订单 */
    function apporders(){
        Db::connect('db_csut')->table('cs_orders_price')->select();
        
    }
    
    function rzorderid($orderId){
        $idlen = strlen($orderId);
        if($idlen>9){
            return $orderId;
        }else{
            return sprintf("%09d", $orderId);
        }
    }
    
    function getproduct($preprice){
        for($j=1;$j<10;$j++){
            $pre = $j*0.1;
            $min_price = $preprice-$pre<0?0.01:$preprice-$pre;
            $max_price = $preprice+$pre;
            $product_entity = Db::table('catalog_product_index_price')
            ->where("price",'>',$min_price)
            ->where("price",'<',$max_price)
            ->find();
            if(count($product_entity)>0) return $product_entity;
        }
        return [];
    }
    
    function excmailfee($price,$type){
        if($type=='epacket' || $type=='chinapost'){
            if($price<700){
                return mt_rand(0, 150);
            }else{
                return 150;
            }
        }else{
            if($price<700){
                return 150;
            }else{
                return mt_rand(200, 500);
            }
        }
    }
    
    function getshiptypes($price=0,$code){
        $code = trim($code);
        $flag = true;//是否全是数字
        $shipptype1 = array(
            ['chinapost','China_Post'],
            ['epacket','ePacket'],
            ['epacket','ePacket'],
            ['epacket','ePacket'],
            ['epacket','ePacket'],
            ['epacket','ePacket']
        );
        $shipptype2 = array(
            ['dhl','DHL'],
            ['dhl','DHL'],
            ['ups','UPS'],
            ['usps','USPS'],
            ['ems','EMS'],
            ['tnt','TNT'],
            ['fedex','FEDEX'],
            ['dhl','DHL']);
        $shipptype3 = array(
            ['usps','USPS'],
            ['ems','EMS'],
            ['tnt','TNT'],
            ['fedex','FEDEX'],
            ['dhl','DHL']);
        
        if(!empty($code)){
            $pattern = '/\D/';
            preg_match($pattern, $code, $matches);
            if(count($matches)>0){
                //非数字
                $shipptype = array_merge($shipptype1, $shipptype3);
            }else{
                $shipptype = array(['dhl','DHL'],['ups','UPS']);
            }
        }else{
            if($price<7000){
                $shipptype = array(
                    ['chinapost','China_Post'],
                    ['epacket','ePacket'],
                    ['epacket','ePacket'],
                    ['epacket','ePacket'],
                    ['epacket','ePacket'],
                    ['epacket','ePacket']
                );
            }else{
                $shipptype = array(
                    ['dhl','DHL'],
                    ['dhl','DHL'],
                    ['ups','UPS'],
                    ['usps','USPS'],
                    ['ems','EMS'],
                    ['tnt','TNT'],
                    ['fedex','FEDEX'],
                    ['dhl','DHL']);
            }
        }
        $count = count($shipptype)-1;
        $index = mt_rand(0, $count);
        if($shipptype[$index][0]=='chinapost'|| $shipptype[$index][0]=='epacket'){
            $fee = mt_rand(0, 15);
        }else{
            $fee = mt_rand(20, 50);
        }
        $fee = $fee*100;
        array_push($shipptype[$index], $fee);
        if(empty($code)){
            $code = $this->getshipcode($shipptype[0]);
        }
        array_push($shipptype[$index], $code);
        return $shipptype[$index];
    }
    function getshipcode($type){
        if($type=='dhl' || $type=='ups'){
            $a = mt_rand(11111, 99999);
            $a .= mt_rand(10000, 99999);
        }else{
            $array1 = ['RH','RO','RJ','RM','RN','RK','LH','LW','LM','LT','LX','SR','RU','EV'];
            $array2 = ['CN','CN','CN','CN','CN','CN','CN','CN','CN','CN','CN','CN','NL','N'];
            $count = count($array1);
            $count--;
            $nm = mt_rand(0, $count);
            $a = $array1[$nm];
            $a .= mt_rand(11111, 99999);
            $a .= mt_rand(1000, 99999);
            $count = count($array2);
            $count--;
            $nm = mt_rand(0, $count);
            $a .= $array2[$nm];
        }
        return $a;
    }
    /* 精确查找商品             慢*/
    function getgoodsByprice($price){
        $price = $price+0.5;//元
        $igood = array();
        for($jj=1;$jj<6;$jj++){
            $sql = "select act_price,goods_id,title,items from $table ";
            $pppprice = sprintf("%.2f",($price/$jj-0.5));
            $mppprice = sprintf("%.2f",($price/$jj+0.5));
            echo $pppprice."-----$jj\n";
            if($pppprice<1) break;
            $sql .= "where act_price BETWEEN $pppprice AND $mppprice limit 10";
            echo $sql."\n";
            $igood = Db::connect('db_csut')->query($sql);
            if(count($igood)>0) break;
        }
        return [$jj,$igood];
    }
    
    /*查找商品 价格浮动20%*/
    function getgoodsByprice2($price,$table){
        $igood = array();
        for($jj=1;$jj<6;$jj++){
            $sql = "select act_price,goods_id,title,items from $table ";
            $pppprice = sprintf("%.2f",(($price*0.8)/$jj));
            $mppprice = sprintf("%.2f",(($price*1.2)/$jj));
            echo $pppprice."-----$jj\n";
            if($pppprice<1) break;
            $sql .= "where act_price BETWEEN $pppprice AND $mppprice limit 10";
            echo $sql."\n";
            $igood = Db::connect('db_csut')->query($sql);
            if(count($igood)>0) break;
        }
        return [$jj,$igood];
    }
    //根据分类获取商品
    function getgoodsByprice3($price,$index=-1,$table){
        $table = 'ec_goods_info_wm';
//         $types = array(
//             ['Women\'s Clothing',20],
//             ['Phones & Accessories',20],
//             ['Computer,Office,Scurity',10],
//             ['Consumer Electronics',10],
//             ['Jewelry&Watches',5],
//             ['Home&Garden,Appliance',10],
//             ['Toys,Kids & Baby',5],
//             ['Sports&Outdoors',10],
//             ['Beauty & Health,Hair',5],
//             ['Automobiles & Motorcycles',10],
//             ['Home Improvement,Tools',10],
//         );
        $types = array(
            ['Women\'s Clothing',2],
            ['Men\'s Clothing',5],
            ['Phones & Accessories',6],
            ['Jewelry&Watches',7],
            ['Computer,Office,Scurity',44],
            ['Beauty & Health,Hair',5],
            ['Consumer Electronics',85],
            ['Home Improvement,Tools',63],
            ['Toys,Kids & Baby',14],
            ['Sports&Outdoors',18],
            ['Automobiles & Motorcycles',12],
            ['Home&Garden,Appliance',55],
        );
        $arr = array();
        foreach($types as $key => $val){
            $arr[$key] = $val[1];
        }
        $rid = $index >= 0 ? $index : get_rand($arr); //根据概率获取分类id
        $type = addslashes($types[$rid][0]);
        $igood = array();
        $subtable = $table.'_'.$this->gettableName($type);
        
        for($jj=1;$jj<6;$jj++){
            
            $sql = "select act_price,goods_id from $subtable ";
            $pppprice = sprintf("%.2f",(($price*0.8)/$jj));
            $mppprice = sprintf("%.2f",(($price*1.2)/$jj));
            echo $pppprice."-----$jj\n";
            if($pppprice<1) break;
            $sql .= "where  act_price BETWEEN $pppprice AND $mppprice limit 10";
            echo $sql."\n";
            $subgoods = Db::connect('db_csut')->query($sql);
            if(count($subgoods)>0) break;
        }
        
        if(count($subgoods)>0){
            $goodsids = array_column($subgoods, 'goods_id');
            $goodsids = implode("','",$goodsids);
            $table = 'ec_goods_info_wm_2019_6_10';
            $sql = "select act_price,goods_id,title,items from $table where goods_id in('$goodsids')";
            $igood = Db::connect('db_csut')->query($sql);
        }
        return [$jj,$igood];
    }
    
    function gettableName($name){
        $name = str_replace(' ', '111', $name);
        $name = str_replace('\'', '222', $name);
        $name = str_replace('&', '333', $name);
        $name = str_replace('\*', '444', $name);
        $name = str_replace(',', '555', $name);
        $name = str_replace('+', '666', $name);
        $name = str_replace('-', '777', $name);
        $name = str_replace('=', '888', $name);
        $name = str_replace('\\', '', $name);
        return $name;
    }
    
    function eccountrys(){
        Db::connect('db_csut')->field('ename,currency_unit')->select();
        return array(
            ''
        );
    }
    function test(){
        $list = Db::connect('db_csut')
        ->table('ec_goods_info_wm_2019_6_10')
        ->field('goods_id,act_price')
        ->select();
        var_dump($list[0]);exit;
        $arr = [];
        $data = bubbleSort($arr);
        dump($data);
    }
    
}