# Contributing to Quantum PHP Framework

Thanks for your interest in contributing to Quantum PHP Framework 💡 Whether you’re fixing a bug, adding a feature, or improving docs — every contribution helps make the framework stronger.

---

## Before You Start

Familiarize yourself with the codebase: Quantum PHP Framework is modular — most features live under `/src` (core logic) and `/modules` (demo templates and optional components). Start by reviewing the `Router`, `Controller`, and `QtModel` classes to understand the framework flow. 
Check existing issues and milestones: look for tickets labeled `good first issue`, `help wanted`, or assigned to an upcoming version. 
Don’t hesitate to open a new issue if you find something worth improving.

---

## Local Setup (Fork-Based)

Quantum consists of **two repositories** that serve different purposes:

🧩 1. `quantum-php-core` **(The core framework)**

If your goal is to contribute to the core framework (e.g., routing, DI, ORM, view engine, etc.):

1. Fork the repository on GitHub and clone your fork locally:

```bash
git clone https://github.com/your-username/quantum-php-core.git
cd quantum-php-core
composer install
vendor/bin/phpunit --stderr
```

🚀 2. `quantum-php-project` **(Starter Project)**

If you want to run Quantum project locally, see how modules work, or test the framework in action, use the starter project:

```bash
git clone https://github.com/softberg/quantum-php-project.git
cd quantum-php-project
composer install
php qt serve
```

You should see the demo project running on `http://127.0.0.1:8000`.

---

## Development Workflow

1. Create a new branch in your fork:

```bash
git checkout -b feature/your-feature-name
```

2. Make your changes — keep code style consistent (PSR-12, no unnecessary dependencies), prefer anonymous functions when working with callbacks to preserve `$this` context, and add or update unit tests if your change affects logic.

3. Run tests:

```bash
vendor/bin/phpunit --stderr
```

4. Commit and push your branch:

```bash
git commit -m "Add: SoftDeletes trait for models"
git push origin feature/your-feature-name
```

5. Open a Pull Request from your fork → `softberg/quantum-php-core`. Describe **what** you changed, **why**, and **how** to test it. Reference any related issues. Always work in a branch, never directly on `main` of your fork.

---

## Testing

Quantum uses **PHPUnit** for tests. If you add new features, make sure they include unit tests — especially for database or HTTP-related components. For in-memory testing, use SQLite in-memory databases (for example, in tests using IdiormDbal).

---

## Code Guidelines

- PHP 7.3+ compatibility is required.  
- Keep class responsibilities clear — avoid bloated classes.  
- Follow the existing directory structure
- Always document public methods.  
- Avoid breaking backward compatibility unless discussed.

---

## Modules & Components

Modules in Quantum are self-contained MVC units. When building or modifying modules, keep reusable logic in `/src/Core`, use CLI commands (`DemoCommand`, `CreateUserCommand`, `CreatePostCommand`, etc.) for setup, and test module installation flow end-to-end before submitting.

---

## Communication

Open a GitHub issue for discussions or questions before large changes. Use clear and respectful communication — feedback is always welcome.

---

> “Code is written once, but read many times.” Keep it clean, minimal, and purposeful.

Happy coding ⚡  
— The Quantum PHP Team
