<?php
namespace Src\controllers;

use Src\entities\UserAccount;
use Src\entities\UserProfile;
// controllers/LoginController.php
// test change
//require_once __DIR__ . '/../entities/UserAccount.php';
//require_once __DIR__ . '/../entities/UserProfile.php';

class LoginController {
    private $userProfileEntity;
	private $userAccountEntity;

    public function __construct($userAccount = null, $userProfile = null) {
        $this->userAccountEntity = $userAccount ?? new UserAccount();
		$this->userProfileEntity = $userProfile ?? new UserProfile();
    }

    public function loginUser($name, $password, $loginAs) {
        // get profile ID using the name selected--
        $profileNameArray = $this->userProfileEntity->getProfileIDByName($loginAs);
        $loginAsID = $profileNameArray['pID'] ?? null;

        // Attempt to get user info from UserAccount entity
        $userData = $this->userAccountEntity->login($name, $password);

        // Case 1: No account found
        if (empty($userData)) {
            return ["This user does not have an account.",""];
        }

        $profileID = $userData['profile'] ?? null;
        if (!$profileID) {
            return ["This user does not have a valid profile assigned.",""];
        }

        // Check if IDs match
        if($loginAsID !== $profileID) {
            return ["No such User exists.",""];
        }

        // Get the profile details using the profile ID
        $profile = $this->userProfileEntity->getProfileByID($profileID);
        if (empty($profile)) {
            return ["The user profile could not be found.",""];
        }

        // Case 2: Profile found but is suspended
        if (strtolower($profile['status']) === 'suspended') {
            return ["This user account's user profile is currently suspended.",""];
        }

        // Case 3: Profile is active → build redirection action
        if (strtolower($profile['status']) === 'active') {
            $pName = str_replace(' ', '', $profile['name']);
            $action = "{$profileID}_{$pName}_Menu";
            return [$action, $userData['aID']];
        }

        // Case 4: Any other status
        return ["This user account cannot be accessed at this time.",""];
    }
}
?>
