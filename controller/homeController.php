<?php
function getAllUsers() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get all data from tbl_users table
    $sql = 'SELECT tbl_users.id, firstname, lastname, passport, latest_day, current_appointment_day, (tbl_bots.email) AS bot_email, priority, status, created_by FROM tbl_users LEFT JOIN tbl_bots ON tbl_users.bot_id = tbl_bots.id ORDER BY priority';
    $result = $conn->query($sql);
    // Set the fetch point first for one more iteration
    // $result->data_seek(0);
    // Create DOM using query result
    $dom = '';
    if ($result->num_rows > 0) {
        $index = 1;
        while ($row = $result->fetch_assoc()) {
            $dom .= '<tr class="border border-solid border-gray-300">' .
                '<td class="py-[12px]">' . $index . '</td>' .
                '<td class="py-[12px]">' . $row['firstname'] . '</td>' .
                '<td class="py-[12px]">' . $row['lastname'] . '</td>' .
                '<td class="py-[12px]">' . $row['passport'] . '</td>' .
                '<td class="py-[12px]">' . $row['latest_day'] . '</td>' .
                '<td class="py-[12px]">' . $row['current_appointment_day'] . '</td>' .
                '<td class="py-[12px]">' . $row['priority'] . '</td>' .
                '<td class="py-[12px]">' . $row['bot_email'] . '</td>' .
                '<td class="py-[12px]">' .
                    '<label class="switch">' .
                        '<input type="checkbox" data-id="' . $row['id'] . '"' .  ($row['status'] == 1 ?' checked' : '') . '>' .
                        '<span class="slider round"></span>' .
                    '</label>' .
                '</td>' .
                '<td class="py-[12px]">' . $row['created_by'] . '</td>' .
                '<td class="py-[12px]">' .
                    '<div class="relative inline-block text-left">' .
                        '<div>' .
                            '<button type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-bg-primary-light px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 duration-300 hover:bg-primary-lighter hover:text-white" data-role="action_dropdown">' .
                                'Action' .
                                '<svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">' .
                                    '<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />' .
                                '</svg>' .
                            '</button>' .
                        '</div>' .
                        '<div class="action-dropdown-menu absolute hidden right-0 z-10 mt-2 w-[100px] origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu">' .
                            '<div class="py-1">' .
                                '<button type="button" class="text-gray-700 block w-full px-4 py-2 text-left text-sm hover:bg-primary-lighter hover:text-white" role="menuitem" tabindex="-1" data-id="' . $row['id'] . '" data-role="edit">Edit</button>' .
                                '<button type="button" class="text-gray-700 block w-full px-4 py-2 text-left text-sm hover:bg-primary-lighter hover:text-white" role="menuitem" tabindex="-1" data-id="' . $row['id'] . '" data-role="delete">Delete</button>' .
                            '</div>' .
                        '</div>' .
                    '</div>' .
                '</td>' .
            '</tr>';
            $index ++;
        }
    } else {
        $dom = '<tr><td colspan="11" class="py-[12px]">No data to display.</td></tr>';
    }
    echo $dom;
}

function addNewUser() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get value from request
    $firstname = $_POST['data']['firstname'];
    $lastname = $_POST['data']['lastname'];
    $passport = $_POST['data']['passport'];
    $latest_day = $_POST['data']['latest_day'];
    $current_appointment_day = $_POST['data']['current_appointment_day'];
    $priority = $_POST['data']['priority'];
    $bot_id = $_POST['data']['bot_id'];
    // Check if it already exists
    $sql = 'SELECT * FROM tbl_users WHERE firstname = "' . $firstname . '" AND lastname = "' . $lastname . '"';
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo 'exist';
        return;
    }
    // Insert new
    session_start();
    $sql = 'INSERT INTO tbl_users (firstname, lastname, passport, latest_day, current_appointment_day, priority, bot_id, created_by) VALUES ("' . $firstname . '", "' . $lastname . '", ' . $passport . ', "' . $latest_day . '", "' . $current_appointment_day . '", ' . $priority . ', ' . $bot_id . ', "' . $_SESSION['name'] . '")';
    $result = $conn->query($sql);
    
    if ($result == TRUE) {
        echo 'success';
    } else {
        echo 'failed';
    }
}

function updateUser() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get value from request
    $row_id = $_POST['data']['row_id'];
    $firstname = $_POST['data']['firstname'];
    $lastname = $_POST['data']['lastname'];
    $passport = $_POST['data']['passport'];
    $latest_day = $_POST['data']['latest_day'];
    $current_appointment_day = $_POST['data']['current_appointment_day'];
    $priority = $_POST['data']['priority'];
    $bot_id = $_POST['data']['bot_id'];
    // Check if it already exists
    $sql = 'SELECT * FROM tbl_users WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo 'not_exist';
        return;
    }
    // Update one
    session_start();
    $sql = 'UPDATE tbl_users SET firstname = "' . $firstname .'", lastname = "' . $lastname . '", passport = ' . $passport . ', latest_day = "' . $latest_day . '", current_appointment_day = "' . $current_appointment_day . '", priority = ' . $priority . ', bot_id = ' . $bot_id . ', created_by = "' . $_SESSION['name'] . '" WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    
    if ($result == TRUE) {
        echo 'success';
    } else {
        echo 'failed';
    }
}

