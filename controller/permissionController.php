<?php
function getAllAdmins() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get all data from tbl_administrators table
    $sql = 'SELECT id, name, email, password, last_signed_in, permission, is_online FROM tbl_administrators';
    $result = $conn->query($sql);
    // Create DOM using query result
    $dom = '';
    if ($result->num_rows > 0) {
        $index = 1;
        while ($row = $result->fetch_assoc()) {
            $dom .= '<tr class="border border-solid border-gray-300">' .
                '<td class="py-[12px] px-[40px]">' . $index . '</td>' .
                '<td class="py-[12px] px-[40px]">' . $row['name'] . '</td>' .
                '<td class="py-[12px] px-[40px]">' . $row['email'] . '</td>' .
                '<td class="py-[12px] px-[40px]" data-password="' . $row['password'] . '">***</td>' .
                '<td class="py-[12px] px-[40px]">' . $row['last_signed_in'] . '</td>' .
                '<td class="py-[12px] px-[40px]">' .
                    '<label class="switch">' .
                        '<input type="checkbox" data-id="' . $row['id'] . '"' .  ($row['permission'] == 1 ?' checked' : '') . '>' .
                        '<span class="slider round"></span>' .
                    '</label>' .
                '</td>' .
                '<td class="py-[12px] px-[40px]">';
                $dom .= ($row['is_online'] == 0) ?
                        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="inline-block w-6 h-6 text-red-700"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-2.625 6c-.54 0-.828.419-.936.634a1.96 1.96 0 00-.189.866c0 .298.059.605.189.866.108.215.395.634.936.634.54 0 .828-.419.936-.634.13-.26.189-.568.189-.866 0-.298-.059-.605-.189-.866-.108-.215-.395-.634-.936-.634zm4.314.634c.108-.215.395-.634.936-.634.54 0 .828.419.936.634.13.26.189.568.189.866 0 .298-.059.605-.189.866-.108.215-.395.634-.936.634-.54 0-.828-.419-.936-.634a1.96 1.96 0 01-.189-.866c0-.298.059-.605.189-.866zm-4.34 7.964a.75.75 0 01-1.061-1.06 5.236 5.236 0 013.73-1.538 5.236 5.236 0 013.695 1.538.75.75 0 11-1.061 1.06 3.736 3.736 0 00-2.639-1.098 3.736 3.736 0 00-2.664 1.098z" clip-rule="evenodd" /></svg>' :
                        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="inline-block w-6 h-6 text-green-700"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-2.625 6c-.54 0-.828.419-.936.634a1.96 1.96 0 00-.189.866c0 .298.059.605.189.866.108.215.395.634.936.634.54 0 .828-.419.936-.634.13-.26.189-.568.189-.866 0-.298-.059-.605-.189-.866-.108-.215-.395-.634-.936-.634zm4.314.634c.108-.215.395-.634.936-.634.54 0 .828.419.936.634.13.26.189.568.189.866 0 .298-.059.605-.189.866-.108.215-.395.634-.936.634-.54 0-.828-.419-.936-.634a1.96 1.96 0 01-.189-.866c0-.298.059-.605.189-.866zm2.023 6.828a.75.75 0 10-1.06-1.06 3.75 3.75 0 01-5.304 0 .75.75 0 00-1.06 1.06 5.25 5.25 0 007.424 0z" clipRule="evenodd" /></svg>';
                $dom .= '</td>' .
                '<td class="py-[12px] px-[40px]">' .
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
        $dom = '<tr><td colspan="8" class="py-[12px] px-[40px]">No data to display.</td></tr>';
    }
    echo $dom;
}

function addNewAdmin() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get value from request
    $name = $_POST['data']['name'];
    $email = $_POST['data']['email'];
    $password = $_POST['data']['password'];
    // Check if it already exists
    $sql = 'SELECT * FROM tbl_administrators WHERE email = "' . $email . '"';
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo 'exist';
        return;
    }
    // Insert new
    session_start();
    $sql = 'INSERT INTO tbl_administrators (name, email, password) VALUES ("' . $name . '", "' . $email . '", "' . $password . '")';
    $result = $conn->query($sql);
    if ($result == TRUE) {
        echo 'success';
    } else {
        echo 'failed';
    }
}

function updateAdmin() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get value from request
    $row_id = $_POST['data']['row_id'];
    $name = $_POST['data']['name'];
    $email = $_POST['data']['email'];
    $password = $_POST['data']['password'];
    // Check if it already exists
    $sql = 'SELECT * FROM tbl_administrators WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo 'not_exist';
        return;
    }
    // Update one
    $sql = 'UPDATE tbl_administrators SET name = "' . $name .'", email = "' . $email . '", password = "' . $password . '" WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    if ($result == TRUE) {
        echo 'success';
    } else {
        echo 'failed';
    }
}

function deleteAdmin() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get value from request
    $row_id = $_POST['data']['row_id'];
    // Check if it already exists
    $sql = 'SELECT * FROM tbl_administrators WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo 'not_exist';
        return;
    }
    // Delete one
    $sql = 'DELETE from tbl_administrators WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    if ($result == TRUE) {
        echo 'success';
    } else {
        echo 'failed';
    }
}

function updatePermission() {
    // Connect to MySQL
    require_once('../db-config.php');
    // Get value from request
    $row_id = $_POST['data']['row_id'];
    $permission = $_POST['data']['permission'];
    // Check if it already exists
    $sql = 'SELECT * FROM tbl_administrators WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        echo 'not_exist';
        return;
    }
    // Update one
    $sql = 'UPDATE tbl_administrators SET permission = ' . $permission . ' WHERE id = ' . $row_id;
    $result = $conn->query($sql);
    if ($result == TRUE) {
        echo 'success';
    } else {
        echo 'failed';
    }
}

// GET Requeset
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'get-all-admins') {
        getAllAdmins();
    }
}
// POST Requeset
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'add-new-admin') {
        addNewAdmin();
    } else if ($_POST['action'] == 'update-admin') {
        updateAdmin();
    } else if ($_POST['action'] == 'delete-admin') {
        deleteAdmin();
    } else if ($_POST['action'] == 'update-permission') {
        updatePermission();
    }
}

?>