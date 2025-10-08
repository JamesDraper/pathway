# Pathway

> **Never mock again.**
> Pathway is a lightweight **command and event dispatcher** that helps you separate what you need to test from what you need to call, so you can stop wiring mocks and start verifying real logic.

---

## How it works

Pathway gives you a **simple, testable structure** for your application logic:

### `prepare -> process -> finalize`

- **prepare()** gather data and call other components.
- **process()** pure, deterministic logic (easy to test).
- **finalize()** perform outcomes (save, emit, notify, etc).

> Keeping `process()` pure means you can unit test your business logic in complete isolation. No mocks, no fakes, no setup overhead.
> The `prepare()` and `finalize()` methods handle real-world coordination and can be tested through integration tests instead.
> The result is clean, predictable tests that reflect how your code actually runs.

---

## Example

```php
final class SendEmailCommand
{
    public function __construct(
        public int $userId,
        public string $template,
    ) {}
}

final class SendEmailHandler
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    public function prepare(SendEmailCommand $command, \Pathway\DispatcherInterface $dispatcher): array
    {
        $user = $this->userService->load($command->userId);

        $templatePath = vsprintf('%s/templates/%s.html', [
            __DIR__,
            $command->template,
        ]);

        $templateBody = file_get_contents($templatePath);

        return [
            'user' => $user,
            'templateBody' => $templateBody,
        ];
    }

    public function process(User $user, string $templateBody): array
    {
        $body = str_replace('{{name}}', $user->getName(), $templateBody);

        return [
            'body' => $body,
            'user' => $user,
        ];
    }

    public function finalize(array $user, string $body): void
    {
        mail($user->getEmail(), 'Welcome!', $body);
    }
}

$dispatcher = new \Pathway\Dispatcher();
$dispatcher->command(new SendEmailCommand(42, 'welcome'));
```

### Events

Pathway handles events similarly to commands, with the same `prepare() -> process() -> finalize()` pattern. Unlike commands, an event can have **multiple handlers**, and the dispatcher **ignores any return values**. Events are fire-and-forget:

```php
$dispatcher = new \Pathway\Dispatcher();
$dispatcher->event(new UserRegisteredEvent($userId));
```
