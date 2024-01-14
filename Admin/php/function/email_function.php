<?php
include 'dbcon.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

// Check if the function is not already defined
if (!function_exists('get_email_content')) {
    function get_email_content($emc) {
        $pdo = connect_to_database();

        if ($pdo) {
            try {
                $query = "SELECT * FROM email_content WHERE id = :emc";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':emc', $emc, PDO::PARAM_INT);
                $stmt->execute();

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
        case 'email':
            sendEmail();
            break;
        case 'assign_reviewer':
            sendEmailAssignReviewer();
            break;
    }

    function sendEmail() {
       $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'qcujournal@gmail.com';
            $mail->Password = 'txtprxrytyqmloth';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $recipients = explode(', ', $_POST['hiddenEmail']);

            $mail->setFrom('qcujournal@gmail.com', 'QCU Journal');

            foreach ($recipients as $recipient) {
                $mail->addAddress($recipient);
            }

            $mail->Subject = $_POST['subject'];

            $mail->isHTML(true);

            $body = '';

            $quillContent = json_decode($_POST['quillContentOne'])->ops;

            foreach ($quillContent as $content) {
                $body .= nl2br($content->insert);
            }

            $mail->Body = $body;

            $id = $_POST['id'];
            $article_id = $_POST['article_id'];
            $articleFilesId = $_POST['checkedData'];
            $articleFilesId1 = $_POST['checkedData1'];
            $revisionFilesId = $_POST['checkedData2'];
            $copyeditedsubmissionFilesIds = $_POST['checkedData3'];
            $copyeditedrevisionFilesIds = $_POST['checkedData4'];
            $copyeditedFilesIds = $_POST['checkedData5'];

            if ($mail->send()) {
                echo "Email sent successfully.";
                if ($id == 1) {
                    updateReviewFiles(1, $articleFilesId);
                    updateArticleStatus($article_id, 4);
                    echo "<script>alert('Send to review successfully.');</script>"; 
                } elseif ($id == 2) {
                    updateArticleStatus($article_id, 7);
                    echo "<script>alert('Decline submission successfully.');</script>";
                } elseif ($id == 3) {
                    updateCopyeditingFiles(1, $articleFilesId1);
                    updateCopyeditingRevisionFiles(1, $revisionFilesId);
                    updateArticleStatus($article_id, 3);
                    echo "<script>alert('Send to copyediting successfully.');</script>";
                } elseif ($id == 4) {
                    echo "<script>alert('Request for revision successfully.');</script>";
                } elseif ($id == 5) {
                    updateCopyeditedFiles(1, $copyeditedFilesIds);
                    updateCopyeditedSubmissionFiles(1, $copyeditedsubmissionFilesIds);
                    updateCopyeditedRevisionFiles(1, $copyeditedrevisionFilesIds);                
                    updateArticleStatus($article_id, 2);
                    echo "<script>alert('Send to production successfully.');</script>";
            } else {
                echo 'Error sending email: ' . $mail->ErrorInfo;
            }
        }
        } catch (Exception $e) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
    }   
    

    function updateArticleStatus($article_id, $status) {
    
        $query = "UPDATE article 
                SET status = ?
                WHERE article_id = ?";
    
        $pdo = connect_to_database();
    
        $stm = $pdo->prepare($query);   
        $check = $stm->execute([$status, $article_id]);
    
        if ($check !== false) {
            echo "<script>alert('Article Status updated successfully');</script>";
        } else {
            echo "<script>alert('Failed to update status data');</script>";
        }
    }

    function updateReviewFiles($status, $articleFilesIds) {
    
        if (!is_array($articleFilesIds)) {
            $articleFilesIds = array($articleFilesIds);
        }
    
        $decodedIds = json_decode($articleFilesIds[0], true);
        $articleFilesIds = array_column($decodedIds, 'articleFilesId');
    
        // Create an array of named parameters for binding
        $params = array(':status' => $status);
        foreach ($articleFilesIds as $key => $articleFilesId) {
            $paramName = ":id$key";
            $params[$paramName] = $articleFilesId;
            $placeholders[] = $paramName;
        }
    
        $placeholders = implode(',', $placeholders);
    
        $query = "UPDATE article_files
                  SET review = :status
                  WHERE article_files_id IN ($placeholders)";
    
        $pdo = connect_to_database();
    
        $pdo->beginTransaction();
    
        $stm = $pdo->prepare($query);
    
        // Bind the parameters
        foreach ($params as $paramName => &$paramValue) {
            $stm->bindParam($paramName, $paramValue, PDO::PARAM_INT);
        }
    
        $check = $stm->execute();
    
        if ($check !== false) {
            echo "Review Files updated successfully";
            $pdo->commit();
        } else {
            echo "Failed to update file review data";
            print_r($stm->errorInfo());
            $pdo->rollBack();
        }
    }

    function updateCopyeditingFiles($status, $articleFilesIds) {
    
        if (!is_array($articleFilesIds)) {
            $articleFilesIds = array($articleFilesIds);
        }
    
        $decodedIds = json_decode($articleFilesIds[0], true);
        $articleFilesIds = array_column($decodedIds, 'articleFilesId');
    
        // Create an array of named parameters for binding
        $params = array(':status' => $status);
        foreach ($articleFilesIds as $key => $articleFilesId) {
            $paramName = ":id$key";
            $params[$paramName] = $articleFilesId;
            $placeholders[] = $paramName;
        }
    
        $placeholders = implode(',', $placeholders);
    
        $query = "UPDATE article_files
                  SET copyediting = :status
                  WHERE article_files_id IN ($placeholders)";
    
        $pdo = connect_to_database();
    
        $pdo->beginTransaction();
    
        $stm = $pdo->prepare($query);
    
        // Bind the parameters
        foreach ($params as $paramName => &$paramValue) {
            $stm->bindParam($paramName, $paramValue, PDO::PARAM_INT);
        }
    
        $check = $stm->execute();
    
        if ($check !== false) {
            echo "Review Files updated successfully";
            $pdo->commit();
        } else {
            echo "Failed to update file review data";
            print_r($stm->errorInfo());
            $pdo->rollBack();
        }
    }

    function updateCopyeditingRevisionFiles($status, $revisionFilesIds) {
    
        if (!is_array($revisionFilesIds)) {
            $revisionFilesIds = array($revisionFilesIds);
        }
    
        $decodedIds = json_decode($revisionFilesIds[0], true);
        $revisionFilesIds = array_column($decodedIds, 'revisionFilesId');
    
        // Create an array of named parameters for binding
        $params = array(':status' => $status);
        foreach ($revisionFilesIds as $key => $revisionFilesId) {
            $paramName = ":id$key";
            $params[$paramName] = $revisionFilesId;
            $placeholders[] = $paramName;
        }
    
        $placeholders = implode(',', $placeholders);
    
        $query = "UPDATE article_revision_files
                  SET copyediting = :status
                  WHERE revision_files_id IN ($placeholders)";
    
        $pdo = connect_to_database();
    
        $pdo->beginTransaction();
    
        $stm = $pdo->prepare($query);
    
        // Bind the parameters
        foreach ($params as $paramName => &$paramValue) {
            $stm->bindParam($paramName, $paramValue, PDO::PARAM_INT);
        }
    
        $check = $stm->execute();
    
        if ($check !== false) {
            echo "Revision Files updated successfully";
            $pdo->commit();
        } else {
            echo "Failed to update file revision data";
            print_r($stm->errorInfo());
            $pdo->rollBack();
        }
    }
      
    function updateCopyeditedFiles($status, $copyeditedFilesIds) {
    
        if (!is_array($copyeditedFilesIds)) {
            $copyeditedFilesIds = array($copyeditedFilesIds);
        }
    
        $decodedIds = json_decode($copyeditedFilesIds[0], true);
        $copyeditedFilesIds = array_column($decodedIds, 'copyeditedFilesId');
    
        // Create an array of named parameters for binding
        $params = array(':status' => $status);
        foreach ($copyeditedFilesIds as $key => $copyeditedFilesId) {
            $paramName = ":id$key";
            $params[$paramName] = $copyeditedFilesId;
            $placeholders[] = $paramName;
        }
    
        $placeholders = implode(',', $placeholders);
    
        $query = "UPDATE article_copyedited_files
                  SET production = :status
                  WHERE copyedited_files_id IN ($placeholders)";
    
        $pdo = connect_to_database();
    
        $pdo->beginTransaction();
    
        $stm = $pdo->prepare($query);
    
        // Bind the parameters
        foreach ($params as $paramName => &$paramValue) {
            $stm->bindParam($paramName, $paramValue, PDO::PARAM_INT);
        }
    
        $check = $stm->execute();
    
        if ($check !== false) {
            echo "Review production Files updated successfully";
            $pdo->commit();
        } else {
            echo "Failed to update file review production data";
            print_r($stm->errorInfo());
            $pdo->rollBack();
        }
    }

    function updateCopyeditedSubmissionFiles($status, $copyeditedsubmissionFilesIds) {

        if (!is_array($copyeditedsubmissionFilesIds)) {
            $copyeditedsubmissionFilesIds = array($copyeditedsubmissionFilesIds);
        }
    
        $decodedIds = json_decode($copyeditedsubmissionFilesIds[0], true);
        $copyeditedsubmissionFilesIds = array_column($decodedIds, 'articleFilesId');
    
        $params = array(':status' => $status);
        foreach ($copyeditedsubmissionFilesIds as $key => $articleFilesId) {
            $paramName = ":id$key";
            $params[$paramName] = $articleFilesId;
            $placeholders[] = $paramName;
        }
    
        $placeholders = implode(',', $placeholders);
    
        $query = "UPDATE article_files
                  SET production = :status
                  WHERE article_files_id IN ($placeholders)";
    
        $pdo = connect_to_database();
    
        $pdo->beginTransaction();
    
        $stm = $pdo->prepare($query);
    
        // Bind the parameters
        foreach ($params as $paramName => &$paramValue) {
            $stm->bindParam($paramName, $paramValue, PDO::PARAM_INT);
        }
    
        $check = $stm->execute();
    
        if ($check !== false) {
            echo "Review production Files updated successfully";
            $pdo->commit();
        } else {
            echo "Failed to update file review production data";
            print_r($stm->errorInfo());
            $pdo->rollBack();
        }
    }

    function updateCopyeditedRevisionFiles($status, $copyeditedrevisionFilesIds) {
        if (!is_array($copyeditedrevisionFilesIds)) {
            $copyeditedrevisionFilesIds = array($copyeditedrevisionFilesIds);
        }
    
        $decodedIds = json_decode($copyeditedrevisionFilesIds[0], true);
        $revisionFileIds = array_column($decodedIds, 'revisionFilesId');

        $params = array(':status' => $status);

        foreach ($revisionFileIds as $key => $revisionFilesId) {
            $paramName = ":id$key";
            $params[$paramName] = $revisionFilesId;
            $placeholders[] = $paramName;
        }
    
        $placeholders = empty($placeholders) ? 'NULL' : implode(',', $placeholders);
    
        $query = "UPDATE article_revision_files
                  SET production = :status
                  WHERE revision_files_id IN ($placeholders)";
    
        $pdo = connect_to_database();
    
        $pdo->beginTransaction();
    
        $stm = $pdo->prepare($query);
    
        foreach ($params as $paramName => &$paramValue) {
            $stm->bindParam($paramName, $paramValue, PDO::PARAM_INT);
        }
    
        $check = $stm->execute();
    
        if ($check !== false) {
            echo "Revision production Files updated successfully";
            $pdo->commit();
        } else {
            echo "Failed to update file revision production data";
            print_r($stm->errorInfo());
            $pdo->rollBack();
        }
    }    

    if (!function_exists('get_reviewer_content')) {
        function get_reviewer_content($emc) {
            $pdo = connect_to_database();
    
            if ($pdo) {
                try {
                    $query = "SELECT * FROM email_content WHERE id = :emc";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':emc', $emc, PDO::PARAM_INT);
                    $stmt->execute();
    
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

    function sendEmailAssignReviewer() {
        $mail = new PHPMailer(true);
 
         try {
             // SMTP configuration
             $mail->isSMTP();
             $mail->Host = 'smtp.gmail.com';
             $mail->SMTPAuth = true;
             $mail->Username = 'qcujournal@gmail.com';
             $mail->Password = 'txtprxrytyqmloth';
             $mail->SMTPSecure = 'ssl';
             $mail->Port = 465;

             $mail->addAddress($_POST['revieweremail']);
             $mail->setFrom('qcujournal@gmail.com', 'QCU Journal');
             $mail->Subject = $_POST['subject'];
             $mail->isHTML(true);
             
             $quillContent = json_decode($_POST['quillContentOne'])->ops;
             
             $body = '';
             
             foreach ($quillContent as $content) {
                 if (isset($content->insert)) {
                     $body .= nl2br($content->insert);
                 }
             }
             
             $mail->Body = $body;
 
             $reviewerid = $_POST['reviewerid'];
             $articleid = $_POST['articleid'];
             $round = $_POST['round'];
 
             if ($mail->send()) {
                 echo "Email sent to reviewer successfully.";
                 assignReviewer($articleid, $reviewerid, $round);
             } else {
                 echo 'Error sending email: ' . $mail->ErrorInfo;
             }
         } catch (Exception $e) {
             echo 'Mailer Error: ' . $mail->ErrorInfo;
         }
     }

     function assignReviewer($articleid, $reviewerid, $round) {
  
        $query = "INSERT INTO reviewer_assigned (article_id, author_id, round) VALUES (?, ?, ?)";
        
        $result = execute_query($query, [$articleid, $reviewerid, $round], true);
        
        if ($result !== false) {
            echo json_encode(['status' => true, 'message' => 'Record added successfully']);
        } else {
            // $errorInfo = get_db_error_info();
            // echo json_encode(['status' => false, 'message' => 'Failed to add record', 'error' => $errorInfo]);
            echo json_encode(['status' => false, 'message' => 'Failed to add record', 'error']);
        }
    }
    

 

     
?>