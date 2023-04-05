<?php

namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use DateTime;

class Socket implements MessageComponentInterface {
    
    public function __construct($sqlConn)
    {
        $this->clients = new \SplObjectStorage;
        $this->sqlConn = $sqlConn;
    }
    
    public function onOpen(ConnectionInterface $socketConn) {
        // Store the new connection in $this->clients
        $this->clients->attach($socketConn);
        echo "New connection! ({$socketConn->resourceId})\n";
        // Get all data from tbl_users table
        $sql = 'SELECT firstname, lastname, passport, latest_day, email, password, status FROM tbl_users LEFT JOIN tbl_bots ON tbl_users.bot_id = tbl_bots.id WHERE ISNULL(email) = FALSE AND status = 1 ORDER BY priority';
        $users = $this->sqlConn->query($sql);
        
        if ($users) {
            $result = array();
            // Generate a new array bounding "users" and "bots" table
            foreach ($users as $key => $user) {
                $row = array(
                    'email' => $user['email'],
                    'password' => $user['password'],
                    'name' => $user['lastname'] . ' ' . $user['firstname'],
                    'passport' => $user['passport'],
                    'latest_day' => $user['latest_day'],
                );
                array_push($result, $row);
            }
            $socketConn->send(json_encode($result));
        }
        else {
            echo 'Query failed with users table!';
            return;
        }
    }

    public function sendLogToBrowser($from, $msg) {
        // Generate datetime
        $datetime = new DateTime();
        $timestamp = $datetime->getTimestamp(); // Get the number of seconds since Unix epoch
        $microseconds = $datetime->format('u'); // Get the number of microseconds
        $totalMilliseconds = $timestamp * 1000 + $microseconds / 1000; // Convert to milliseconds
        $data = json_decode($msg);
        $data = json_decode(json_encode($data), true);
        $sql = 'INSERT INTO tbl_logs (email, name, passport, log, created_at) VALUES("' . $data['email'] . '", "' . $data['name'] . '", ' . $data['passport'] . ', "' . $data['log'] . '", ' . $totalMilliseconds . ')';
        $this->sqlConn->query($sql);
        
        // Broadcast to all connected clients except me
        foreach ($this->clients as $client ) {
            if ( $from->resourceId == $client->resourceId ) {
                continue;
            }

            $timestamp = round($totalMilliseconds / 1000);
            $dateTimeString = date('Y-m-d H:i:s', $timestamp) . '.' . sprintf('%03d', $totalMilliseconds % 1000);
            $data = json_decode($msg);
            $data = json_decode(json_encode($data), true);
            $data['created_at'] = $dateTimeString;
            $client->send(
                json_encode(
                    array(
                        'to' => 'browser',
                        'row_data' => $data
                    )
                )
            );
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // receive message from client
        echo "Received new message : $msg\n";

        if($msg == 'admin-call-bot') {
            $sql = 'SELECT firstname, lastname, passport, latest_day, email, password, status FROM tbl_users LEFT JOIN tbl_bots ON tbl_users.bot_id = tbl_bots.id WHERE ISNULL(email) = FALSE AND status = 1 ORDER BY priority';
            $users = $this->sqlConn->query($sql);

            if ($users) {
                echo "Data successfully retrieved from db\n";
                $result = array();
                // Generate a new array
                foreach ($users as $key => $user) {
                    $row = array(
                        'email' => $user['email'],
                        'password' => $user['password'],
                        'name' => $user['firstname'] . ' ' . $user['lastname'],
                        'passport' => $user['passport'],
                        'latest_day' => $user['latest_day'],
                    );
                    array_push($result, $row);
                }
                echo "Sending data successfully prepared\n";
                // Broadcast to the clients who are connecting to this socket server (but for bots)
                foreach ($this->clients as $client ) {
                    echo $client->resourceId;
                    if ( $from->resourceId == $client->resourceId ) {
                        continue;
                    }
                    echo "Data sent to client No." . $client->resourceId . "\n";
                    // $result = array(array('email'=>"ddd@ddd.com", 'password'=>'ddd', 'name'=>'ok', 'passport'=>'99','latest_day'=>'2023-10-01'));
                    
                    $client->send(json_encode($result));
                }
                // Send to the client success code
                echo "Send success callback to client";
                $from->send(
                    json_encode(
                        array(
                            'to' => 'browser',
                            'status' => 'success'
                        )
                   )
                );
            } else {
                // Send to the client failed code
                echo "Send failed callback to client";
                $from->send(
                    json_encode(
                        array(
                            'to' => 'browser',
                            'status' => 'failed'
                        )
                   )
                );
            }
            return;
        }

        // Set interval 5 times
        for ($i = 1; $i <= 5; $i ++) {
            $this->sendLogToBrowser($from, $msg);
            // Wait for 200 milliseconds
            sleep(0.2);
        }
    }

    public function onClose(ConnectionInterface $conn) {
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }

    public function getAllClients() {
        return $this->clients;
    }
}
?>