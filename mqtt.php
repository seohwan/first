<?php
$start_timestamp = microtime(true);

//information of A
$client = new Mosquitto\Client();
$client->onConnect('connect');
$client->onDisconnect('disconnect');
$client->onSubscribe('subscribe');
$client->onMessage('message');
$client->connect("localhost", 1883, 60);
$client->subscribe('+/+/+/0/0/+/0', 0);

//information of B
$client1 = new Mosquitto\Client();
$client1->onConnect('connect');
$client1->onDisconnect('disconnect');
$client1->onSubscribe('subscribe');
$client1->onMessage('message1');
$client1->connect("localhost", 1883, 60);
$client1->subscribe('+/+/0/+/0/0/0', 0);

//information of C
$client2 = new Mosquitto\Client();
$client2->onConnect('connect');
$client2->onDisconnect('disconnect');
$client2->onSubscribe('subscribe');
$client2->onMessage('message2');
$client2->connect("localhost", 1883, 60);
$client2->subscribe('+/0/0/0/+/0/0', 0);

//information of D
$client3 = new Mosquitto\Client();
$client3->onConnect('connect');
$client3->onDisconnect('disconnect');
$client3->onSubscribe('subscribe');
$client3->onMessage('message3');
$client3->connect("localhost", 1883, 60);
$client3->subscribe('+/+/+/0/0/+/+', 1);

//# of testcases
$testcase=3;
//initiation of flags to check repetition
$check1=-1;
$check2=-1;
$check3=-1;
$check4=-1;
//index of the publish
$idx=0;
//initiation of the input topic
for($i=1;$i<7;$i++)
{
    $str[$i]=0;
}

//case of the publish
while ($testcase--) 
{
    for($i=0; $i < 3; $i++)
    {
        $client->loop();
        $client1->loop();
        $client2->loop();
        $client3->loop();
    }
    //input of the publish topic
    $str[1]=1;
    $string=$idx."/";
    $string=$string.$str[1]."/".$str[2]."/".$str[3]."/".$str[4]."/".$str[5]."/".$str[6];        
    $mid = $client->publish( $string , "new string \t" . date('Y-m-d H:i:s'), 1, 0); 
    echo "\n\n";    

    $idx++;  
}
//disconnetion of the clients
$client->disconnect();
$client1->disconnect();
$client2->disconnect();
$client3->disconnect();
//unset the clients
unset($client);
unset($client1);
unset($client2);
unset($client3);
//when client connect the broker, it prints out the text
function connect($r) {
    echo "I got code {$r}\n";
}
//when client subscribe the broker, it prints out the text
function subscribe() {
    echo "Subscribed to a topic\n";
}
//when client gets the message from the broker, it checks the repetition and prints out the text
function message($message) {
    global $check1;
    //divide the string with delimiter '/'
    $strTok = explode('/',$message->topic);
    if($check1 != $strTok[0])
    {
        printf("\nclient got a message on topic %s with payload:%s", 
                $message->topic, $message->payload);
        //save the index of publish to check repetition
        $check1 = $strTok[0];
    }
}
//same as message
function message1($message) {
    global $check2;
    $strTok = explode('/',$message->topic);
    if($check2 != $strTok[0]) 
    {   
        printf("\nclient1 got a message on topic %s with payload:%s", 
                $message->topic, $message->payload);
        $check2 = $strTok[0];
    }
}
//same as message
function message2($message) {
    global $check3;
    $strTok = explode('/',$message->topic); 
    if($check3 != $strTok[0])
    {
        printf("\nclient2 got a message on topic %s with payload:%s", 
                $message->topic, $message->payload);
        $check3 = $strTok[0];
    }
}
//same as message
function message3($message) {
    global $check4;
    $strTok = explode('/', $message->topic);
    if($check4 != $strTok[0])
    {
        printf("\nclient3 got a message on topic %s with payload:%s", 
                $message->topic, $message->payload);
        $check4 = $strTok[0];
    }
}
//when client disconnect with the broker, it prints out the text
function disconnect() {
    echo "\nDisconnected cleanly";
}

$end_timestamp = microtime(true);
$duration = $end_timestamp - $start_timestamp;
error_log("\nExecution took ".$duration." milliseconds.");

