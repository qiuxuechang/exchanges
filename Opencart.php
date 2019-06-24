<?php
namespace app\myexec\controller;
use think\Db;

class Opencart
{
    
    function vendors(){
        $n=1000;
        $i = 0;
        do{
            $start = $i*$n;
            $products = Db::connect('db_oc')->table('oc_product')
            ->where('date_available','2019-06-10')
            ->field('product_id')
            ->limit($start,$n)
            ->select();
            $i ++;
            if(count($products)>0){
                foreach($products as $val){
                    $data = [
                        'seller_id'=>93261,
                        'product_id'=>$val['product_id'],
                        'is_featured'=>'0',
                        'is_category_featured'=>'0',
                        'is_approved'=>'1',
                        'created_at'=>'2019-06-10',
                        'updated_at'=>'2019-06-10'
                    ];
                    $res = Db::connect('db_oc')->table('oc_purpletree_vendor_products')->insert($data);
                    echo $res."\n";
                }
            }
            
        }while($n==1000);
        echo "======END========";
    }
}