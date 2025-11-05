<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/controllers/LoginController.php';
require_once __DIR__ . '/../src/entities/UserAccount.php';
require_once __DIR__ . '/../src/entities/UserProfile.php';

class LoginControllerTest extends TestCase
{
    private $loginController;
    private $userAccountMock;
    private $userProfileMock;

    protected function setUp(): void
    {
        // Create mocks for UserAccount and UserProfile
        $this->userAccountMock = $this->createMock(UserAccount::class);
        $this->userProfileMock = $this->createMock(UserProfile::class);

        // Inject mocks into LoginController
        $this->loginController = new LoginController(
            $this->userAccountMock,
            $this->userProfileMock
        );
    }

    public function testNoAccountFound()
    {
        $this->userAccountMock->method('login')->willReturn([]);
        $this->userProfileMock->method('getProfileIDByName')->willReturn(['pID'=>1]);

        $result = $this->loginController->loginUser('alice','password','User Admin');
        $this->assertEquals(["This user does not have an account.",""], $result);
    }

    public function testProfileActive()
    {
        $this->userAccountMock->method('login')->willReturn(['profile'=>1,'aID'=>10]);
        $this->userProfileMock->method('getProfileIDByName')->willReturn(['pID'=>1]);
        $this->userProfileMock->method('getProfileByID')->willReturn(['status'=>'active','name'=>'User Admin']);

        $result = $this->loginController->loginUser('alice','password','User Admin');
        $this->assertEquals(['1_UserAdmin_Menu',10], $result);
    }

    public function testProfileSuspended()
    {
        $this->userAccountMock->method('login')->willReturn(['profile'=>1,'aID'=>10]);
        $this->userProfileMock->method('getProfileIDByName')->willReturn(['pID'=>1]);
        $this->userProfileMock->method('getProfileByID')->willReturn(['status'=>'suspended','name'=>'User Admin']);

        $result = $this->loginController->loginUser('alice','password','User Admin');
        $this->assertEquals(["This user account's user profile is currently suspended.",""], $result);
    }
}
?>
