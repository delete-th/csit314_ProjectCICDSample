<?php
// entities/UserProfile.php
require_once __DIR__ . '/../config/DBConnection.php';

class UserProfile {

    // create new profile
    public static function createProfile($name, $description, $status) {
        global $conn; // use the global connection

		$sql = $conn->prepare("INSERT INTO userprofile (name, description, status) VALUES (?, ?, ?)");
		if (!$sql)
			return false;
			
		$sql->bind_param("sss", 
				$name,
				$description,
				$status );
					
		$result = $sql->execute();
		$sql->close();
		return $result;
    }

    // view profiles
	public function viewProfiles() { 
		global $conn;

		$result = $conn->query("SELECT * FROM userprofile");
		
		while ($row = $result->fetch_assoc()) {
            $profiles[] = $row;
        }
		return $profiles; // return array
	}
	
	public function getProfileByID($pID) {
		global $conn;
		$stmt = $conn->prepare("SELECT * FROM userProfile WHERE pID = ?");
		$stmt->bind_param("i", $pID);
		$stmt->execute();
		return $stmt->get_result()->fetch_assoc();
	}

    // used in login --
    public function getProfileIDByName($loginAs) {
        global $conn;
        $stmt = $conn->prepare("SELECT pID FROM userprofile WHERE name = ?");
        $stmt->bind_param("s", $loginAs);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

	// update profile by ID
    public function updateProfile($pID, $name, $description, $status) {
        global $conn;

        $stmt = $conn->prepare("UPDATE userprofile 
                                SET name = ?, description = ?, status = ? 
                                WHERE pID = ?");
        if (!$stmt)
            return false;

        $stmt->bind_param("sssi", $name, $description, $status, $pID);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

	// suspend profile by ID
	public function suspendProfile($pID): bool {
		global $conn;
		$stmt = $conn->prepare("UPDATE userProfile SET status = ? WHERE pID = ?");
		$status = 'Suspended';
		$stmt->bind_param("si", $status, $pID);
		return $stmt->execute();
	}

    public function searchUserProfile($searchBy, $searchInput) {
        global $conn;

        // Only allow valid column names to avoid SQL injection
        $allowedColumns = ['id' => 'pID', 'name' => 'name', 'description' => 'description'];
        $column = $allowedColumns[$searchBy] ?? 'name';

        $sql = "SELECT * FROM userprofile WHERE $column LIKE ?";
        $stmt = $conn->prepare($sql);
        $like = "%" . $searchInput . "%";
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $result = $stmt->get_result();

        $profiles = [];
        while ($row = $result->fetch_assoc()) {
            $profiles[] = $row;
        }

        $stmt->close();
        return $profiles;
    }
}
?>
