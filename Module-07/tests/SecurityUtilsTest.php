<?php

use PHPUnit\Framework\TestCase;
require __DIR__ . "/../src/Utils/SecurityUtils.php";

class SecurityUtilsTest extends TestCase {
    
    protected function setUp(): void {
        $_SESSION = []; // Reset session for each test
    }

    public function testSanitizeInput() {
        $input = '<script>alert("xss");</script>';
        $sanitized = SecurityUtils::sanitizeInput($input);
        $this->assertNotEquals($input, $sanitized);
        $this->assertStringContainsString('alert', $sanitized); // Check if 'alert' is still in the string but sanitized
    }

    public function testVerifyCsrfToken() {
        $_SESSION['csrf_token'] = 'token123';
        $_POST['csrf_token'] = 'token123';
        
        $this->assertNull(SecurityUtils::verifyCsrfToken()); // Expect no exception to be thrown
        
        $_POST['csrf_token'] = 'invalid_token';
        $this->expectException(Exception::class);
        SecurityUtils::verifyCsrfToken();
    }

    public function testGenerateCsrfToken() {
        $token = SecurityUtils::generateCsrfToken();
        $this->assertEquals($_SESSION['csrf_token'], $token);
        $this->assertEquals(64, strlen($token)); // Check token length (32 bytes hexadecimal)
    }

    public function testValidateRegistrationForm() {
        $validData = ['nom' => 'John', 'prenom' => 'Doe', 'adresse' => '123 Street', 'email' => 'john.doe@example.com', 'password' => 'Password1', 'confirmPassword' => 'Password1'];
        $errors = SecurityUtils::validateRegistrationForm(...array_values($validData));
        $this->assertEmpty($errors);
        
        $invalidData = ['nom' => 'John3', 'prenom' => 'Doe!', 'adresse' => '123+Street', 'email' => 'john.doe', 'password' => 'pass', 'confirmPassword' => 'password'];
        $errors = SecurityUtils::validateRegistrationForm(...array_values($invalidData));
        $this->assertNotEmpty($errors);
    }

    protected function tearDown(): void {
        $_SESSION = [];
        $_POST = [];
    }
}
