<?php

namespace Tests\Unit\Mail;

use Tests\TestCase;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class WelcomeMailTest extends TestCase
{
    /**
     * Test that WelcomeMail can be instantiated with valid parameters.
     *
     * @return void
     */
    public function test_welcome_mail_can_be_instantiated()
    {
        $userName = 'John Doe';
        $userEmail = 'john@example.com';

        $mail = new WelcomeMail($userName, $userEmail);

        $this->assertInstanceOf(WelcomeMail::class, $mail);
        $this->assertEquals($userName, $mail->userName);
        $this->assertEquals($userEmail, $mail->userEmail);
    }

    /**
     * Test that WelcomeMail has correct subject.
     *
     * @return void
     */
    public function test_welcome_mail_has_correct_subject()
    {
        $mail = new WelcomeMail('John Doe', 'john@example.com');
        $built = $mail->build();

        $this->assertEquals('Welcome to Follow The Money (FTM) - Account Created!', $built->subject);
    }

    /**
     * Test that WelcomeMail uses correct view.
     *
     * @return void
     */
    public function test_welcome_mail_uses_correct_view()
    {
        $mail = new WelcomeMail('John Doe', 'john@example.com');
        $built = $mail->build();

        $this->assertEquals('emails.welcome', $built->view);
    }

    /**
     * Test that WelcomeMail passes data to view correctly.
     *
     * @return void
     */
    public function test_welcome_mail_passes_data_to_view()
    {
        $userName = 'John Doe';
        $userEmail = 'john@example.com';

        $mail = new WelcomeMail($userName, $userEmail);
        $viewData = $mail->build()->viewData;

        $this->assertArrayHasKey('userName', $viewData);
        $this->assertArrayHasKey('userEmail', $viewData);
        $this->assertArrayHasKey('appUrl', $viewData);
        $this->assertEquals($userName, $viewData['userName']);
        $this->assertEquals($userEmail, $viewData['userEmail']);
    }

    /**
     * Test that WelcomeMail uses appUrl from config.
     *
     * @return void
     */
    public function test_welcome_mail_uses_app_url_from_config()
    {
        Config::set('app.url', 'https://test.example.com');

        $mail = new WelcomeMail('John Doe', 'john@example.com');
        $viewData = $mail->build()->viewData;

        $this->assertEquals('https://test.example.com', $viewData['appUrl']);
    }

    /**
     * Test that WelcomeMail uses default URL when config is not set.
     *
     * @return void
     */
    public function test_welcome_mail_uses_default_url_when_config_not_set()
    {
        Config::set('app.url', null);

        $mail = new WelcomeMail('John Doe', 'john@example.com');
        $viewData = $mail->build()->viewData;

        $this->assertEquals('https://FTM.ng', $viewData['appUrl']);
    }

    /**
     * Test that WelcomeMail can be queued.
     *
     * @return void
     */
    public function test_welcome_mail_can_be_queued()
    {
        Mail::fake();

        $userName = 'John Doe';
        $userEmail = 'john@example.com';

        Mail::to($userEmail)->send(new WelcomeMail($userName, $userEmail));

        Mail::assertSent(WelcomeMail::class, function ($mail) use ($userName, $userEmail) {
            return $mail->userName === $userName && $mail->userEmail === $userEmail;
        });
    }

    /**
     * Test that WelcomeMail implements ShouldQueue.
     *
     * @return void
     */
    public function test_welcome_mail_implements_should_queue()
    {
        $mail = new WelcomeMail('John Doe', 'john@example.com');

        $this->assertContains('Illuminate\Contracts\Queue\ShouldQueue', class_implements($mail));
    }

    /**
     * Test that WelcomeMail uses correct queue configuration.
     *
     * @return void
     */
    public function test_welcome_mail_uses_correct_queue_configuration()
    {
        $mail = new WelcomeMail('John Doe', 'john@example.com');
        
        // Check that the mail is queued on the 'emails' queue
        $this->assertEquals('emails', $mail->queue);
    }
}
