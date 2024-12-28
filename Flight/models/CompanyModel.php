<?php
include_once '../php/includes/db.php';
class CompanyModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = connectToDB();
    }

    // Fetch company profile details


    // Update company profile details
    public function updateCompanyProfile($companyId, $name, $bio, $address, $logoImg = null)
    {
        $sql = "UPDATE company SET name = :name, bio = :bio, address = :address";
        if ($logoImg) {
            $sql .= ", logo_img = :logo_img";
        }
        $sql .= " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $params = [
            'name' => $name,
            'bio' => $bio,
            'address' => $address,
            'id' => $companyId,
        ];

        if ($logoImg) {
            $params['logo_img'] = $logoImg;
        }

        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    // Fetch flights list for a company


}
