<?php
include 'dbcon.php';

if (!function_exists('get_announcement_list')) {
    function get_announcement_list()
    {
        $pdo = connect_to_database();

        if ($pdo) {
            try {
                $query = "SELECT * FROM announcement WHERE status = 1";
                $stmt = $pdo->query($query);

                $result = $stmt->fetchAll(PDO::FETCH_OBJ);

                return $result;
            } catch (PDOException $e) {

                echo "Error: " . $e->getMessage();
                return false;
            }
        }

        return false;
    }
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'add':
        addRecord();
        break;
    case 'archive':
        archiveAnnouncement();
        break;
    case 'fetch':
        fetchUserData();
        break;
    case 'update':
        updateAnnouncementData();
        break;
}
        
function fetchUserData() {
    $announcement_id = $_POST['announcement_id'];

    

    $result = execute_query("SELECT * FROM announcement WHERE announcement_id = ?", [$announcement_id]);

    header('Content-Type: application/json');

    if ($result !== false) {
        echo json_encode(['status' => true, 'data' => $result]);
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to fetch user data']);
    }
}
                
function addRecord()
{
    try {
        $announcementtype = $_POST['announcementtype'];
        $title = $_POST['title'];
        $announcement_description = $_POST['announcement_description'];
        $announcement = $_POST['announcement'];
        
        $uploadPath = "../../../Files/announcement-image/";

        $files = isset($_FILES['upload_image']) ? $_FILES['upload_image'] : ''; 
        $file_name = basename($_FILES["upload_image"]["name"]);
        
        $timestamp = time();
        $success = true;
        $hashedTimestamp = hash('sha256', (string)$timestamp);
        $last12Hash = substr($hashedTimestamp, -12);

        $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $hashfilename = $last12Hash . '-' . $announcementtype . '-' . $title . '.' . $imageFileType;

        $allowedFileTypes = array('jpg', 'jpeg', 'png', 'gif');
        
        if (!in_array($imageFileType, $allowedFileTypes)) {
            $success = false;
            throw new Exception("File $hashfilename - Invalid file type ({$imageFileType})");
        }

        $upload_image = $uploadPath . $hashfilename;

        if ($success && move_uploaded_file($_FILES["upload_image"]["tmp_name"], $upload_image)) {
            // File uploaded successfully
        } else {
            throw new Exception('Failed to move uploaded file.');
        }
    
        $query = "INSERT INTO announcement (title, announcementtype, announcement_description, announcement, upload_image) 
                  VALUES (?, ?, ?, ?, ?)";
    
        $result = execute_query($query, [$title, $announcementtype, $announcement_description, $announcement, $hashfilename], true);
    
        if ($result !== true) {
            echo json_encode(['status' => true, 'message' => 'Record added successfully']);
        } else {
            throw new Exception('Failed to add record');
        }
    } catch (Exception $e) {
        echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        error_log($e->getMessage(), 0);
    }
}
           
function updateAnnouncementData() {
    try {
        $announcement_id = $_POST['announcement_id'];
        $updatedData = $_POST['updated_data'];

        $query = "UPDATE announcement 
                    SET title = ?, announcement_description = ?, announcement = ? 
                    WHERE announcement_id = ?";
        
        $pdo = connect_to_database();

        $stm = $pdo->prepare($query);
        $check = $stm->execute([
            $updatedData['title'],
            $updatedData['announcement_description'],
            $updatedData['announcement'],
            $announcement_id
        ]);

        header('Content-Type: application/json');

        if ($check !== false) {
            echo json_encode(['status' => true, 'message' => 'Announcement data updated successfully']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to update Announcement data']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function archiveAnnouncement() {
    $announcement_id = $_POST['announcement_id'];

    $query = "UPDATE announcement SET status = 0 WHERE announcement_id = ?";
    $result = execute_query($query, [$announcement_id]);

    echo json_encode(['status' => $result !== false, 'message' => $result !== false ? 'Announcement archived successfully' : 'Failed to archive announcement']);
}


?>

    
