<?php
$db_name = 'usermgr';
$db_user = 'postgres';
$sql_file_path = './schema.sql';

// Use shell_exec to run the psql command. The '-f' flag executes commands from a file.
// The '--no-password' flag assumes trust or uses environment variables for password.
// Be cautious with security when using shell_exec with user input.
$command = "psql -d $db_name -U $db_user -f $sql_file_path";

// Execute the command
$output = shell_exec($command);

if ($output === null) {
    echo "SQL file executed using psql command-line tool.";
} else {
    echo "Error executing SQL file via psql: " . $output;
}
