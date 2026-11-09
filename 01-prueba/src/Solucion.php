<?php
declare(strict_types = 1);

$path =__DIR__."/Data/cybersecurity_attacks.csv";
if(is_readable($path)) $dataset = fopen($path,"r");

function showHeaders($dataset):void{
    $headers = fgetcsv(stream:$dataset,separator:",",escape:"");
    foreach($headers as $head) echo $head.PHP_EOL;
}

function showColumn($dataset,string $columnName):void{
    $limit = 0;
    $headers = fgetcsv(stream:$dataset,separator:",",escape:"");
    while(($row = fgetcsv(stream:$dataset,separator:",",escape:""))!==false /*&& $limit < 200*/){
        $data = array_combine($headers,$row);
        if(trim($data[$columnName])==='') continue;
        echo $data[$columnName].PHP_EOL;
        $limit ++;
    }
}

function showAttackTypes($dataset){
    $stats=[];
    $headers = fgetcsv(stream:$dataset,separator:",",escape:"");
    while(($row = fgetcsv(stream:$dataset,separator:",",escape:""))!==false){
        $data = array_combine($headers,$row);
        if(trim($data['Attack Type'])==='') continue;
        if(trim($data['Action Taken'])==='Blocked') continue;
        $severity=$data['Severity Level'];
        $type=$data['Attack Type'];
        $stats[$severity]['Total']=($stats[$severity]['Total'] ?? 0)+1;
        $stats[$severity][$type]=($stats[$severity][$type] ?? 0)+1;
    }
    fclose($dataset);
    print_r($stats);
}

function showActions($dataset){
    $stats=[];
    $headers = fgetcsv(stream:$dataset,separator:",",escape:"");
    while(($row = fgetcsv(stream:$dataset,separator:",",escape:""))!==false){
        $data = array_combine($headers,$row);
        if(trim($data['Attack Type'])==='') continue;
        $alerts=trim($data['Alerts/Warnings'])?: 'No Alert';
        $action=$data['Action Taken'];
        $attackType=$data['Attack Type'];
        $stats[$alerts]['Total']=($stats[$alerts]['Total'] ?? 0)+1;
        $stats[$alerts][$action]['Total']=($stats[$alerts][$action]['Total'] ?? 0)+1;
        //$stats[$alerts][$action]=($stats[$alerts][$action] ?? 0)+1;
        $stats[$alerts][$action][$attackType]=($stats[$alerts][$action][$attackType] ?? 0)+1;
    }
    fclose($dataset);
    print_r($stats);
}

//showHeaders($dataset);
//showColumn($dataset,'Alerts/Warnings');
//showAttackTypes($dataset);
showActions($dataset);

