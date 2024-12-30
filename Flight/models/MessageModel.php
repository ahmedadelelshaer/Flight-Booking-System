<?php
include_once '../php/includes/db.php';
class MessageModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = connectToDB();
    }

    public function Send_Message1($sender_id, $receiver_id, $message,$date)
    {

        $sql = "INSERT INTO messages (sender_id, receiver_id, content,date) VALUES (:sender_id, :receiver_id, :content,:date)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'content' => $message,
            'date' => $date
        ]);
        return true;
    }

    public function Send_Message($sender_id, $receiver_id, $message,$date)
    {
        $sql = "SELECT company_id FROM flights WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $receiver_id]);
        $companyId = $stmt->fetchColumn();
        if (!$companyId) {
            return false;
        }
        $sql = "INSERT INTO messages (sender_id, receiver_id, content,date) VALUES (:sender_id, :receiver_id, :content,:date)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'sender_id' => $sender_id,
            'receiver_id' => $companyId,
            'content' => $message,
            'date' => $date
        ]);
        return true;
    }
    public function get_messages($receiver_id)
    {
        $sql = "SELECT * FROM messages WHERE receiver_id = :receiver_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['receiver_id' => $receiver_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}