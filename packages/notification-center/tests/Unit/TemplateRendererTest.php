<?php

namespace malikad778\NotificationCenter\Tests\Unit;

use malikad778\NotificationCenter\Tests\TestCase;
use malikad778\NotificationCenter\Services\NotificationTemplateService;
use malikad778\NotificationCenter\Models\NotificationTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TemplateRendererTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_template_with_variables()
    {
        $template = NotificationTemplate::create([
            'name' => 'welcome_email',
            'channel' => 'mail',
            'subject' => 'Welcome {{ name }}',
            'content' => 'Hello {{ name }}, welcome to {{ app }}!',
        ]);

        $service = app(NotificationTemplateService::class);
        
        $rendered = $service->render($template, [
            'name' => 'John',
            'app' => 'My App'
        ]);

        $this->assertEquals('Hello John, welcome to My App!', $rendered);
    }
}
