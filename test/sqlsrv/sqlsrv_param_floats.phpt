--TEST--
Test insertion with floats
--FILE--
﻿<?php
include 'tools.inc';

function ExecData($withParams)
{
    include 'autonomous_setup.php';
       
    set_time_limit(0);  
    sqlsrv_configure('WarningsReturnAsErrors', 1);  
    sqlsrv_get_config('WarningsReturnAsErrors');    
    
    // Connect
    $connectionInfo = array("UID"=>$username, "PWD"=>$password);
    $conn = sqlsrv_connect($serverName, $connectionInfo);
    if( !$conn ) { FatalError("Could not connect.\n"); }

    $tableName = GetTempTableName();
    
    $stmt = sqlsrv_query($conn, "CREATE TABLE $tableName ([c1_float] float, [c2_real] real)");
    sqlsrv_free_stmt($stmt);
    
    if ($withParams) 
    {
        $stmt = sqlsrv_prepare($conn, "INSERT INTO $tableName (c1_float, c2_real) VALUES (?, ?)", array(array(&$v1, SQLSRV_PARAM_IN), array(&$v2, SQLSRV_PARAM_IN)));
    }
    else 
    {
        $stmt = sqlsrv_prepare($conn, "INSERT INTO $tableName (c1_float, c2_real) VALUES (?, ?)", array(&$v1, &$v2));	
    }
    
    $values = array();

    $v1 = 1.0;  
    array_push($values, $v1);
    $v2 = 2.0;  
    array_push($values, $v2);
    sqlsrv_execute($stmt); 
    
    $v1 = 11.0; 
    array_push($values, $v1);
    $v2 = 12.0; 
    array_push($values, $v2);
    sqlsrv_execute($stmt); 
    
    $v1 = 21.0; 
    array_push($values, $v1);
    $v2 = 22.0; 
    array_push($values, $v2);
    sqlsrv_execute($stmt); 
    
    $v1 = 31.0; 
    array_push($values, $v1);
    $v2 = 32.0; 
    array_push($values, $v2);
    sqlsrv_execute($stmt); 
    
    $v1 = 41.0; 
    array_push($values, $v1);
    $v2 = 42.0; 
    array_push($values, $v2);
    sqlsrv_execute($stmt); 
        
    sqlsrv_free_stmt($stmt);   
    
    $idx = 0;
    $stmt = sqlsrv_query($conn, "SELECT * FROM $tableName");  
    while ($result = sqlsrv_fetch($stmt))
    {
        for ($i = 0; $i < 2; $i++) 
        {
            $value = sqlsrv_get_field($stmt, $i); 

            $expected = $values[$idx++];
            $diff = abs(($value - $expected) / $expected);
            if ($diff > _EPSILON)
            {
                echo "Value $value is unexpected\n";                
            }
        }
    }
    sqlsrv_free_stmt($stmt);   
    sqlsrv_close($conn);           
}

function Repro()
{
    StartTest("sqlsrv_statement_exec_param_floats");
    try
    {
        ExecData(true);
        ExecData(false);
    }
    catch (Exception $e)
    {
        echo $e->getMessage();
    }
    echo "\nDone\n";
    EndTest("sqlsrv_statement_exec_param_floats");
}

Repro();

?>
--EXPECT--
﻿
...Starting 'sqlsrv_statement_exec_param_floats' test...

Done
...Test 'sqlsrv_statement_exec_param_floats' completed successfully.
