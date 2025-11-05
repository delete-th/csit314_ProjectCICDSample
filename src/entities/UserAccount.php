<?php
// entities/UserAccount.php
require_once __DIR__ . '/../config/DBConnection.php';

class UserAccount {

    // Function: login
    // Purpose: Verify username & password and return only name, password, and profile
    public function login($name, $password) {
        global $conn; // Use global database connection

        $sql = "
            SELECT
				aID,
                name, 
                password, 
                userProfile AS profile
            FROM useraccount
            WHERE name = ? AND password = ?
        ";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            return []; // Fail silently if preparation fails
        }

        $stmt->bind_param("ss", $name, $password);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            return [
				'aID'	   => $row['aID'],
                'name'     => $row['name'],
                'password' => $row['password'],
                'profile'  => $row['profile']
            ];
        }

        // Close statement before returning
        $stmt->close();

        // Return empty array if user not found
        return [];
    }
	
	 // CREATE new account
    public function createUserAccount($name, $password, $phoneNumber, $address, $dob, $userProfile, $status) {
        global $conn;

        $stmt = $conn->prepare("INSERT INTO useraccount (name, password, phoneNumber, address, dob, userProfile, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt)
            return false;

        // Hash password before storing (do later)
        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt->bind_param("sssssis", 
            $name,
            $password,
            $phoneNumber,
            $address,
            $dob,
            $userProfile,
            $status
        );

        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // VIEW all accounts
    public function viewUserAccounts($asArray = true) {
        global $conn;

        $result = $conn->query("SELECT a.*, p.name AS profile_name 
                                FROM useraccount a 
                                LEFT JOIN userprofile p ON a.userProfile = p.pID");

        if (!$result)
            return [];

        if ($asArray) {
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            return $rows ?: [];
        }

        return $result;
    }

    // GET single account by ID
    public function getUserAccountByID($aID) {
        global $conn;

        $stmt = $conn->prepare("SELECT a.*, p.name AS profile_name 
                                FROM useraccount a 
                                LEFT JOIN userprofile p ON a.userProfile = p.pID
                                WHERE a.aID = ?");
        $stmt->bind_param("i", $aID);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // UPDATE account
    public function updateUserAccount($aID, $name, $password, $phoneNumber, $address, $dob, $userProfile, $status) {
        global $conn;

		$stmt = $conn->prepare("UPDATE useraccount 
								SET name = ?, password = ?, phoneNumber = ?, address = ?, dob = ?, userProfile = ?, status = ? 
								WHERE aID = ?");
		$stmt->bind_param(
			"sssssisi",
			$name,        // s
			$password,    // s
			$phoneNumber, // s
			$address,     // s
			$dob,         // s
			$userProfile, // i
			$status,      // s
			$aID          // i
		);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    // SUSPEND account (soft delete)
    public function suspendUserAccount($aID): bool {
        global $conn;

        $stmt = $conn->prepare("UPDATE useraccount SET status = ? WHERE aID = ?");
        $status = 'Suspended';
        $stmt->bind_param("si", $status, $aID);
        return $stmt->execute();
    }

    // SEARCH user accounts
    public function searchUserAccounts($searchBy, $searchInput) {
        global $conn;

        // Only allow valid columns for safety
        $allowedColumns = [
            'id' => 'aID',
            'name' => 'a.name',
            'phone' => 'a.phoneNumber',
            'address' => 'a.address',
            'status' => 'a.status',
            'profile' => 'p.name'
        ];

        $column = $allowedColumns[$searchBy] ?? 'a.name';

        $sql = "SELECT a.*, p.name AS profile_name 
                FROM useraccount a 
                LEFT JOIN userprofile p ON a.userProfile = p.pID
                WHERE $column LIKE ?";

        $stmt = $conn->prepare($sql);
        $like = "%" . $searchInput . "%";
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $result = $stmt->get_result();

        $accounts = [];
        while ($row = $result->fetch_assoc()) {
            $accounts[] = $row;
        }

        $stmt->close();
        return $accounts;
    }
}
?>
