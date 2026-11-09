<?php
declare(strict_types = 1);
namespace App;

class Logica{
    private const string PATH_FILE = __DIR__."/../data/orders.json";
    private array $orders;
    public function __construct(){
        $file_json = is_readable($this::PATH_FILE) ? file_get_contents($this::PATH_FILE):null;
        $this->orders = json_decode($file_json,true);
    }

    public function getOrders(string|null $status,string|null $category,bool $sort = false):array{
        if(!$status && !$category && !$sort) return $this->orders;
        $newOrders = [];
        $newOrders = array_filter(
            $this->orders,
            fn(array $row):bool=>
                (!$status || $row['status'] === $status)
                &&(!$category || $row['category'] === $category) 
        );
        if($sort) usort($newOrders,fn(array $a, array $b) => $b['amount'] <=> $a['amount']);
        return array_values($newOrders);
    }

    public function getKpis():array{
        $quantity = 0;
        $totalAmount = 0;
        $avgAmount = 0;
        $totalPerStatus = [];
        $totalInvalids = 0;

        foreach($this->orders as $order){
            if((float)$order['amount'] <= 0) $totalInvalids += 1;
            if((float)$order['amount'] > 0 && trim($order['status']) === "completed"){
                $totalAmount += (float)$order['amount'];
                $quantity += 1;
            }
            if((float)$order['amount'] > 0){
                $totalPerStatus[$order['status']] = ($totalPerStatus[$order['status']]??0) + 1;
            }
        }
        $avgAmount = $quantity > 0 ? $totalAmount/$quantity : 0;
        return [
            'Total Recaudado'=>number_format($totalAmount,2),
            'Ticket Promedio'=>number_format($avgAmount,2),
            'Ordenes por estado'=>$totalPerStatus,
            'Registros invalidos'=>$totalInvalids
        ];
    }

}