function deleteUser() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get value from request
    $row_id = $_POST['data']['row_id'];
    // Check if it already exists
    $sql = 'SELECT * FROM tbl_users WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo 'not_exist';
        return;
    }
    // Delete one
    $sql = 'DELETE from tbl_users WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    
    if ($result == TRUE) {
        echo 'success';
    } else {
        echo 'failed';
    }
}

function updateUserStatus() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get value from request
    $row_id = $_POST['data']['row_id'];
    $status = $_POST['data']['status'];
    // Check if it already exists
    $sql = 'SELECT * FROM tbl_users WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo 'not_exist';
        return;
    }
    // Update one
    $sql = 'UPDATE tbl_users SET status = ' . $status . ' WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    if ($result == TRUE) {
        echo 'success';
    } else {
        echo 'failed';
    }
}

function getAllBots() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get all data from tbl_bots table
    $sql = 'SELECT id, email, password FROM tbl_bots';
    $result = $conn->query($sql);
    // Create DOM using query result
    $dom = '';
    if ($result->num_rows > 0) {
        $index = 1;
        while ($row = $result->fetch_assoc()) {
            $dom .= '<tr class="border border-solid border-gray-300">' .
                '<td class="py-[12px]">' . $index . '</td>' .
                '<td class="py-[12px]">' . $row['email'] . '</td>' .
                '<td class="py-[12px]">' . $row['password'] . '</td>' .
                '<td class="py-[12px]">' .
                    '<div class="relative inline-block text-left">' .
                        '<div>' .
                            '<button type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-bg-primary-light px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 duration-300 hover:bg-primary-lighter hover:text-white" data-role="action_dropdown">' .
                                'Action' .
                                '<svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">' .
                                    '<path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />' .
                                '</svg>' .
                            '</button>' .
                        '</div>' .
                        '<div class="action-dropdown-menu absolute hidden right-0 z-10 mt-2 w-[100px] origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu">' .
                            '<div class="py-1">' .
                                '<button type="button" class="text-gray-700 block w-full px-4 py-2 text-left text-sm hover:bg-primary-lighter hover:text-white" role="menuitem" tabindex="-1" data-id="' . $row['id'] . '" data-role="edit">Edit</button>' .
                                '<button type="button" class="text-gray-700 block w-full px-4 py-2 text-left text-sm hover:bg-primary-lighter hover:text-white" role="menuitem" tabindex="-1" data-id="' . $row['id'] . '" data-role="delete">Delete</button>' .
                            '</div>' .
                        '</div>' .
                    '</div>' .
                '</td>' .
            '</tr>';
            $index ++;
        }
    } else {
        $dom = '<tr><td colspan="4" class="py-[12px]">No data to display.</td></tr>';
    }
    echo $dom;
}

function addNewBot() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get value from request
    $email = $_POST['data']['email'];
    $password = $_POST['data']['password'];
    // Check if it already exists
    $sql = 'SELECT * FROM tbl_bots WHERE email = "' . $email . '"';
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo 'exist';
        return;
    }
    // Insert new
    $sql = 'INSERT INTO tbl_bots (email, password) VALUES ("' . $email . '", "' . $password . '")';
    $result = $conn->query($sql);
    
    if ($result == TRUE) {
        echo 'success';
    } else {
        echo 'failed';
    }
}

function updateBot() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get value from request
    $row_id = $_POST['data']['row_id'];
    $email = $_POST['data']['email'];
    $password = $_POST['data']['password'];
    // Check if it already exists
    $sql = 'SELECT * FROM tbl_bots WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo 'not_exist';
        return;
    }
    // Update one
    $sql = 'UPDATE tbl_bots SET email = "' . $email .'", password = "' . $password . '" WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    
    if ($result == TRUE) {
        echo 'success';
    } else {
        echo 'failed';
    }
}

function deleteBot() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get value from request
    $row_id = $_POST['data']['row_id'];
    // Check if it already exists
    $sql = 'SELECT * FROM tbl_bots WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo 'not_exist';
        return;
    }
    // Delete one
    $sql = 'DELETE from tbl_bots WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    
    if ($result == TRUE) {
        echo 'success';
    } else {
        echo 'failed';
    }
}

// GET Requeset
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'get-all-users') {
        getAllUsers();
    } else if ($_GET['action'] == 'get-available-priorities') {
        getPriorityDropdownMenu();
    } else if ($_GET['action'] == 'get-all-bots') {
        getAllBots();
    }
}
// POST Requeset
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'add-new-user') {
        addNewUser();
    } else if ($_POST['action'] == 'update-user') {
        updateUser();
    } else if ($_POST['action'] == 'delete-user') {
        deleteUser();
    } else if ($_POST['action'] == 'update-user-status') {
        updateUserStatus();
    }
    else if ($_POST['action'] == 'add-new-bot') {
        addNewBot();
    } else if ($_POST['action'] == 'update-bot') {
        updateBot();
    } else if ($_POST['action'] == 'delete-bot') {
        deleteBot();
    }
}

?>