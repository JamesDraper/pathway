# Pathway

Pathway is a command dispatcher for PHP built around one idea: **keep side effects out of your business logic, so you can test it without mocks.**

Each handler is split into three steps:

| Step         | Job                                                       | Typical contents                               | How you test it |
|--------------|-----------------------------------------------------------|------------------------------------------------|-----------------|
| `prepare()`  | Gather everything the logic needs                         | Database reads, file reads, API calls, clock   | Integration     |
| `process()`  | Make the decisions                                        | Pure business logic, no I/O                    | Unit            |
| `finalise()` | Act on those decisions                                    | Database writes, file writes, events, emails   | Integration     |

The result of each step is passed into the next, and the result of the last step is returned from `dispatch()`.

## Why

In most applications the interesting logic is tangled up with I/O. A handler loads a record, makes some decisions, saves the result and sends a notification, all in one method. To unit test the decisions you have to mock the repository, the mailer and anything else it touches. The mocks are tedious to write, they couple the tests to implementation details, and they only test that your code talks to the mocks the way you expected, not that it works.

Pathway gives that handler a fixed shape that separates the parts:

- **`prepare()`** reads. It turns the command into the data the logic needs.
- **`process()`** decides. It receives plain values and returns plain values. If it is pure (no I/O and no hidden state), it can be unit tested by calling it with inputs and checking the output. No mocks needed.
- **`finalise()`** writes. It takes the decisions made by `process()` and applies them to the outside world.

`prepare()` and `finalise()` are thin and deal only with real infrastructure. That makes them a natural fit for integration tests against a real database or filesystem, where a mock would prove nothing anyway. `process()` holds the logic and gets fast, simple unit tests.

The result is fewer mocks, or in many cases none at all.

## Example

A command is a plain object:

```php
final class RenameUser
{
    public function __construct(
        public readonly int $userId,
        public readonly string $name,
    ) {}
}
```

Its handler is named after it (`RenameUser` → `RenameUserHandler`) and created through a public static `make()` method:

```php
final class RenameUserHandler
{
    public function __construct(private readonly UserRepository $users) {}

    public static function make(): self
    {
        return new self(new PdoUserRepository(/* ... */));
    }

    // Reads: load what the logic needs.
    public function prepare(RenameUser $command): array
    {
        return [
            'user' => $this->users->find($command->userId),
            'name' => $command->name,
        ];
    }

    // Decides: pure logic, no I/O.
    public function process(User $user, string $name): array
    {
        $name = trim($name);

        if ($name === '') {
            throw new InvalidName('A name cannot be empty.');
        }

        return ['user' => $user->withName($name)];
    }

    // Writes: persist the outcome.
    public function finalise(User $user): User
    {
        $this->users->save($user);

        return $user;
    }
}
```

Dispatching the command runs all three steps in order:

```php
$user = $dispatcher->dispatch(new RenameUser(42, 'Ada'));
```

## Testing

`process()` never touches the repository, so it doesn't matter what the handler was constructed with. Call it directly with real values:

```php
public function testItTrimsTheName(): void
{
    $handler = new ReflectionClass(RenameUserHandler::class)->newInstanceWithoutConstructor();

    $result = $handler->process(new User(42, 'Old name'), '  Ada  ');

    $this->assertSame('Ada', $result['user']->name);
}

public function testItRejectsAnEmptyName(): void
{
    $handler = new ReflectionClass(RenameUserHandler::class)->newInstanceWithoutConstructor();

    $this->expectException(InvalidName::class);

    $handler->process(new User(42, 'Old name'), '   ');
}
```

No repository mock, no expectations about which methods get called. Every edge case of the business rules can be covered this way, quickly.

`prepare()` and `finalise()` are covered by a smaller number of integration tests against a real (test) database: does `prepare()` load the right user, does `finalise()` save it. Those tests check the I/O actually works, which a mock never could.

## How values flow between steps

- Only `process()` is required. `prepare()` and `finalise()` are optional, and all three must be public and non-static.
- The first step in the chain receives the command. (It is also passed the handler as a second argument, which it is free to ignore.)
- Each later step receives the return value of the step before it, which must be an array:
  - a **list** (`[$a, $b]`) is passed as positional arguments;
  - an **associative array** (`['user' => $user]`) is matched to parameters by name. Parameters with defaults may be left out; unknown keys are an error.
- The return value of the last step is the return value of `dispatch()`.

If a step returns something that doesn't fit the next step's signature, Pathway throws an exception that names the method, the parameter and the types involved.
